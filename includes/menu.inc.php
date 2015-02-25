<?php
if (isset($_SESSION['user_id'])): $session_user = getUserInfo($db, $_SESSION['user_id']); endif;
$session_status = getSessionStatus();
?>
            <!-- SIDE MENU -->
            <a href="#menu" id="menuLink" class="menu-link"><span></span></a>
            <div id="menu">
                <div class="pure-menu pure-menu-open">
                    <a class="pure-menu-heading" href="index.php"><?php echo $yaptc_company; ?></a>
                    <ul>
<?php if ($session_status == false): ?>
                        <!-- MENU FOR ALL LOGGED OUT -->
                        <li class="<?php if ($yaptc_pagename==lang('LOGIN')): echo "pure-menu-selected"; endif; ?>"><a href="login.php"><?php echo lang('LOGIN'); ?></a></li>
<?php elseif ($session_status == true): ?>
                        <!-- MENU FOR ALL LOGGED IN -->
                        <li class="<?php if ($yaptc_pagename==lang('HOME')): echo "pure-menu-selected"; endif; ?>"><a href="index.php"><?php echo lang('HOME'); ?></a></li>
                        <li class="<?php if ($yaptc_pagename==lang('ACCOUNT')): echo "pure-menu-selected"; endif; ?>"><a href="profile.php"><?php echo lang('ACCOUNT'); ?></a></li>
<?php if ($session_user["0"]["usertype"] == "Administrator"): ?>
                        <!-- ADDITIONAL MENU IF LOGGED IN AS ADMIN -->
                        <li class="<?php if ($yaptc_pagename==lang('USERS')): echo "pure-menu-selected"; endif; ?>"><a href="users.php"><?php echo lang('USERS'); ?></a></li>
                        <li class="<?php if ($yaptc_pagename==lang('PUNCH_EDITOR')): echo "pure-menu-selected"; endif; ?>"><a href="manualpunch.php"><?php echo lang('PUNCH_EDITOR'); ?></a></li>
                        <li class="<?php if ($yaptc_pagename==lang('REPORTS')): echo "pure-menu-selected"; endif; ?>"><a href="reports.php"><?php echo lang('REPORTS'); ?></a></li>
<?php endif; ?>
                        <!-- MENU FOR ALL LOGGED IN - BOTTOM END -->
                        <li class="<?php if ($yaptc_pagename==lang('LOGOUT')): echo "pure-menu-selected"; endif; ?>"><a href="logout.php"><?php echo lang('LOGOUT'); ?></a></li>
<?php endif; ?>
                    </ul>
                </div>
            </div>
            <div id="main">
                <div class="header">
                    <h1><?php echo $yaptc_pagename; ?></h1>
                    <h2><?php if (isset($_SESSION['user_id'])): echo lang('USER') . ": " . $session_user["0"]["firstname"] . ' ' . $session_user["0"]["lastname"]; else: echo lang('PLEASE_LOG_IN'); endif; ?></h2>
                </div>
                <div class="content">
