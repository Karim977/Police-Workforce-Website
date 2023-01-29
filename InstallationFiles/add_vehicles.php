<?php
session_start();
if (!isset($_SESSION["username"])) {

    echo '<span class="homepage"> Restricted access! </span>';
    echo '<br>';
    echo '<span class="homepage"> USER NOT LOGGED IN! </span>';
    exit();
}
$VEHICLE_LICENCE_LENGTH = 7;
?>
<!DOCTYPE html>
<html lang="en">

<head>
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
    <div class="center" id="form">
        <h1>Enter Vehicle Information</h1>
        <form method="post">
            <div class="txt_field">
                <input maxlength="7" type="text" name="vehicle_licence" required>
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
            <div class="txt_field">
                <input maxlength="16" type="text" name="owner_licence" required>
                <span></span>
                <label>Owner's licence</label>
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
    require("functions.php");
    // Open the database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        die();
    }
    if (isset($_GET["licence_status"]) && $_GET["licence_status"] == 0) {
        echo '<span class="homepage"> Entered licence is Invalid!</span>';
    }
    // Check if we have the new owner's licence FROM THE Session variable (Reverse connection from add_owner). 
    if (isset($_SESSION['owner_licence'])) {
        $sql = 'SELECT People_ID FROM People WHERE People_licence = "' . $_SESSION['owner_licence'] . '";';
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $owner_ID = $row["People_ID"];
        $arr = $_SESSION;
        check_insert_vehicles($conn, $owner_ID, $arr);
        unset($_SESSION['owner_licence']);
        unset($_SESSION['vehicle_licence']);
        unset($_SESSION['make']);
        unset($_SESSION['model']);
        unset($_SESSION['colour']);
    }
    if (isset($_POST['vehicle_licence'])) // Check only if 1 element is present as all elements have the required field and must be submitted
    {
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
        $flag = check_vehicle_presence($_POST['vehicle_licence'], $conn);
        if ($flag) {
            echo '<span class="homepage"> Vehicle already registered!</span>';
            exit();
        }
        // Search for the owner first and check his/her precense in the database
        $sql = 'SELECT People_ID FROM People WHERE People_licence = "' . $_POST['owner_licence'] . '";';
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $owner_ID = $row['People_ID'];
            $arr = $_POST;
            check_insert_vehicles($conn, $owner_ID, $arr);
        }
        // First time if no owner was found, it will redirect to add_owner.php Then when it redirects back, this sql query will have a return as we just added the owner 
        elseif (mysqli_num_rows($result) == 0) {
            // Save data submitted by from in order not to forget it
            $_SESSION['vehicle_licence'] = $_POST['vehicle_licence'];
            $_SESSION['make'] = $_POST['make'];
            $_SESSION['model'] = $_POST['model'];
            $_SESSION['colour'] = $_POST['colour'];
            redirect("add_owner.php");
        }
        // In case there are many people with the same name, Ask the officer for which owner licence number to search on
    }
    ?>
</body>

</html>