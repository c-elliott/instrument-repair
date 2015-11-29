<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: job_manage.php
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

// Session checks
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

// Regenerate session
session_regenerate_id($currSessionID);
$_SESSION['id'] = session_id();
$updateUserSession = $sql->updateQuery($querys->updateUserSession($_SESSION['userid'], $_SESSION['id']));

// GET handling
if(isset($_GET['id']) && is_numeric($_GET['id'])) {

	// Make sure we have a job ID
	$jobid = $global->Clean($_GET['id']);
	$validjob = $sql->runNumRowsQuery($querys->getJob($jobid));

        // If customer ID is not valid, send back to customer list
	if ($validjob == NULL) {
	    echo '<meta http-equiv="refresh" content="0; url=joblist.php" />';

        // If customer ID is valid
	} else {
                // Get customer information
		$job = $sql->runArrayQuery($querys->getJob($jobid));


// Get freindly names for customer name and equipment name
$dbCustomer = $sql->runArrayQuery($querys->getCustomer($job['cid']));
$dbEquipment = $sql->runArrayQuery($querys->getEquipmentName($job['iid']));

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $PRODUCT_HEADER; ?> : Job ID <?php echo $jobid; ?> - <?php echo $dbCustomer['customername']; ?></title>
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
      
        <table class="table table-bordered table-hover table-striped tablesorter">
        <thead>
          <tr>
           <td colspan="2"><div class="text-center"><h3>Job Details</h3></div></td>
          </tr>
          </thead>
          <tbody>
          <tr>
          <form method="post" action="">
            <td><strong>Customer:</strong></td>
            <td><?php echo $dbCustomer['customername']; ?> | Account No: <?php echo $dbCustomer['customerno']; ?></td>
          </tr>
          <tr>
            <td><strong>Phone Number:</strong></td>
            <td><?php echo $dbCustomer['customerphone']; ?> | Contact Name: <?php echo $dbCustomer['customercontact']; ?></td>
          </tr>
          <tr>
            <td><strong>Email Address:</strong></td>
            <td><?php echo $dbCustomer['customeremail']; ?></td>
          </tr>
          <tr>
            <td><strong>Equipment:</strong></td>
            <td><?php echo $dbEquipment['equipment']; ?> | SN: <?php echo $job['serialno']; ?> | SO: <?php echo $job['serviceord']; ?> | EXTERNAL: <?php echo $job['externalno']; ?></td>
          </tr>
          <tr>
            <td><strong>Date Received:</strong></td>
            <td><?php echo $job['daterecv']; ?> | Loan Onsite: <select name="repairloan" id="repairloan">
                <option value="0" <?php if ($job['repairloan'] == 0) { echo "selected"; }; ?>>NO</option>
                <option value="1" <?php if ($job['repairloan'] == 1) { echo "selected"; }; ?>>YES</option>
                </select></td>
          </tr>
          <tr>
            <td><strong>Type</strong></td>
            <td><select name="repairtype" id="repairtype">
                <option value="1" <?php if ($job['repairtype'] == 1) { echo "selected"; }; ?>>WARRANTY (REPAIR)</option>
                <option value="2" <?php if ($job['repairtype'] == 2) { echo "selected"; }; ?>>WARRANTY (MANUFACTURER)</option>
                <option value="3" <?php if ($job['repairtype'] == 3) { echo "selected"; }; ?>>CONTRACT (PPM)</option>
		<option value="4" <?php if ($job['repairtype'] == 4) { echo "selected"; }; ?>>CONTRACT (ALL-INC)</option>
		<option value="5" <?php if ($job['repairtype'] == 5) { echo "selected"; }; ?>>CHARGEABLE</option>
		<option value="6" <?php if ($job['repairtype'] == 6) { echo "selected"; }; ?>>TRADE-IN</option>
		<option value="7" <?php if ($job['repairtype'] == 7) { echo "selected"; }; ?>>PERM-REP</option>
                </select></td>
          </tr>      
          <tr>
            <td><strong>Status</strong></td>
            <td><select name="repairstatus" id="repairstatus">
                <option value="1" <?php if ($job['repairstatus'] == 1) { echo "selected"; }; ?>>WAIT INITIAL CHECK</option>
                <option value="2" <?php if ($job['repairstatus'] == 2) { echo "selected"; }; ?>>IN-HOUSE REPAIR</option>
                <option value="3" <?php if ($job['repairstatus'] == 3) { echo "selected"; }; ?>>EXTERNAL REPAIR (GENERAL)</option>
				<option value="6" <?php if ($job['repairstatus'] == 6) { echo "selected"; }; ?>>EXTERNAL REPAIR (TEM)</option>
				<option value="7" <?php if ($job['repairstatus'] == 7) { echo "selected"; }; ?>>EXTERNAL REPAIR (THQ)</option>
				<option value="8" <?php if ($job['repairstatus'] == 8) { echo "selected"; }; ?>>EXTERNAL REPAIR (EVANS)</option>
                <option value="4" <?php if ($job['repairstatus'] == 4) { echo "selected"; }; ?>>REPAIR COMPLETED</option>
				<option value="5" <?php if ($job['repairstatus'] == 5) { echo "selected"; }; ?>>WAIT LOAN RETURN</option>
				<option value="9" <?php if ($job['repairstatus'] == 9) { echo "selected"; }; ?>>AWAITING RESPONSE</option>
				<option value="10" <?php if ($job['repairstatus'] == 10) { echo "selected"; }; ?>>ON HOLD</option>
				<option value="11" <?php if ($job['repairstatus'] == 11) { echo "selected"; }; ?>>RMA APPLIED</option>
				<option value="12" <?php if ($job['repairstatus'] == 12) { echo "selected"; }; ?>>AWAITING PARTS</option>
				<option value="13" <?php if ($job['repairstatus'] == 13) { echo "selected"; }; ?>>PO RECEIVED</option>
                </select> <strong>Engineer:</strong> <select name="engineer" id="engineer">
<?php 
// Get list of engineers
$engineerList = $sql->fetchWhileRows($querys->getUserList());
// Begin loop 
$i = 0;
	while ($i < count($engineerList)) {
      if ($job['uid'] == $engineerList[$i]['uid']) {
         echo "<option value=\"" . $engineerList[$i]['uid'] . "\"selected >" . $engineerList[$i]['firstname'] . " " . $engineerList[$i]['lastname'] . "</option>";
      } else {
         echo "<option value=\"" . $engineerList[$i]['uid'] . "\">" . $engineerList[$i]['firstname'] . " " . $engineerList[$i]['lastname'] . "</option>";
      };
      $i++;
}
?>
</select></td>
          </tr>
          <tr>
            <td><strong>Customer Last Updated:</strong></td>
            <td><?php if ($job['custupdated'] == NULL) { echo "NEVER"; } else { echo $job['custupdated']; }; ?></td>
          </tr>  
          <tr>
            <td><strong>Notes:</strong></td>
            <td><textarea name="notes" id="notes" cols="76" rows="4"><?php echo $job['notes']; ?></textarea></td>
          </tr>  
          <tr>
            <td><strong>Job Status:</strong></td>
            <td>Cust Updated Today:&nbsp; <select name="custupdated" id="custupdated">
                <option value="0"selected>NO</option>
                <option value="1">YES</option>
                </select>&nbsp;&nbsp;&nbsp;
                Status:&nbsp;
                <select name="closejob" id="closejob">
                <option value="0" <?php if ($job['closed'] == 0) { echo "selected"; }; ?>>OPEN</option>
                <option value="1" <?php if ($job['closed'] == 1) { echo "selected"; }; ?>>CLOSED</option>
                </select>&nbsp;&nbsp;&nbsp;
		Update Reqd:&nbsp;
                <select name="updateflag" id="updateflag">
                <option value="0" <?php if ($job['updateflag'] == 0) { echo "selected"; }; ?>>No</option>
                <option value="1" <?php if ($job['updateflag'] == 1) { echo "selected"; }; ?>>Yes</option>
                </select>
            </td>
          </tr>
		  
		  <?php
		  // If job is closed provide the closure date
		  if ($job['closed'] == 1) {
			echo "<tr>";
			echo "<td><strong>Closure Date:</strong></td>";
            echo "<td>". $job['closeddate'] . "</td>";
			echo "</tr>";
		  };
		  ?>
          </tbody>
         </table>
        
    <div class="text-center">
          <?php
			// USERLEVELS : Only available for userlevel 2 and above
			if ($_SESSION['userlevel'] >= 2) {
				echo "<input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Update & return to equipment list\" class=\"btn btn-primary\">";
			};
			?>
			</form>
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
if (isset($_POST['repairloan']) && 
	isset($_POST['repairtype']) && 
	isset($_POST['repairstatus']) && 
	isset($_POST['engineer']) && 
	isset($_POST['notes']) &&
	isset($_POST['custupdated']) &&
	isset($_POST['closejob']) &&
	isset($_POST['updateflag']) &&
	$_POST['submit']) {
	
		// Clean the POST data
		$repairloan = $global->Clean($_POST['repairloan']);
		$repairtype = $global->Clean($_POST['repairtype']);
		$repairstatus = $global->Clean($_POST['repairstatus']);
		$engineer = $global->Clean($_POST['engineer']);
		$notes = $global->textareaClean($_POST['notes']);
		$custupdated = $global->Clean($_POST['custupdated']);
		$closejob = $global->Clean($_POST['closejob']);
		$updateflag = $global->Clean($_POST['updateflag']);

		// Make sure these values are not blank
		if($repairloan == NULL	|| $repairloan == " ") {
			alertERR("Repair loan cannot be blank.");
		} elseif($repairtype == NULL || $repairtype == " ") {
			alertERR("Repair type cannot be blank.");
		} elseif($repairstatus == NULL || $repairstatus == " ") {
			alertERR("Repair status cannot be blank."); 
		} elseif($engineer == NULL || $engineer == " ") {
			alertERR("Engineer cannot be blank."); 
		} else {
		
		// Update the database
		$sql->updateQuery($querys->updateJob($jobid, $repairloan, $repairtype, $repairstatus, $engineer, $notes, $closejob, $updateflag));

		// Log the change
		$today = date("d-m-Y H:i:s");
		$custlog = $dbCustomer['customername'];
		$sql->insertQuery($querys->insertActivity("Saved Changes to Job ID: $jobid - $custlog", $today, $userDetails[uid]));
		
		// Update job closure date if we need to
		if ($closejob == 1) {
			$today = date("d-m-Y");
			$sql->updateQuery($querys->updateJobClosuredate($jobid, $today));

			// Log the change
                	$today = date("d-m-Y H:i:s");
                	$custlog = $dbCustomer['customername'];
                	$sql->insertQuery($querys->insertActivity("Closed Job ID: $jobid - $custlog", $today, $userDetails[uid]));
		}
        
		// Update the custupdated field if we need to
		if ($custupdated == 1) {
		   $today = date("d-m-Y");
		   $sql->updateQuery($querys->updateJobUpdated($jobid, $today));
		}
		
		// Close the window
		echo "<script type='text/javascript'>";
		echo "self.close();";
		echo "</script>";

} // End POST variable null checks
} // End POST variable isset checks
// End GET handling
}
} else {
echo '<meta http-equiv="refresh" content="0; url=customerlist.php" />';
}

// End Session handling
} else {
header('Location: login.php?err_session');
};
?>
