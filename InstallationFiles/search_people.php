<?php
session_start();
if (!isset($_SESSION["username"])) {

    echo '<span class="homepage"> Restricted access! </span>';
    echo '<br>';
    echo '<span class="homepage"> USER NOT LOGGED IN! </span>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap-css/css/bootstrap.min.css">
    <title>Search people</title>
</head>

<body id="blurred-bg">
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
        <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
        <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <div class="center">
        <h1>Search for a person</h1>
        <form method="post">
            <div class="txt_field">
                <input type="text" id="n" name="name" optional>
                <span></span>
                <label>Name</label>
            </div>
            <div class="txt_field">
                <input maxlength='16' id="l" type="text" name="licence" optional>
                <span></span>
                <label>Licence Number</label>
            </div>
            <input type="submit" value="Search">
        </form>
    </div>
    <?php
    require("db.inc.php");
    // Open the database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        die();
    }
    // else echo "MySQL connection OK<br>";  // useful for testing
    if (isset($_POST['name']) && $_POST['name'] != "") {
        $search_criteria = "name";
    }
    if (isset($_POST['licence']) && $_POST['licence'] != "") {
        # Priority given to name if it is set.
        if (!isset($search_criteria)) {
            $search_criteria = "licence";
        }
    } else {
        echo '<span class="homepage"> Please enter information in either field Person/Licence</span><br>';
    }
    // if a form has been submitted, insert a new record                                              
    if (isset($search_criteria)) // check contents of $_POST supervariables
    {
        if ($search_criteria == "name") {
            $lower_case = strtolower($_POST['name']);
            $name = ucfirst($lower_case);
            $sql = 'SELECT * FROM People WHERE People_name LIKE "%' . $name . '%"';
        } elseif ($search_criteria == "licence") {
            $sql = 'SELECT * FROM People WHERE People_licence = "' . $_POST["licence"] . '"';
        }
        $User_ID = $_SESSION['reporter_ID'];
        // send query to database
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo '<ul class="list" style="font-size: 25px" >';
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row["People_name"];
                echo "<li>Name: " . $name . "</li>";
                $address = $row["People_address"];
                echo "<li>Address: " . $address . "</li>";
                $YOB = $row["People_YOB"];
                echo "<li>Year of Birth: " . $YOB . "</li>";
                $licence = $row["People_licence"];
                echo "<li>Licence: " . $licence . "</li>";
                echo "<hr>";
                $audit = "INSERT INTO Audit_Owner (Timestamp, User_ID, Action, OLD_owner_name, OLD_owner_address, OLD_owner_YOB, OLD_owner_licence)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'SEARCH', '$name', '$address', '$YOB', '$licence')";
                $query_result = mysqli_query($conn, $audit);
            }
            echo "<ul>";
        } else {
            echo '<span class="homepage"> Person/Licence number is not available in the Database!</span>';
        }
    }
    ?>
</body>

</html>