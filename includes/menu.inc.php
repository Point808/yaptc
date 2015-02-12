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
// eventually i should go back here and oiinly allow menu options to open based on the user type details...

  // If user is not logged in, only show login option
  if (!isset($_SESSION['user_id']) || !isset($_SESSION['signature']) || !isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true || $_SESSION['signature'] != md5($_SESSION['user_id'] . $_SERVER['HTTP_USER_AGENT']))
  {


              echo '<li'; if ($yaptc_pagename=='Login') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="login.php">Login</a></li>';

  }
  else
  {

              echo '<li'; if ($yaptc_pagename=='Home') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="index.php">Home</a></li>';
              echo '<li'; if ($yaptc_pagename=='Profile') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="profile.php">Profile</a></li>';
              echo '<li'; if ($yaptc_pagename=='Time') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="time.php">Time</a></li>';
              echo '<li'; if ($yaptc_pagename=='Dashboard') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="dashboard.php">Dashboard</a></li>';
              echo '<li'; if ($yaptc_pagename=='Reports') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="reports.php">Reports</a></li>';
              echo '<li'; if ($yaptc_pagename=='Logout') {echo ' class="pure-menu-selected">';} else {echo '>';} echo '<a href="logout.php">Logout</a></li>';
}
            ?>
          </ul>
        </div>
      </div>

      <div id="main">

        <div class="header">
          <h1><?php echo $yaptc_pagename; ?></h1>
          <h2><?php if (isset($_SESSION['user_id'])) {echo "User: " . $_SESSION['firstname'] . ' ' . $_SESSION['lastname'];} else {echo "Please log in to use the timecard system";}?></h2>
        </div>

        <div class="content">
