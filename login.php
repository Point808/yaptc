<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = "Login";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == true):
header ("Refresh:3; url=index.php", true, 303);
echo "<h2 class=\"content-subhead\">You are already logged in...</h2>";
else: ?>
<!-- ********** BEGIN CONTENT ********** -->

<?php
require_once($yaptc_lib . "phpass-0.3/PasswordHash.php");
$hasher = new PasswordHash(8, FALSE);
if (!empty($_POST)):
    $query = "SELECT id, password, UNIX_TIMESTAMP(created) AS salt, firstname, lastname FROM users WHERE username = :username";
    $stmt  = $yaptc_db->prepare($query);
    $stmt->execute(array(
        ':username' => $_POST['username']
    ));
    $user = $stmt->fetchObject();
    if ($user && $user->password == $hasher->CheckPassword($_POST['password'], $user->password)):
        session_regenerate_id();
        $_SESSION['user_id']   = $user->id;
        $_SESSION['loggedIn']  = TRUE;
        $_SESSION['signature'] = md5($user->id . $_SERVER['HTTP_USER_AGENT']);
        $_SESSION['firstname'] = $user->firstname;
        $_SESSION['lastname']  = $user->lastname;
        session_write_close();
        header("Location: index.php");
    else:
        header("Refresh:3; url=login.php", true, 303);
        echo "<h2 class=\"content-subhead\">Login failed, please try again...</h2>";
    endif;
endif;
?>
<h2 class="content-subhead">User Login</h2>
<form class="pure-form" action="login.php" method="post">
    <fieldset class="pure-group" id="login">
        <input type="text" class="pure-input-1" placeholder="Username" id="username" name="username" />
        <input type="password" class="pure-input-1" placeholder="Password" id="password" name="password" />
    </fieldset>
    <button type="submit" class="pure-button button-success pure-input-1 pure-button-primary" value="Login">Sign in</button>
</form>

<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
