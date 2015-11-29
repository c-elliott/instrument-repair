<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: job_add.php
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

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrument Repair Portal : Add Job</title>
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
           <td colspan="2"><div class="text-center"><h3>Add Job</h3></div></td>
          </tr>
          </thead>
          <tbody>
          <tr>
     <form method="post" action="">
            <td><strong>Customer:</strong></td>
            <td><select name="customer" id="customer">
            <?php 
            // Get list of customers
            $customerList = $sql->fetchWhileRows($querys->getCustomerList());
            // Begin loop 
            $i = 0;
            	while ($i < count($customerList)) {
                     echo "<option value=\"" . $customerList[$i]['id'] . "\">" . $customerList[$i]['customername'] . "</option>";
                  $i++;
            }
            ?>
            </select>
            <a href="JavaScript:newPopup('customer_add.php');" ><button type="button" class="btn btn-primary btn-xs">Add Customer</button></a>  
	  </td>
          </tr>
          <tr>
            <td><strong>Equipment:</strong></td>
            <td><select name="equipment" id="equipment">
            <?php 
            // Get list of equipment
            $equipmentList = $sql->fetchWhileRows($querys->getEquipmentList());
            // Begin loop 
            $i = 0;
            	while ($i < count($equipmentList)) {
                     echo "<option value=\"" . $equipmentList[$i]['id'] . "\">" . $equipmentList[$i]['equipment'] . "</option>";
                  $i++;
            }
            ?>
            </select>
            <a href="JavaScript:newPopup('equipment_add.php');" ><button type="button" class="btn btn-primary btn-xs">Add Equipment</button></a>
            </td>
          </tr>
          <tr>
            <td><strong>Serial Number:</strong></td>
            <td><input name="serialno" type="text" id="serialno" size="25" required/></td>
          </tr>
          <tr>
            <td><strong>Service Order:</strong></td>
            <td><input name="serviceorder" type="text" id="serviceorder" size="25" required/> | External: <input name="externalno" type="text" id="externalno" size="25" /></td>
          </tr>
          <tr>
            <td><strong>Date Received:</strong></td>
            <td><input name="daterecv" type="text" id="daterecv" size="25" placeholder="DD/MM/YYYY" required/> | Loan Onsite: <select name="repairloan" id="repairloan">
                <option value="0" selected>NO</option>
                <option value="1">YES</option>
                </select></td>
          </tr>
          <tr>
            <td><strong>Type:</strong></td>
            <td><select name="repairtype" id="repairtype">
                <option value="1">WARRANTY (REPAIR)</option>
                <option value="2">WARRANTY (MANUFACTURER)</option>
                <option value="3">CONTRACT (PPM)</option>
		<option value="4">CONTRACT (ALL-INC)</option>
		<option value="5">CHARGEABLE</option>
		<option value="6">TRADE-IN</option>
		<option value="7">PERM-REP</option>
                </select>
            </td>
          </tr>      
          <tr>
            <td><strong>Status:</strong></td>
            <td><select name="repairstatus" id="repairstatus">
                <option value="1">WAIT INITIAL CHECK</option>
                <option value="2">IN-HOUSE REPAIR</option>
                <option value="3">EXTERNAL REPAIR (GENERAL)</option>
				<option value="6">EXTERNAL REPAIR (TEM)</option>
				<option value="7">EXTERNAL REPAIR (THQ)</option>
				<option value="8">EXTERNAL REPAIR (EVANS)</option>
                		<option value="4">REPAIR COMPLETED</option>
				<option value="5">WAIT LOAN RETURN</option>
				<option value="9">AWAITING RESPONSE</option>
				<option value="10">ON HOLD</option>
				<option value="11">RMA APPLIED</option>
				<option value="12">AWAITING PARTS</option>
				<option value="13">PO RECEIVED</option>
                </select>
            </td>
          </tr>
          <tr>
            <td><strong>Engineer:</strong></td>
            <td><select name="engineer" id="engineer">
            <?php 
            // Get list of engineers
            $engineerList = $sql->fetchWhileRows($querys->getUserList());
            // Begin loop 
            $i = 0;
            	while ($i < count($engineerList)) {
                     echo "<option value=\"" . $engineerList[$i]['uid'] . "\">" . $engineerList[$i]['firstname'] . " " . $engineerList[$i]['lastname'] . "</option>";
                  $i++;
            }
            ?>
            </select>
            </td>
          </tr>  
          <tr>
            <td><strong>Notes:</strong></td>
            <td><textarea name="notes" id="notes" cols="76" rows="4"></textarea></td>
          </tr>
          </tbody>
         </table>
        
    <div class="text-center">
      <input type="submit" name="submit" id="submit" value="Add to database & return to job list" class="btn btn-primary"></form>
      
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
  <script src="files/js/bootstrap.min.js"></script>
  <script type="text/javascript">
  // Popup window code
 function newPopup(url) {
        var rid = Math.floor((Math.random() * 100) + 1);
        popupWindow = window.open(
                url,rid,'height=750,width=750,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes')
 }
