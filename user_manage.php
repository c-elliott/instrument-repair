<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: user_manage.php
*
*/

// Load includes
require ('dbconnect.php');
require ('globals.php');
require ('querys.php');
$sql = new mysql();
$querys = new querys($sql);
$global = new globals($sql, $querys);

// Start session
session_start();

// Get user details from database
$userDetails = $sql->runArrayQuery($querys->getUserDetailsWoPass($_SESSION['userid']));

// Prepare current Session ID and Useragent information
$currUserAgent = $global->SessEncrypt($_SERVER['HTTP_USER_AGENT']);
$currSessionID = session_id();

// Check that UserAgent and Session ID is untouched
if (isset($userDetails['useragent']) &&
    isset($userDetails['sessionid']) &&
    isset($_SESSION['userAgent']) &&
    isset($_SESSION['id']) &&
    isset($_SESSION['userid']) &&
    $currUserAgent == $userDetails['useragent'] &&
    $currUserAgent == $_SESSION['userAgent'] &&
    $currSessionID == $userDetails['sessionid'] &&
    $currSessionID == $_SESSION['id']) {

// Regenerate session to deter hijacking
session_regenerate_id($currSessionID);
$_SESSION['id'] = session_id();
$updateUserSession = $sql->updateQuery($querys->updateUserSession($_SESSION['userid'], $_SESSION['id']));

// GET handling
if(isset($_GET['id']) && is_numeric($_GET['id'])) {

	// Make sure we have a valid user ID
	$puserid = $global->Clean($_GET['id']);
	$validpuser = $sql->runNumRowsQuery($querys->getUser($puserid));

    // If user ID is not valid, send back to customer list
	if ($validpuser == NULL) {
	    echo '<meta http-equiv="refresh" content="0; url=customerlist.php" />';
	} else {
		$puser = $sql->runArrayQuery($querys->getUser($puserid));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $PRODUCT_HEADER; ?> : User ID <?php echo $puserid; ?> - <?php echo $puser['firstname']; ?> <?php echo $puser['lastname']; ?></title>
    <link href="files/css/bootstrap.min.css" rel="stylesheet">
    <link href="files/css/font-awesome.min.css" rel="stylesheet">
    <link href="files/css/jquery.toastmessage.css" rel="stylesheet">
    <script src="files/js/jquery-1.10.2.min.js"></script>
    <script src="files/js/jquery.toastmessage.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="files/js/html5shiv.js"></script>
      <script src="files/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
  
    <div class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <img src="files/logo.png" alt="<?php echo $PRODUCT_HEADER; ?>">
          &nbsp;&nbsp;&nbsp;
        </div>
      </div>
    </div>

    <div class="container">
          <?php
// USERLEVELS : Only available for userlevel 3 and above
if ($_SESSION['userlevel'] >= 3) {
?>
      
        <table class="table table-bordered table-hover table-striped tablesorter">
        <thead>
          <tr>
           <td colspan="2"><div class="text-center"><h3>Edit User Details</h3></div></td>
          </tr>
          </thead>
          <tbody>
          <tr>
          <form method="post" action="">
            <td><strong>First Name:</strong></td>
            <td><input name="firstname" type="text" id="firstname" value="<?php echo $puser['firstname']; ?>" size="50" required autofocus /></td>
          </tr>
          <tr>
            <td><strong>Last Name:</strong></td>
            <td><input name="lastname" type="text" id="lastname" value="<?php echo $puser['lastname']; ?>" size="50" required /></td>
          </tr>
          <tr>
            <td><strong>Email Address:</strong></td>
            <td><input name="email" type="text" id="lastname" value="<?php echo $puser['email']; ?>" size="50" required /></td>
          </tr>
          <tr>
            <td><strong>Username:</strong></td>
            <td><?php echo $puser['username']; ?> (Cannot be changed.)</td>
          </tr>
          <tr>
            <td><strong>Password:</strong></td>
            <td><input name="password" type="password" id="password" placeholder="Leave BLANK to keep existing password." size="50" /></td>
          </tr>
           <tr>
            <td><strong>Last Login:</strong></td>
            <td><?php echo $puser['lastlogin']; ?> from IP <?php echo $puser['lastloginip']; ?></td>
          </tr>
           <tr>
            <td><strong>Session Hash:</strong></td>
            <td><?php echo $puser['sessionid']; ?></td>
          </tr>
           <tr>
            <td><strong>Useragent Hash:</strong></td>
            <td><?php echo $puser['useragent']; ?></td>
          </tr>
          </tbody>
         </table>
        
    <div class="text-center">
      <input type="submit" name="submit" id="submit" value="Update & return to customer list" class="btn btn-primary"></form>
                  <?php
// USERLEVELS : Only available for userlevel 3 and above
} else {
echo "<div class=\"text-center\">";
echo "You do not have permission to do this.";
}
?>  
      <br /><br />
      <span class="h6"><?php echo $PRODUCT_FOOTER; ?></span><br />
      <span class="h6"><?php echo $PRODUCT_VERSION; ?></span><br />
    </div>
  </div>
  <script src="files/js/bootstrap.min.js"></script>

</body>
</html>
<?php

// Check to see if we recieved all fields from POST
if (isset($_POST['firstname']) && 
	isset($_POST['lastname']) && 
	isset($_POST['email']) &&
	isset($_POST['password']) &&
	$_POST['submit']) {
	
		// Clean the POST data
		$firstname = $global->Clean($_POST['firstname']);
		$lastname = $global->Clean($_POST['lastname']);
		$email = $global->Clean($_POST['email']);
		$password = $global->Clean($_POST['password']);
		
		// Make sure these values are not blank
		if($firstname == NULL	|| $firstname == " ") {
			alertERR("First name cannot be blank.");
		} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			alertERR("Please enter a valid email address.");
		} elseif($lastname == NULL || $lastname == " ") {
			alertERR("Last name cannot be blank.");
		} elseif($password == NULL || $password == " ") {
		
		    // If password was blank, update firstname, lastname and email address fields
			$sql->updateQuery($querys->updateUserWOPass($puserid, $firstname, $lastname, $email));

			   // Close the window
               echo "<script type='text/javascript'>";
               echo "self.close();";
               echo "</script>";
			
		} else {
		
		// Otherwise update the password too
		$encpass = $global->Encrypt($password);
		$sql->updateQuery($querys->updateUser($puserid, $firstname, $lastname, $email, $encpass));

                // Close the window
               echo "<script type='text/javascript'>";
               echo "self.close();";
               echo "</script>";
		
} // End POST variable null checks
} // End POST variable isset checks
// End GET handling
}
} else {
echo '<meta http-equiv="refresh" content="0; url=userlist.php" />';
}

// End Session handling
} else {
header('Location: login.php?err_session');
};
?>
