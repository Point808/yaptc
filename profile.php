<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Profile";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
//********** BEGIN CONTENT **********//
// Is user logged in?  If not, they shouldn't be here - kill all variables and redirect to login...
if (!isset($_SESSION['user_id']) || !isset($_SESSION['signature']) || !isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true || $_SESSION['signature'] != md5($_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT']))
{
session_start();
session_unset();
session_destroy();
header ("Refresh:3; url=login.php", true, 303);
echo "<h2 class=\"content-subhead\">You are not logged in!!!</h2>";
}
else
{
echo "<h2 class=\"content-subhead\">Profile Information</h2>";
echo "<p>You may make changes to your user profile below if you wish. Updates will take effect immediately on pressing \"Save\".</p>";
    $query = "SELECT users.id, users.password, users.created, users.username, users.firstname, users.lastname, users.email, usertypes.typename AS usertype
              FROM users, usertypes
              WHERE users.id = :id";
      $stmt = $sql->prepare($query);
      $stmt->execute(array(':id' => $_SESSION['user_id']));
      $user = $stmt->fetchObject();
echo "<form class=\"pure-form pure-form-aligned\" action=\"profile.php\" method=\"post\">";
echo "<fieldset>";
echo "<div class=\"pure-control-group\">";
echo "<label for=\"username\">Username</label>";
echo "<input class=\"pure-input-1-2\" type=\"text\" name=\"username\" maxlength=\"50\" value=\"$user->username\" readonly>";
echo "</div>";
echo "<div class=\"pure-control-group\">";
echo "<label for=\"created\">Created</label>";
echo "<input class=\"pure-input-1-2\" type=\"text\" name=\"created\" value=\"$user->created\" readonly>";
echo "</div>";
echo "<div class=\"pure-control-group\">";
echo "<label for=\"usertype\">User Type</label>";
echo "<input class=\"pure-input-1-2\" type=\"text\" name=\"usertype\" maxlength=\"50\" value=\"$user->usertype\" readonly>";
echo "</div>";
echo "<div class=\"pure-control-group\">";
echo "<label for=\"firstname\">First Name</label>";
echo "<input class=\"pure-input-1-2\" type=\"text\" name=\"firstname\" maxlength=\"50\" value=\"$user->firstname\">";
echo "</div>";
echo "<div class=\"pure-control-group\">";
echo "<label for=\"lastname\">Last Name</label>";
echo "<input class=\"pure-input-1-2\" type=\"text\" name=\"lastname\" maxlength=\"50\" value=\"$user->lastname\">";
echo "</div>";
echo "<div class=\"pure-control-group\">";
echo "<label for=\"email\">Email</label>";
echo "<input class=\"pure-input-1-2\" type=\"text\" name=\"email\" maxlength=\"100\" value=\"$user->email\">";
echo "</div>";
echo "<div class=\"pure-controls\">";
echo "<button type=\"submit\" class=\"pure-button button-xlarge button-success\">Save</button>";
echo "</div>";
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
  }



//********** END CONTENT **********//
require_once($yaptc_inc . "footer.inc.php");
?>
