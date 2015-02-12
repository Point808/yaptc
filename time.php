<?php
  session_start();

  // Load config...
  require_once("config.inc.php");

  // Page title mod
  $yaptc_pagename = 'Time';

  // Load header
  require_once($yaptc_inc . "header.inc.php");

  // Load menu
  require_once($yaptc_inc . "menu.inc.php");

  //************************ CONTENT START ************************

  // If user is not logged in, give error and option to go to login
  if (!isset($_SESSION['user_id']) || !isset($_SESSION['signature']) || !isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true || $_SESSION['signature'] != md5($_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT']))
  {
    session_destroy();

    echo "not logged in!!!";
    exit();
  }
  else
  {

// content for logged-in users here
$userid = $_SESSION['user_id'];

$result = $sql->prepare("SELECT punches.id as punchid, users.id as user, punchtypes.punchname as type, punches.time as time, punches.notes as notes, punches.modified as modified FROM punches INNER JOIN users ON punches.userid = users.id INNER JOIN punchtypes ON punches.punchtypeid = punchtypes.id WHERE users.id = $userid ORDER BY punches.id DESC LIMIT 1");
$result->execute();
$last = $result->fetchObject();
echo "You have been punched $last->type since $last->time.";


// eventually i will get these in one query - for now this is separate to show all punches vs the last punch and status
$result = $sql->prepare("SELECT punches.id as punchid, users.id as user, punchtypes.punchname as type, punches.time as time, punches.notes as notes, punches.modified as modified FROM punches INNER JOIN users ON punches.userid = users.id INNER JOIN punchtypes ON punches.punchtypeid = punchtypes.id WHERE users.id = $userid ORDER BY punches.id DESC");
$result->execute();


echo '<table class="pure-table">';
echo '<thead>';
echo '<tr>';
echo '<th>Time</th>';
echo '<th>Type</th>';
echo '<th>Changed</th>';
echo '<th>Notes</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
while ($row = $result->fetch(PDO::FETCH_ASSOC))
{
$time = $row['time'];
$type = $row['type'];
$changed = $row['modified'];
if ($changed == "1") {$chg="YES";} else {$chg="NO";}
$notes = $row['notes'];
echo "<tr>";
echo "<td>$time</td>";
echo "<td>$type</td>";
echo "<td>$chg</td>";
echo "<td>$notes</td>";
echo "</tr>";
}
echo '</tbody>';
echo '</table>';



// end logged-in content
  }

  //************************ CONTENT END ************************
  // Load footer
  require_once($yaptc_inc . "footer.inc.php");
?>