</script>

</body>
</html>
<?php

// Check to see if we recieved all fields from POST
if (isset($_POST['customer']) && 
	isset($_POST['equipment']) && 
	isset($_POST['serialno']) && 
	isset($_POST['serviceorder']) && 
	isset($_POST['daterecv']) &&
	isset($_POST['repairtype']) &&
	isset($_POST['repairstatus']) &&
	isset($_POST['engineer']) &&
	isset($_POST['notes']) &&
	isset($_POST['externalno']) &&
	isset($_POST['repairloan']) &&
	$_POST['submit']) {
	
		// Clean the POST data
		$customer = $global->Clean($_POST['customer']);
		$equipment = $global->Clean($_POST['equipment']);
		$serialno = $global->Clean($_POST['serialno']);
		$serviceorder = $global->Clean($_POST['serviceorder']);
		$daterecv = $global->Clean($_POST['daterecv']);
		$repairtype = $global->Clean($_POST['repairtype']);
		$repairstatus = $global->Clean($_POST['repairstatus']);
		$engineer = $global->Clean($_POST['engineer']);
		$notes = $global->textareaClean($_POST['notes']);
		$externalno = $global->Clean($_POST['externalno']);
		$repairloan = $global->Clean($_POST['repairloan']);

		// Make sure these values are not blank
		if($customer == NULL || $customer == " ") {
			alertERR("Customer cannot be blank.");
		} elseif($equipment == NULL || $equipment == " ") {
			alertERR("Equipment cannot be blank.");
		} elseif($serialno == NULL || $serialno == " ") {
			alertERR("Serial number cannot be blank."); 
		} elseif($serviceorder == NULL || $serviceorder == " ") {
			alertERR("Service order cannot be blank."); 
		} elseif($daterecv == NULL || $daterecv == " ") {
			alertERR("Date recieved cannot be blank."); 
		} elseif($repairtype == NULL || $repairtype == " ") {
			alertERR("Repair type cannot be blank."); 
		} elseif($repairstatus == NULL || $repairstatus == " ") {
			alertERR("Repair status cannot be blank."); 
		} elseif($engineer == NULL || $engineer == " ") {
			alertERR("Engineer cannot be blank."); 
		} elseif($repairloan == NULL || $repairloan == " ") {
			alertERR("Repair loan cannot be blank."); 
		} else {
		
		// Add new job to database
		$closed = "0";
		$sql->insertQuery($querys->insertJob($customer, $equipment, $serialno, $serviceorder, $daterecv, $repairtype, $repairstatus, $engineer, $notes, $closed, $externalno, $repairloan));
       
		// Log the change
                $today = date("d-m-Y H:i:s");
                $sql->insertQuery($querys->insertActivity("Created New Job - Customer ID: $customer", $today, $userDetails[uid]));
 
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
