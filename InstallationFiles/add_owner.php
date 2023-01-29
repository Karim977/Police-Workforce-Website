<?php
session_start();
if (!isset($_SESSION["username"])) {

    echo '<span class="homepage"> Restricted access! </span>';
    echo '<br>';
    echo '<span class="homepage"> USER NOT LOGGED IN! </span>';
    exit();
}
$OWNER_LICENCE_LENGTH = 16
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap-css/css/bootstrap.min.css">
    <title>Add Owner</title>
</head>

<body id="blurred-bg">
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
        <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
        <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <span class="homepage"><a href="reset.php?dest=add_vehicles.php" style="color: red"> Reset</a> </span> <br>
    <div class="center">
        <h1>Enter Owner's Information</h1>
        <form method="post">
            <div class="txt_field">
                <input type="text" name="owner_name" required>
                <span></span>
                <label>Name</label>
            </div>
            <div class="txt_field">
                <input type="text" name="address" optional>
                <span></span>
                <label>Address</label>
            </div>
            <div class="txt_field">
                <input type="number" name="YOB" optional>
                <span></span>
                <label>Year of Birth</label>
            </div>
            <div class="txt_field">
                <input maxlength="16" type="text" name="owner_licence" required>
                <span></span>
                <label>Licence</label>
            </div>
            <input type="submit" value="Add">
        </form>
    </div>
    <?php
    function redirect($url)
    {
        header('Location: ' . $url);
        die();
    }
    require("db.inc.php");
    // Open the database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        die();
    }
    if (isset($_POST['owner_name'])) {
        $is_correct = strlen($_POST['owner_licence']) == $OWNER_LICENCE_LENGTH;
        if (!$is_correct) {
            echo "<br>";
            echo "<span class='homepage'> Owner licence is invalid!</span>";
            echo "<br><br><br>";
            echo "<span class='homepage'> Please enter a combination of";
            echo "<br>";
            echo "'$OWNER_LICENCE_LENGTH' Characters/Digits.</span>";
            exit();
        }
        $sql = 'Insert INTO People (People_name, People_licence) VALUES ("' . $_POST['owner_name'] . '","' . $_POST['owner_licence'] . '");';
        $result = mysqli_query($conn, $sql);
        if (isset($_POST['address'])) {
            $sql = 'UPDATE People SET People_address ="' . $_POST['address'] . '" WHERE People_licence = "' . $_POST['owner_licence'] . '";';
            $result1 = mysqli_query($conn, $sql);
        }
        if (isset($_POST['YOB'])) {
            $sql = 'UPDATE People SET People_YOB =' . $_POST['YOB'] . ' WHERE People_licence = "' . $_POST['owner_licence'] . '";';
            $result2 = mysqli_query($conn, $sql);
        }
        if ($result) {
            // Redirect back to the main add_vehicle website with the new owner's licence set as a session variable
            $_SESSION['owner_licence'] = $_POST['owner_licence'];
            $owner_name = $_POST['owner_name'];
            $owner_licence = $_POST['owner_licence'];
            $User_ID = $_SESSION['reporter_ID'];
            $audit = "INSERT INTO Audit_Owner (Timestamp, User_ID, Action, NEW_owner_name, NEW_owner_licence)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'INSERT', '$owner_name', '$owner_licence')";
            $query_result = mysqli_query($conn, $audit);
            redirect('add_vehicles.php');
        } else {
            echo '<span class="homepage"> An error has occured during addition of the new owner! </span>';
        }
    }
    ?>
</body>

</html>