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
    <title>Incidents</title>
</head>

<body id="blurred-bg">
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
        <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
        <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <span class="homepage"><a href="add_Incident.php" style="color: red"> Add new Incident</a> </span> <br>
    <br><br>
    <table id="customers">
        <tr>
            <th>Incident ID</th>
            <th>Incident Date</th>
            <th>Incident Report</th>
            <th>Vehicle's licence</th>
            <th>Owner's licence</th>
        </tr>
        <?php
        require("functions.php");
        require("db.inc.php");
        // Open the database connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            die();
        }
        // prints any message whether from successfull deletion or editing
        if (isset($_SESSION["message"])) {
            $message = $_SESSION["message"];
            echo '<span class="homepage">' . $message . '</span>';
            unset($_SESSION["message"]);
        }
        if (isset($_GET["delete"])) {
            $Incident_ID = $_GET["delete"];
            # Get Audit record before deletion
            $sql1 = "SELECT * FROM Incident WHERE Incident_ID = '$Incident_ID'";
            # Seperately figuring out owner_licence, vehicle_licence, offence_description instead of joining for scalability
            $result1 = mysqli_query($conn, $sql1);
            $row = mysqli_fetch_assoc($result1);
            $sql = "Delete FROM Incident WHERE Incident_ID = '$Incident_ID'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                // Stores successful deletion message in a session variable and redirects back to index.php
                $_SESSION["message"] = '<span class="homepage"> Successful Incident deletion! </span>';
                $User_ID = $_SESSION['reporter_ID'];
                $offence_ID = $row['Offence_ID'];
                $vehicle_ID = $row['Vehicle_ID'];
                $owner_ID = $row['People_ID'];
                $OLD_Incident_Date = $row['Incident_Date'];
                $OLD_Incident_Report = $row['Incident_Report'];
                $licence_array = get_licences_from_ID($owner_ID, $vehicle_ID, $offence_ID, $conn);
                $OLD_Owner_licence = $licence_array['owner_licence'];
                $OLD_Vehicle_licence = $licence_array['vehicle_licence'];
                $OLD_Offence_description = $licence_array['offence_description'];
                $audit = "INSERT INTO Audit_Incident (Timestamp, User_ID, Action, OLD_Vehicle_licence, OLD_Owner_licence, OLD_Incident_Date, OLD_Incident_Report, OLD_Offence_description)
                    VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'DELETE', '$OLD_Vehicle_licence', '$OLD_Owner_licence', '$OLD_Incident_Date', '$OLD_Incident_Report', '$OLD_Offence_description')";
                $query_result = mysqli_query($conn, $audit);
                header("location: view_Incident.php");
            }
        }
        $sql = 'SELECT Incident_ID, Incident_Date, I.Vehicle_ID, I.People_ID, Incident_Report, User_ID, Offence_ID, Vehicle_licence, People_licence FROM Incident I JOIN People P USING (People_ID) JOIN Vehicle V USING (Vehicle_ID);';
        // send query to database
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Incident_ID = $row["Incident_ID"];
                $Incident_Date = $row["Incident_Date"];
                $Incident_Report = $row["Incident_Report"];
                $Vehicle_licence = $row["Vehicle_licence"];
                $Owner_licence = $row["People_licence"];
                $offence_ID = $row['Offence_ID'];
                $sql3 = "SELECT Offence_description FROM Offence WHERE Offence_ID = '$offence_ID';";
                $result3 = mysqli_query($conn, $sql3);
                $row3 = mysqli_fetch_assoc($result3);
                $offence_description = $row3['Offence_description'];
                $User_ID = $_SESSION['reporter_ID'];
                # Storing only important information in the Audit
                $audit = "INSERT INTO Audit_Incident (Timestamp, User_ID, Action, OLD_Vehicle_licence, OLD_Owner_licence, OLD_Incident_Date, OLD_Incident_Report, OLD_Offence_description)
                    VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'SEARCH', '$Vehicle_licence', '$Owner_licence', '$Incident_Date', '$Incident_Report', '$offence_description')";
                $query_result = mysqli_query($conn, $audit);
        ?>
        <tr>
            <td>
                <?php echo $Incident_ID ?>
            </td>
            <td>
                <?php echo $Incident_Date ?>
            </td>
            <td>
                <?php echo $Incident_Report ?>
            </td>
            <td>
                <?php echo $Vehicle_licence ?>
            </td>
            <td>
                <?php echo $Owner_licence ?>
            </td>
            <td><a href="edit_Incident.php?edit=<?php echo $Incident_ID ?> " class="btn btn-info"> Edit </a></td>
            <td><a href="view_Incident.php?delete=<?php echo $Incident_ID ?> " class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete this item?');"> Delete </a></td>
        </tr>
        <?php
            }
        }
        ?>

</body>

</html>