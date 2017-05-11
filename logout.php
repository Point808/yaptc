<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = lang('LOGOUT');
$yaptc_pageicon = '<i class="fa fa-sign-out"></i> ';
require_once($yaptc_inc . "header.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
                    <!-- ********** BEGIN CONTENT ********** -->
<?php killSession(); ?>
                    <!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
