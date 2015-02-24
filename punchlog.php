<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = "Punch Log";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
<!-- ********** BEGIN CONTENT ********** -->


<?php
$userid = $_SESSION['user_id'];

// This is to get the current user status - in or out - and the notes and times associated for use in the form
$result = $yaptc_db->prepare("SELECT punches.id as punchid, users.id as user, punches.intime as intime, punches.outtime as outtime, punches.notes as notes FROM punches INNER JOIN users ON punches.userid = users.id WHERE users.id = $userid ORDER BY punches.id DESC LIMIT 1");
$result->execute();
$last = $result->fetchObject();

// Let's build the page - this is the header with current status IF allowed
if ($yaptc_allowadvancedpunch == 'yes'):

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
  if (!empty($_POST['punchtime'])) { $p_punchtime = $_POST['punchtime'] . ':00'; $p_modified = "1"; } else { $p_punchtime = $timenow; $p_modified = "0"; }
} else { $p_punchtime = $timenow; $p_modified = "0"; }

// Is the user currently punched in?  If so, insert the punch out record, otherwise, insert a new punch in
if ($status=="In") {
  $query = "UPDATE punches SET outtime = :p_punchtime, notes = :p_notes, modified = :p_modified WHERE id = :p_punchid";
  $stmt = $yaptc_db->prepare($query);
  $stmt->execute(array(
        ':p_punchid'    => $punchid,
        ':p_notes'    => $p_notes,
        ':p_punchtime'    => $p_punchtime,
        ':p_modified'    => $p_modified,
    ));
  } else {
  $query = "INSERT INTO punches (userid, notes, intime, modified) VALUES (:p_userid, :p_notes, :p_punchtime, :p_modified)";
  $stmt = $yaptc_db->prepare($query);
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
endif;




echo "<h2 class=\"content-subhead\">Punch History</h2>";
echo "<p>Below is your full punch history, sorted newest to oldest.</p>";
?>

<table class="pure-table">
<thead><tr><th>In</th><th>Out</th><th>Name</th><th>Hours</th><th>Flagged</th><th>Notes</th></tr></thead>
        <tbody><?php foreach (listPunches($db, $session_user["0"]["userid"]) as $row): ?>
        <tr><td><?php echo $row['intime']; ?></td><td><?php echo $row['outtime']; ?></td><td><?php echo $row['lastname'] . ", " . $row['firstname']; ?></td><td><?php echo $row['punchhours']; ?></td><td><?php echo $row['modified']; ?></td><td><?php echo $row['notes']; ?></td></tr><?php endforeach; ?>
        </tbody>
        </table>




<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
