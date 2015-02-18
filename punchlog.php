<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Punch Log";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
// Is user logged in?  If not, they shouldn't be here - kill all variables and redirect to login...
if (!isset($_SESSION['user_id']) || !isset($_SESSION['signature']) || !isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true || $_SESSION['signature'] != md5($_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT']))
{
session_start();
session_unset();
session_destroy();
header ("Refresh:3; url=login.php", true, 303);
echo "<h2 class=\"content-subhead\">You are not logged in!!!</h2>";
}
else
{
//********** BEGIN CONTENT **********//
$userid = $_SESSION['user_id'];
$timenow = date('Y-m-d H:i');

// This is to get the current user status - in or out - and the notes and times associated for use in the form
$result = $sql->prepare("SELECT punches.id as punchid, users.id as user, punches.intime as intime, punches.outtime as outtime, punches.notes as notes FROM punches INNER JOIN users ON punches.userid = users.id WHERE users.id = $userid ORDER BY punches.id DESC LIMIT 1");
$result->execute();
$last = $result->fetchObject();

// Let's build the page - this is the header with current status
echo "<h2 class=\"content-subhead\">Advanced Punch</h2>";
if(!isset($last->user)) {
  echo "<p>You do not appear to have any punches on record.</p>";
  $status = "Out";
  } else {
  if (!empty($last->outtime)) { $status = "Out"; $statustime = $last->outtime; } else { $status = "In"; $statustime = $last->intime; $punchid = $last->punchid; $notes = $last->notes; }
echo "<p>You have been Punched $status since " . date('g:i a \o\n M jS, Y', strtotime($statustime)) . ".</p>";
}

echo "<p>Use this form to enter a specific time on your punch.  NOTE: changing the time from the current time will cause a flag on your log for the administrator to review, so we suggest you enter a reason why in the notes field (i.e. forgot punch, working from home, system down, etc).</p>";
echo "<form class=\"pure-form pure-form-stacked\" action=\"punchlog.php\" method=\"post\">";
echo "<fieldset>";
echo "<label for=\"punchtime\">Punch Time</label>";
echo "<input type=\"text\" name=\"punchtime\" placeholder=\"$timenow\" maxlength=\"20\">";
echo "<label for=\"notes\">Notes</label>";
if (isset($notes)) { 
echo "<input type=\"text\" name=\"notes\" placeholder=\"Enter notes if needed\" maxlength=\"255\" value=\"$notes\">";
} else {
echo "<input type=\"text\" name=\"notes\" placeholder=\"Enter notes if needed\" maxlength=\"255\">";
}
echo "<div class=\"pure-controls\">";
if ($status=="In") {
echo "<button type=\"submit\" class=\"pure-button button-xlarge button-success pure-button-disabled\">Punch IN</button>";
echo "<button type=\"submit\" class=\"pure-button button-xlarge button-error\">Punch OUT</button>";
  } else {
echo "<button type=\"submit\" class=\"pure-button button-xlarge button-success\">Punch IN</button>";
echo "<button type=\"submit\" class=\"pure-button button-xlarge button-error pure-button-disabled\">Punch OUT</button>";
}
echo "</div>";




// If the posted variables are not empty, we must be trying to insert a new punch.  Use the form values to insert new record
if (!empty($_POST)) {

// Is the notes field set? If so, use, otherwise set to null
if (isset($_POST['notes'])) { if (!empty($_POST['notes'])) { $p_notes = $_POST['notes']; } else { $p_notes = NULL; } } else { $p_notes = NULL; }

// Is the punch time field set? If so, use, otherwise set to now
if (isset($_POST['punchtime'])) {
  if (!empty($_POST['punchtime'])) { $p_punchtime = $_POST['punchtime'] . ':00'; $p_modified = "1"; } else { $p_punchtime = $timenow . ':00'; $p_modified = "0"; }
} else { $p_punchtime = $timenow . ':00'; $p_modified = "0"; }

// Is the user currently punched in?  If so, insert the punch out record, otherwise, insert a new punch in
if ($status=="In") {
  $query = "UPDATE punches SET outtime = :p_punchtime, notes = :p_notes, modified = :p_modified WHERE id = :p_punchid";
  $stmt = $sql->prepare($query);
  $stmt->execute(array(
        ':p_punchid'    => $punchid,
        ':p_notes'    => $p_notes,
        ':p_punchtime'    => $p_punchtime,
        ':p_modified'    => $p_modified,
    ));
  } else {
  $query = "INSERT INTO punches (userid, notes, intime, modified) VALUES (:p_userid, :p_notes, :p_punchtime, :p_modified)";
  $stmt = $sql->prepare($query);
  $stmt->execute(array(
    ':p_userid' => $_SESSION['user_id'],
        ':p_notes'    => $p_notes,
        ':p_punchtime'    => $p_punchtime,
        ':p_modified'    => $p_modified,
    ));
  }

// And then send user back to this page to see the updates
header('Location: '.$_SERVER['PHP_SELF']);
}

// Close out the form...
echo "</fieldset>";
echo "</form>";


echo "<h2 class=\"content-subhead\">Punch History</h2>";
echo "<p>Below is your full punch history, sorted newest to oldest.</p>";
$result = $sql->prepare("SELECT punches.id as punchid, users.id as user, punches.intime as intime, punches.outtime as outtime, punches.notes as notes, punches.modified as modified FROM punches INNER JOIN users ON punches.userid = users.id WHERE users.id = $userid ORDER BY punches.id DESC");
$result->execute();
echo '<table class="pure-table">';
echo '<thead>';
echo '<tr>';
echo '<th>Time In</th>';
echo '<th>Time Out</th>';
echo '<th>Hours</th>';
echo '<th>Flag</th>';
echo '<th>Notes</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
while ($row = $result->fetch(PDO::FETCH_ASSOC))
{
$intime = $row['intime'];
$outtime = $row['outtime'];
$date1 = new DateTime($intime);
$date2 = new DateTime($outtime);
$seconds = abs($date1->getTimestamp()-$date2->getTimestamp());
$flag = $row['modified'];
if ($flag == "1") {$flg="YES";} else {$flg="";}
$notes = $row['notes'];
echo "<tr>";
echo "<td>$intime</td>";
echo "<td>$outtime</td>";
echo "<td>" . number_format((float)(($seconds/60)/60), 2, '.', '') . "</td>";
echo "<td>$flg</td>";
echo "<td>$notes</td>";
echo "</tr>";
}
echo '</tbody>';
echo '</table>';



//********** END CONTENT **********//
}
require_once($yaptc_inc . "footer.inc.php");
?>
