<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = lang('ACCOUNT');
$yaptc_pageicon = '<i class="fa fa-cog"></i> ';
require_once($yaptc_inc . "header.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
                    <!-- ********** BEGIN CONTENT ********** -->
<?php
if (isset($_POST['saveprofile'])):
    if (empty($_POST['password']) && empty($_POST['newpassword2'])):
        setUserInfo($yaptc_db, $session_user["0"]["userid"], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $session_user["0"]["usertypeid"], $session_user["0"]["password"]);
        header('Location: ' . $_SERVER['PHP_SELF']);
elseif (strlen($_POST['password']) < $yaptc_min_password):
echo "Password must be at least $yaptc_min_password characters.";
elseif (!empty($_POST['password']) && empty($_POST['newpassword2'])):
echo "Please confirm password if you wish to change it";
elseif ($_POST['password'] != $_POST['newpassword2']):
echo "New passwords do not match";
elseif (!empty($_POST['password']) && ($_POST['password'] = $_POST['newpassword2'])):
// change pw
require_once($yaptc_lib . "phpass-0.3/PasswordHash.php");
    $hasher = new PasswordHash(8, FALSE);
    $password = $hasher->HashPassword($_POST['password']);
setUserInfo($yaptc_db, $session_user["0"]["userid"], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $session_user["0"]["usertypeid"], $password);
        header('Location: ' . $_SERVER['PHP_SELF']);
endif;
endif;

// Set up pagination
$page_num = 1;
if(!empty($_GET['pnum'])):
    $page_num = filter_input(INPUT_GET, 'pnum', FILTER_VALIDATE_INT);
    if(false === $page_num):
        $page_num = 1;
    endif;
endif;
$offset = ($page_num - 1) * $yaptc_rowsperpage;
$row_count = count(listPunches($yaptc_db, $session_user["0"]["userid"]));
$page_count = 0;
if (0 === $row_count): else: $page_count = (int)ceil($row_count / $yaptc_rowsperpage); if($page_num > $page_count): $page_num = 1; endif; endif;
?>


    <!-- Begin page content -->
    <div class="container">
      <div class="page-header">
        <h2><i class="glyphicon glyphicon-edit"></i> <?php echo lang('EDIT_PROFILE'); ?></h2>
      </div>
      <p class="lead"><?php echo lang('ACCOUNT_INFO_DESC') . $yaptc_min_password; ?></p>


                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <fieldset>
                            <div class="form-group row">
                                    <label for="username" class="col-sm-2 col-form-label"><?php echo lang('USERNAME'); ?></label>
                                    <div class="col-sm-2"><input class="form-control" type="text" name="username" id="username" maxlength="50" value="<?php echo $session_user["0"]["username"]; ?>" readonly /></div>
                                    <label for="created" class="col-sm-2 col-form-label"><?php echo lang('CREATED'); ?></label>
                                    <div class="col-sm-2"><input class="form-control" type="text" name="created" id="created" value="<?php echo $session_user["0"]["created"]; ?>" readonly /></div>
                                    <label for="usertype" class="col-sm-2 col-form-label"><?php echo lang('USERTYPE'); ?></label>
                                    <div class="col-sm-2"><input class="form-control" type="text" name="usertype" id="usertype" maxlength="50" value="<?php echo $session_user["0"]["usertype"]; ?>" readonly /></div>
                                </div>
                            <div class="form-group row">
                                    <label for="firstname" class="col-sm-2 col-form-label"><?php echo lang('FIRSTNAME'); ?></label>
                                    <div class="col-sm-2"><input class="form-control" type="text" name="firstname" id="firstname" maxlength="50" value="<?php echo $session_user["0"]["firstname"]; ?>" /></div>
                                    <label for="lastname" class="col-sm-2 col-form-label"><?php echo lang('LASTNAME'); ?></label>
                                    <div class="col-sm-2"><input class="form-control" type="text" name="lastname" id="lastname" maxlength="50" value="<?php echo $session_user["0"]["lastname"]; ?>" /></div>
                                    <label for="email" class="col-sm-2 col-form-label"><?php echo lang('EMAIL'); ?></label>
                                    <div class="col-sm-2"><input class="form-control" type="text" name="email" id="email" maxlength="100" value="<?php echo $session_user["0"]["email"]; ?>" /></div>
                                </div>
                            <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label"><?php echo lang('NEW') . " " . lang('PASSWORD'); ?></label>
                                    <div class="col-sm-2"><input class="form-control" type="password" name="password" id="password" /></div>
                                    <label for="newpassword2" class="col-sm-2 col-form-label"><?php echo lang('CONFIRM') . " " . lang('NEW') . " " . lang('PASSWORD'); ?></label>
                                    <div class="col-sm-2"><input class="form-control" type="password" name="newpassword2" id="newpassword2" /></div>
                                    <div class="col-sm-4"><button type="submit" name="saveprofile" id="saveprofile" class="form-control btn btn-block btn-primary"><?php echo lang('SAVE'); ?></button></div>
                                </div>

                        </fieldset>
                    </form>
</div>


    <div class="container">
      <div class="page-header">
        <h2><i class="glyphicon glyphicon-folder-open"></i> <?php echo lang('PUNCH_HISTORY_HEADER'); ?></h2>
      </div>
      <p class="lead"><?php echo lang('PUNCH_HISTORY_DESC'); ?></p>

                    <table class="table table-striped">
                        <thead>
                            <tr><th colspan="4"><?php echo lang('PAGE') . ": "; for ($i = 1; $i <= $page_count; $i++): if ($i === $page_num): echo $i . ' '; else: echo '<a href="' . $_SERVER['PHP_SELF'] . '?pnum=' . $i . '">' . $i . '</a> '; endif; endfor; ?></th></tr>
                            <tr><th><?php echo lang('IN') . " / " . lang('OUT'); ?></th><th><?php echo lang('HOURS'); ?></th><th><?php echo lang('FLAG'); ?></th><th><?php echo lang('NOTES'); ?></th></tr>
                        </thead>
                        <tbody>
<?php foreach (listPunches($yaptc_db, $session_user["0"]["userid"], $yaptc_rowsperpage, $offset) as $row): ?>
                            <tr>
                                <td><?php echo $row['intime'] . " / " . $row['outtime']; ?></td><td><?php echo $row['punchhours']; ?></td><td><?php echo $row['modified']; ?></td><td><?php echo $row['notes']; ?></td>
                            </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>

</div>
                    <!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
