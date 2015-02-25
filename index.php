<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = lang('HOME');
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
                    <!-- ********** BEGIN CONTENT ********** -->
<?php
// Get punch status for buttons and times
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

// Process posted data from quickpunch section
if (isset($_POST['quickpunch'])):
    if (!empty($_POST['notes'])): $notes = $_POST['notes']; else: $notes = NULL; endif;
    if ($session_status == lang('IN')): punchOut($yaptc_db, $punchid, $notes, $timenow, NULL);
        elseif ($session_status == lang('OUT')): punchIn($yaptc_db, $_SESSION['user_id'], $notes, $timenow, NULL);
        endif;
    header('Location: ' . $_SERVER['PHP_SELF']);
    endif;

// Process posted data from advancedpunch section
if (isset($_POST['advancedpunch'])):
    if (!empty($_POST['notes'])): $notes = $_POST['notes']; else: $notes = NULL; endif;
    if (!empty($_POST['punchtime'])): $punchtime = $_POST['punchtime']; else: $punchtime = $timenow; endif;
    if ($session_status == lang('IN')): punchOut($yaptc_db, $punchid, $notes, $timenow, NULL);
        elseif ($session_status == lang('OUT')): punchIn($yaptc_db, $_SESSION['user_id'], $notes, $punchtime, NULL);
        endif;
    header('Location: ' . $_SERVER['PHP_SELF']);
    endif;

// HTML section for quick punch only
if ($yaptc_allowuseradvancedpunch == "no"): ?>
                    <h2 class="content-subhead"><?php echo lang('QUICK_PUNCH'); ?></h2>
                    <p><?php echo $session_message; ?></p>
                    <p><?php echo lang('QUICK_PUNCH_PARAGRAPH'); ?></p>
                    <form class="pure-form pure-form-stacked" action="index.php" method="post">
                        <fieldset>
                            <input type="text" name="notes" placeholder="<?php echo lang('NOTES_PLACEHOLDER'); ?>" maxlength="255" value="<?php if (isset($notes)): echo $notes; endif; ?>" />
                            <button type="submit" class="pure-button <?php echo lang('PUNCH') . " "; if ($session_status == lang('IN')): echo "button-error"; elseif ($session_status == lang('OUT')): echo "button-success"; endif;?>" name="quickpunch"><?php echo lang('PUNCH') . " "; if ($session_status == lang('IN')): echo lang('OUT'); elseif ($session_status == lang('OUT')): echo lang('IN'); endif;?></button>
                        </fieldset>
                    </form>

<?php
// HTML section for advanced punch only
elseif ($yaptc_allowuseradvancedpunch == "yes"): ?>
                    <h2 class="content-subhead"><?php echo lang('ADVANCED_PUNCH'); ?></h2>
                    <p><?php echo $session_message; ?></p>
                    <p><?php echo lang('ADVANCED_PUNCH_PARAGRAPH'); ?></p>
                    <form class="pure-form pure-form-stacked" action="index.php" method="post">
                        <fieldset>
                            <input type="text" name="punchtime" placeholder="<?php echo $timenow; ?>" />
                            <input type="text" name="notes" placeholder="<?php echo lang('NOTES_PLACEHOLDER'); ?>" maxlength="255" value="<?php if (isset($notes)): echo $notes; endif; ?>" />
                            <button type="submit" class="pure-button <?php echo lang('PUNCH') . " "; if ($session_status == lang('IN')): echo "button-error"; elseif ($session_status == lang('OUT')): echo "button-success"; endif;?>" name="advancedpunch"><?php echo lang('PUNCH') . " "; if ($session_status == lang('IN')): echo lang('OUT'); elseif ($session_status == lang('OUT')): echo lang('IN'); endif;?></button>
                        </fieldset>
                    </form>

<?php endif; ?>
                    <!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
