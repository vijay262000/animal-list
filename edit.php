<?php
// Database 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "animal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// verify connection
if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

// Check the form are submitted or not
if ($_SERVER["REQUEST_METHOD"] == "POST")
 {
    
    if(isset($_POST['id']) && isset($_POST['name']) && isset($_POST['category']) && isset($_POST['description'])) 
    {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $category = $_POST['category'];
        $description = $_POST['description'];

        // edit animal details 
        $sql = "UPDATE animal_info SET a_name=?, category=?, description=? WHERE sr_no=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $category, $description, $id);
        
        if ($stmt->execute())
        {
            echo "Animal details updated successfully";
            header("Location: index.php");
        }
         else 
        {
            echo "Error updating animal details: " . $conn->error;
        }
    } 
    else
    {
        echo "One or more required fields are missing";
    }
}

// Check the id from url
if(isset($_GET['id'])) 
{
    $id = $_GET['id'];
    
    
    $sql = "SELECT * FROM animal_info WHERE sr_no=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        //show the form pre-filled with animal details for editing
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo isset($row['sr_no']) ? htmlspecialchars($row['sr_no']) : ''; ?>">
            
            <table>
                <tr>
                    <th>
                        <label for="name">Name:</label>
                    </th>
                    <th>
                        <label for="image">Photo Upload:</label>
                    </th>
                    <th>
                        <label for="category">Category:</label>
                    </th>
                    <th>
                        <label for="description">Description:</label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="text" id="name" name="name" placeholder="Enter Animal Name" value="<?php echo isset($row['a_name']) ? htmlspecialchars($row['a_name']) : ''; ?>">
                    </td>
                    <td>
                        <input type="file" id="image" name="image">
                    </td>
                    <td>
                        <input type="text" id="category" name="category" placeholder="Enter Animal Category" value="<?php echo isset($row['category']) ? htmlspecialchars($row['category']) : ''; ?>">
                    </td>
                    <td>
                        <textarea id="description" name="description" placeholder="Enter Animal Description"><?php echo isset($row['description']) ? htmlspecialchars($row['description']) : ''; ?></textarea>
                    </td>
                </tr>
            </table>
            <input type="submit" value="Update">
        </form>
        <?php
    }
     else
    {
        header("Location: display_data.php");
        echo "Animal with ID " . htmlspecialchars($id) . " not found";
    }
}
 else 
{
    echo "ID not provided";
}

$stmt->close();
$conn->close();
?>
