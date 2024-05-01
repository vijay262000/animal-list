<?php
// Database 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "animal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// verify connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check the id from url
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete the record from the database
    $sql = "DELETE FROM animal_info WHERE sr_no=$id"; 
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
        header("Location: index.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "ID not provided";
}

$conn->close();
?>


