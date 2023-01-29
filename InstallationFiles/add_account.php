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
    <title>Add police user</title>
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
        <h1>Add new Login credentials</h1>
        <form method="post">
            <div class="txt_field">
                <input type="text" name="username" placeholder="username" required>
                <span></span>
            </div>
            <div class="txt_field">
                <input type="password" name="password" placeholder="password" required>
                <span></span>
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
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $User_ID = $_SESSION['reporter_ID'];
        $sql = "Insert INTO Login (Username, Password) VALUES ('$username', '$password');";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            echo '<span class="homepage"> Successfully added new credentials! </span>';
            $sql = "INSERT INTO Audit_Login (Timestamp, User_ID, Action, NEW_username, NEW_password)
            VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'INSERT', '$username', '$password')";
            // send query to database
            $result = mysqli_query($conn, $sql);
        } else {
            echo '<span class="homepage"> Username is used before! </span>';
        }
    }
    ?>
</body>

</html>