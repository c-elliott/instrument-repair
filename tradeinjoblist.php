<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: tradeinjoblist.php
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

// Delete an existing job if we recieve GET info
if(isset($_GET['deleteid'])) {
	if(is_numeric($_GET['deleteid'])) {
       $rawjobid = $_GET['deleteid'];
       $jobid = $global->Clean($rawjobid);	
	   $sql->removeQuery($querys->removeJob($jobid));
	// Success window
	echo '<meta http-equiv="refresh" content="0; url=joblist.php" />';
	
	} // End If is Int
	else {
	echo '<meta http-equiv="refresh" content="0; url=joblist.php" />';
	}
} // End if GET is Set
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrument Repair Portal : Job List</title>
    <link href="files/css/bootstrap.min.css" rel="stylesheet">
    <link href="files/css/font-awesome.min.css" rel="stylesheet">
    <script src="files/js/jquery-1.10.2.min.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="files/js/html5shiv.js"></script>
      <script src="files/js/respond.min.js"></script>
    <![endif]-->
	<style type="text/css">
	.rowflag {
		background-color: #F49D91 !important;
	}
	.flagtxt {
		color: #F49D91;
		font-weight: bold;
	}
        .rowflag-info {
                background-color: #FE9A2E !important;
        }
        .flagtxt-info {
                color: #FE9A2E;
                font-weight: bold;
        }
	</style>
  </head>

  <body>
  
    <div class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <img src="files/logo.png" alt="<?php echo $PRODUCT_HEADER; ?>">
          &nbsp;&nbsp;&nbsp;
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="joblist.php"><i class="fa fa-wrench"></i> Repairs</a></li>
            <li class="active"><a href="tradeinjoblist.php"><i class="fa fa-medkit"></i> Trade-Ins</a></li>
            <li><a href="archivejoblist.php"><i class="fa fa-archive"></i> Archive</a></li>
            <li><a href="customerlist.php"><i class="fa fa-users"></i> Customers</a></li>
            <li><a href="equipmentlist.php"><i class="fa fa-list-alt"></i> Equipment</a></li>
            <li><a href="userlist.php"><i class="fa fa-key"></i> Users</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="myaccount.php"><i class="fa fa-user"></i> Profile</a></li>
            <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container">
    
         Note: This page shows outstanding trade-in and perm-rep only.<br /><br />
        <table class="table table-bordered table-hover table-condensed">
          <thead>
          <tr>
            <td><strong>ID</strong></td>
            <td><strong>Customer</strong></td>
            <td><strong>Equipment</strong></td>
            <td><strong>S/N</strong></td>
            <td><strong>Date Recv</strong></td>
            <td><strong>Service Order</strong></td>
            <td><strong>Loan</strong></td>
            <td><strong>Type</strong></td>
            <td><strong>Status</strong></td>
            <td><strong>Cust Updated</strong></td>
            <td><strong>Engineer</strong></td>
            <td><strong>Action</strong></td>
          </tr>
          </thead>
          <tbody>
<?php

//
// START Pagination
//

// Here we have the total row count
$rows = $sql->runNumRowsQuery($querys->getTradeinJobList());

// This is the number of results we want displayed per page
$page_rows = 30;

// This tells us the page number of our last page
$last = ceil($rows/$page_rows);

// This makes sure $last cannot be less than 1
if($last < 1){
	$last = 1;
}

// Establish the $pagenum variable
$pagenum = 1;

// Get pagenum from URL vars if it is present, else it is = 1
if(isset($_GET['page'])){
	$pagenum = preg_replace('#[^0-9]#', '', $_GET['page']);
}

// This makes sure the page number isn't below 1, or more than our $last page
if ($pagenum < 1) { 
    $pagenum = 1; 
} else if ($pagenum > $last) { 
    $pagenum = $last; 
}

// This sets the range of rows to query for the chosen $pagenum
$limit = 'LIMIT ' .($pagenum - 1) * $page_rows .',' .$page_rows;

// This is your query again, it is for grabbing just one page worth of rows
$jobs = $sql->fetchWhileRows($querys->getPaginationTableOpenTradeIN('jobs', 'id', 'ASC', $limit));

// Establish the $paginationCtrls variable
$paginationCtrls = '';

