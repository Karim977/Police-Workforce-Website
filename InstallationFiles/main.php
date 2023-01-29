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
    <title>Document</title>
</head>

<body id="blurred-bg">
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
        <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
        <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <?php
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
        echo '<span class="homepage">' . $message . '</span><br>';
        unset($_SESSION["message"]);
    }
    ;
    $num_people = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DISTINCT COUNT(*) 'COUNT' FROM People;"))['COUNT'];
    $num_vehicles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DISTINCT COUNT(*) 'COUNT' FROM Vehicle;"))['COUNT'];
    $num_incidents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DISTINCT COUNT(*) 'COUNT' FROM Incident;"))['COUNT'];
    $num_credentials = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DISTINCT COUNT(*) 'COUNT' FROM Login;"))['COUNT'];
    $num_fines = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DISTINCT COUNT(*) 'COUNT' FROM Fines;"))['COUNT'];
    ?>
    <span class="homepage" style="color: white">Choose one of the following:</span>
    <!-- https://learn.shayhowe.com/html-css/creating-lists/ -->
    <nav class="navigation">
        <ul>
            <!-- https://mdbootstrap.com/docs/standard/components/list-group/ -->
            <li><a href="search_people.php">Search people</a><span class="badge badge-primary rounded-pill">
                    <?php echo $num_people; ?>
                </span></li>
            <li><a href="search_vehicles.php">Search vehicles</a><span class="badge badge-primary rounded-pill">
                    <?php echo $num_vehicles; ?>
                </span></li>
            <li><a href="add_vehicles.php">Add vehicles</a><span class="badge badge-primary rounded-pill">
                    <?php echo $num_vehicles; ?>
                </span></li>
            <li><a href="add_Incident.php">Incidents</a><span class="badge badge-primary rounded-pill">
                    <?php echo $num_incidents; ?>
                </span></li>
            <!-- This is specialized for administrator "daniels" only -->
            <?php
            if ($_SESSION["username"] == "daniels") {
            ?>
            <li><a href="add_account.php">Add new police credentials</a><span class="badge badge-primary rounded-pill">
                    <?php echo $num_credentials; ?>
                </span></li>
            <li><a href="add_fine.php">Add fines</a><span class="badge badge-primary rounded-pill">
                    <?php echo $num_fines; ?>
                </span></li>
            <?php
            }
            ?>
            <li><a href="change_password.php">Change Password</a></li>
        </ul>
    </nav>

</body>

</html>