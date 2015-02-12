<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Home";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
//********** BEGIN CONTENT **********//

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

$userid = $_SESSION['user_id'];
$result = $sql->prepare("SELECT punches.id as punchid, users.id as user, punchtypes.id as typeid, punchtypes.punchname as type, punches.time as time, punches.notes as notes, punches.modified as modified FROM punches INNER JOIN users ON punches.userid = users.id INNER JOIN punchtypes ON punches.punchtypeid = punchtypes.id WHERE users.id = $userid ORDER BY punches.id DESC LIMIT 1");
$result->execute();
$last = $result->fetchObject();
echo "<h2 class=\"content-subhead\">Current Status</h2>";
echo "<p>You have been Punched $last->type since " . date('g:i a \o\n M jS, Y', strtotime($last->time)) . ".</p>";
echo "<h2 class=\"content-subhead\">Quick Punch</h2>";
echo "<p>Clicking the button below will immediately enter a new punch for you depending on your current status.  Any notes you enter will be attached to the punch for your administrator to review.</p>";
echo "<form class=\"pure-form pure-form-stacked\">";
echo "<fieldset>";
echo "<input type=\"notes\" placeholder=\"Enter notes if needed\" maxlength=\"255\">";
echo "<div class=\"pure-controls\">";
if ($last->typeid=="00000000001") {
  //$result = $sql->prepare("INSERT INTO punches (userid, punchtypeid, time) VALUES ($userid, "00000000002", NOW())");
  //$result->execute();
  //$punch = $result->fetchObject();
echo "<button type=\"submit\" class=\"pure-button button-xlarge button-success\">Punch OUT</button>";
  } else {
echo "<button type=\"submit\" class=\"pure-button button-xlarge button-success\">Punch IN</button>";
}
echo "</div>";
echo "</fieldset>";
echo "</form>";






  }

//********** END CONTENT **********//
require_once($yaptc_inc . "footer.inc.php");
?>
