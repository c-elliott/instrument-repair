<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: equipment_add.php
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
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrument Repair Portal : Add Equipment</title>
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
// USERLEVELS : Only available for userlevel 2 and above
if ($_SESSION['userlevel'] >= 2) {
?>
      
        <table class="table table-bordered table-hover table-striped tablesorter">
        <thead>
          <tr>
           <td colspan="2"><div class="text-center"><h3>Add New Equipment</h3></div></td>
          </tr>
          </thead>
          <tbody>
          <tr>
          <form method="post" action="">
            <td><strong>Equipment Name:</strong></td>
            <td><input name="equipment" type="text" id="equipment" size="50" placeholder="Please enter a value" required autofocus /></td>
          </tr>
          </tbody>
         </table>
        
    <div class="text-center">
      <input type="submit" name="submit" id="submit" value="Add to database & return to equipment list" class="btn btn-primary"></form>
            <?php
// USERLEVELS : Only available for userlevel 2 and above
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
  </div>
  <script src="files/js/bootstrap.min.js"></script>

</body>
</html>
<?php

// Check to see if we recieved all fields from POST
if (isset($_POST['equipment']) && 
	$_POST['submit']) {
		// Clean the POST data
		$equipment = $global->Clean($_POST['equipment']);
		
		// Make sure these values are not blank
		if($equipment == NULL	|| $equipment == " ") {
			alertERR("Equipment name cannot be blank.");
		} else {
		
		// Update the database
		$sql->insertQuery($querys->insertEquipment($equipment));

		// Close the window
		echo "<script type='text/javascript'>";
		echo "self.close();";
		echo "</script>";
		
} // End POST variable null checks
} // End POST variable isset checks
// End Session handling
} else {
header('Location: login.php?err_session');
};
?>
