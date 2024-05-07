<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal List</title>
</head>

<body>
    <?php
    // Database 
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "animal";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verify connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // User count
    $visitor_count = 0;
    $counter_file = "counter.txt";
    if (file_exists($counter_file)) {
        $visitor_count = intval(file_get_contents($counter_file));
    }
    $visitor_count++;
    file_put_contents($counter_file, $visitor_count);

    $category_query = "SELECT DISTINCT category FROM animal_info";
    $category_result = $conn->query($category_query);

    $life_expectancy_query = "SELECT DISTINCT life_expectancy FROM animal_info";
    $life_expectancy_result = $conn->query($life_expectancy_query);

    $category_filter = isset($_GET['category']) ? $_GET['category'] : '';
    $life_expectancy_filter = isset($_GET['life_expectancy']) ? $_GET['life_expectancy'] : '';
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';

    $sql = "SELECT * FROM animal_info";
    if (!empty($category_filter)) {
        $sql .= " WHERE category = '$category_filter'";
    }
    if (!empty($life_expectancy_filter)) {
        if (!empty($category_filter)) {
            $sql .= " AND";
        } else {
            $sql .= " WHERE";
        }
        $sql .= " life_expectancy = '$life_expectancy_filter'";
    }
    if ($sort_by == 'date') {
        $sql .= " ORDER BY submission_date DESC";
    } elseif ($sort_by == 'alphabetical') {
        $sql .= " ORDER BY a_name ASC";
    }
    $result = $conn->query($sql);
    ?>

    <h1>Animal List</h1>
    <p>Total Visitors: <?php echo $visitor_count; ?></p>
    <form action="" method="get">
        <label for="sort_by">Sort by:</label>
        <select name="sort_by" id="sort_by">
            <option value="date" <?php if ($sort_by == 'date') echo 'selected'; ?>>Date</option>
            <option value="alphabetical" <?php if ($sort_by == 'alphabetical') echo 'selected'; ?>>Alphabetical</option>
        </select>
        <label for="category"> Category:</label>
        <select name="category" id="category">
            <option value="">All</option>
            <?php
            if ($category_result->num_rows > 0) {
                while ($row = $category_result->fetch_assoc()) {
                    echo "<option value='" . $row['category'] . "'>" . $row['category'] . "</option>";
                }
            }
            ?>
        </select>
        <label for="life_expectancy"> Life Expectancy:</label>
        <select name="life_expectancy" id="life_expectancy">
            <option value="">All</option>
            <?php
            if ($life_expectancy_result->num_rows > 0) {
                while ($row = $life_expectancy_result->fetch_assoc()) {
                    echo "<option value='" . $row['life_expectancy'] . "'>" . $row['life_expectancy'] . "</option>";
                }
            }
            ?>
        </select>
        <input type="submit" value="Apply Filters">
    </form>
    <table border="1">
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Category</th>
            <th>Description</th>
            <th>Life Expectancy</th>
            <th>Options</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><img src='uploads/" . $row['image'] . "' alt='Animal Image' style='width:100px;height:100px;'></td>";
                echo "<td>" . $row['a_name'] . "</td>";
                echo "<td>" . $row['category'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['life_expectancy'] . "</td>";
                echo "<td><a href='?id=" . $row['sr_no'] . "&action=edit'>Edit</a> | <a href='?id=" . $row['sr_no'] . "&action=delete'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No animals found</td></tr>";
        }
        ?>
    </table>
    <?php
    // Handle the delete 
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($action === 'delete' && $id > 0) {
            
            $delete_query = "DELETE FROM animal_info WHERE sr_no = $id";
            if ($conn->query($delete_query) === TRUE) {
                echo "<p>Record deleted successfully</p>";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
                //after the delete refresh page
                header("Refresh:0");
                exit();
            } else {
                echo "Error deleting record: " . $conn->error;
            }
        }
    }

    // Handle the edit functionality
    if (isset($_GET['action']) && $_GET['action'] === 'edit') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            // detect the information edit
            $edit_query = "SELECT * FROM animal_info WHERE sr_no = $id";
            $edit_result = $conn->query($edit_query);
            if ($edit_result->num_rows > 0) {
                $edit_row = $edit_result->fetch_assoc();
                // show edited form here
                echo "<h2>Edit Animal</h2>";
                echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>";
                echo "<input type='hidden' name='id' value='$id'>";
                echo "<input type='text' name='a_name' value='" . $edit_row['a_name'] . "'><br> <br>"; 
                echo "<input type='text' name='category' value='" . $edit_row['category'] . "'><br><br>";
                echo "<input type='file' name='image'><br>";
                echo "<textarea name='description'>".$edit_row['description']."</textarea><br><br>";
                echo "<input type='text' name='life_expectancy' value='".$edit_row['life_expectancy']."'><br><br>";
                
                echo "<input type='submit' value='Submit'>";
                echo "</form>";
            } else {
                echo "<p>Animal not found for editing.</p>";
            }
        } else {
            echo "<p>Invalid animal ID for editing.</p>";
        }
    }

 
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
           
            $a_name = $_POST['a_name'];
            $category = $_POST['category'];
          
            if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){
                
                
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
              
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
                // Check file size
                if ($_FILES["image"]["size"] > 500000) {
                    echo "Sorry, your file is too large.";
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        echo "The file ". htmlspecialchars( basename( $_FILES["image"]["name"])). " has been uploaded.";
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                }
            }
            $description = $_POST['description'];
            $life_expectancy = $_POST['life_expectancy'];
            $update_query = "UPDATE animal_info SET a_name='$a_name', category='$category', image='$target_file', description='$description', life_expectancy='$life_expectancy' WHERE sr_no=$id";
            if ($conn->query($update_query) === TRUE) {
                echo "<p>Record updated successfully</p>";
                // Redirect to avoid resubmission
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }
    ?>

</body>

</html>

<?php
$conn->close();
?>


