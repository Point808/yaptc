<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Users";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
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
//********** BEGIN CONTENT **********//
echo "<h2 class=\"content-subhead\">System Users</h2>";
echo "<p>Editing to be added, for now, it doesn;t exist</p>";
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
echo "</tr>";
}
echo '</tbody>';
echo '</table>';


echo "<h2 class=\"content-subhead\">Add User</h2>";
echo "<p>Use the following form to add users to the system.  Passwords must be 8+ characters.  Email must be filled out, and username must be unique.</p>";

require_once($yaptc_lib . "phpass-0.3/PasswordHash.php");
if (!empty($_POST))
{
    if (empty($_POST['username']))
    {
        echo "Username cannot be empty.";
    }
    if (preg_match('/[^a-zA-Z0-9 .-_]/', $_POST['username']))
    {
        echo "Username contains illegal characters.";
    }
    if (empty($_POST['password']))
    {
        echo "Password cannot be empty.";
    }
    if (strlen($_POST['password']) < 8)
    {
        echo "Password must be at least 8 charcaters.";
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
        echo "Please confirm password.";
    }
    if ($_POST['password'] != $_POST['password_confirm'])
    {
        echo "Passwords do not match.";
    }
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email)
    {
        echo "Not a valid email address.";
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
        echo "That username is already in use.";
        }
        if ($existing->email == $email)
        {
        echo "That email address is already in use.";
        }
    }
}

/**
 * If the form has been submitted and no errors were detected, we can proceed
 * to account creation.
 */
if (!empty($_POST) && empty($errors))
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
    $query = "INSERT INTO users (username, password, email, created, usertype)
              VALUES (:username, :password, :email, NOW(), :usertype)";
    $stmt = $sql->prepare($query);
    $success = $stmt->execute(array(
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

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <fieldset id="registration">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" />
                <span class="error">
                    <?php echo isset($errors['username']) ? $errors['username'] : ''; ?>
                </span><br />

                <label for="email">Email Address</label>
                <input type="text" id="email" name="email" />
                <span class="error">
                    <?php echo isset($errors['email']) ? $errors['email'] : ''; ?>
                </span><br />

                <label for="password">Password</label>
                <input type="password" id="password" name="password" />
                <span class="error">
                    <?php echo isset($errors['password']) ? $errors['password'] : ''; ?>
                </span><br />

                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" />
                <span class="error">
                    <?php echo isset($errors['password_confirm']) ? $errors['password_confirm'] : ''; ?>
                </span><br />
                <input type="hidden" name="usertype" value="00000000001"/>
                <input type="submit" value="Submit" />
            </fieldset>
        </form>
    </body>
</html>
<?php


}

//********** END CONTENT **********//
require_once($yaptc_inc . "footer.inc.php");
?>
