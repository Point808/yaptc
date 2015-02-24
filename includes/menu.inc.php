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


// Get logged-in user's profile information
$session_user = getUserInfo($db, $_SESSION['user_id']);
$session_status = getSessionStatus();

// Menu Setup

// For logged-out users
if ($session_status == false):
  echo '<li'; if ($yaptc_pagename=='Login') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="login.php">Login</a></li>';
// For logged-in users, depending on access
elseif ($session_status == true):
// Home
echo '<li'; if ($yaptc_pagename=='Home'): echo ' class="pure-menu-selected">'; else: echo '>'; endif; echo '<a href="index.php">Home</a></li>';
// Profile Menu
echo '<li'; if ($yaptc_pagename=='Profile'): echo ' class="pure-menu-selected">'; else: echo '>'; endif; echo '<a href="profile.php">Profile</a></li>';
// Punch Log Menu
echo '<li'; if ($yaptc_pagename=='Punch Log'): echo ' class="pure-menu-selected">'; else: echo '>'; endif; echo '<a href="punchlog.php">Punch Log</a></li>';
// Users Menu
if ($session_user["0"]["usertype"] == "Administrator"):
  echo '<li'; if ($yaptc_pagename=='Users'): echo ' class="pure-menu-selected">'; else: echo '>'; endif; echo '<a href="users.php">Users</a></li>';
endif;
// Manual Punch
if ($session_user["0"]["usertype"] == "Administrator"):
  echo '<li'; if ($yaptc_pagename=='Manual Punch'): echo ' class="pure-menu-selected">'; else: echo '>'; endif; echo '<a href="manualpunch.php">Manual Punch</a></li>';
endif;
// Reports Menu
if ($session_user["0"]["usertype"] == "Administrator"):
  echo '<li'; if ($yaptc_pagename=='Reports'): echo ' class="pure-menu-selected">'; else: echo '>'; endif; echo '<a href="reports.php">Reports</a></li>';
endif;
// Logout Menu
echo '<li'; if ($yaptc_pagename=='Logout'): echo ' class="pure-menu-selected">'; else: echo '>'; endif; echo '<a href="logout.php">Logout</a></li>';

endif;

?>
          </ul>
        </div>
      </div>

      <div id="main">

        <div class="header">
          <h1><?php echo $yaptc_pagename; ?></h1>
          <h2><?php if (isset($_SESSION['user_id'])): echo "User: " . $session_user["0"]["firstname"] . ' ' . $session_user["0"]["lastname"]; else: echo "Please log in to use the timecard system"; endif; ?></h2>
          <h4><?php if (!empty($adminmessage)): echo "<div class=\"adminmessage\">" . $adminmessage . "</div>"; endif; ?></h4>
        </div>

        <div class="content">
