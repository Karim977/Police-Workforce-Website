<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION["username"])) {
    echo '<span class="homepage"> Restricted access! </span>';
    echo '<br>';
    echo '<span class="homepage"> USER NOT LOGGED IN! </span>';
    exit();
}
require("functions.php");
require("db.inc.php");
// Open the database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
}
?>

<head>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap-css/css/bootstrap.min.css">
    <title>Add Incident</title>
</head>

<body id="blurred-bg">
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
        <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
        <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <div class="center" id="form">
        <h1>ADD Incident Report</h1>
        <form method="post">
            <div class="txt_field_small_margin">
                <input type="text" name="report" placeholder='Statement' required>
                <span></span>
            </div>
            <div class="txt_field_small_margin">
                <input type="date" name="date" required>
                <span></span>
            </div>
            <div class="txt_field_small_margin">
                <input maxlength='7' type="text" name="vehicle_plate" placeholder='Vehicle Plate Number' required>
                <span></span>
            </div>
            <div class="txt_field_small_margin">
                <input type="text" maxlength='16' name="owner_licence" placeholder='Owner licence' required>
                <span></span>
            </div>
            <span> Is Driver Owner?</span><br>
            <input type="radio" name="is_owner" value='yes' required> <label> YES</label><br>
            <input type="radio" name="is_owner" value='no'> <label> NO</label><br>
            <span></span>
            <span> Choose offence type:</span>
            <?php
            // Collecting the list of offences from database
            $sql = "SELECT Offence_ID, Offence_description FROM Offence;";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) == 0) {
                echo " THERE ARE NO OFFENCES IN THE DATABASE!";
                exit();
            }
            ?>
            <div>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $offence_name = $row["Offence_description"];
                    $offence_ID = $row["Offence_ID"];
                    echo '<input type="radio" id="' . $offence_ID . '" name="offence_ID" value="' . $offence_ID . '" required>';
                    echo "<span></span>";
                    echo '<label for="offence_name">' . $offence_name . ' </label><br>';
                }
                ?>
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


    echo "<ul class='list'>"; // start list  
    echo "<br> <br><li><a href='view_Incident.php'> View Incidents</a></li>";
    echo "</ul>";

    // Store the post variables in Session variables just in case we redirected to any other page and then unset them at the end of the page
    if (isset($_POST['owner_licence'])) {
        $_SESSION["report"] = $_POST["report"];
        $_SESSION["date"] = $_POST["date"];
        $_SESSION["offence_ID"] = $_POST["offence_ID"];
        $_SESSION["vehicle_licence"] = $_POST["vehicle_plate"];
        $_SESSION["owner_licence"] = $_POST["owner_licence"];
        $_SESSION["is_owner"] = $_POST["is_owner"];
    }

    // Check if owner is present
    if (isset($_POST['owner_licence'])) {
        $owner_licence = $_SESSION["owner_licence"];
        $sql = "Select People_ID FROM People WHERE People_licence = '$owner_licence';";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            $_SESSION['new_owner'] = true;
            redirect('add_owner_by_licence.php');
        } else {
            $row = mysqli_fetch_assoc($result);
            // Set a temporary owner licence variable in order not to lose it when we redirect
            $_SESSION['owner_ID'] = $row["People_ID"];
        }
    }

    // Check if vehicle is present
    if (isset($_SESSION["vehicle_licence"])) {
        $vehicle_plate = $_SESSION["vehicle_licence"];
        // If there are records, then we know that the vehicle information is there.
        $sql = "Select Vehicle_ID FROM Vehicle WHERE Vehicle_licence = '$vehicle_plate';";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            redirect('add_vehicle_by_ownerID.php');
        } else {
            // If there are records, then we already know the car
            // If a new owner was added then we need to add a new record and retrieve the new vehicle_ID
            // Ownership will be updated in the next if statement
            $row = mysqli_fetch_assoc($result);
            $_SESSION['vehicle_ID'] = $row['Vehicle_ID'];
        }
    }
    if (isset($_SESSION['owner_ID']) and isset($_SESSION['vehicle_ID'])) {
        // Check if the vehicle is associated with this owner (In case vehicle licence found and owner licence found but they are not linked to each other)
        $vehicle_ID = $_SESSION["vehicle_ID"];
        $owner_ID = $_SESSION["owner_ID"];
        if ($_SESSION["is_owner"] == 'yes') {
            $sql = "Select Vehicle_ID FROM Ownership WHERE People_ID = '$owner_ID' and Vehicle_ID = '$vehicle_ID';";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) == 0) {
                // Link this owner to this vehicle
                $sql = "Insert INTO Ownership (Vehicle_ID, People_ID) VALUES ('$vehicle_ID' , '$owner_ID');";
                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    echo '<span class="homepage"> Vehicle is already linked to an <br>';
                    echo "existing owner!</span>";
                    echo '<span class="homepage"> Vehicle cannot <br>';
                    echo 'be linked to multiple owners!</span>';
                    exit();
                } else {
                    $User_ID = $_SESSION['reporter_ID'];
                    $licence_array = get_licences_from_ID($owner_ID, $vehicle_ID, $offence_ID, $conn);
                    $owner_licence = $licence_array['owner_licence'];
                    $vehicle_licence = $licence_array['vehicle_licence'];
                    $audit = "INSERT INTO Audit_Ownership (Timestamp, User_ID, Action, Owner_licence, Vehicle_licence)
                    VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'INSERT', '$owner_licence', '$vehicle_licence')";
                    $query_result = mysqli_query($conn, $audit);
                }
            }
        }
        $date = $_SESSION["date"];
        $report = $_SESSION["report"];
        $offence_ID = $_SESSION["offence_ID"];
        $reporter_ID = $_SESSION["reporter_ID"];
        $sql = "INSERT INTO Incident(Vehicle_ID, People_ID, Incident_Date, Incident_Report, USER_ID, Offence_ID) VALUES ('$vehicle_ID', '$owner_ID', '$date', '$report', '$reporter_ID', '$offence_ID');";
        $result = mysqli_query($conn, $sql);
        $User_ID = $_SESSION['reporter_ID'];
        if ($result) {
            echo '<span class="homepage"> Incident ADDED successfully!</span>';
            $licence_array = get_licences_from_ID($owner_ID, $vehicle_ID, $offence_ID, $conn);
            $owner_licence = $licence_array['owner_licence'];
            $vehicle_licence = $licence_array['vehicle_licence'];
            $offence_description = $licence_array['offence_description'];
            # Storing only important information in the Audit
            $audit = "INSERT INTO Audit_Incident (Timestamp, User_ID, Action, NEW_Vehicle_licence, NEW_Owner_licence, NEW_Incident_Date, NEW_Incident_Report, NEW_Offence_description)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'INSERT', '$vehicle_licence', '$owner_licence', '$date', '$report', '$offence_description')";
            $query_result = mysqli_query($conn, $audit);
        } else {
            echo '<span class="homepage"> Similar incident recorded!</span>';
        }
        $all = False;
        reset_session_vars($all);
    }
    ?>
</body>

</html>