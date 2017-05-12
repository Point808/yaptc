<!doctype html>
<html lang="<?php echo $yaptc_language; ?>">
    <head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="css/bootstrap.min.css"/>
        <link rel="stylesheet" href="css/sticky-footer-navbar.css">
	<link rel="stylesheet" href="css/font-awesome.css"/>
	<link rel="stylesheet" href="css/animate.min.css"/>
	<link rel="stylesheet" href="css/styles.css"/>
        <link rel="stylesheet" href="css/ie10-viewport-bug-workaround.css">
	<!-- [if lt IE 9]>
		<script src="js/html5shiv.js" type="text/javascript"></script>
		<script src="js/respond.min.js" type="text/javascript"></script>
	<![endif] -->
        <meta name="description" content="<?php echo lang('META_DESC'); ?>" />
        <title><?php echo $yaptc_company . " > " . $yaptc_appname . " > " . $yaptc_pagename; ?></title>
    </head>
    <body>

<!--get user stuff-->
<?php
if (isset($_SESSION['user_id'])): $session_user = getUserInfo($yaptc_db, $_SESSION['user_id'], "1", "0"); endif;
$session_status = getSessionStatus();
?>



    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><?php echo $yaptc_company . " > " . $yaptc_appname; ?></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <?php if ($session_status == false): ?>
                <!-- MENU FOR ALL LOGGED OUT -->
                <li class="<?php if ($yaptc_pagename==lang('LOGIN')): echo "active"; endif; ?>"><a href="login.php"><i class="fa fa-sign-in"></i> <?php echo lang('LOGIN'); ?></a></li>
            <?php elseif ($session_status == true): ?>
                <!-- MENU FOR ALL LOGGED IN -->
                <li class="<?php if ($yaptc_pagename==lang('HOME')): echo "active"; endif; ?>"><a href="index.php"><i class="fa fa-home"></i> <?php echo lang('HOME'); ?></a></li>
                <li class="<?php if ($yaptc_pagename==lang('ACCOUNT')): echo "active"; endif; ?>"><a href="profile.php"><i class="fa fa-cog"></i> <?php echo lang('ACCOUNT'); ?></a></li>
            <?php if ($session_user["0"]["usertype"] == "Administrator"): ?>
                <!-- ADDITIONAL MENU IF LOGGED IN AS ADMIN -->
                <li class="<?php if ($yaptc_pagename==lang('USERS')): echo "active"; endif; ?>"><a href="users.php"><i class="fa fa-users"></i> <?php echo lang('USERS'); ?></a></li>
                <li class="<?php if ($yaptc_pagename==lang('PUNCH_EDITOR')): echo "active"; endif; ?>"><a href="manualpunch.php"><i class="fa fa-clock-o"></i> <?php echo lang('PUNCH_EDITOR'); ?></a></li>
                <li class="<?php if ($yaptc_pagename==lang('REPORTS')): echo "active"; endif; ?>"><a href="reports.php"><i class="fa fa-newspaper-o"></i> <?php echo lang('REPORTS'); ?></a></li>
            <?php endif; ?>
                <!-- MENU FOR ALL LOGGED IN - BOTTOM END -->
                <li class="<?php if ($yaptc_pagename==lang('LOGOUT')): echo "pure-menu-selected"; endif; ?>"><a href="logout.php"><i class="fa fa-sign-out"></i> <?php echo lang('LOGOUT'); ?></a></li>
            <?php endif; ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
