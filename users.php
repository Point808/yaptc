<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = lang('USERS');
$yaptc_pageicon = '<i class="fa fa-users"></i> ';
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
                    <!-- ********** BEGIN CONTENT ********** -->

<?php if($session_user["0"]["usertype"] !== "Administrator"): header("Location: index.php"); ?>
                    <h2 class="content-subhead"><?php echo lang('NOT_AUTHORIZED'); ?></h2>
<?php endif; ?>

                    <h2 class="content-subhead"><i class="fa fa-user-plus"></i> <?php echo lang('ADD_USER'); ?></h2>
                    <p><?php echo lang('ADD_USER_DESC') . $yaptc_min_password; ?></p>

<?php
require_once($yaptc_lib . "phpass-0.3/PasswordHash.php");
if (!empty($_POST['newuser'])):
    if (empty($_POST['username'])): $errors['username'] = lang('USERNAME_NOTEMPTY'); endif;
    if (preg_match('/[^a-zA-Z0-9 .-_]/', $_POST['username'])): $errors['username'] = lang('ILLEGAL_CHARACTERS'); endif;
    if (empty($_POST['password'])): $errors['password'] = lang('PASSWORD_NOTEMPTY'); endif;
    if (strlen($_POST['password']) < $yaptc_min_password): $errors['password'] = lang('MIN_PASSWORD_LENGTH') . $yaptc_min_password; endif;
    if (empty($_POST['password_confirm'])): $errors['password_confirm'] = lang('PASSWORD_NOTCONFIRMED'); endif;
    if ($_POST['password'] != $_POST['password_confirm']): $errors['password_confirm'] = lang('PASSWORD_NOTMATCH'); endif;
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email): $errors['email'] = lang('EMAIL_NOTVALID'); endif;
    $query = "SELECT username, email
              FROM users
              WHERE username = :username OR email = :email";
    $stmt = $yaptc_db->prepare($query);
    $stmt->execute(array(
        ':username' => $_POST['username'],
        ':email' => $email
    ));
    $existing = $stmt->fetchObject();
    if ($existing):
        if ($existing->username == $_POST['username']): $errors['username'] = lang('USERNAME_USED'); endif;
        if ($existing->email == $email): $errors['email'] = lang('PASSWORD_USED'); endif;
    endif;
endif;

if (!empty($_POST['newuser']) && empty($errors)):
    $hasher = new PasswordHash(8, FALSE);
    $password = $hasher->HashPassword($_POST['password']);



    $query = "INSERT INTO users (firstname, lastname, username, password, email, created, usertype)
              VALUES (:firstname, :lastname, :username, :password, :email, NOW(), :usertype)";
    $stmt = $yaptc_db->prepare($query);
    $success = $stmt->execute(array(
        ':firstname' => $_POST['firstname'],
        ':lastname' => $_POST['lastname'],
        ':username' => $_POST['username'],
        ':password' => $password,
        ':email'    => $_POST['email'],
        ':usertype'    => $_POST['usertype'],
    ));
    if ($success): $message = "Account created."; else: echo "Account could not be created. Please try again later."; endif;
 endif;
?>

        <?php if (isset($message)): ?>
        <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (isset($errors['registration'])): ?>
        <p class="error"><?php echo $errors['registration']; ?></p>
        <?php endif; ?>

                    <form class="pure-form pure-form-stacked" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <fieldset id="registration">
                            <div class="pure-g">
                                <div class="pure-u-1 pure-u-sm-1-4">
                                    <input type="text" class="pure-input pure-u-23-24" id="firstname" name="firstname" placeholder="First Name" required />
<?php echo isset($errors['firstname']) ? $errors['firstname'] : ''; ?>
                                </div>
                                <div class="pure-u-1 pure-u-md-1-4">
                                    <input type="text" class="pure-input pure-u-23-24" id="lastname" name="lastname" placeholder="Last Name" required />
<?php echo isset($errors['lastname']) ? $errors['lastname'] : ''; ?>
                                </div>
                                <div class="pure-u-1 pure-u-md-1-4">
                                    <input type="text" class="pure-input pure-u-23-24" id="username" name="username" placeholder="Username" required />
<?php echo isset($errors['username']) ? $errors['username'] : ''; ?>
                                </div>
                                <div class="pure-u-1 pure-u-md-1-4">
                                    <input type="text" class="pure-input pure-u-23-24" id="email" name="email" placeholder="Email" />
