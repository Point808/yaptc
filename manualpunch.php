<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = lang('PUNCH_EDITOR');
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
                    <!-- ********** BEGIN CONTENT ********** -->

<?php if($session_user["0"]["usertype"] !== "Administrator"): header("Location: index.php"); ?>
                    <h2 class="content-subhead"><?php echo lang('NOT_AUTHORIZED'); ?></h2>
<?php endif; ?>





<?php
if (!empty($_POST['punchuser'])) {
if (isset($_POST['notes'])) { if (!empty($_POST['notes'])) { $notes = $_POST['notes']; } else { $notes = NULL; } } else { $notes = NULL; }
if (isset($_POST['punchtime'])) {
// this needs work to check existing modified flag!!! i.e. if already set to 1, leave as 1!!!
  if (!empty($_POST['punchtime'])) { $punchtime = $_POST['punchtime']; $modified = "1"; } else { $punchtime = $timenow; }
} else { $punchtime = $timenow; }

// Is the user currently punched in?  If so, insert the punch out record, otherwise, insert a new punch in
if ($_POST['status']=="In") {
  punchOut($yaptc_db, $_POST['punchid'], $notes, $punchtime, $modified);
  } else {
  punchIn($yaptc_db, $_POST['userid'], $notes, $punchtime, $modified);
  }

header('Location: '.$_SERVER['PHP_SELF']);
}






?>



<?php 
if (!empty($_POST['editpunch'])) {
editPunch($db, $_POST['editpunch'], $_POST[$_POST['editpunch'] . "-intime"], $_POST[$_POST['editpunch'] . "-outtime"], $_POST[$_POST['editpunch'] . "-notes"]);
}
if (!empty($_POST['deletepunch'])) {
deletePunch($db, $_POST['deletepunch']);
}

// Set up pagination
$page_num = 1;
if(!empty($_GET['pnum'])):
    $page_num = filter_input(INPUT_GET, 'pnum', FILTER_VALIDATE_INT);
    if(false === $page_num):
        $page_num = 1;
    endif;
endif;
$offset = ($page_num - 1) * $rowsperpage;
$row_count = count(listPunches($db, "%"));
$page_count = 0;
if (0 === $row_count): else: $page_count = (int)ceil($row_count / $rowsperpage); if($page_num > $page_count): $page_num = 1; endif; endif;
?>



                    <h2 class="content-subhead"><?php echo lang('EDIT_PUNCH_HEADER'); ?></h2>
                    <p><?php echo lang('EDIT_PUNCH_DESC'); ?></p>
                    <form method="post" onsubmit="return confirm('<?php echo lang('SAVE_PUNCH_WARNING'); ?>')">
                        <table class="pure-table pure-table-striped">
                            <thead>
                                <tr><th colspan="6"><?php echo lang('PAGE') . ": "; for ($i = 1; $i <= $page_count; $i++): if ($i === $page_num): echo $i . ' '; else: echo '<a href="' . $_SERVER['PHP_SELF'] . '?pnum=' . $i . '">' . $i . '</a> '; endif; endfor; ?></th></tr>
                                <tr><th><?php echo lang('IN') . "/" . lang('OUT'); ?></th><th><?php echo lang('NAME'); ?></th><th><?php echo lang('HOURS'); ?></th><th><?php echo lang('FLAG'); ?></th><th><?php echo lang('NOTES'); ?></th><th><?php echo lang('ACTIONS'); ?></th></tr>
                            </thead>
                            <tbody>
<?php foreach (listPunches($db, "%", $rowsperpage, $offset) as $row): ?>
                                <tr>
                                    <td><input type="text" name="<?php echo $row['punchid']; ?>-intime" value="<?php echo $row['intime']; ?>" /><input type="text" name="<?php echo $row['punchid']; ?>-outtime" value="<?php echo $row['outtime']; ?>" /></td><td><?php echo $row['lastname'] . ", " . $row['firstname']; ?></td><td><?php echo $row['punchhours']; ?></td><td><?php echo $row['modified']; ?></td><td><input type="text" name="<?php echo $row['punchid']; ?>-notes" value="<?php echo $row['notes']; ?>" /></td><td><button type="submit" name="editpunch" value="<?php echo $row['punchid']; ?>" class="pure-button button-success"><?php echo lang('SAVE'); ?></button><button type="submit" name="deletepunch" value="<?php echo $row['punchid']; ?>" class="pure-button button-error"><?php echo lang('DELETE'); ?></button></td>

                                </tr>
<?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>

                    <!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
