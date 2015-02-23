<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = "Users";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
<!-- ********** BEGIN CONTENT ********** -->

<?php if ($userLogged == true && $userAccess == "Administrator"): ?>
<h2 class="content-subhead">Add User</h2>
<p>All fields are required!  Password must be 4+ characters.  Username and email must be unique.</p>
<?php
require_once($yaptc_lib . "phpass-0.3/PasswordHash.php");
if (!empty($_POST['newuser']))
{
    if (empty($_POST['username']))
    {
        $errors['username'] = "Username cannot be empty.";
    }
    if (preg_match('/[^a-zA-Z0-9 .-_]/', $_POST['username']))
    {
        $errors['username'] = "Username contains illegal characters.";
    }
    if (empty($_POST['password']))
    {
        $errors['password'] = "Password cannot be empty.";
    }
    if (strlen($_POST['password']) < 4)
    {
        $errors['password'] = "Password must be at least 4 charcaters.";
    }
    if (empty($_POST['password_confirm']))
    {
        $errors['password_confirm'] = "Please confirm password.";
    }
    if ($_POST['password'] != $_POST['password_confirm'])
    {
        $errors['password_confirm'] = "Passwords do not match.";
    }
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email)
    {
        $errors['email'] = "Not a valid email address.";
    }
    $query = "SELECT username, email
              FROM users
              WHERE username = :username OR email = :email";
    $stmt = $yaptc_db->prepare($query);
    $stmt->execute(array(
        ':username' => $_POST['username'],
        ':email' => $email
    ));

    $existing = $stmt->fetchObject();

    if ($existing)
    {
        if ($existing->username == $_POST['username'])
        {
        $errors['username'] = "That username is already in use.";
        }
        if ($existing->email == $email)
        {
        $errors['email'] = "That email address is already in use.";
        }
    }
}

if (!empty($_POST['newuser']) && empty($errors))
{
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

    if ($success)
    {
        $message = "Account created.";
    }
    else
    {
        echo "Account could not be created. Please try again later.";
    }
}

?>

        <?php if (isset($message)): ?>
        <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Note that we're again checking that each array key exists before
             trying to use it, in order to prevent undefined index notices. -->
        <?php if (isset($errors['registration'])): ?>
        <p class="error"><?php echo $errors['registration']; ?></p>
        <?php endif; ?>

        <form class="pure-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <fieldset id="registration" class="pure-group">
        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-1-2">
                <input type="text" class="pure-input" id="firstname" name="firstname" placeholder="First Name" required />
                    <?php echo isset($errors['firstname']) ? $errors['firstname'] : ''; ?>
                <input type="text" class="pure-input" id="lastname" name="lastname" placeholder="Last Name" required />
                    <?php echo isset($errors['lastname']) ? $errors['lastname'] : ''; ?>
                <input type="text" class="pure-input" id="username" name="username" placeholder="Username" required />
                    <?php echo isset($errors['username']) ? $errors['username'] : ''; ?>
</div>
            <div class="pure-u-1 pure-u-md-1-2">
                <input type="text" class="pure-input" id="email" name="email" placeholder="Email" />
                    <?php echo isset($errors['email']) ? $errors['email'] : ''; ?>
                <input type="password" class="pure-input" id="password" name="password" placeholder="Password" required />
                    <?php echo isset($errors['password']) ? $errors['password'] : ''; ?>
                <input type="password" class="pure-input" id="password_confirm" name="password_confirm" placeholder="Confirm Password" required />
                    <?php echo isset($errors['password_confirm']) ? $errors['password_confirm'] : ''; ?>
</div>
            <div class="pure-u-1 pure-u-md-1">
                <label for="usertype">Access Level</label>
                <select id="usertype" name="usertype" required />
<option value="00000000002">User</option>
<option value="00000000001">Administrator</option>
</select>
                    <?php echo isset($errors['usertype']) ? $errors['usertype'] : ''; ?>
<button type="submit" class="pure-button button-success" value="Submit" name="newuser">Create</button>
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
    if ($deletequery !== false) {

        header("Location: {$_SERVER['PHP_SELF']}", true, 303);
        exit;
    }
}
}


?>

<h2 class="content-subhead">User List</h2>
<p>Current users.  To edit, select the edit button in the right column.</p>
<table class="pure-table">
<thead>
<tr>
<th>First Name</th>
<th>Last Name</th>
<th>Username</th>
<th>Email</th>
<th>Created</th>
<th>User Type</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<tr>
<?php
foreach (listUsers($yaptc_db) as $row) {
echo "<td>" . $row['firstname'] . "</td>";
echo "<td>" . $row['lastname'] . "</td>";
echo "<td>" . $row['username'] . "</td>";
echo "<td>" . $row['email'] . "</td>";
echo "<td>" . $row['created'] . "</td>";
echo "<td>" . $row['usertype'] . "</td>";
?>
<td><form method="post" onsubmit="return confirm('WARNING! - WARNING! - WARNING! This will delete the user and ALL punches associated with them.  There is NO UNDO!  Are you sure?')">
<input type="hidden" name="_METHOD" value="DELETE">
<input type="hidden" name="deleteid" value="<?php echo $row['userid']; ?>"><button button class="button-error pure-button" name="deluser" value="deluser" type="submit" <?php if ($row['username'] == "admin"): echo "disabled"; endif; ?>>Delete</button></form></td>
</tr>
<?php } ?>
</tbody>
</table>

<?php else: ?>
<h2 class="content-subhead">NOT AUTHORIZED!</h2>
<?php endif; ?>

<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