<?php echo isset($errors['email']) ? $errors['email'] : ''; ?>
                                </div>
                                <div class="pure-u-1 pure-u-md-1-4">
                                    <input type="password" class="pure-input pure-u-23-24" id="password" name="password" placeholder="Password" required />
<?php echo isset($errors['password']) ? $errors['password'] : ''; ?>
                                </div>
                                <div class="pure-u-1 pure-u-md-1-4">
                                    <input type="password" class="pure-input pure-u-23-24" id="password_confirm" name="password_confirm" placeholder="Confirm Password" required />
<?php echo isset($errors['password_confirm']) ? $errors['password_confirm'] : ''; ?>
                                </div>
                                <div class="pure-u-1 pure-u-md-1-4">
                                    <select id="usertype" class="pure-input-23-24" name="usertype" required>
                                        <option value="00000000002" selected>User</option>
                                        <option value="00000000001">Administrator</option>
                                    </select>
<?php echo isset($errors['usertype']) ? $errors['usertype'] : ''; ?>
                                </div>
                                <div class="pure-u-1 pure-u-md-1-4">
                                    <button type="submit" class="pure-button button-success pure-u-23-24" value="Submit" name="newuser"><i class="fa fa-user-plus"></i> </button>
                                </div>
                            </div>
                        </fieldset>
                    </form>

<?php



// delete user only if submitted by button
if (!empty($_POST['deluser']))
{
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' || ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['_METHOD'] == 'DELETE')) {
    $deleteid = (int) $_POST['deleteid'];
$deletequery = $yaptc_db->prepare("DELETE FROM users WHERE users.id=$deleteid");
$deletequery->execute();
echo "user deleted!";
        header('Location: ' . $_SERVER['PHP_SELF']);

}
}

// Set up pagination
$page_num = 1;
if(!empty($_GET['pnum'])):
    $page_num = filter_input(INPUT_GET, 'pnum', FILTER_VALIDATE_INT);
    if(false === $page_num):
        $page_num = 1;
    endif;
endif;
$offset = ($page_num - 1) * $yaptc_rowsperpage;
$row_count = count(getUserInfo($yaptc_db, "%"));
$page_count = 0;
if (0 === $row_count): else: $page_count = (int)ceil($row_count / $yaptc_rowsperpage); if($page_num > $page_count): $page_num = 1; endif; endif;
?>

                    <h2 class="content-subhead"><i class="fa fa-list"></i> <?php echo lang('USER_LIST_HEADER'); ?></h2>
                    <p><?php echo lang('USER_LIST_DESC'); ?></p>
                    <table class="pure-table pure-table-striped">
                        <thead>
                            <tr><th colspan="6"><?php echo lang('PAGE') . ": "; for ($i = 1; $i <= $page_count; $i++): if ($i === $page_num): echo $i . ' '; else: echo '<a href="' . $_SERVER['PHP_SELF'] . '?pnum=' . $i . '">' . $i . '</a> '; endif; endfor; ?></th></tr>
                            <tr><th><?php echo lang('NAME'); ?></th><th><?php echo lang('USERNAME'); ?></th><th><?php echo lang('EMAIL'); ?></th><th><?php echo lang('CREATED'); ?></th><th><?php echo lang('USERTYPE'); ?></th><th><?php echo lang('ACTIONS'); ?></th></tr>
                        </thead>
                        <tbody>
<?php foreach (getUserInfo($yaptc_db, "%", $yaptc_rowsperpage, $offset) as $row): ?>
                            <tr>
                                <td><?php echo $row['lastname'] . ", " . $row['firstname']; ?></td><td><?php echo $row['username']; ?></td><td><?php echo $row['email']; ?></td><td><?php echo $row['created']; ?></td><td><?php echo $row['usertype']; ?></td><td><form method="post" onsubmit="return confirm('<?php echo lang('DELETE_WARNING'); ?>')"><input type="hidden" id="_METHOD" name="_METHOD" value="DELETE" /><input type="hidden" id="deleteid" name="deleteid" value="<?php echo $row['userid']; ?>" /><button class="button-error pure-button" id="deluser" name="deluser" value="deluser" type="submit" <?php if ($row['username'] == "admin"): echo "disabled"; endif; ?>><i class="fa fa-trash"></i> </button></form></td>
                            </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
