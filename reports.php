<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Reports";
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
echo "<h2 class=\"content-subhead\">Punch History</h2>";
echo "<p>Below is your company punch history.  You can use the form boxes to narrow down the results as needed, by date, user, or a combination.</p>";


echo "<form class=\"pure-form pure-form-stacked\" action=\"reports.php\" method=\"post\">";
echo "    <fieldset>";
echo "        <div class=\"pure-g\">";
echo "             <div class=\"pure-u-1 pure-u-md-1-3\">";
echo "                <label for=\"order\">Sort Order</label>";
echo "                <select name=\"order\" class=\"pure-input-1-2\">";
echo "                    <option>Newest to Oldest</option>";
echo "                    <option>Oldest to Newest</option>";
echo "                </select>";
echo "            </div>";
echo "        </div>";
echo "        <button type=\"submit\" class=\"pure-button pure-button-primary\">Submit</button>";
echo "    </fieldset>";
echo "</form>";



// tag order to query depending on drop-down
if ($_POST['order'] == "Newest to Oldest") {
 $order="ORDER BY punches.id DESC"; }
 else {
 $order="ORDER BY punches.id"; }

// actual query
$query = "SELECT
  punches.id as punchid,
  users.id as user,
  users.firstname as firstname,
  users.lastname as lastname,
  punches.intime as intime,
  punches.outtime as outtime,
  punches.notes as notes,
  punches.modified as modified
  FROM punches
  INNER JOIN users ON punches.userid = users.id $order";

   $stmt = $sql->prepare($query);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

//set up table header and open table
echo '<table class="pure-table">';
echo '<thead>';
echo '<tr>';
echo '<th>First Name</th>';
echo '<th>Last Name</th>';
echo '<th>Time In</th>';
echo '<th>Time Out</th>';
echo '<th>Hours</th>';
echo '<th>Flag</th>';
echo '<th>Notes</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// $rows is an array containing all records...
foreach ($rows as $row)
    echo "<tr><td>" . $row['firstname'] . "</td><td>" . $row['lastname'] . "</td><td>" . $row['intime'] . "</td><td>" . $row['outtime'] . "</td><td>" . $row['hours'] . "</td><td>" . $row['flag'] . "</td><td>" . $row['notes'] . "</td></tr>";

echo '</tbody>';
echo '</table>';

//********** END CONTENT **********//
}
require_once($yaptc_inc . "footer.inc.php");
?>
