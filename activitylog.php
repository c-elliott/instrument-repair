<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: activitylog.php
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
    <title>Instrument Repair Portal : Activity Log</title>
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
            <li><a href="tradeinjoblist.php"><i class="fa fa-medkit"></i> Trade-Ins</a></li>
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
<strong>Activity Log</strong><br /><br />    
        <table class="table table-bordered table-hover table-condensed">
          <thead>
          <tr>
            <td><strong>ID</strong></td>
            <td><strong>Action</strong></td>
            <td><strong>Date</strong></td>
            <td><strong>User</strong></td>
          </tr>
          </thead>
          <tbody>
<?php


//
// START Pagination
//

// Here we have the total row count
$rows = $sql->runNumRowsQuery($querys->getActivityList());

// This is the number of results we want displayed per page
$page_rows = 15;

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
$jobs = $sql->fetchWhileRows($querys->getPaginationTable('activitylog', 'aid', 'DESC', $limit));

// Establish the $paginationCtrls variable
$paginationCtrls = '';

// If there is more than 1 page worth of results
if($last != 1){
	/* First we check if we are on page one. If we are then we don't need a link to 
	   the previous page or the first page so we do nothing. If we aren't then we
	   generate links to the first page, and to the previous page. */
	if ($pagenum > 1) {
		$paginationCtrls .= '<li><a href="activitylog.php?page=1"> First </a></li>';
        $previous = $pagenum - 1;
		$paginationCtrls .= '<li><a href="activitylog.php?page='.$previous.'"> Previous </a></li>';
		// Render clickable number links that should appear on the left of the target page number
		for($ib = $pagenum-2; $ib < $pagenum; $ib++){
			if($ib > 0){
				$paginationCtrls .= '<li><a href="activitylog.php?page='.$ib.'">'.$ib.'</a></li>';
			}
	    }
    }
	// Render the target page number, but without it being a link
	$paginationCtrls .= '<li class="active"><a href="#"> '.$pagenum.' </a></li>';
	// Render clickable number links that should appear on the right of the target page number
	for($ib = $pagenum+1; $ib <= $last; $ib++){
		$paginationCtrls .= '<li><a href="activitylog.php?page='.$ib.'">'.$ib.'</a></li>';
		if($ib >= $pagenum+3){
			break;
		}
	}
	// This does the same as above, only checking if we are on the last page, and then generating the "Next"
    if ($pagenum != $last) {
        $next = $pagenum + 1;
        $paginationCtrls .= '<li><a href="activitylog.php?page='.$next.'"> Next </a></li>';
        $paginationCtrls .= '<li><a href="activitylog.php?page='.$last.'"> Last ('.$last.') </a></li>';
    }
}

//
// End Pagination
//

// Begin loop and populate form
$i = 0;
while ($i < count($jobs)) {

// Get users full name
$user = $sql->runArrayQuery($querys->getUser($jobs[$i]['uid']));
// Populate the table
echo "	<tr>";
echo "            <td>" . $jobs[$i]['aid'] . "</td>";
echo "            <td>" . $jobs[$i]['action'] . "</td>";
echo "            <td>" . $jobs[$i]['date'] . "</td>";
echo "            <td>" . $user['firstname'] . " " . $user['lastname'] . "</td>";
echo "	</tr>";

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
 	popupWindow = window.open(
 		url,'popUpWindow','height=750,width=750,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes')
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
