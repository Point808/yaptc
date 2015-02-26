<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = lang('ACCOUNT');
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
                    <!-- ********** BEGIN CONTENT ********** -->
<?php
if (isset($_POST['saveprofile'])):
    if (empty($_POST['password']) && empty($_POST['newpassword2'])):
        setUserInfo($db, $session_user["0"]["userid"], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $session_user["0"]["usertypeid"], $session_user["0"]["password"]);
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
setUserInfo($db, $session_user["0"]["userid"], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $session_user["0"]["usertypeid"], $password);
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
$offset = ($page_num - 1) * $rowsperpage;
$row_count = count(listPunches($db, $session_user["0"]["userid"]));
$page_count = 0;
if (0 === $row_count): else: $page_count = (int)ceil($row_count / $rowsperpage); if($page_num > $page_count): $page_num = 1; endif; endif;
?>

                    <h2 class="content-subhead"><?php echo lang('ACCOUNT_INFO_HEADER'); ?></h2>
                    <p><?php echo lang('ACCOUNT_INFO_DESC') . $yaptc_min_password; ?></p>
                    <form class="pure-form pure-form-stacked" action="profile.php" method="post">
                        <fieldset>
                            <div class="pure-g">
                                <div class="pure-u-1 pure-u-md-1-3">
                                    <label for="username"><?php echo lang('USERNAME'); ?></label>
                                    <input type="text" name="username" id="username" maxlength="50" value="<?php echo $session_user["0"]["username"]; ?>" readonly />
                                    <label for="created"><?php echo lang('CREATED'); ?></label>
                                    <input type="text" name="created" id="created" value="<?php echo $session_user["0"]["created"]; ?>" readonly />
                                    <label for="usertype"><?php echo lang('USERTYPE'); ?></label>
                                    <input type="text" name="usertype" id="usertype" maxlength="50" value="<?php echo $session_user["0"]["usertype"]; ?>" readonly />
                                </div>
                                <div class="pure-u-1 pure-u-md-1-3">
                                    <label for="firstname"><?php echo lang('FIRSTNAME'); ?></label>
                                    <input type="text" name="firstname" id="firstname" maxlength="50" value="<?php echo $session_user["0"]["firstname"]; ?>" />
                                    <label for="lastname"><?php echo lang('LASTNAME'); ?></label>
                                    <input type="text" name="lastname" id="lastname" maxlength="50" value="<?php echo $session_user["0"]["lastname"]; ?>" />
                                    <label for="email"><?php echo lang('EMAIL'); ?></label>
                                    <input type="text" name="email" id="email" maxlength="100" value="<?php echo $session_user["0"]["email"]; ?>" />
                                </div>
                                <div class="pure-u-1 pure-u-md-1-3">
                                    <label for="password"><?php echo lang('NEW') . " " . lang('PASSWORD'); ?></label>
                                    <input type="password" name="password" id="password" />
                                    <label for="newpassword2"><?php echo lang('CONFIRM') . " " . lang('NEW') . " " . lang('PASSWORD'); ?></label>
                                    <input type="password" name="newpassword2" id="newpassword2" />
                                </div>
                                <div class="pure-controls pure-u-1">
                                    <button type="submit" name="saveprofile" id="saveprofile" class="pure-input-1 pure-button button-success "><?php echo lang('SAVE'); ?></button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                    <h2 class="content-subhead"><?php echo lang('PUNCH_HISTORY_HEADER'); ?></h2>
                    <p><?php echo lang('PUNCH_HISTORY_DESC'); ?></p>



                    <table class="pure-table pure-table-striped">
                        <thead>
                            <tr><th colspan="4"><?php echo lang('PAGE') . ": "; for ($i = 1; $i <= $page_count; $i++): if ($i === $page_num): echo $i . ' '; else: echo '<a href="' . $_SERVER['PHP_SELF'] . '?pnum=' . $i . '">' . $i . '</a> '; endif; endfor; ?></th></tr>
                            <tr><th><?php echo lang('IN') . " / " . lang('OUT'); ?></th><th><?php echo lang('HOURS'); ?></th><th><?php echo lang('FLAG'); ?></th><th><?php echo lang('NOTES'); ?></th></tr>
                        </thead>
                        <tbody>
<?php foreach (listPunches($db, $session_user["0"]["userid"], $rowsperpage, $offset) as $row): ?>
                            <tr>
                                <td><?php echo $row['intime'] . " / " . $row['outtime']; ?></td><td><?php echo $row['punchhours']; ?></td><td><?php echo $row['modified']; ?></td><td><?php echo $row['notes']; ?></td>
                            </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
