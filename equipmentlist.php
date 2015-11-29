<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: equipmentlist.php
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

// Delete an existing equipment if we recieve GET info
if(isset($_GET['deleteid'])) {
	if(is_numeric($_GET['deleteid'])) {
       $rawequipmentid = $_GET['deleteid'];
       $equipmentid = $global->Clean($rawequipmentid);	
	   $sql->removeQuery($querys->removeEquipment($equipmentid));
	   echo '<meta http-equiv="refresh" content="0; url=equipmentlist.php" />';
	} else {
	   echo '<meta http-equiv="refresh" content="0; url=equipmentlist.php" />';
	}
} // End if GET is Set

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrument Repair Portal : Equipment List</title>
    <link href="files/css/bootstrap.min.css" rel="stylesheet">
    <link href="files/css/font-awesome.min.css" rel="stylesheet">
    <script src="files/js/jquery-1.10.2.min.js"></script>
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
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="joblist.php"><i class="fa fa-wrench"></i> Repairs</a></li>
            <li><a href="tradeinjoblist.php"><i class="fa fa-medkit"></i> Trade-Ins</a></li>
            <li><a href="archivejoblist.php"><i class="fa fa-archive"></i> Archive</a></li>
            <li><a href="customerlist.php"><i class="fa fa-users"></i> Customers</a></li>
            <li class="active"><a href="equipmentlist.php"><i class="fa fa-list-alt"></i> Equipment</a></li>
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
    
         <form method="post" action="">
         <input name="searchitem" type="text" id="searchitem" size="30" placeholder="Search for equipment" />
         <select name="searchtype" id="searchtype">
         <option value="1">Equipment Name</option>
         </select>
         <input type="submit" name="searchsubmit" id="searchsubmit" value="Search" class="btn btn-primary btn-xs"/>
         </form>
         <br />
         
        <table class="table table-bordered table-hover table-striped tablesorter">
          <thead>
          <tr>
            <td><strong>ID</strong></td>
            <td><strong>Equipment</strong></td>
            <td><strong>Action</strong></td>
          </tr>
          </thead>
          <tbody>
<?php
// Search function START
// Check to see if we recieved all fields from POST
if (isset($_POST['searchtype']) && 
	isset($_POST['searchitem']) && 
	$_POST['searchsubmit']) {
	
		// Clean the POST data
		$searchtype = $global->Clean($_POST['searchtype']);
		$searchitem = $global->Clean($_POST['searchitem']);

		// Make sure these values are not blank
		if($searchtype == NULL	|| $searchtype == " ") {
			echo "No search results found. I didn't have anything to search for. <br /><br />";
		} elseif($searchitem == NULL || $searchitem == " ") {
			echo "No search results found. I didn't have anything to search for. <br /><br />";
		} else {
		
		  // Search by account number
		  if ($searchtype == 1) {
		    $equipment = $sql->fetchWhileRows($querys->searchEquipmentName($searchitem));
			echo "<strong>Search Results:</strong> <br /><br />";
		    // If we returned no results
		    if ($equipment == NULL) {
		    echo "No search results found. <br /><br />";
		    }
		  }
		  
		} // End null checks
} else {
//
// START Pagination
//

// Here we have the total row count
$rows = $sql->runNumRowsQuery($querys->getEquipmentList());

// This is the number of results we want displayed per page
$page_rows = 10;

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
$equipment = $sql->fetchWhileRows($querys->getPaginationTable('equipment', 'id', 'ASC', $limit));

// Establish the $paginationCtrls variable
$paginationCtrls = '';

// If there is more than 1 page worth of results
if($last != 1){
	/* First we check if we are on page one. If we are then we don't need a link to 
	   the previous page or the first page so we do nothing. If we aren't then we
	   generate links to the first page, and to the previous page. */
	if ($pagenum > 1) {
		$paginationCtrls .= '<li><a href="equipmentlist.php?page=1"> First </a></li>';
        $previous = $pagenum - 1;
		$paginationCtrls .= '<li><a href="equipmentlist.php?page='.$previous.'"> Previous </a></li>';
		// Render clickable number links that should appear on the left of the target page number
		for($ib = $pagenum-2; $ib < $pagenum; $ib++){
			if($ib > 0){
				$paginationCtrls .= '<li><a href="equipmentlist.php?page='.$ib.'">'.$ib.'</a></li>';
			}
	    }
    }
	// Render the target page number, but without it being a link
	$paginationCtrls .= '<li class="active"><a href="#"> '.$pagenum.' </a></li>';
	// Render clickable number links that should appear on the right of the target page number
	for($ib = $pagenum+1; $ib <= $last; $ib++){
		$paginationCtrls .= '<li><a href="equipmentlist.php?page='.$ib.'">'.$ib.'</a></li>';
		if($ib >= $pagenum+3){
			break;
		}
	}
	// This does the same as above, only checking if we are on the last page, and then generating the "Next"
    if ($pagenum != $last) {
        $next = $pagenum + 1;
        $paginationCtrls .= '<li><a href="equipmentlist.php?page='.$next.'"> Next </a></li>';
        $paginationCtrls .= '<li><a href="equipmentlist.php?page='.$last.'"> Last ('.$last.') </a></li>';
    }
}

//
// End Pagination
//

} // Closing bracket from search function

// Begin loop and populate form
$i = 0;
while ($i < count($equipment)) {

echo "          <tr>";
echo "            <td>" . $equipment[$i]['id'] . "</td>";
echo "            <td>" . $equipment[$i]['equipment'] . "</td>";
// USERLEVELS : Only available for userlevel 2 and above
if ($_SESSION['userlevel'] >= 2) {

echo "            <td><a href=\"JavaScript:newPopup('equipment_manage.php?id=" . $equipment[$i]['id'] . "');\" ><button type=\"button\" class=\"btn btn-primary btn-xs\">Details</button></a> <a href=\"equipmentlist.php?deleteid=" . $equipment[$i]['id'] . "\" onclick=\"return confirm('Are you sure you want to delete Equipment ID: " . $equipment[$i]['id'] . "?')\"><button type=\"button\" class=\"btn btn-danger btn-xs\">Delete</button></a></td>";
echo "          </tr>";
// USERLEVELS : Allow userlevel 1 to see details
} elseif ($_SESSION['userlevel'] == 1) {
	echo "            <td><a href=\"JavaScript:newPopup('equipment_manage.php?id=" . $equipment[$i]['id'] . "');\" ><button type=\"button\" class=\"btn btn-primary btn-xs\">Details</button></a></td>";
	echo "          </tr>";
} else {
echo "            <td>-</td>";
echo "            </tr>";
};

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
echo "<a href=\"JavaScript:newPopup('equipment_add.php');\" ><button type\"button\" class=\"btn btn-primary\">Add New Equipment</button></a>";
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
} else {
header('Location: login.php?err_session');
};
?>
