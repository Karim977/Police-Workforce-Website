<?php
session_start();
require("functions.php");
if (isset($_GET["logout"])) {
  $all = True;
  reset_session_vars($all);
  header("location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Police Workforce</title>
  <script type="text/javascript" src="script.js"></script>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="bootstrap-css/css/bootstrap.min.css">
</head>

<body>
  <div id='form' class="center">
    <h1>Police Force Login</h1>
    <form method="post">
      <div class="txt_field">
        <input type="text" name="username" required>
        <span></span>
        <label>Reporter's Username</label>
      </div>
      <div class="txt_field">
        <input type="password" name="password" required>
        <span></span>
        <label>Password</label>
      </div>
      <input type="submit" value="Login">
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
  // if a form has been submitted, insert a new record                                              
  if (isset($_POST['username']) && isset($_POST['password'])) // check contents of $_POST supervariables
  {
    // construct the SELECT query
    $sql = 'SELECT * FROM Login WHERE username = "' . $_POST["username"] . '" AND password = "' . $_POST["password"] . '"';
    #echo $sql;
    // send query to database
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      $_SESSION['username'] = $row["Username"];
      $_SESSION['reporter_ID'] = $row["User_ID"];
      #echo $row["Username"];
    } else // if query result is empty 
    {
      echo '<span class="homepage"> Invalid credentials!</span>';
    }
  }
  if (isset($_SESSION["username"])) { ?>
    <a href="main.php" class="btn btn-info btn-lg" id='homepage'>
          <span class="glyphicon glyphicon-log-out"></span> Homepage
    </a>
    <a href="index.php?logout" class="btn btn-info btn-lg" id='logout'>
          <span class="glyphicon glyphicon-log-out"></span> Log out
    </a>
    <br><br><br><br><br>
    <span class="change_pw"> Hello <?php echo $_SESSION["username"]; ?>! </span>
    <br>
    <span class="change_pw"> Click <a href="change_password.php" style="color: red">here</a> to change your password! </span>
    <script>
    remove_by_id('form');
    </script>
    <?php
  }
  ?>
</body>

</html>