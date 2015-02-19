    <div id="layout">
      <!-- Menu toggle -->
      <a href="#menu" id="menuLink" class="menu-link">
        <!-- Hamburger icon -->
        <span></span>
      </a>

      <div id="menu">
        <div class="pure-menu pure-menu-open">
          <a class="pure-menu-heading" href="index.php"><?php echo $yaptc_company; ?></a>
          <ul>
            <?php


// Get login status and access level
if (getSessionStatus() == true) { $userLogged = true; $userAccess = getSessionAccess($sql); } else { $userLogged = false; $userAccess = ""; }

// All menu options - only ones with permissions allowed are shown to logged-in users.
// Home
if ($userLogged == true) {
  echo '<li'; if ($yaptc_pagename=='Home') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="index.php">Home</a></li>';
  }
// Profile
if ($userLogged == true) {
  echo '<li'; if ($yaptc_pagename=='Profile') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="profile.php">Profile</a></li>';
  }
// Punch Log
if ($userLogged == true) {
  echo '<li'; if ($yaptc_pagename=='Punch Log') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="punchlog.php">Punch Log</a></li>';
  }
// Users
if ($userLogged == true && $userAccess == "Administrator") {
  echo '<li'; if ($yaptc_pagename=='Users') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="users.php">Users</a></li>';
  }
// Reports
if ($userLogged == true && $userAccess == "Administrator") {
  echo '<li'; if ($yaptc_pagename=='Reports') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="reports.php">Reports</a></li>';
  }       
// Logout
if ($userLogged == true) {
  echo '<li'; if ($yaptc_pagename=='Logout') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="logout.php">Logout</a></li>';
  }
// Login
if ($userLogged == false) {
  echo '<li'; if ($yaptc_pagename=='Login') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="login.php">Login</a></li>';
  }

?>
          </ul>
        </div>
      </div>

      <div id="main">

        <div class="header">
          <h1><?php echo $yaptc_pagename; ?></h1>
          <h2><?php if (isset($_SESSION['user_id'])) {echo "Logged as: " . $_SESSION['firstname'] . ' ' . $_SESSION['lastname'];} else {echo "Please log in to use the timecard system";}?></h2>
          <h4><?php if (!empty($adminmessage)) {echo "<div class=\"successmessage\">" . $adminmessage . "</div>"; } ?></h4>
        </div>

        <div class="content">
