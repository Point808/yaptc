<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Profile";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false) {
killSession();
} else {
//********** BEGIN CONTENT **********//

$query = "SELECT users.id, users.password, users.created, users.username, users.firstname, users.lastname, users.email, usertypes.typename AS usertype FROM users, usertypes WHERE users.id = :id";
  $stmt = $sql->prepare($query);
  $stmt->execute(array(':id' => $_SESSION['user_id']));
  $user = $stmt->fetchObject();
?>
<h2 class\"content-subhead">Profile Information</h2>
<p>You may make changes to your user profile below if you wish. Updates will take effect immediately on pressing "Save".</p>
<form class="pure-form pure-form-aligned" action="profile.php" method="post">
<fieldset>
<div class="pure-control-group">
<label for="username">Username</label>
<input type="text" name="username" maxlength="50" value="<?php echo $user->username; ?>" readonly>
</div>
<div class="pure-control-group">
<label for="created">Created</label>
<input type="text" name="created" value="<?php echo $user->created; ?>" readonly>
</div>
<div class="pure-control-group">
<label for="usertype">User Type</label>
<input type="text" name="usertype" maxlength="50" value="<?php echo $user->usertype; ?>" readonly>
</div>
<div class="pure-control-group">
<label for="firstname">First Name</label>
<input type="text" name="firstname" maxlength="50" value="<?php echo $user->firstname; ?>">
</div>
<div class="pure-control-group">
<label for="lastname">Last Name</label>
<input type="text" name="lastname" maxlength="50" value="<?php echo $user->lastname; ?>">
</div>
<div class="pure-control-group">
<label for="email">Email</label>
<input type="text" name="email" maxlength="100" value="<?php echo $user->email; ?>">
</div>
<div class="pure-controls">
<button type="submit" class="pure-button button-xlarge button-success">Save</button>
</div>

<?php
    if (!empty($_POST)) {
$query = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email WHERE id = :userid";
   $stmt = $sql->prepare($query);
$stmt->execute(array(
        ':userid' => $_SESSION['user_id'],
        ':firstname' => $_POST['firstname'],
        ':lastname'    => $_POST['lastname'],
        ':email'    => $_POST['email']
    ));
header('Location: '.$_SERVER['PHP_SELF']);
exit;
}
echo "</fieldset>";
echo "</form>";



//********** END CONTENT **********//
}
require_once($yaptc_inc . "footer.inc.php");
?>
