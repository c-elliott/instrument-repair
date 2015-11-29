<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: login.php
*
*/

// Force HTTPS for security
if($_SERVER["HTTPS"] != "on") {
 $pageURL = "Location: https://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
 }
 header($pageURL);
}

// Load includes
require ('dbconnect.php');
require ('globals.php');
require ('querys.php');
$sql = new mysql();
$querys = new querys($sql);
$global = new globals($sql, $querys);

// Store HTML Login form as a variable
$loginform='
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> '.$PRODUCT_HEADER.' : Login</title>
    <link href="files/css/bootstrap.min.css" rel="stylesheet">
    <link href="files/css/login.css" rel="stylesheet">
    <link href="files/css/font-awesome.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="files/js/html5shiv.js"></script>
      <script src="files/js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>';

// Here we will handle some different login errors
if(isset($_GET['err_disabled'])) {
	$loginform .=' <div class="alert alert-danger text-center"><strong>Account Disabled.</strong> You may have used the wrong password too many times.</div>';
} elseif(isset($_GET['err_failedauth'])) {
	$loginform .=' <div class="alert alert-danger text-center"><strong>Oops!</strong> Login failed, please try again.</div>';
} elseif(isset($_GET['err_session'])) {
	$loginform .=' <div class="alert alert-danger text-center"><strong>Invalid or non-existent session.</strong> Please login.</div>';
} elseif(isset($_GET['logout'])) {
	$loginform .=' <div class="alert alert-success text-center"><strong>Success!</strong> You have logged out. Please login to continue working.</div>';
} else {
	$loginform .=' <div class="well text-center">This is a secure area, your IP Address <strong>' . $global->getIP() . '</strong> has been logged. No unauthorized access permitted.</div>';
}

// Continue with the login form
$loginform .= '    <div class="container">
      <form class="form-signin" role="form" method="post" action="login.php">
        <h1 class="form-signin-heading"><img src="files/logo.png" alt="Instrument Repair Portal"></h1>
        <div class="form-group input-group">
        <span class="input-group-addon"><i class="fa fa-user"></i></span>
        <input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
        </div>
        <div class="form-group input-group">
        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
      </form>
      <div class="footer text-center">'.$PRODUCT_FOOTER.'</div>
    </div>
  </body>
</html>
';

// Get the time in a nice format for later
$now = date("d-m-Y H:i:s");

// Proceed with authentication if we recieved form data
if ((isset($_POST['username']) && isset($_POST['password']))) {

// Clean the input
$username = $global->Clean($_POST['username']);
$password = $global->Clean($_POST['password']);

// Get password from database and encrypt the password we recieved from POST
$getpass = $sql->runQuery($querys->getPassword($username));
$dbpass = $getpass['password'];
$encpass = $global->rebuildEncryption($password, $dbpass);

// Authenticate
$numrows = $sql->runNumRowsQuery($querys->getUserDetails($username, $encpass));
$dbUserDetails = $sql->runQuery($querys->getUserDetails($username, $encpass));

// Check to see if login was successful
if ($numrows != 0) {

    // Proceed if the account is not disabled
    if ($dbUserDetails['userlevel'] != 0) {

        // Initialize session
        session_start();
        $_SESSION['id'] = session_id();
        $_SESSION['userAgent'] = $global->SessEncrypt($_SERVER['HTTP_USER_AGENT']);
        $_SESSION['userlevel'] = $dbUserDetails['userlevel'];
        $_SESSION['userid'] = $dbUserDetails['uid'];
    
        // Write session information to database
        $updateUserSession = $sql->updateQuery($querys->updateUserSession($_SESSION['userid'], $_SESSION['id']));
        $updateUserAgent = $sql->updateQuery($querys->updateUserAgent($_SESSION['userid'], $_SESSION['userAgent']));

        // Log the successful login to auth table
        $sql->insertQuery($querys->insertAuthlog($dbUserDetails['uid'], 1, $global->getIP(), $now)); 
		
	// Update IP and timestamp against user account
	$sql->updateQuery($querys->updateUserLastlog($dbUserDetails['uid'], $global->getIP(), $now)); 

        // Send to joblist.php
        header("Location: joblist.php");

    } else {
        // If the account is disabled
        // Send them back to login page with disabled message
        header("Location: login.php?err_disabled");
    }

} else {
    // If authentication failed
    // Make sure any existing session is destroyed
    session_start();
    session_unset();
    session_destroy();
   
    // Log the failure
    $sql->insertQuery($querys->insertAuthlog($dbUserDetails['uid'], 0, $global->getIP(), $now));

    // Send them back to login page with failed message
    header("Location: login.php?err_failedauth");
}

} else {
    // Make sure any existing session is destroyed
    session_start();
    session_unset();
    session_destroy();

    // Send them to login as we didn't recieve POST data
    echo $loginform;
}
?>
