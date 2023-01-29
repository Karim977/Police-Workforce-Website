<?php
session_start();
if (!isset($_SESSION["username"])) {

    echo '<span class="homepage"> Restricted access! </span>';
    echo '<br>';
    echo '<span class="homepage"> USER NOT LOGGED IN! </span>';
    exit();
}
// Restrict access only when there is a GET variable
if (!isset($_GET["edit"])) {
    echo '<span class="homepage"> Invalid access! </span>';
    echo '<br><span class="homepage"> This form should be accessed only when editing a specific incident! </span>';
    exit();
}
function reset_session_vars()
{
    $username = $_SESSION['username'];
    $reporter_ID = $_SESSION['reporter_ID'];
    // Stackoverflow comment
    // Unsetting all session variables
    $helper = array_keys($_SESSION);
    foreach ($helper as $key) {
        unset($_SESSION[$key]);
    }
    $_SESSION['username'] = $username;
    $_SESSION['reporter_ID'] = $reporter_ID;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap-css/css/bootstrap.min.css">
    <title>Edit Incident</title>
</head>

<body id="blurred-bg">
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
        <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
        <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <span class="homepage"><a href="view_Incident.php" style="color: red"> View Incidents</a> </span> <br>
    <span class="homepage"><a href="add_Incident.php" style="color: red"> Add new Incident</a> </span> <br>
    <?php
    require("db.inc.php");
    // Open the database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        die();
    }
    // Collecting the information related to this Incident
    $Incident_ID = $_GET["edit"];
    $sql = "SELECT * FROM Incident I JOIN People P USING (People_ID) JOIN Vehicle V USING (Vehicle_ID) JOIN Offence USING (Offence_ID) WHERE Incident_ID='$Incident_ID';";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $OLD_Incident_Report = $row["Incident_Report"];
        $OLD_Incident_Date = $row["Incident_Date"];
        $OLD_Vehicle_licence = $row["Vehicle_licence"];
        $OLD_Owner_licence = $row["People_licence"];
        $OLD_offence_ID = $row["Offence_ID"];
    }
    ?>
    <div class="center" id="form">
        <h1>Edit Incident</h1>
        <form method="post">
            <div class="txt_field_small_margin">
                <input type="text" name="report" placeholder="Statement" value="<?php echo $OLD_Incident_Report; ?>"
                    required>
                <span></span>
            </div>
            <div class="txt_field_small_margin">
                <input type="date" name="date" value="<?php echo $OLD_Incident_Date; ?>" required>
                <span></span>
            </div>
            <div class="txt_field_small_margin">
                <input type="text" name="vehicle_plate" placeholder="Vehicle Plate Number"
                    value="<?php echo $OLD_Vehicle_licence; ?>" required>
                <span></span>
            </div>
            <div class="txt_field_small_margin">
                <input type="text" name="owner_licence" placeholder="Owner's licence"
                    value="<?php echo $OLD_Owner_licence; ?>" required>
                <span></span>
            </div>
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
                    // This checks the old value of the offence
                    if ($offence_ID == $OLD_offence_ID) {
                        echo '<input type="radio" id="' . $offence_ID . '" name="offence_ID" value="' . $offence_ID . '" checked required>';
                    } else {
                        echo '<input type="radio" id="' . $offence_ID . '" name="offence_ID" value="' . $offence_ID . '" required>';
                    }
                    echo "<span></span>";
                    echo '<label for="offence_name">' . $offence_name . ' </label><br>';
                }
                ?>
            </div>
            <input type="submit" value="Edit">
        </form>
    </div>
    <?php
    if (isset($_POST['owner_licence'])) {
        $Vehicle_licence = $_POST['vehicle_plate'];
        $Owner_licence = $_POST["owner_licence"];
        $offence_ID = $_POST["offence_ID"];
        $Incident_Date = $_POST["date"];
        $Incident_Report = $_POST["report"];
        $offence_ID = $_POST["offence_ID"];
        $sql3 = "SELECT Offence_description FROM Offence WHERE Offence_ID = '$offence_ID';";
        $result3 = mysqli_query($conn, $sql3);
        $row3 = mysqli_fetch_assoc($result3);
        $offence_description = $row3['Offence_description'];
        // Retrieve corresponding IDs from tables
        $sql = "SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence='$Vehicle_licence'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $Vehicle_ID = $row["Vehicle_ID"];
        } else {
            echo '<span class="homepage"> Vehicle licence is not present in database! </span>';
            exit();
        }
        $sql = "SELECT People_ID FROM People WHERE People_licence='$Owner_licence'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $Owner_ID = $row["People_ID"];
        } else {
            echo '<span class="homepage"> Owner licence is not present in database! </span>';
            exit();
        }
        echo $Vehicle_ID;
        echo $Owner_ID;
        $sql = "UPDATE Incident SET Vehicle_ID='$Vehicle_ID', People_ID='$Owner_ID', Incident_Date='$Incident_Date', Incident_Report='$Incident_Report', Offence_ID='$offence_ID' WHERE Incident_ID='$Incident_ID';";
        $result = mysqli_query($conn, $sql);
        echo $sql;
        if ($result) {
            $_SESSION["message"] = 'Incident updated successfully!';
            $User_ID = $_SESSION['reporter_ID'];
            # Storing only important information in the Audit
            $sql3 = "SELECT Offence_description FROM Offence WHERE Offence_ID = '$OLD_offence_ID';";
            $result3 = mysqli_query($conn, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $OLD_Offence_description = $row3['Offence_description'];
            $audit = "INSERT INTO Audit_Incident (Timestamp, User_ID, Action, OLD_Vehicle_licence, NEW_Vehicle_licence, OLD_Owner_licence, NEW_Owner_licence, OLD_Incident_Date, NEW_Incident_Date, OLD_Incident_Report, NEW_Incident_Report, OLD_Offence_description, NEW_Offence_description)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'UPDATE', '$OLD_Vehicle_licence', '$Vehicle_licence', '$OLD_Owner_licence', '$Owner_licence', '$OLD_Incident_Date', '$Incident_Date', '$OLD_Incident_Report', '$Incident_Report', '$OLD_Offence_description', '$offence_description')";
            $query_result = mysqli_query($conn, $audit);
            header("location: view_Incident.php");
        }
    }
    ?>
</body>

</html>