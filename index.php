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

<?php $punchStatus = getPunchStatus($yaptc_db, $_SESSION['user_id']); ?>
<h2 class="content-subhead">Current Status</h2>
<?php if (!isset($punchStatus['0'])): $status = "Out"; ?>
    <p>You do not appear to have any punches on record.</p>
<?php else:
    if (!empty($punchStatus['3'])): $status = "Out"; $statustime = $punchStatus['3'];
        else: $status = "In"; $statustime = $punchStatus['2']; $punchid = $punchStatus['0']; $notes = $punchStatus['4'];
    endif; ?>
<p>You have been Punched <?php echo $status; ?> since <?php echo date('g:i a \o\n M jS, Y', strtotime($statustime)); ?>.</p>
<?php endif; ?>

<h2 class="content-subhead">Quick Punch</h2>
<p>Clicking the button below will immediately enter a new punch for you depending on your current status.  Any notes you enter will be attached to the punch for your administrator to review.</p>
<form class="pure-form pure-form-stacked" action="index.php" method="post">
<fieldset>
<input class="pure-input-1" type="text" name="notes" placeholder="Enter notes if needed" maxlength="255" value="<?php if (isset($notes)): echo $notes; endif; ?>">
<div class="pure-controls">
<?php if ($status == "In"): ?>
    <button type="submit" class="pure-button button-xlarge button-success pure-button-disabled">Punch IN</button>
    <button type="submit" class="pure-button button-xlarge button-error">Punch OUT</button>
<?php elseif ($status == "Out"): ?>
    <button type="submit" class="pure-button button-xlarge button-success">Punch IN</button>
    <button type="submit" class="pure-button button-xlarge button-error pure-button-disabled">Punch OUT</button>
<?php endif; ?>
</div>
</fieldset>
</form>

<?php
if (!empty($_POST)):
    if (isset($_POST['notes'])):
        if (!empty($_POST['notes'])): $notes = $_POST['notes'];
            else: $notes = NULL;
            endif;
        else: $notes = NULL;
        endif;
    if ($status == "In"): punchOut($yaptc_db, $punchid, $notes);
        elseif ($status == "Out"): punchIn($yaptc_db, $_SESSION['user_id'], $notes);
        endif;
    header('Location: ' . $_SERVER['PHP_SELF']);
    endif; ?>

<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
