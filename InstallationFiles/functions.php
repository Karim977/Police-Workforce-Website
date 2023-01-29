<?php
function check_insert_vehicles($conn, $owner_ID, $arr)
{
    $sql = 'SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = "' . $arr['vehicle_licence'] . '";';
    // Check if no car entry is found i.e new car.
    $result = mysqli_query($conn, $sql);
    $vehicle_licence = $arr['vehicle_licence'];
    // We already checked if vehicle is present or not in add_vehicles.php
    if (mysqli_num_rows($result) == 0) {
        $make = $arr['make'];
        $model = $arr['model'];
        $colour = $arr['colour'];
        $User_ID = $_SESSION['reporter_ID'];
        // Enter new car's info in Vehicle. Enter in Ownership vehicle ID and Owner_ID
        $sql = 'INSERT INTO Vehicle (Vehicle_licence, Vehicle_make, Vehicle_model, Vehicle_colour) VALUES
                    ("' . $arr['vehicle_licence'] . '","' . $arr['make'] . '","' . $arr['model'] . '","' . $arr['colour'] . '");';
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $audit = "INSERT INTO Audit_Vehicle (Timestamp, User_ID, Action, NEW_Vehicle_make, NEW_Vehicle_model , NEW_Vehicle_colour, NEW_Vehicle_licence)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'INSERT', '$make', '$model', '$colour', '$vehicle_licence')";
            $query_result = mysqli_query($conn, $audit);
        }
        // Retrieve the new car's ID
        $sql = 'SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = "' . $arr['vehicle_licence'] . '";';
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $vehicle_ID = $row["Vehicle_ID"];
    }
    $sql = "INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES ('$owner_ID', '$vehicle_ID');";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo '<span class="homepage"> Vehicle ADDED successfully!</span>';
        # Retrieve owner_ID
        $sql = "SELECT People_licence FROM People WHERE People_ID = '$owner_ID'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $owner_licence = $row["People_licence"];
            $audit = "INSERT INTO Audit_Ownership (Timestamp, User_ID, Action, Owner_licence, Vehicle_licence)
                VALUES (CURRENT_TIMESTAMP(), '$User_ID', 'INSERT', '$owner_licence', '$vehicle_licence')";
            $query_result = mysqli_query($conn, $audit);
        }
    } else {
        echo '<span class="homepage"> Error adding vehicle and owner together!</span>';
    }
}

function get_licences_from_ID($owner_ID, $vehicle_ID, $offence_ID, $conn)
{
    $sql1 = "SELECT People_licence FROM People WHERE People_ID = '$owner_ID';";
    $result1 = mysqli_query($conn, $sql1);
    $row1 = mysqli_fetch_assoc($result1);
    $owner_licence = $row1['People_licence'];
    $sql2 = "SELECT Vehicle_licence FROM Vehicle WHERE Vehicle_ID = '$vehicle_ID';";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);
    $vehicle_licence = $row2['Vehicle_licence'];
    if ($offence_ID != NULL) {
        $sql3 = "SELECT Offence_description FROM Offence WHERE Offence_ID = '$offence_ID';";
        $result3 = mysqli_query($conn, $sql3);
        $row3 = mysqli_fetch_assoc($result3);
        $ofence_description = $row3['Offence_description'];
        $array = array(
            "owner_licence" => $owner_licence,
            "vehicle_licence" => $vehicle_licence,
            "offence_description" => $ofence_description
        );
    }
    else {
        $array = array(
            "owner_licence" => $owner_licence,
            "vehicle_licence" => $vehicle_licence,
        );
    }
    
    return $array;
}
function reset_session_vars($all)
{
    $username = $_SESSION['username'];
    $reporter_ID = $_SESSION['reporter_ID'];
    // Stackoverflow comment
    // Unsetting all session variables
    $helper = array_keys($_SESSION);
    foreach ($helper as $key) {
        unset($_SESSION[$key]);
    }
    if ($all == False) {
        $_SESSION['username'] = $username;
        $_SESSION['reporter_ID'] = $reporter_ID;  
    }
}

function check_vehicle_presence($vehicle_licence, $conn) {
    $sql = 'SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = "' . $vehicle_licence . '";';
    // Check if no car entry is found i.e new car.
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return True;
    }
    return False;
}
?>