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
<!-- Coding By CodingNepal - youtube.com/codingnepal -->
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Change password</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap-css/css/bootstrap.min.css">
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
        <h1>Change Password</h1>
        <form method="post">
            <div class="txt_field">
                <input type="password" name="old_password" required>
                <span></span>
                <label>Old Password</label>
            </div>
            <div class="txt_field">
                <input type="password" name="new_password" required>
                <span></span>
                <label>New Password</label>
            </div>
            <input type="submit" value="Login">
        </form>
    </div>
    <?php
    function redirect($url)
    {
        header('Location: ' . $url);
        die();
    }
    #isset($_GET["del"]))   
    require("db.inc.php");
    // Open the database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        die();
    }
    // else echo "MySQL connection OK<br>";  // useful for testing  
    // if a form has been submitted, insert a new record                                              
    if (
        isset($_POST['old_password']) && $_POST['old_password'] != "" &&
        isset($_POST['new_password']) && $_POST['new_password'] != ""
    ) // check contents of $_POST supervariables
    {
        $sql = 'SELECT * FROM Login WHERE User_ID = "' . $_SESSION['reporter_ID'] . '"';
        // send query to database
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        if ($_POST['old_password'] != $row["Password"]) {
            echo '<span class="homepage"> Old Password is INCORRECT! </span>';
        } else {
            $sql = 'UPDATE Login SET Password="' . $_POST['new_password'] . '" WHERE User_ID=' . $_SESSION['reporter_ID'];
            // send query to database
            $result = mysqli_query($conn, $sql);
            $User_ID = $_SESSION['reporter_ID'];
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $sql = "INSERT INTO Audit_Login (Timestamp, User_ID, Action, OLD_password, NEW_password)
            VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'Update', '$old_password', '$new_password')";
            // send query to database
            $result = mysqli_query($conn, $sql);
            $_SESSION["message"] = '<span class="homepage"> Password successfully changed! </span>';
            redirect("main.php");
        }
    }
    ?>
</body>

</html>