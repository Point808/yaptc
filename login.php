<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Login";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
//********** BEGIN CONTENT **********//

// Is user logged in?  If so, tell them and go to main...
if (isset($_SESSION['user_id']) && isset($_SESSION['signature']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] = true && $_SESSION['signature'] = md5($_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT']))
{
header ("Refresh:3; url=index.php", true, 303);
echo "<h2 class=\"content-subhead\">You are already logged in...</h2>";
}
else
{
echo "<h2 class=\"content-subhead\">User Login</h2>";
  echo "<form class=\"pure-form\" action=\"login.php\" method=\"post\">";
  echo "<fieldset class=\"pure-group\" id=\"login\">";
  echo "<label for=\"username\">Username</label>";
  echo "<input type=\"text\" class=\"pure-input-1-2\" placeholder=\"Username\" id=\"username\" name=\"username\">";
  echo "<label for=\"password\">Password</label>";
  echo "<input type=\"password\" class=\"pure-input-1-2\" placeholder=\"Password\" id=\"password\" name=\"password\">";
  echo "</fieldset>";
  echo "<button type=\"submit\" class=\"pure-button pure-input-1-2 pure-button-primary\" value=\"Login\">Sign in</button>";
  echo "</form>";
require_once($yaptc_lib . "phpass-0.3/PasswordHash.php");
    $hasher = new PasswordHash(8, FALSE);
    if (!empty($_POST)) {
      $query = "SELECT id, password, UNIX_TIMESTAMP(created) AS salt, firstname, lastname FROM users WHERE username = :username";
      $stmt = $sql->prepare($query);
      $stmt->execute(array(':username' => $_POST['username']));
      $user = $stmt->fetchObject();
      if ($user && $user->password == $hasher->CheckPassword($_POST['password'], $user->password)) {
        session_regenerate_id();
        $_SESSION['user_id']   = $user->id;
        $_SESSION['loggedIn']  = TRUE;
        $_SESSION['signature'] = md5($user->id . $_SERVER['HTTP_USER_AGENT']);
        $_SESSION['firstname']   = $user->firstname;
        $_SESSION['lastname']   = $user->lastname;
        session_write_close();
        echo "Login successful...";
         header("Location: index.php");

      }
      else
      {
header ("Refresh:3; url=login.php", true, 303);
echo "<h2 class=\"content-subhead\">Login failed, please try again...</h2>";
      }
    }

  }


//********** END CONTENT **********//
require_once($yaptc_inc . "footer.inc.php");
?>
