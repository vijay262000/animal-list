<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
 {
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

// Collection of data
$a_name = isset($_POST['a_name']) ? $_POST['a_name'] : ''; 
$category = isset($_POST['category']) ? $_POST['category'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$life_expectancy = isset($_POST['life_expectancy']) ? $_POST['life_expectancy'] : '';


$target_dir = "uploads/"; 

$image = $_FILES['image']['name'];

if(isset($_FILES['image']))
 {
    $image = $_FILES['image']['name'];
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
} 
else 
{
    $image = ''; 
}


// create SQL statement
$sql = "INSERT INTO animal_info (a_name, category, image, description, life_expectancy) 
        VALUES ('$a_name', '$category', '$image', '$description', '$life_expectancy')";

//  SQL statement
if ($conn->query($sql) === TRUE) 
{
    
    header("Location:index.php");
    exit();
} 
else
{
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
}


?>



<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body 
        {
            font-family: Arial, Helvetica, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        input[type=text],
        select,
        textarea
         {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 6px;
            margin-bottom: 16px;
            resize: vertical;
        }

        input[type=submit]
         {

            background-color: #070707;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 170px;




        }

        .container 
        {
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
        }

        .center 
        {

            max-width: 500px;
            margin: auto;
            background: white;
            padding: 10px;
        }

        .centers 
        {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="center">
        <h3 class="centers">Animal Form</h3>
        <div class="container">
            <form method="post" enctype="multipart/form-data"> 
                <label>Animal Name</label>
                <input type="text" name="a_name" placeholder="Animal name.">
                <label>Categeory</label> 
                <select id="category" name="category"> 
                    <option value="herbivores">Herbivores</option>
                    <option value="omnivores">Omnivores</option>
                    <option value="carnivores">Carnivores</option>
                </select>
                <label>Photo Upload</label> <br><br>
                <input type="file" id="myFile" name="image">
                <br><br>
                <label for="description">Description</label> 
                <textarea id="description" name="description" placeholder="Write something.." style="height:200px"></textarea>
                <label for="life_expectancy">Life expectancy:</label><br>
                <select id="life_expectancy" name="life_expectancy" required>
                    <option value="0-1 year">0-1 year</option>
                    <option value="1-5 years">1-5 years</option>
                    <option value="5-10 years">5-10 years</option>
                    <option value="10+ years">10+ years</option>
                </select>
                <div class="g-recaptcha" data-sitekey="6Ldrv8wpAAAAAD2XC90BUzA7Sm5LhJFSbewwb8kU"></div>
                <br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
</body>

</html>