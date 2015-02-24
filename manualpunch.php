<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = "Manual Punch";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
<!-- ********** BEGIN CONTENT ********** -->


<?php
$timenow = date('Y-m-d H:i');
if (!empty($_POST)) {


if (isset($_POST['notes'])) { if (!empty($_POST['notes'])) { $notes = $_POST['notes']; } else { $notes = NULL; } } else { $notes = NULL; }
if (isset($_POST['punchtime'])) {
// this needs work to check existing modified flag!!! i.e. if already set to 1, leave as 1!!!
  if (!empty($_POST['punchtime'])) { $punchtime = $_POST['punchtime'] . ':00'; $modified = "1"; } else { $punchtime = $timenow . ':00'; }
} else { $punchtime = $timenow . ':00'; }

// Is the user currently punched in?  If so, insert the punch out record, otherwise, insert a new punch in
if ($_POST['status']=="In") {
  punchOut($yaptc_db, $_POST['punchid'], $notes, $punchtime, $modified);
  } else {
  punchIn($yaptc_db, $_POST['userid'], $notes, $punchtime, $modified);
  }

header('Location: '.$_SERVER['PHP_SELF']);
}






if ($session_user["0"]["usertype"] == "Administrator"): ?>
<h2 class="content-subhead">User Status</h2>
<p>Below is the current state of all users.  You may enter punches for them using the buttons, or edit existing punches in the next section.</p>

<table class="pure-table">
<thead>
<tr>
<th>Name</th>
<th>Status</th>
<th>Notes</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<tr>
<?php

foreach (getUserInfo($db, "%") as $row) {
echo "<td>" . $row['lastname'] . ", " . $row['firstname'] . "</td>";

$user_punch = listPunches($db, $row['userid'], 1);
if (!$user_punch): $status = "Out"; $statustime = "No Punches"; $notes="";
elseif (!empty($user_punch['0']['outtime'])): $status = "Out"; $statustime = date('g:i a \o\n M jS, Y', strtotime($user_punch['0']['outtime'])); $punchid = ""; $notes="";
else: $status = "In"; $statustime = date('g:i a \o\n M jS, Y', strtotime($user_punch['0']['intime'])); $punchid = $user_punch['0']['punchid']; if (!empty($user_punch['0']['notes'])): $notes = $user_punch['0']['notes']; else: $notes=""; endif;
endif;

echo "<td>" . $status . " since " . $statustime . "</td>"; ?>
<form method="post" onsubmit="return confirm('Are you sure you want to punch this user NOW?')">
<td><input type="text" name="notes" placeholder="<?php echo $notes; ?>"></td>
<td>
<input type="hidden" name="_METHOD" value="PUNCH">
<input type="hidden" name="userid" value="<?php echo $row['userid']; ?>">
<input type="hidden" name="punchid" value="<?php echo $punchid; ?>">
<input type="hidden" name="status" value="<?php echo $status; ?>">
<input type="text" name="punchtime" placeholder="<?php echo $timenow; ?>" maxlength="20">
<?php if ($status == "In"): ?>
    <button type="submit" name="punchuser" value="punchuser" class="pure-button button-error">Punch OUT</button>
<?php elseif ($status == "Out"): ?>
    <button type="submit" name="punchuser" value="punchuser" class="pure-button button-success">Punch IN</button>
<?php endif; ?>
</form>


</td>
</tr>
<?php } ?>
</tbody>
</table>

<?php else: ?>
<h2 class="content-subhead">NOT AUTHORIZED!</h2>
<?php endif; ?>


<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
