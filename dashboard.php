<?php
  session_start();

  // Load config...
  require_once("config.inc.php");

  // Page title mod
  $yaptc_pagename = 'Dashboard';

  // Load header
  require_once($yaptc_inc . "header.inc.php");

  // Load menu
  require_once($yaptc_inc . "menu.inc.php");

  //************************ CONTENT START ************************

  // If user is not logged in, give error and option to go to login
  if (!isset($_SESSION['user_id']) || !isset($_SESSION['signature']) || !isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true || $_SESSION['signature'] != md5($_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT']))
  {
    session_destroy();

    echo "not logged in!!!";
    exit();
  }
  else
  {

// content for logged-in users here

    $query = "SELECT users.id, users.firstname, users.lastname, , users.created, users.username, users.firstname, users.lastname, users.email, usertypes.typename AS usertype
              FROM users, punches, punchtypes
              WHERE users.id = :id";
      $stmt = $sql->prepare($query);
      $stmt->execute(array(':id' => $_SESSION['user_id']));
      $user = $stmt->fetchObject();
    echo 'You may make changes to your user profile below if you wish.  Updates will take effect immediately on pressing "Save".';
    echo '<form class="pure-form" action="profile.php" method="post">';
    echo '<fieldset class="pure-group" id="userinfo">';
    echo '<label for="username">Username</label>';
    echo "<input type=\"text\" class=\"pure-input-1-2\" placeholder=\"$user->username\" value=\"$user->username\" id=\"username\" name=\"username\" readonly>";
    echo '<label for="created">Created</label>';
    echo "<input type=\"text\" class=\"pure-input-1-2\" placeholder=\"$user->created\" value=\"$user->created\" id=\"created\" name=\"created\" readonly>";
    echo '<label for="usertype">User Type</label>';
    echo "<input type=\"text\" class=\"pure-input-1-2\" placeholder=\"$user->usertype\" value=\"$user->usertype\" id=\"usertype\" name=\"usertype\" readonly>";
    echo '<label for="firstname">First Name</label>';
    echo "<input type=\"text\" class=\"pure-input-1-2\" placeholder=\"$user->firstname\" id=\"firstname\" name=\"firstname\">";
    echo '<label for="lastname">Last Name</label>';
    echo "<input type=\"text\" class=\"pure-input-1-2\" placeholder=\"$user->lastname\" id=\"lastname\" name=\"lastname\">";
    echo '<label for="username">Email Address</label>';
    echo "<input type=\"text\" class=\"pure-input-1-2\" placeholder=\"$user->email\" id=\"username\" name=\"username\">";
    echo '</fieldset>';
    echo '<button type="submit" class="pure-button pure-input-1-2 pure-button-primary" value="Update">Save Changes</button>';
    echo '</form>';

// end logged-in content
  }

  //************************ CONTENT END ************************
  // Load footer
  require_once($yaptc_inc . "footer.inc.php");
?>
