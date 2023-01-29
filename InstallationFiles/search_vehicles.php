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
    <title>Search vehicles</title>
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
        <h1>Search for a vehicle</h1>
        <form method="post">
            <div class="txt_field">
                <input type="text" name="vehicle_registration" required>
                <span></span>
                <label>Vehicle Registration</label>
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
    if (isset($_POST['vehicle_registration']) && $_POST['vehicle_registration'] != "") // check contents of $_POST supervariables
    {
        $sql = 'SELECT Vehicle_ID, Vehicle_make, Vehicle_model, Vehicle_colour, Vehicle_licence, People_name, People_licence FROM Vehicle LEFT JOIN Ownership USING (Vehicle_ID) LEFT JOIN People USING (People_ID) WHERE Vehicle_licence = "' . $_POST['vehicle_registration'] . '";';
        // send query to database
        $result = mysqli_query($conn, $sql);
        $User_ID = $_SESSION['reporter_ID'];
        if (mysqli_num_rows($result) > 0) {
            echo '<ul class="list" style="font-size: 25px" >';
            while ($row = mysqli_fetch_assoc($result)) {
                $vehicle_make = $row["Vehicle_make"];
                $vehicle_model = $row["Vehicle_model"];
                $vehicle_colour = $row["Vehicle_colour"];
                $vehicle_licence = $row["Vehicle_licence"];
                echo "<li>Vehicle make: '$vehicle_make' </li>";
                echo "<li>Vehicle model: '$vehicle_model' </li>";
                echo "<li>Vehicle colour: '$vehicle_colour' </li>";
                echo "<li>Vehicle licence: '$vehicle_licence' </li>";
                if ($row["People_name"] == "")
                    $row["People_name"] = "Unknown";
                echo "<li>Owner's name: " . $row["People_name"] . "</li>";
                if ($row["People_licence"] == "")
                    $row["People_licence"] = "Unknown";
                echo "<li>Owner's licence: " . $row["People_licence"] . "</li>";
                echo "<hr>";
                $audit = "INSERT INTO Audit_Vehicle (Timestamp, User_ID, Action, OLD_Vehicle_make, OLD_Vehicle_model, OLD_Vehicle_colour, OLD_Vehicle_licence)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'SEARCH', '$vehicle_make', '$vehicle_model', '$vehicle_colour', '$vehicle_licence')";
                $query_result = mysqli_query($conn, $audit);
            }
            echo "<ul>";
        } else {
            echo '<span class="homepage"> Vehicle is not found in the Database!</span>';
        }
    }
    ?>
</body>

</html>