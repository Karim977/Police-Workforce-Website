<?php
session_start();
if (!isset($_SESSION["username"])) {

    echo '<span class="homepage"> Restricted access! </span>';
    echo '<br>';
    echo '<span class="homepage"> USER NOT LOGGED IN! </span>';
    exit();
}
if ($_SESSION["username"] != "daniels") {
    echo '<span class="homepage"> Restricted access! </span>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap-css/css/bootstrap.min.css">
    <title>Add Fine</title>
</head>

<body id="blurred-bg">
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
        <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
        <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <div id="incident_form" class="center">
        <h1>Enter Owner and vehicle associated</h1>
        <form method="post">
            <div class="txt_field">
                <input type="text" name="owner_licence" placeholder="owner_licence" required>
                <span></span>
            </div>
            <div class="txt_field">
                <input type="text" name="vehicle_licence" placeholder="vehicle_licence" required>
                <span></span>
            </div>
            <input type="submit" value="Search">
        </form>
    </div>
    <?php
    require("db.inc.php");
    require("functions.php");
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
    #This will work only if received post information and wasn't redirected
    if (isset($_POST['owner_licence']) and !isset($_GET['add'])) {
        $owner_licence = $_POST['owner_licence'];
        $vehicle_licence = $_POST['vehicle_licence'];
        #Check if owner is present
        $sql = "SELECT People_ID FROM People WHERE People_licence='$owner_licence';";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            echo '<span class="homepage"> Owner does not exist!</span>';
            exit();
        } else {
            $row = mysqli_fetch_assoc($result);
            $Owner_ID = $row["People_ID"];
        }
        #Check if vehicle is present
        $sql = "SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence='$vehicle_licence';";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 0) {
            echo '<span class="homepage"> Vehicle does not exist! </span>';
            exit();
        } else {
            $row = mysqli_fetch_assoc($result);
            $Vehicle_ID = $row["Vehicle_ID"];
        }
        # Listing the incidents related to the owner and vehicle to choose from  
        $sql = "SELECT * FROM Incident WHERE People_ID = '$Owner_ID' AND Vehicle_ID = '$Vehicle_ID';";
        $result = mysqli_query($conn, $sql);
        $User_ID = $_SESSION['reporter_ID'];
        if (mysqli_num_rows($result) > 0) {
        echo '<br><span class="homepage"> Choose the specific Incident from the table below:</span>';
    ?>
    <script>
        document.getElementById("incident_form").remove();
    </script>
    <table id="customers">
        <tr>
            <th>Incident Date</th>
            <th>Incident Report</th>
            <th>Vehicle's licence</th>
            <th>Owner's licence</th>
            <th>Offence description</th>
        </tr>
        <?php

            while ($row = mysqli_fetch_assoc($result)) {
                $Incident_ID = $row["Incident_ID"];
                $Incident_Date = $row["Incident_Date"];
                $Incident_Report = $row["Incident_Report"];
                $offence_ID = $row["Offence_ID"];
                # Retrieve owner_licence, Vehicle licence, offence for audit purpose
                $licence_array = get_licences_from_ID($Owner_ID, $Vehicle_ID, $offence_ID, $conn);
                $owner_licence = $licence_array['owner_licence'];
                $vehicle_licence = $licence_array['vehicle_licence'];
                $offence_description = $licence_array['offence_description'];
                # Storing only important information in the Audit
                $audit = "INSERT INTO Audit_Incident (Timestamp, User_ID, Action, NEW_Vehicle_licence, NEW_Owner_licence, NEW_Incident_Date, NEW_Incident_Report, NEW_Offence_description)
                    VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'SEARCH', '$vehicle_licence', '$owner_licence', '$Incident_Date', '$Incident_Report', '$offence_description')";
                $query_result = mysqli_query($conn, $audit);
        ?>
        <tr>
            <td>
                <?php echo $Incident_Date ?>
            </td>
            <td>
                <?php echo $Incident_Report ?>
            </td>
            <td>
                <?php echo $vehicle_licence ?>
            </td>
            <td>
                <?php echo $owner_licence ?>
            </td>
            <td>
                <?php echo $offence_description ?>
            </td>
            <td><a href="add_fine.php?add=<?php echo $Incident_ID ?> " class="btn btn-secondary btn-lg"> Choose </a>
            </td>
        </tr>
        <?php
            }
        } else {
            echo '<span class="homepage"> There are no Incidents linking the owner with the vehicle! </span>';
        }
    }
    if (isset($_GET['add'])) {
        $Incident_ID = $_GET['add']
            ?>
        <div id="incident_form" class="center">
            <h1>Enter Fine Information</h1>
            <form method="post">
                <div class="txt_field">
                    <input type="number" name="fine_amount" placeholder="Fine Amount" required>
                    <span></span>
                </div>
                <div class="txt_field">
                    <input type="number" name="fine_points" placeholder="Fine Points" required>
                    <span></span>
                </div>
                <input type="submit" value="Search">
            </form>
        </div>
        <?php
        if (isset($_POST['fine_amount'])) {
            $fine_amount = $_POST['fine_amount'];
            $fine_points = $_POST['fine_points'];
            $sql = "Insert INTO Fines (Fine_Amount, Fine_Points, Incident_ID) VALUES ('$fine_amount' , '$fine_points', '$Incident_ID');";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $_SESSION["message"] = '<span class="homepage"> Added fine record Successfully! </span><br><span class="homepage"> Add another fine or navigate to homepage! </span>';
                # Retrieve Incident report
                $sql1 = "SELECT Incident_Report FROM Incident WHERE Incident_ID = '$Incident_ID';";
                $result1 = mysqli_query($conn, $sql1);
                $row1 = mysqli_fetch_assoc($result1);
                $Incident_Report = $row1['Incident_Report'];
                $User_ID = $_SESSION['reporter_ID'];
                $sql = "INSERT INTO Audit_Fines (Timestamp, User_ID, Action, NEW_Fine_Amount, NEW_Fine_Points, Incident_Report)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'INSERT', '$fine_amount', '$fine_points', '$Incident_Report')";
                // send query to database
                $result = mysqli_query($conn, $sql);
                header("location: add_fine.php");
            } else {
                echo '<span class="homepage"> A fine has already been added to this Incident!</span>';
                exit();
            }
        }
    }
        ?>
</body>

</html>