// If there is more than 1 page worth of results
if($last != 1){
	/* First we check if we are on page one. If we are then we don't need a link to 
	   the previous page or the first page so we do nothing. If we aren't then we
	   generate links to the first page, and to the previous page. */
	if ($pagenum > 1) {
		$paginationCtrls .= '<li><a href="tradeinjoblist.php?page=1"> First </a></li>';
        $previous = $pagenum - 1;
		$paginationCtrls .= '<li><a href="tradeinjoblist.php?page='.$previous.'"> Previous </a></li>';
		// Render clickable number links that should appear on the left of the target page number
		for($ib = $pagenum-2; $ib < $pagenum; $ib++){
			if($ib > 0){
				$paginationCtrls .= '<li><a href="tradeinjoblist.php?page='.$ib.'">'.$ib.'</a></li>';
			}
	    }
    }
	// Render the target page number, but without it being a link
	$paginationCtrls .= '<li class="active"><a href="#"> '.$pagenum.' </a></li>';
	// Render clickable number links that should appear on the right of the target page number
	for($ib = $pagenum+1; $ib <= $last; $ib++){
		$paginationCtrls .= '<li><a href="tradeinjoblist.php?page='.$ib.'">'.$ib.'</a></li>';
		if($ib >= $pagenum+3){
			break;
		}
	}
	// This does the same as above, only checking if we are on the last page, and then generating the "Next"
    if ($pagenum != $last) {
        $next = $pagenum + 1;
        $paginationCtrls .= '<li><a href="tradeinjoblist.php?page='.$next.'"> Next </a></li>';
        $paginationCtrls .= '<li><a href="tradeinjoblist.php?page='.$last.'"> Last ('.$last.') </a></li>';
    }
}

//
// End Pagination
//

