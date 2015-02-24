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

<?php
$session_punch = listPunches($db, $session_user["0"]["userid"], 1);
if (!isset($session_punch['0']['intime'])):
    $session_status = lang('OUT');
    $session_message = lang('PUNCH_STATUS') . ": " . lang('NO_PUNCHES');
else:
    if (!empty($session_punch['0']['outtime'])):
        $session_status = lang('OUT');
        $statustime = $session_punch['0']['outtime'];
        $session_message = lang('PUNCH_STATUS') . ": " . $session_status . " " . lang('SINCE') . " " . date('g:i a \o\n M jS, Y', strtotime($statustime));
    else:
        $session_status = lang('IN');
        $statustime = $session_punch['0']['intime'];
        $punchid = $session_punch['0']['punchid'];
        $notes = $session_punch['0']['notes'];
        $session_message = lang('PUNCH_STATUS') . ": " . $session_status . " " . lang('SINCE') . " " . date('g:i a \o\n M jS, Y', strtotime($statustime));
    endif;
endif;
?>








<h2 class="content-subhead"><?php echo lang('QUICK_PUNCH'); ?></h2>
<p><?php echo $session_message; ?></p>
<p><?php echo lang('QUICK_PUNCH_PARAGRAPH'); ?></p>
<form class="pure-form pure-form-stacked" action="index.php" method="post">
<fieldset id="punch">
<input type="text" name="notes" placeholder="Enter notes if needed" maxlength="255" value="<?php if (isset($notes)): echo $notes; endif; ?>">
<?php if ($session_status == lang('IN')): ?>
    <button type="submit" class="pure-button button-success pure-button-disabled">Punch IN</button>
    <button type="submit" class="pure-button button-error">Punch OUT</button>
<?php elseif ($session_status == lang('OUT')): ?>
    <button type="submit" class="pure-button button-success">Punch IN</button>
    <button type="submit" class="pure-button button-error pure-button-disabled">Punch OUT</button>
<?php endif; ?>
</fieldset>
</form>

<?php




$punchtime = date('Y-m-d H:i:s');
if (!empty($_POST)):
    if (!empty($_POST['notes'])): $notes = $_POST['notes']; else: $notes = NULL; endif;
    if ($session_status == lang('IN')): punchOut($yaptc_db, $punchid, $notes, $punchtime, NULL);
        elseif ($session_status == lang('OUT')): punchIn($yaptc_db, $_SESSION['user_id'], $notes, $punchtime, NULL);
        endif;
    header('Location: ' . $_SERVER['PHP_SELF']);
    endif;



?>



<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
