<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
require_once($yaptc_lang);
$yaptc_pagename = lang('LOGIN');
$yaptc_pageicon = '<i class="fa fa-sign-in"></i> ';
require_once($yaptc_inc . "header.inc.php");
if (getSessionStatus() == true):
header('Location: index.php');
else: ?>
                    <!-- ********** BEGIN CONTENT ********** -->
<?php
// hash password for comparison
require_once($yaptc_lib . "phpass-0.3/PasswordHash.php");
$hasher = new PasswordHash(8, FALSE);
// compare if posted
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
    endif;
endif;
?>

    <div class="container">
      <form class="form-signin" action="login.php" method="post">
        <h2 class="form-signin-heading"><?php echo lang('PLEASE_LOG_IN'); ?></h2>
        <label for="username" class="sr-only"><?php echo lang('EMAIL'); ?></label>
        <input type="text" id="username" name="username" class="form-control" placeholder="<?php echo lang('USERNAME'); ?>" required autofocus>
        <label for="password" class="sr-only"><?php echo lang('PASSWORD'); ?></label>
        <input type="password" id="password" name="password" class="form-control" placeholder="<?php echo lang('PASSWORD'); ?>" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="login"><?php echo lang('LOGIN'); ?></button>
      </form>

<?php echo lang('LOGIN'); ?>

    </div>

<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
