<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = "Home";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
<!-- ********** BEGIN CONTENT ********** -->



<h2 class="content-subhead">Current Status</h2>
<?php
$timenow = date('Y-m-d H:i');
$session_punch = listPunches($db, $session_user["0"]["userid"], 1);
if (!isset($session_punch['0']['intime'])): $status = "Out"; ?>
    <p>You do not appear to have any punches on record.</p>
<?php else:
    if (!empty($session_punch['0']['outtime'])): $status = "Out"; $statustime = $session_punch['0']['outtime'];
        else: $status = "In"; $statustime = $session_punch['0']['intime']; $punchid = $session_punch['0']['punchid']; $notes = $session_punch['0']['notes'];
    endif; ?>
<p>You have been Punched <?php echo $status; ?> since <?php echo date('g:i a \o\n M jS, Y', strtotime($statustime)); ?>.</p>
<?php endif; ?>

<h2 class="content-subhead">Quick Punch</h2>
<p>Clicking the button below will immediately enter a new punch for you depending on your current status.  Any notes you enter will be attached to the punch for your administrator to review.</p>
<form class="pure-form pure-form-stacked" action="index.php" method="post">
<fieldset id="punch">
<input type="text" name="notes" placeholder="Enter notes if needed" maxlength="255" value="<?php if (isset($notes)): echo $notes; endif; ?>">
<?php if ($status == "In"): ?>
    <button type="submit" class="pure-button button-success pure-button-disabled">Punch IN</button>
    <button type="submit" class="pure-button button-error">Punch OUT</button>
<?php elseif ($status == "Out"): ?>
    <button type="submit" class="pure-button button-success">Punch IN</button>
    <button type="submit" class="pure-button button-error pure-button-disabled">Punch OUT</button>
<?php endif; ?>
</fieldset>
</form>

<?php




$punchtime = date('Y-m-d H:i:s');
if (!empty($_POST)):
    if (!empty($_POST['notes'])): $notes = $_POST['notes']; else: $notes = NULL; endif;
    if ($status == "In"): punchOut($yaptc_db, $punchid, $notes, $punchtime, NULL);
        elseif ($status == "Out"): punchIn($yaptc_db, $_SESSION['user_id'], $notes, $punchtime, NULL);
        endif;
    header('Location: ' . $_SERVER['PHP_SELF']);
    endif;



?>



<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
