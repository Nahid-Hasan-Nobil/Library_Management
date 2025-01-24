<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = []; // Initialize an array for validation errors

    $cookieName = $_POST['book-title'] ?? ''; 
    $cookieValue = $_POST['student-name'] ?? ''; 

    // Database connection
    $conn = mysqli_connect('localhost', 'root', '', 'library');
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Read JSON data with tokens from token.json
    $jsonFile = 'token.json';
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
        $validTokens = $data['token'] ?? [];
    } else {
        $validTokens = [];
        error_log("Token file not found."); // Log an error if the file is missing
    }

    // Used tokens array (simulated from cookies)
    $usedTokens = isset($_COOKIE['used-tokens']) ? json_decode($_COOKIE['used-tokens'], true) : [];

    // Validation Logic
    $studentName = $_POST['student-name'] ?? '';
    if (empty($studentName) || !preg_match("/^[a-zA-Z\s.]+$/", $studentName)) {
        $errors[] = "Student name must contain only letters and spaces, and cannot be empty.";
    }

    $studentId = $_POST['student-id'] ?? '';
    if (empty($studentId) || !preg_match("/^[A-Za-z0-9\-]+$/", $studentId)) {
        $errors[] = "Student ID must be alphanumeric and cannot be empty.";
    }

    $bookTitle = $_POST['book-title'] ?? '';
    if (empty($bookTitle) || !preg_match("/^[a-zA-Z0-9\s]+$/", $bookTitle)) {
        $errors[] = "Book title must contain only letters, numbers, and spaces, and cannot be empty.";
    } else {
        // Check if the book exists in the database
        $query = "SELECT count FROM books WHERE booktitle = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $bookTitle);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $bookCount);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($bookCount === null) {
            $errors[] = "The book titled '$bookTitle' does not exist in the library database.";
        } elseif ($bookCount <= 0) {
            $errors[] = "The book titled '$bookTitle' is currently unavailable.";
        }
    }

    $token = $_POST['token'] ?? '';
    $isValidToken = in_array($token, $validTokens);

    if (empty($token)) {
        $errors[] = "Token is required.";
    } elseif (!$isValidToken) {
        $borrowDate = $_POST['borrow-date'] ?? '';
        $returnDate = $_POST['return-date'] ?? '';
        if (!empty($borrowDate) && !empty($returnDate)) {
            $borrowDateTime = strtotime($borrowDate);
            $returnDateTime = strtotime($returnDate);
            $dateDiff = ($returnDateTime - $borrowDateTime) / (60 * 60 * 24);

            if ($dateDiff > 10) {
                $errors[] = "For invalid tokens, the return date must be within 10 days of the borrow date.";
            }
        }
    } else {
        if (in_array($token, $usedTokens)) {
            $errors[] = "Token has already been used. Please use a new token.";
        }
    }

    $borrowDate = $_POST['borrow-date'] ?? '';
    $returnDate = $_POST['return-date'] ?? '';
    if (empty($borrowDate) || empty($returnDate)) {
        $errors[] = "Both borrow date and return date are required.";
    } else {
        $borrowDateTime = strtotime($borrowDate);
        $returnDateTime = strtotime($returnDate);
        $dateDiff = ($returnDateTime - $borrowDateTime) / (60 * 60 * 24);

        if ($dateDiff < 0) {
            $errors[] = "The return date cannot be before the borrow date.";
        } elseif ($isValidToken && $dateDiff > 30) {
            $errors[] = "The return date must be within 30 days of the borrow date for valid tokens.";
        }
    }

    $fees = $_POST['fees'] ?? '';
    if (!is_numeric($fees) || $fees < 0) {
        $errors[] = "Fees must be a positive number.";
    }

    $paid = $_POST['paid'] ?? '';
    if (empty($paid)) {
        $errors[] = "Please select if the fee has been paid.";
    }

    if (!empty($errors)) {
        echo "<div style='max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ddd; font-family: Arial, sans-serif;'>";
        echo "<h2 style='text-align: center; color: #333;'>Validation Errors</h2>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        if ($isValidToken) {
            $usedTokens[] = $token;
            setcookie('used-tokens', json_encode($usedTokens), time() + 86400);
        }

        echo "<div style='max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ddd; font-family: Arial, sans-serif;'>";
        echo "<h2 style='text-align: center; color: #333;'>Borrow Receipt</h2>";
        echo "<hr>";
        echo "<p><strong>Student Full Name:</strong> $studentName</p>";
        echo "<p><strong>Student AIUB ID:</strong> $studentId</p>";
        echo "<p><strong>Book Title:</strong> $bookTitle</p>";
        echo "<p><strong>Borrow Date:</strong> $borrowDate</p>";
        echo "<p><strong>Token:</strong> $token</p>";
        echo "<p><strong>Return Date:</strong> $returnDate</p>";
        echo "<p><strong>Fees:</strong> $fees Tk</p>";
        echo "<p><strong>Paid:</strong> $paid</p>";
        echo "<hr>";
        echo "<p style='text-align: center;'>Thank you for using the library service.</p>";
        echo "</div>";
    }

    mysqli_close($conn);
} else {
    echo "<p>No form data submitted.</p>";
}
?>
