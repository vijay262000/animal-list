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

// user count
$visitor_count = 0;
$counter_file = "counter.txt";
if (file_exists($counter_file))
{
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
if (!empty($category_filter)) 
{
    $sql .= " WHERE category = '$category_filter'";
}
if (!empty($life_expectancy_filter)) 
{
    if (!empty($category_filter)) {
        $sql .= " AND";
    } else {
        $sql .= " WHERE";
    }
    $sql .= " life_expectancy = '$life_expectancy_filter'";
}
if ($sort_by == 'date')
{
    $sql .= " ORDER BY submission_date DESC";
} elseif ($sort_by == 'alphabetical')
{
    $sql .= " ORDER BY a_name ASC";
}
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal List</title>
</head>

<body>
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
            if ($category_result->num_rows > 0)
            {
                while ($row = $category_result->fetch_assoc())
                {
                    echo "<option value='" . $row['category'] . "'>" . $row['category'] . "</option>";
                }
            }
            ?>
        </select>
        <label for="life_expectancy"> Life Expectancy:</label>
        <select name="life_expectancy" id="life_expectancy">
            <option value="">All</option>
            <?php
            if ($life_expectancy_result->num_rows > 0) 
            {
                while ($row = $life_expectancy_result->fetch_assoc())
                 {
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
        if ($result->num_rows > 0) 
        {
            while ($row = $result->fetch_assoc())
             {
                echo "<tr>";
                echo "<td><img src='uploads/" . $row['image'] . "' alt='Animal Image' style='width:100px;height:100px;'></td>";
                echo "<td>" . $row['a_name'] . "</td>";
                echo "<td>" . $row['category'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['life_expectancy'] . "</td>";
                echo "<td><a href='edit.php?id=" . $row['sr_no'] . "'>Edit</a> | <a href='delete.php?id=" . $row['sr_no'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        }
         else
        {
            echo "<tr><td colspan='6'>No animals found</td></tr>";
        }
        ?>
    </table>
</body>

</html>

<?php
$conn->close();
?>
