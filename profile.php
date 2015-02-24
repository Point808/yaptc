<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = "Profile";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
<!-- ********** BEGIN CONTENT ********** -->

<?php
if (!empty($_POST)):
    if (empty($_POST['password']) && empty($_POST['newpassword2'])):
        setUserInfo($db, $session_user["0"]["userid"], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['usertypeid'], $session_user["0"]["password"]);
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
setUserInfo($db, $session_user["0"]["userid"], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['usertypeid'], $password);
        header('Location: ' . $_SERVER['PHP_SELF']);
endif;
endif;
?>


<h2 class="content-subhead">Profile Information</h2>
<p>You may make changes to your user profile below if you wish. Updates will take effect immediately on pressing "Save".</p>
<p>To change your password, enter a new password twice below and press save.  Password minimum length is <?php echo $yaptc_min_password; ?></p>
<form class="pure-form pure-form-stacked" action="profile.php" method="post">
<fieldset id="update">
<div class="pure-g">
<div class="pure-u-1 pure-u-md-1-3">
<label for="username">Username</label>
<input type="text" name="username" maxlength="50" value="<?php echo $session_user["0"]["username"]; ?>" readonly>
<label for="created">Created</label>
<input type="text" name="created" value="<?php echo $session_user["0"]["created"]; ?>" readonly>
<label for="usertype">User Type</label>
<input type="text" name="usertype" maxlength="50" value="<?php echo $session_user["0"]["usertype"]; ?>" readonly>
<input type="hidden" name="usertypeid" maxlength="50" value="<?php echo $session_user["0"]["usertypeid"]; ?>" readonly>
</div>
<div class="pure-u-1 pure-u-md-1-3">
<label for="firstname">First Name</label>
<input type="text" name="firstname" maxlength="50" value="<?php echo $session_user["0"]["firstname"]; ?>">
<label for="lastname">Last Name</label>
<input type="text" name="lastname" maxlength="50" value="<?php echo $session_user["0"]["lastname"]; ?>">
<label for="email">Email</label>
<input type="text" name="email" maxlength="100" value="<?php echo $session_user["0"]["email"]; ?>">
</div>
<div class="pure-u-1 pure-u-md-1-3">
<label for="password">New Password</label>
<input type="password" name="password">
<label for="newpassword2">Confirm Password</label>
<input type="password" name="newpassword2">
</div>
<div class="pure-controls pure-u-1">
<button type="submit" class="pure-input-1 pure-button button-success ">Save</button>
</div>
</div>
</fieldset>
</form>




<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
