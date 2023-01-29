<?php
session_start();
if (!isset($_SESSION["username"])) {

    echo '<span class="homepage"> Restricted access! </span>';
    echo '<br>';
    echo '<span class="homepage"> USER NOT LOGGED IN! </span>';
    exit();
}
$VEHICLE_LICENCE_LENGTH = 7
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap-css/css/bootstrap.min.css">
    <title>Add vehicle</title>
</head>

<body id="blurred-bg">
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
        <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
        <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <span class="homepage"><a href="reset.php?dest=add_Incident.php" style="color: red"> Reset</a> </span> <br>
    <div class="center" id="form">
        <h1>Enter New Vehicle Information</h1>
        <form method="post">
            <div class="txt_field">
                <input maxlength='7' type="text" name="vehicle_licence" required>
                <span></span>
                <label>Plate Number</label>
            </div>
            <div class="txt_field">
                <input type="text" name="make" required>
                <span></span>
                <label>Make</label>
            </div>
            <div class="txt_field">
                <input type="text" name="model" required>
                <span></span>
                <label>Model</label>
            </div>
            <div class="txt_field">
                <input type="text" name="colour" required>
                <span></span>
                <label>Colour</label>
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
    if (isset($_POST['vehicle_licence'])) // Check only if 1 element is present as all elements have the required field and must be submitted
    {
        $vehicle_licence = $_POST['vehicle_licence'];
        $is_correct = strlen($_POST['vehicle_licence']) == $VEHICLE_LICENCE_LENGTH;
        if (!$is_correct) {
            echo "<br>";
            echo "<span class='homepage'> Vehicle licence is invalid!</span>";
            echo "<br><br><br>";
            echo "<span class='homepage'> Please enter a combination of";
            echo "<br>";
            echo "'$VEHICLE_LICENCE_LENGTH' Characters/Digits.</span>";
            exit();
        }
        $make = $_POST['make'];
        $model = $_POST['model'];
        $colour = $_POST['colour'];
        $sql = "Insert INTO Vehicle (Vehicle_make, Vehicle_model, Vehicle_colour, Vehicle_licence) VALUES ('$make', '$model', '$colour', '$vehicle_licence');";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            echo '<span class="homepage"> An error has occured during addition of the new vehicle! </span>';
            exit();
        }
        $sql = "SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$vehicle_licence';";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        // Set a temporary owner licence variable in order not to lose it when we redirect
        $_SESSION['vehicle_ID'] = $row["Vehicle_ID"];
        // overwrite the initial vehicle licence received from post 
        $_SESSION["vehicle_licence"] = $row["Vehicle_licence"];
        # Audit
        $User_ID = $_SESSION['reporter_ID'];
        $audit = "INSERT INTO Audit_Vehicle (Timestamp, User_ID, Action, NEW_Vehicle_make, NEW_Vehicle_model , NEW_Vehicle_colour, NEW_Vehicle_licence)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'INSERT', '$make', '$model', '$colour', '$vehicle_licence')";
        $query_result = mysqli_query($conn, $audit);
        redirect('add_Incident.php');
    }
    ?>
</body>

</html>