// Begin loop and populate form
$i = 0;
while ($i < count($jobs)) {

// IF START - Prevent closed jobs appearing.
if ($jobs[$i]['closed'] == 0){

// Get friendly names for customer, equipment and assigned engineer
$dbCustomerName = $sql->runArrayQuery($querys->getCustomerName($jobs[$i]['cid']));
$dbEquipmentName = $sql->runArrayQuery($querys->getEquipmentName($jobs[$i]['iid']));
$dbUserName = $sql->runArrayQuery($querys->getUserName($jobs[$i]['uid']));

// Get daterecv in YYYY-MM-DD format + 5 Days
$daterecv_explode = explode('/',$jobs[$i]['daterecv'],3);
$daterecvRes = "". $daterecv_explode[2] ."-". $daterecv_explode[1] ."-". $daterecv_explode[0] ."";
$daterecvCalc = strtotime ( '+5 day' , strtotime ( $daterecvRes ) ) ;
$daterecv = date ( 'Y-m-d' , $daterecvCalc );

// Get custupdated in YYYY-MM-DD format + 14 Days
$custupdated_explode = explode('/',$jobs[$i]['custupdated'],3);
$custupdatedRes = "". $custupdated_explode[2] ."-". $custupdated_explode[1] ."-". $custupdated_explode[0] ."";
$custupdatedCalc = strtotime ( '+14 day' , strtotime ( $custUpdatedRes ) ) ;
$custupdated = date ( 'Y-m-d' , $custupdatedCalc );

// Today's date
$datetoday = date("Y-m-d");

// Flag jobs that have been waiting initial check over 5 days
if ($datetoday >= $daterecv && $jobs[$i]['repairstatus'] == 1) {
	echo "          <tr class=\"rowflag\">";
// Flag job if it needs a status update from servicemed
} elseif ($jobs[$i]['updateflag'] == 1) {
	echo "          <tr class=\"rowflag-info\">";
} else {
	echo "          <tr>";
};

// Rest of the table
echo "            <td>" . $jobs[$i]['id'] . "</td>";
echo "            <td><a href=\"JavaScript:newPopup('customer_manage.php?id=" . $jobs[$i]['cid'] . "');\" >" . $dbCustomerName['customername'] . "</a>";
echo "            <td>" . $dbEquipmentName['equipment'] . "</td>";
echo "            <td>" . $jobs[$i]['serialno'] . "</td>";
echo "            <td>" . $jobs[$i]['daterecv'] . "</td>";
echo "            <td>" . $jobs[$i]['serviceord'] . "</td>";

// Display correct loan type
if ($jobs[$i]['repairloan'] == 1) {
	echo "<td>YES</td>";
} elseif ($jobs[$i]['repairloan'] == 0) {
	echo "<td>NO</td>";
};

// Display correct repair type
if ($jobs[$i]['repairtype'] == 1) {
	echo "<td>WARRANTY (REPAIR)</td>";
} elseif ($jobs[$i]['repairtype'] == 2) {
	echo "<td>WARRANTY (MANUFACTURER)</td>";
} elseif ($jobs[$i]['repairtype'] == 3) {
	echo "<td>CONTRACT (PPM)</td>";
} elseif ($jobs[$i]['repairtype'] == 4) {
	echo "<td>CONTRACT (ALL-INC)</td>";
} elseif ($jobs[$i]['repairtype'] == 5) {
	echo "<td>CHARGEABLE</td>";
} elseif ($jobs[$i]['repairtype'] == 6) {
        echo "<td>TRADE-IN</td>";
} elseif ($jobs[$i]['repairtype'] == 7) {
        echo "<td>PERM-REP</td>";
}

// Display correct repair status
if ($jobs[$i]['repairstatus'] == 1) {
echo "<td>WAIT INITIAL CHECK</td>";
} elseif ($jobs[$i]['repairstatus'] == 2) {
echo "<td>IN-HOUSE REPAIR</td>";
} elseif ($jobs[$i]['repairstatus'] == 3) {
echo "<td>EXTERNAL REPAIR</td>";
} elseif ($jobs[$i]['repairstatus'] == 4) {
echo "<td>REPAIR COMPLETED</td>";
} elseif ($jobs[$i]['repairstatus'] == 5) {
	echo "<td>WAIT LOAN RETURN</td>";
} elseif ($jobs[$i]['repairstatus'] == 6) {
	echo "<td>EXTERNAL REPAIR (TEM)</td>";
} elseif ($jobs[$i]['repairstatus'] == 7) {
	echo "<td>EXTERNAL REPAIR (THQ)</td>";
} elseif ($jobs[$i]['repairstatus'] == 8) {
	echo "<td>EXTERNAL REPAIR (EVANS)</td>";
} elseif ($jobs[$i]['repairstatus'] == 9) {
	echo "<td>AWAITING RESPONSE</td>";
} elseif ($jobs[$i]['repairstatus'] == 10) {
	echo "<td>ON HOLD</td>";
} elseif ($jobs[$i]['repairstatus'] == 11) {
	echo "<td>RMA APPLIED</td>";
} elseif ($jobs[$i]['repairstatus'] == 12) {
	echo "<td>AWAITING PARTS</td>";
} elseif ($jobs[$i]['repairstatus'] == 13) {
        echo "<td>PO RECEIVED</td>";
};

// Diplay NEVER if customer not updated
if ($jobs[$i]['custupdated'] == NULL || $jobs[$i]['custupdated'] == 0) {
echo "            <td>NEVER</td>";
} else {
echo "            <td>" . $jobs[$i]['custupdated'] . "</td>";
};

echo "            <td>" . $dbUserName['firstname'] ." ". $dbUserName['lastname'] . "</td>";

// USERLEVELS : Only available for userlevel 2 and above
if ($_SESSION['userlevel'] >= 2) {
	echo "            <td><a href=\"JavaScript:newPopup('job_manage.php?id=" . $jobs[$i]['id'] . "');\" ><button type=\"button\" class=\"btn btn-primary btn-xs\">Details</button></a> <a href=\"joblist.php?deleteid=" . $jobs[$i]['id'] . "\" onclick=\"return confirm('Are you sure you want to delete Job ID: " . $jobs[$i]['id'] . "?')\"><button type=\"button\" class=\"btn btn-danger btn-xs\">Delete</button></a></td>";
	echo "          </tr>";
// USERLEVELS : Allow userlevel 1 to see details
} elseif ($_SESSION['userlevel'] == 1) {
	echo "            <td><a href=\"JavaScript:newPopup('job_manage.php?id=" . $jobs[$i]['id'] . "');\" ><button type=\"button\" class=\"btn btn-primary btn-xs\">Details</button></a></td>";
	echo "          </tr>";
} else {
echo "            <td>-</td>";
echo "            </tr>";
};

// IF END - Prevent closed jobs appearing.
}

$i++;
}
?>
          </tbody>
        </table>
        
        <ul class="pagination">
<?php echo $paginationCtrls; ?>
</ul>

    <div class="text-center">
      <?php
// USERLEVELS : Only available for userlevel 2 and above
if ($_SESSION['userlevel'] >= 2) {
echo "<a href=\"JavaScript:newPopup('job_add.php');\" ><button type\"button\" class=\"btn btn-primary\">Add New Job</button></a>";
};
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
// End session check
} else {
header('Location: login.php?err_session');
};
?>
