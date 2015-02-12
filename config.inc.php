<?php
  // User session variables
  $yaptc_dirpath = '/usr/share/nginx/html/yaptc/'; // absolute path to yaptc
  $yaptc_webpath = 'http://localhost/yaptc/'; // where is the web url for the root of this app?
  $yaptc_appname = 'Timecard System'; // name to display in title bar and other headers
  $yaptc_company = 'Point808'; // name of your company
  $sql = new PDO('mysql:host=localhost;dbname=yaptc;', 'yaptc', 'yaptcpassw0rd');

  // Other variables probably won't change
  $_SESSION['yaptc_dir'] = $yaptc_dirpath; 
  $_SESSION['yaptc_url'] = $yaptc_webpath;
  $yaptc_inc = $yaptc_dirpath . 'includes/';
  $yaptc_incweb = $yaptc_webpath . 'includes/';
  $yaptc_lib = $yaptc_dirpath . 'lib/';


  // Has the app been configured (i.e. does a config.inc.php file exist?)
  if (!file_exists($_SESSION['yaptc_dir'] . 'config.inc.php'))
  echo "app has not been configured.  please creat a config.inc.php file in your root dir";

?>

