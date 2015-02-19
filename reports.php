<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Reports";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false) {
killSession();
} else {
//********** BEGIN CONTENT **********//

echo "<h2 class=\"content-subhead\">Punch History</h2>";
echo "<p>Below is your company punch history.  The below drop-down can be used to select pre-configured reports.  Other reports are currently being written.</p>";


echo "<form class=\"pure-form pure-form-stacked\" action=\"reports.php\" method=\"post\">";
echo "    <fieldset>";
echo "        <div class=\"pure-g\">";
echo "             <div class=\"pure-u-1\">";
echo "                <label for=\"reporttype\">Report Type</label>";
echo "                <select name=\"reporttype\" class=\"pure-input-1-2\">";
if (isset($_POST['reporttype'])) { echo "<option value=\"" . $_POST['reporttype'] . "\">" . $_POST['reporttype'] . "</option><option>----------</option>";}
else { echo "<option></option>";}
echo "                    <option value=\"Hours per week per user\">Hours per week per user</option>";
echo "                    <option value=\"Hours per month per user\">Hours per month per user</option>";
echo "                </select>";
echo "            </div>";
echo "        </div>";
echo "        <button type=\"submit\" class=\"pure-button pure-button-primary\">Submit</button>";
echo "    </fieldset>";
echo "</form>";

if (isset($_POST['reporttype'])) {
if ($_POST['reporttype'] == "Hours per week per user") {
$query = "SELECT
YEAR(punches.intime) AS g_year,
WEEK(punches.intime) AS g_week,
SUM(TIME_TO_SEC(TIMEDIFF(punches.outtime, punches.intime))/3600) AS punchhours,
  punches.id as punchid,
  users.id as user,
  users.username as username,
  users.firstname as firstname,
  users.lastname as lastname,
  punches.intime as intime,
  punches.outtime as outtime,
  punches.notes as notes,
  punches.modified as modified
  FROM punches
INNER JOIN users ON punches.userid = users.id
GROUP BY g_year, g_week, users.username;";
$stmt = $sql->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

//set up table header and open table
echo '<table class="pure-table">';
echo '<thead>';
echo '<tr>';
echo '<th>Year</th>';
echo '<th>Week#</th>';
echo '<th>Username</th>';
echo '<th>Hours</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// $rows is an array containing all records...
foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>" . $row['g_year'] . "</td>";
    echo "<td>" . $row['g_week'] . "</td>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>" . $row['punchhours'] . "</td>";
    echo "</tr>";
}
echo '</tbody>';
echo '</table>';
}
elseif ($_POST['reporttype'] == "Hours per month per user") {
$query = "SELECT
YEAR(punches.intime) AS g_year,
MONTH(punches.intime) AS g_month,
SUM(TIME_TO_SEC(TIMEDIFF(punches.outtime, punches.intime))/3600) AS punchhours,
  punches.id as punchid,
  users.id as user,
  users.username as username,
  users.firstname as firstname,
  users.lastname as lastname,
  punches.intime as intime,
  punches.outtime as outtime,
  punches.notes as notes,
  punches.modified as modified
  FROM punches
INNER JOIN users ON punches.userid = users.id
GROUP BY g_year, g_month, users.username;";
$stmt = $sql->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

//set up table header and open table
echo '<table class="pure-table">';
echo '<thead>';
echo '<tr>';
echo '<th>Year</th>';
echo '<th>Month</th>';
echo '<th>Username</th>';
echo '<th>Hours</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// $rows is an array containing all records...
foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>" . $row['g_year'] . "</td>";
    echo "<td>" . $row['g_month'] . "</td>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>" . $row['punchhours'] . "</td>";
    echo "</tr>";
}
echo '</tbody>';
echo '</table>';
}
else {
  echo "no query";
}
} else { echo "no query"; }

//********** END CONTENT **********//
}
require_once($yaptc_inc . "footer.inc.php");
?>
