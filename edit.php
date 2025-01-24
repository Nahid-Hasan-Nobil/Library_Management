<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $isbnNo = $_POST['isbnno'] ?? '';
    $bookTitle = $_POST['book-title'] ?? '';
    $author = $_POST['author'] ?? '';
    $count = $_POST['count'] ?? '';
    $category = $_POST['category'] ?? '';
    $errors = [];

    // Validate ISBN Number (Primary Key)
    if (empty($isbnNo) || !preg_match("/^[0-9\-]+$/", $isbnNo)) {
        $errors[] = "ISBN number must be numeric and can include hyphens.";
    }

    // Optional validation for other fields
    if (!empty($bookTitle) && !preg_match("/^[a-zA-Z0-9\s]+$/", $bookTitle)) {
        $errors[] = "Book title must contain only letters, numbers, and spaces.";
    }
    if (!empty($author) && !preg_match("/^[a-zA-Z\s.]+$/", $author)) {
        $errors[] = "Author name must contain only letters, spaces, and periods.";
    }
    if (!empty($count) && (!is_numeric($count) || $count <= 0)) {
        $errors[] = "Count must be a positive number.";
    }
    if (!empty($category) && !preg_match("/^[a-zA-Z\s]+$/", $category)) {
        $errors[] = "Category must contain only letters and spaces.";
    }

    // Database Connection and Update Logic
    if (empty($errors)) {
        $conn = mysqli_connect('localhost', 'root', '', 'library');
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Prepare the update query
        $sql = "UPDATE books SET ";
        $updates = [];
        if (!empty($bookTitle)) $updates[] = "booktitle='" . mysqli_real_escape_string($conn, $bookTitle) . "'";
        if (!empty($author)) $updates[] = "author='" . mysqli_real_escape_string($conn, $author) . "'";
        if (!empty($count)) $updates[] = "count='" . mysqli_real_escape_string($conn, $count) . "'";
        if (!empty($category)) $updates[] = "category='" . mysqli_real_escape_string($conn, $category) . "'";
        $sql .= implode(", ", $updates);
        $sql .= " WHERE isbnno='" . mysqli_real_escape_string($conn, $isbnNo) . "'";

        // Execute the query
        if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
            echo "<p>Book information updated successfully.</p>";
        } else {
            echo "<p>Failed to update book information. Ensure the ISBN number exists in the database.</p>";
        }

        mysqli_close($conn);
    } else {
        // Display validation errors
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p>Invalid request method.</p>";
}
?>
