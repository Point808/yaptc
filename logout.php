<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Logout";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
//********** BEGIN CONTENT **********//

// Does user have any session settings active?  Kill them all...
if (isset($_SESSION['user_id']) || isset($_SESSION['signature']) || isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] = true || $_SESSION['signature'] = md5($_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT']))
{
session_start();
session_unset();
session_destroy();
header ("Refresh:3; url=login.php", true, 303);
echo "<h2 class=\"content-subhead\">You have successfully logged out...</h2>";
}
else
{
session_start();
session_unset();
session_destroy();
header ("Location: login.php");
}

//********** END CONTENT **********//
require_once($yaptc_inc . "footer.inc.php");
?>
