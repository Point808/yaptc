<?php

// Get user list for users management page
function listUsers($yaptc_db) {
   $stmt = $yaptc_db->query("SELECT users.id as userid, users.username as username, users.email as email, users.created as created, users.firstname as firstname, users.lastname as lastname, users.usertype as usertypeid, usertypes.typename as usertype
FROM yaptc.users
INNER JOIN usertypes ON users.usertype = usertypes.id
ORDER BY users.lastname ASC;");
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update user profile
function updateUserProfile($yaptc_db, $userid, $firstname, $lastname, $email)
{
$stmt = $yaptc_db->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email WHERE id = :userid;");
$stmt->execute(array(
    ':userid' => $userid,
    ':firstname' => $firstname,
    ':lastname' => $lastname,
    ':email' => $email,
    ));
}

// Update user profile
function updateUserPassword($yaptc_db, $userid, $password)
{
$stmt = $yaptc_db->prepare("UPDATE users SET password = :password WHERE id = :userid;");
$stmt->execute(array(
    ':userid' => $userid,
    ':password' => $password,
    ));
}


// Get login status - returns true or false
function getSessionStatus()
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['signature']) || !isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true || $_SESSION['signature'] != md5($_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT'])) {
        return false;
    } else {
        return true;
    }
}

// Kick user and go to login
function killSession()
{
    session_unset();
    session_destroy();
    session_write_close();
    header("Location: login.php");
}

// Get user access level.  Call with $sql passed or it will not work correctly
function getSessionAccess($yaptc_db)
{
    if (isset($_SESSION['user_id'])) {
        $query3 = "SELECT users.id as userid, usertypes.typename AS usertype FROM yaptc.users INNER JOIN yaptc.usertypes ON users.usertype = usertypes.id WHERE users.id = :id";
        $stmt3  = $yaptc_db->prepare($query3);
        $stmt3->execute(array(
            ':id' => $_SESSION['user_id']
        ));
        $user3 = $stmt3->fetchObject();
        return $user3->usertype;
    }
}

// Punch Out
function punchOut($yaptc_db, $punchid, $notes)
{
$stmt = $yaptc_db->prepare("UPDATE punches SET punches.outtime = NOW(), punches.notes = :notes WHERE punches.id = :punchid;");
$stmt->execute(array(
    ':punchid' => $punchid,
    ':notes' => $notes
    ));
}

// Punch In
function punchIn($yaptc_db, $userid, $notes)
{
$stmt = $yaptc_db->prepare("INSERT INTO punches (punches.userid, punches.notes, punches.intime) VALUES (:userid, :notes, NOW());");
$stmt->execute(array(
    ':userid' => $userid,
    ':notes' => $notes
    ));
}

// Get punch status - returns array
function getPunchStatus($yaptc_db, $userid)
{
    $stmt = $yaptc_db->prepare("SELECT punches.id as punchid, users.id as userid, punches.intime as intime, punches.outtime as outtime, punches.notes as notes FROM punches INNER JOIN users ON punches.userid = users.id WHERE users.id = :userid ORDER BY punches.intime DESC LIMIT 1;");
    $stmt->execute(array(
        ':userid' => $userid
        ));
    $result = $stmt->fetch( PDO::FETCH_ASSOC );
    return array ($result['punchid'], $result['userid'], $result['intime'], $result['outtime'], $result['notes']);
}

// Report - Weekly Hours by Week then User
function reportWeeklyByUser($yaptc_db) {
   $stmt = $yaptc_db->query("SELECT YEAR(punches.intime) AS g_year, WEEK(punches.intime) AS g_week, ROUND(SUM(TIME_TO_SEC(TIMEDIFF(punches.outtime, punches.intime))/3600),2) AS punchhours, punches.id as punchid, users.id as user, users.username as username, users.firstname as firstname, users.lastname as lastname, punches.intime as intime, punches.outtime as outtime, punches.notes as notes, punches.modified as modified FROM punches INNER JOIN users ON punches.userid = users.id GROUP BY g_year, g_week, users.username;");
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Report - Monthly Hours by Month then User
function reportMonthlyByUser($yaptc_db) {
   $stmt = $yaptc_db->query("SELECT YEAR(punches.intime) AS g_year, MONTHNAME(punches.intime) AS g_month, ROUND(SUM(TIME_TO_SEC(TIMEDIFF(punches.outtime, punches.intime))/3600),2) AS punchhours, punches.id as punchid, users.id as user, users.username as username, users.firstname as firstname, users.lastname as lastname, punches.intime as intime, punches.outtime as outtime, punches.notes as notes, punches.modified as modified FROM punches INNER JOIN users ON punches.userid = users.id GROUP BY g_year, g_month, users.username;");
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
