<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = "Profile";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else:
//********** BEGIN CONTENT **********// ?>

<?php
require_once($yaptc_lib . "phpass-0.3/PasswordHash.php");
$query = "SELECT users.id, users.password, users.created, users.username, users.firstname, users.lastname, users.email, usertypes.typename AS usertype FROM users, usertypes WHERE users.id = :id";
  $stmt = $yaptc_db->prepare($query);
  $stmt->execute(array(':id' => $_SESSION['user_id']));
  $user = $stmt->fetchObject();
?>

<?php if (isset($errors['update'])): ?>
        <p class="error"><?php echo $errors['update']; ?></p>
        <?php endif; ?>

<h2 class="content-subhead">Profile Information</h2>
<p>You may make changes to your user profile below if you wish. Updates will take effect immediately on pressing "Save".</p>
<p>PASSWORD CHANGE IS NOT CURRENTLY IMPLEMENTED</p>
<form class="pure-form pure-form-stacked" action="profile.php" method="post">
<fieldset id="update">
<div class="pure-g">
<div class="pure-u-1 pure-u-md-1-3">
<label for="username">Username</label>
<input type="text" name="username" maxlength="50" value="<?php echo $user->username; ?>" readonly>
<label for="created">Created</label>
<input type="text" name="created" value="<?php echo $user->created; ?>" readonly>
<label for="usertype">User Type</label>
<input type="text" name="usertype" maxlength="50" value="<?php echo $user->usertype; ?>" readonly>
</div>
<div class="pure-u-1 pure-u-md-1-3">
<label for="firstname">First Name</label>
<input type="text" name="firstname" maxlength="50" value="<?php echo $user->firstname; ?>">
<label for="lastname">Last Name</label>
<input type="text" name="lastname" maxlength="50" value="<?php echo $user->lastname; ?>">
<label for="email">Email</label>
<input type="text" name="email" maxlength="100" value="<?php echo $user->email; ?>">
</div>
<div class="pure-u-1 pure-u-md-1-3">
<label for="newpassword1">New Password</label>
<input type="password" name="newpassword1" maxlength="50" disabled>
<label for="newpassword2">Confirm Password</label>
<input type="password" name="newpassword2" maxlength="50" disabled>
<?php echo isset($errors['newpassword2']) ? $errors['newpassword2'] : ''; ?>
</div>
<div class="pure-controls pure-u-1">
<button type="submit" class="pure-input-1 pure-button button-success ">Save</button>
</div>
</div>
</fieldset>
</form>

<?php
if (!empty($_POST)):
    if (empty($_POST['newpassword1']) && empty($_POST['newpassword2'])):
        updateUserProfile($yaptc_db, $_SESSION['user_id'], $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['newpassword1'], $_POST['newpassword2']);
        header('Location: ' . $_SERVER['PHP_SELF']);
    elseif (!empty($_POST['newpassword1']) || !empty($_POST['newpassword2'])):
        $errors['newpassword2'] = "New passwords do not match.";
    elseif ($_POST['newpassword1'] != $_POST['newpassword2']):
        $errors['newpassword2'] = "New passwords do not match.";

    endif;
//otherwise what?
endif;
?>



<?php //********** END CONTENT **********//
endif;
require_once($yaptc_inc . "footer.inc.php");
?>
