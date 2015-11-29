<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: customer_add.php
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

// Make sure we display the correct information
if($_POST) {
$selectedCNA = $global->Clean($_POST['customername']);
$selectedCN = $global->Clean($_POST['customerno']);
$selectedCC = $global->Clean($_POST['customercontact']);
$selectedCP = $global->Clean($_POST['customerphone']);
$selectedCE = $global->Clean($_POST['customeremail']);
$selectedCNO = $global->Clean($_POST['notes']);
} else {
$selectedCNA = NULL;
$selectedCN = NULL;
$selectedCC = NULL;
$selectedCP = NULL;
$selectedCE = NULL;
$selectedCNO = NULL;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrument Repair Portal : Add Customer</title>
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
           <td colspan="2"><div class="text-center"><h3>Add New Customer</h3></div></td>
          </tr>
          </thead>
          <tbody>
          <tr>
          <form method="post" action="">
            <td><strong>Customer Name:</strong></td>
            <td><input name="customername" type="text" id="customername" size="50" value="<?php echo $selectedCNA; ?>" placeholder="Please enter a value" required autofocus /></td>
          </tr>
          <tr>
            <td><strong>Account Number:</strong></td>
            <td><input name="customerno" type="text" id="customerno" size="50" value="<?php echo $selectedCN; ?>" placeholder="Please enter a value" required /></td>
          </tr>
          <tr>
            <td><strong>Contact Name:</strong></td>
            <td><input name="customercontact" type="text" id="customercontact" size="50" value="<?php echo $selectedCC; ?>" placeholder="Please enter a value" required /></td>
          </tr>
          <tr>
            <td><strong>Phone Number:</strong></td>
            <td><input name="customerphone" type="text" id="customerphone" size="50" value="<?php echo $selectedCP; ?>" placeholder="Please enter a value" required /></td>
          </tr>
          <tr>
            <td><strong>Email Address:</strong></td>
            <td><input name="customeremail" type="text" id="customeremail" size="50" value="<?php echo $selectedCE; ?>" placeholder="No email address? Type no@email.com" required /></td>
          </tr>
          <tr>
            <td><strong>Notes:</strong></td>
            <td><textarea name="notes" id="notes" cols="60" rows="4"><?php echo $selectedCNO; ?></textarea></td>
          </tr>  
          </tbody>
         </table>
        
    <div class="text-center">
      <input type="submit" name="submit" id="submit" value="Add to database & return to customer list" class="btn btn-primary"></form>
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
if (isset($_POST['customername']) && 
	isset($_POST['customerno']) && 
	isset($_POST['customercontact']) &&
	isset($_POST['customerphone']) &&
	isset($_POST['customeremail']) &&
	isset($_POST['notes']) &&
	$_POST['submit']) {
		// Clean the POST data
		$customername = $global->Clean($_POST['customername']);
		$customerno = $global->Clean($_POST['customerno']);
		$customercontact = $global->Clean($_POST['customercontact']);
		$customerphone = $global->Clean($_POST['customerphone']);
		$customeremail = $global->Clean($_POST['customeremail']);
		$notes = $global->textareaClean($_POST['notes']);
		
		// Make sure these values are not blank
		if($customername == NULL	|| $customername == " ") {
			alertERR("Customer name cannot be blank.");
		} elseif(!filter_var($customeremail, FILTER_VALIDATE_EMAIL)) {
			alertERR("Please enter a valid email address.");
		} elseif($customerno == NULL || $customerno == " ") {
			alertERR("Customer number cannot be blank.");
		} elseif($customerphone == NULL || $customerphone == " ") {
			alertERR("Customer phone cannot be blank."); 
		} else {
		
		// Update the database
		$sql->insertQuery($querys->insertCustomer($customername, $customerno, $customercontact, $customerphone, $customeremail, $notes));

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
