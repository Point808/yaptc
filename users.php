<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Users";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false) {
killSession();
} else {
//********** BEGIN CONTENT **********//

echo "<h2 class=\"content-subhead\">Add User</h2>";
echo "<p>Use the following form to add users to the system.  Passwords must be 8+ characters.  Email must be filled out, and username must be unique.</p>";

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
    if (strlen($_POST['password']) < 8)
    {
        $errors['password'] = "Password must be at least 8 charcaters.";
    }
    // OPTIONAL
    // Force passwords to contain at least one number and one special character.
    /*
    if (!preg_match('/[0-9]/', $_POST['password']))
    {
        $errors['password'] = "Password must contain at least one number.";
    }
    if (!preg_match('/[\W]/', $_POST['password']))
    {
        $errors['password'] = "Password must contain at least one special character.";
    }
    */
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

    /**
     * Check that the username and email aren't already in our database.
     * Note the use of prepared statements. If you aren't using prepared
     * statements, be sure to escape your data before passing it to the query.
     *
     * Note also the absence of SELECT *
     * Grab the columns you need, nothing more.
     */
    $query = "SELECT username, email
              FROM users
              WHERE username = :username OR email = :email";
    $stmt = $sql->prepare($query);
    $stmt->execute(array(
        ':username' => $_POST['username'],
        ':email' => $email
    ));

    /**
     * There may well be more than one point of failure, but all we really need
     * is the first one.
     */
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

/**
 * If the form has been submitted and no errors were detected, we can proceed
 * to account creation.
 */
if (!empty($_POST['newuser']) && empty($errors))
{
    /**
     * Hash password before storing in database
     */
    $hasher = new PasswordHash(8, FALSE);
    $password = $hasher->HashPassword($_POST['password']);

    /**
     * I'm going to mention it again because it's important; if you aren't using
     * prepared statements, be sure to escape your data before passing it to
     * your query.
     */
    $query = "INSERT INTO users (firstname, lastname, username, password, email, created, usertype)
              VALUES (:firstname, :lastname, :username, :password, :email, NOW(), :usertype)";
    $stmt = $sql->prepare($query);
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

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>User Registration</title>
    </head>
    <body>
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


}


// delete user only if submitted by button
if (!empty($_POST['deluser']))
{
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' || ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['_METHOD'] == 'DELETE')) {
    $deleteid = (int) $_POST['deleteid'];
$deletequery = $sql->prepare("DELETE FROM users WHERE users.id=$deleteid");
$deletequery->execute();
echo "user deleted!";
    if ($deletequery !== false) {

        header("Location: {$_SERVER['PHP_SELF']}", true, 303);
        exit;
    }
}
}


echo "<h2 class=\"content-subhead\">User List</h2>";
echo "<p>Current users.  To edit, select the edit button in the right column.</p>";
$result = $sql->prepare("SELECT users.id as userid, users.username as username, users.email as email, users.created as created, users.firstname as firstname, users.lastname as lastname, users.usertype as usertypeid, usertypes.typename as usertype
FROM yaptc.users
INNER JOIN usertypes ON users.usertype = usertypes.id
ORDER BY users.lastname ASC;");
$result->execute();
echo '<table class="pure-table">';
echo '<thead>';
echo '<tr>';
echo '<th>First Name</th>';
echo '<th>Last Name</th>';
echo '<th>Username</th>';
echo '<th>Email</th>';
echo '<th>Created</th>';
echo '<th>User Type</th>';
echo '<th>Actions</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
while ($row = $result->fetch(PDO::FETCH_ASSOC))
{
echo "<tr>";
echo "<td>" . $row['firstname'] . "</td>";
echo "<td>" . $row['lastname'] . "</td>";
echo "<td>" . $row['username'] . "</td>";
echo "<td>" . $row['email'] . "</td>";
echo "<td>" . $row['created'] . "</td>";
echo "<td>" . $row['usertype'] . "</td>";
?><td><form method="post" onsubmit="return confirm('Are you sure you want to delete this user?')">
<input type="hidden" name="_METHOD" value="DELETE">
<input type="hidden" name="deleteid" value="<?php echo $row['userid']; ?>"><button name="deluser" value="deluser" type="submit">Delete</button></form></td>
<?php
echo "</tr>";
}
echo '</tbody>';
echo '</table>';


//********** END CONTENT **********//
require_once($yaptc_inc . "footer.inc.php");
?>
