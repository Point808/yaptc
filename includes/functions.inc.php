<?php

// Current Time
$timenow = date('Y-m-d H:i:s');


// Get user list for users management page
function listUsers($yaptc_db) {
   $stmt = $yaptc_db->query("SELECT users.id as userid, users.username as username, users.email as email, users.created as created, users.firstname as firstname, users.lastname as lastname, users.usertype as usertypeid, usertypes.typename as usertype
FROM yaptc.users
INNER JOIN usertypes ON users.usertype = usertypes.id
ORDER BY users.lastname ASC;");
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
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



// Edit Punch
function editPunch($yaptc_db, $punchid, $intime, $outtime, $notes)
{
$stmt = $yaptc_db->prepare("UPDATE punches SET punches.intime = :intime, punches.outtime = :outtime, punches.notes = :notes WHERE punches.id = :punchid;");
$stmt->execute(array(
    ':punchid' => $punchid,
    ':intime' => $intime,
    ':outtime' => $outtime,
    ':notes' => $notes
    ));
}
// Delete Punch
function deletePunch($yaptc_db, $punchid)
{
$stmt = $yaptc_db->prepare("DELETE FROM punches WHERE punches.id = :punchid;");
$stmt->execute(array(
    ':punchid' => $punchid
    ));
}


// Punch Out
function punchOut($yaptc_db, $punchid, $notes, $outtime, $modified=NULL)
{
$stmt = $yaptc_db->prepare("UPDATE punches SET punches.outtime = :outtime, punches.notes = :notes, punches.modified = :modified WHERE punches.id = :punchid;");
$stmt->execute(array(
    ':punchid' => $punchid,
    ':modified' => $modified,
    ':outtime' => $outtime,
    ':notes' => $notes
    ));
}

// Punch In
function punchIn($yaptc_db, $userid, $notes, $punchtime, $modified=NULL)
{
$stmt = $yaptc_db->prepare("INSERT INTO punches (punches.userid, punches.notes, punches.intime, punches.modified) VALUES (:userid, :notes, :punchtime, :modified);");
$stmt->execute(array(
    ':userid' => $userid,
    ':notes' => $notes,
    ':punchtime' => $punchtime,
    ':modified' => $modified
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



// List punches sorted by intime.  Pass uid or % for all.  Pass limit to restrict row results.  Default is set to tons of 9's because no wildcard exists for limit in mysql or pgsql
function listPunches($db, $uid, $limit = "999999999999999") {
    $stmt = $db->prepare('
        SELECT
        ROUND(TIME_TO_SEC(TIMEDIFF(punches.outtime, punches.intime))/3600,2) AS punchhours,
        punches.id as punchid,
        punches.intime as intime,
        punches.outtime as outtime,
        users.id AS userid,
        users.firstname as firstname,
        users.lastname as lastname,
        REPLACE (punches.modified, "1", "YES") as modified,
        punches.notes as notes
        FROM yaptc.punches
        INNER JOIN yaptc.users ON punches.userid = users.id
        WHERE users.id LIKE :uid
        ORDER BY punches.intime DESC
        LIMIT :limit
    ');
    $stmt->execute(array(
        ':uid' => $uid,
        ':limit' => $limit,
    ));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get user info from user id.  Pass uid or % for all.
function getUserInfo($db, $uid) {
    $stmt = $db->prepare('
        SELECT
        users.id AS userid,
        users.username AS username,
        users.firstname AS firstname,
        users.lastname AS lastname,
        users.email AS email,
        usertypes.typename AS usertype,
        usertypes.id AS usertypeid,
        users.created AS created,
        users.password AS password
        FROM yaptc.users
        INNER JOIN yaptc.usertypes ON users.usertype = usertypes.id
        WHERE users.id LIKE :uid
        ORDER BY users.lastname ASC;
    ');
    $stmt->execute(array(
        ':uid' => $uid
    ));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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



// Set user info from user id
function setUserInfo($db, $uid, $firstname, $lastname, $email, $usertypeid, $password) {
    $stmt = $db->prepare('
        UPDATE
        yaptc.users
        SET
        firstname = :firstname,
        lastname = :lastname,
        email = :email,
        usertype = :usertypeid,
        password = :password
        WHERE id = :uid
    ');
    $stmt->execute(array(
        ':uid' => $uid,
        ':firstname' => $firstname,
        ':lastname' => $lastname,
        ':email' => $email,
        ':usertypeid' => $usertypeid,
        ':password' => $password,
    ));

}

// Report - Weekly Hours by Week then User
function reportWeeklyByUser($yaptc_db)
{
    $statement = $yaptc_db->prepare('
        SELECT
        YEAR(punches.intime) AS g_year,
        WEEK(punches.intime) AS g_week,
        ROUND(SUM(TIME_TO_SEC(TIMEDIFF(punches.outtime, punches.intime))/3600),2) AS punchhours,
        punches.intime as intime,
        punches.outtime as outtime,
        users.firstname as firstname,
        users.lastname as lastname,
        REPLACE (punches.modified, "1", "YES") as modified,
        punches.notes as notes
        FROM yaptc.punches
        INNER JOIN yaptc.users ON punches.userid = users.id
        GROUP BY g_year, g_week, users.username
    ');
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

// Report - Monthly Hours by Month then User
function reportMonthlyByUser($yaptc_db)
{
    $statement = $yaptc_db->prepare('
        SELECT
        YEAR(punches.intime) AS g_year,
        MONTHNAME(punches.intime) AS g_month,
        ROUND(SUM(TIME_TO_SEC(TIMEDIFF(punches.outtime, punches.intime))/3600),2) AS punchhours,
        punches.intime as intime,
        punches.outtime as outtime,
        users.firstname as firstname,
        users.lastname as lastname,
        REPLACE (punches.modified, "1", "YES") as modified,
        punches.notes as notes
        FROM yaptc.punches
        INNER JOIN yaptc.users ON punches.userid = users.id
        GROUP BY g_year, g_month, users.username;
    ');
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}



// EOF
?>
