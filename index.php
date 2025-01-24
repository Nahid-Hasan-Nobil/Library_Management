<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <!-- Move id-container outside top-block but still inside the container -->
        <div class="id-container">
            <img src="id.png" alt="Your ID">
        </div>
        <div class="top-block">
            <h3>Books List</h3>
            <?php
                // Database connection
                $conn = mysqli_connect('localhost', 'root', '', 'library');

                // Check connection
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Fetch books from the database
                $sql = "SELECT booktitle, author, isbnno, count, category FROM books";
                $result = mysqli_query($conn, $sql);

                // Check if books are available
                if (mysqli_num_rows($result) > 0) {
                    // Start the table
                    echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; margin-top: 10px; border-collapse: collapse;'>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>ISBN Number</th>
                                <th>Count</th>
                                <th>Category</th>
                            </tr>";
                    // Output each book in the table
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['booktitle']) . "</td>
                                <td>" . htmlspecialchars($row['author']) . "</td>
                                <td>" . htmlspecialchars($row['isbnno']) . "</td>
                                <td>" . htmlspecialchars($row['count']) . "</td>
                                <td>" . htmlspecialchars($row['category']) . "</td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No books available in the library.</p>";
                }

                // Close connection
                mysqli_close($conn);
            ?>
        </div>
        <div class="middle-block">
    <h3>Edit Book Information</h3>
    <form class="edit-form" method="post" action="edit.php">
        <label for="isbnno">ISBN Number (Primary Key)</label>
        <input type="text" id="isbnno" name="isbnno" required placeholder="Enter ISBN Number">

        <label for="book-title">Book Title</label>
        <input type="text" id="book-title" name="book-title" placeholder="Enter New Book Title">

        <label for="author">Author</label>
        <input type="text" id="author" name="author" placeholder="Enter New Author">

        <label for="count">Count</label>
        <input type="number" id="count" name="count" placeholder="Enter New Count">

        <label for="category">Category</label>
        <input type="text" id="category" name="category" placeholder="Enter New Category">

        <button type="submit" name="update">Update Book</button>
    </form>
</div>

        <div class="semi-bottom-block-container">
            <!-- Left Semi-Bottom Block -->
            <div class="semi-bottom-block">
                <h3>Token List</h3>
                <?php
                    // Example JSON string
                    $jsonData = file_get_contents('token.json');
                    // Decode JSON data into PHP array
                    $data = json_decode($jsonData, true);

                    // Check if 'token' exists and is an array
                    if (isset($data['token']) && is_array($data['token'])) {
                        echo "<ul>";
                        foreach ($data['token'] as $token) {
                            echo "<li>" . htmlspecialchars($token) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "No tokens available.";
                    }
                ?>
            </div>

            <!-- Right Semi-Bottom Block -->
            <div class="semi-bottom-block">
                <h3>Used Tokens</h3>
                <?php
                    // Retrieve used tokens from the POST data or cookies
                    $usedTokens = isset($_COOKIE['used-tokens']) ? json_decode($_COOKIE['used-tokens'], true) : [];

                    if (!empty($usedTokens)) {
                        echo "<ul>";
                        foreach ($usedTokens as $usedToken) {
                            echo "<li>" . htmlspecialchars($usedToken) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No tokens have been used yet.</p>";
                    }
                ?>
            </div>
        </div>
        <div class="side">
            <!-- Small Block 1 -->
            <div class="small-block">
                <img src="img2.jpg" alt="Image 2">
            </div>

            <!-- Small Block 2 -->
            <div class="small-block">
                <img src="img3.jpg" alt="Image 3">
            </div>

            <!-- Small Block 3 -->
            <div class="small-block">
                <img src="img4.jpg" alt="Image 4">
            </div>
        </div>
        <div class="semi-block-container">
            <!-- Left Semi-Block -->
            <div class="semi-block">
                <h3>Borrow Books</h3>
                <form class="borrow-form" method="post" action="process.php">
                    <label for="student-name">Student Full Name</label>
                    <input type="text" id="student-name" name="student-name"placeholder="Enter Student Name">

                    <label for="student-id">Student AIUB ID</label>
                    <input type="text" id="student-id" name="student-id" placeholder="Enter Student Id">

                    <label for="book-title">Book Title</label>
                    <input type="text" id="book-title" name="book-title" placeholder="Enter Book Title">

                    <label for="borrow-date">Borrow Date</label>
                    <input type="date" id="borrow-date" name="borrow-date" placeholder="Enter Borrow Date">

                    <label for="token">Token</label>
                    <input type="text" id="token" name="token" placeholder="Enter Valid Token">

                    <label for="return-date">Return Date</label>
                    <input type="date" id="return-date" name="return-date" placeholder="Enter Return Date">

                    <label for="fees">Fees</label>
                    <input type="number" id="fees" name="fees" placeholder="Enter Fee Amount">

                    <label for="paid">Paid</label>
                    <select id="paid" name="paid">
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>

                    <button type="submit" name="submit">Submit</button>
                </form>
            </div>

            <!-- Right Semi-Block -->
            <div class="semi-block">
                <h3>Entry Books</h3>
                <form class="entry-form" method="post" action="entry.php">
                    <label for="book-title">Book Title</label>
                    <input type="text" id="book-title" name="book-title" placeholder="Enter Book Title">

                    <label for="author">Author</label>
                    <input type="text" id="author" name="author"placeholder="Enter Author Name">

                    <label for="isbn-no">ISBN No</label>
                    <input type="text" id="isbn-no" name="isbn-no" placeholder="Enter ISBN Number">

                    <label for="count">Count (Number of Copies)</label placeholder="Enter NO Of Copy">
                    <input type="number" id="count" name="count">

                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" placeholder="Enter Book Category">

                    <button type="submit" name="store">Store Book</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
