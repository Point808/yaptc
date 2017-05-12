<?php
session_start();
if(file_exists("config.inc.php")){
  require_once("config.inc.php");
}else{
echo "Configuration file not found - please complete setup before continuing.";
exit;
}
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = lang('HOME');
$yaptc_pageicon = '<i class="fa fa-home"></i>';
require_once($yaptc_inc . "header.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
                    <!-- ********** BEGIN CONTENT ********** -->


<?php
// Get punch status for buttons and times
$session_punch = listPunches($yaptc_db, $session_user["0"]["userid"], 1);
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
    if ($session_status == lang('IN')): punchOut($yaptc_db, $punchid, $notes, $timenow, NULL); $session_status = lang('OUT'); $session_message = lang('PUNCH_STATUS') . ": " . $session_status . " " . lang('SINCE') . " " . date('g:i a \o\n M jS, Y', strtotime($statustime)); $notes = NULL;
        elseif ($session_status == lang('OUT')): punchIn($yaptc_db, $_SESSION['user_id'], $notes, $timenow, NULL); $session_status = lang('IN'); $session_message = lang('PUNCH_STATUS') . ": " . $session_status . " " . lang('SINCE') . " " . date('g:i a \o\n M jS, Y', strtotime($statustime));
        endif;
    header('Location: ' . $_SERVER['PHP_SELF']);
    endif;

// Process posted data from advancedpunch section
if (isset($_POST['advancedpunch'])):
    if (!empty($_POST['notes'])): $notes = $_POST['notes']; else: $notes = NULL; endif;
    if (!empty($_POST['punchtime'])): $punchtime = $_POST['punchtime']; else: $punchtime = $timenow; endif;
    if ($session_status == lang('IN')): punchOut($yaptc_db, $punchid, $notes, $timenow, NULL); $session_status = lang('OUT'); $session_message = lang('PUNCH_STATUS') . ": " . $session_status . " " . lang('SINCE') . " " . date('g:i a \o\n M jS, Y', strtotime($statustime)); $notes = NULL;
        elseif ($session_status == lang('OUT')): punchIn($yaptc_db, $_SESSION['user_id'], $notes, $punchtime, NULL); $session_status = lang('IN'); $session_message = lang('PUNCH_STATUS') . ": " . $session_status . " " . lang('SINCE') . " " . date('g:i a \o\n M jS, Y', strtotime($statustime));
        endif;
    header('Location: ' . $_SERVER['PHP_SELF']);
    endif;
?>

// HTML
  <!-- Begin page content -->
    <div class="container">
      <div class="page-header">
        <h2><?php echo $yaptc_pageicon; echo $yaptc_pagename; ?></h2>
      </div>
      <p class="lead"><?php if (isset($_SESSION['user_id'])): echo lang('USER') . ": " . $session_user["0"]["firstname"] . ' ' . $session_user["0"]["lastname"]; else: echo lang('PLEASE_LOG_IN'); endif; ?></p>
      <p class="lead"><?php echo $session_message; ?></p>
    </div>


<?php
// HTML section for quick punch only
if ($yaptc_allowuseradvancedpunch == "no"): ?>
  <!-- Begin page content -->
    <div class="container">
      <div class="page-header">
        <h2><i class="glyphicon glyphicon-time"></i> <?php echo lang('QUICK_PUNCH'); ?></h2>
      </div>
      <p class="lead"><?php echo lang('QUICK_PUNCH_PARAGRAPH'); ?></p>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <fieldset id="quickpunch">
                            <div class="form-group row">
<div class="col-sm-8">
                            <input class="form-control" type="text" name="notes" placeholder="<?php echo lang('NOTES_PLACEHOLDER'); ?>" maxlength="255" value="<?php if (isset($notes)): echo $notes; endif; ?>" />
</div>
<div class="col-sm-4">
                            <button type="submit" class="form-control btn btn-block <?php if ($session_status == lang('IN')): echo "btn-danger"; elseif ($session_status == lang('OUT')): echo "btn-success"; endif;?>" name="quickpunch"><?php if ($session_status == lang('IN')): echo '<i class="glyphicon glyphicon-stop"></i> ' . lang('PUNCH') . ' ' . lang('OUT'); elseif ($session_status == lang('OUT')): echo '<i class="glyphicon glyphicon-play"></i> ' . lang('PUNCH') . ' ' . lang('IN'); endif;?></button>
</div>
</div>
                        </fieldset>
                    </form>
    </div>
    </div>


<?php
// HTML section for advanced punch only
elseif ($yaptc_allowuseradvancedpunch == "yes"): ?>
  <!-- Begin page content -->
    <div class="container">
      <div class="page-header">
        <h2><i class="glyphicon glyphicon-time"></i> <?php echo lang('ADVANCED_PUNCH'); ?></h2>
      </div>
      <p class="lead"><?php echo lang('ADVANCED_PUNCH_PARAGRAPH'); ?></p>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <fieldset id="advancedpunch">
                            <div class="form-group row">
<div class="col-sm-3">
                            <input class="form-control" type="text" name="punchtime" placeholder="<?php echo $timenow; ?>" />
</div>
<div class="col-sm-6">
                            <input class="form-control" type="text" name="notes" placeholder="<?php echo lang('NOTES_PLACEHOLDER'); ?>" maxlength="255" value="<?php if (isset($notes)): echo $notes; endif; ?>" />
</div>
<div class="col-sm-3">
                            <button type="submit" class="form-control btn btn-block <?php if ($session_status == lang('IN')): echo "btn-danger"; elseif ($session_status == lang('OUT')): echo "btn-success"; endif;?>" name="advancedpunch"><?php if ($session_status == lang('IN')): echo '<i class="glyphicon glyphicon-stop"></i> ' . lang('PUNCH') . ' ' . lang('OUT'); elseif ($session_status == lang('OUT')): echo '<i class="glyphicon glyphicon-play"></i> ' . lang('PUNCH') . ' ' . lang('IN'); endif;?></button>
</div>
</div>
                        </fieldset>
                    </form>
</div>
</div>

<?php endif; ?>
                    <!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
