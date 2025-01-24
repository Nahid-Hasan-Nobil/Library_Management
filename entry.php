<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = []; // Initialize an array for validation errors

    $bookTitle = $_POST['book-title'] ?? '';
    $author = $_POST['author'] ?? '';
    $isbnNo = $_POST['isbn-no'] ?? '';
    $count = $_POST['count'] ?? '';
    $category = $_POST['category'] ?? '';
  

    // Validate Book Title
    if (empty($bookTitle) || !preg_match("/^[a-zA-Z0-9\s]+$/", $bookTitle)) {
        $errors[] = "Book title must contain only letters, numbers, and spaces, and cannot be empty.";
    }

    // Validate Author Name
    if (empty($author) || !preg_match("/^[a-zA-Z\s.]+$/", $author)) {
        $errors[] = "Author name must contain only letters, spaces, and periods, and cannot be empty.";
    }

    // Validate ISBN Number
    if (empty($isbnNo) || !preg_match("/^[0-9\-]+$/", $isbnNo)) {
        $errors[] = "ISBN number must be numeric and can include hyphens.";
    } else {
        // Check if the ISBN number already exists in the database
        $conn = mysqli_connect('localhost', 'root', '', 'library');
        if ($conn) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM books WHERE isbnno = ?");
            $stmt->bind_param("s", $isbnNo);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                $errors[] = "The ISBN number already exists in the library database.";
            }
            $stmt->close();
            mysqli_close($conn);
        } else {
            $errors[] = "Database connection failed. Could not validate ISBN number.";
        }
    }

    // Validate Count
    if (empty($count) || !is_numeric($count) || $count <= 0) {
        $errors[] = "Count must be a positive number.";
    }

    // Validate Category
    if (empty($category) || !preg_match("/^[a-zA-Z\s]+$/", $category)) {
        $errors[] = "Category must contain only letters and spaces, and cannot be empty.";
    }

 
    if (empty($errors)) {

        $conn = mysqli_connect('localhost', 'root', '', 'library');

     
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $sql = "INSERT INTO books (booktitle, author, isbnno, count, category) 
                VALUES ('$bookTitle', '$author', '$isbnNo', '$count', '$category')";

        if (mysqli_query($conn, $sql)) {
            echo "<div style='max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ddd; font-family: Arial, sans-serif;'>";
            echo "<h2 style='text-align: center; color: #333;'>Book Entry Successful</h2>";
            echo "<hr>";
            echo "<p><strong>Book Title:</strong> $bookTitle</p>";
            echo "<p><strong>Author:</strong> $author</p>";
            echo "<p><strong>ISBN Number:</strong> $isbnNo</p>";
            echo "<p><strong>Count:</strong> $count</p>";
            echo "<p><strong>Category:</strong> $category</p>";
            echo "<hr>";
            echo "<p style='text-align: center;'>The book has been successfully added to the library system.</p>";
            echo "</div>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        
        mysqli_close($conn);
    } else {
      
        echo "<div style='max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ddd; font-family: Arial, sans-serif;'>";
        echo "<h2 style='text-align: center; color: #333;'>Validation Errors</h2>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
} else {
    echo "<p>No form data submitted.</p>";
}
?>
