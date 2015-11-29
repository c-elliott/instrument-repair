<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: querys.php
*
*/
    	// Start the SQL queries class
	class querys {
	
	    	// Create a function to connect our queries to the SQL class
		function querys($sql) {
			$this->connect = $sql;
		}

        	// SQL queries begin below, no comments should be needed here

		function getPassword($username) {
			$query = "
			SELECT password FROM `users` 
			WHERE
			`username` = '". $username ."' 
			LIMIT 1; 
			";
			return $query;
		}

                function getUserFirstname($username) {
                        $query = "
                        SELECT firstname FROM `users`
                        WHERE
                        `username` = '". $username ."'
                        LIMIT 1;
                        ";
                        return $query;
                }
		
		function getUserDetails($username, $password) {
			$query = "
			SELECT * FROM `users`
			WHERE
			`username` = '". $username ."'
			AND
			`password` = '". $password ."'
			LIMIT 1;
			";
			return $query;
		}
                
		function getUserDetailsWoPass($uid) {
			$query = "
			SELECT * FROM `users`
			WHERE
			`uid` = '". $uid ."';
			";
			return $query;
		}

		function updateUserSession($userid, $sessionid) {
			$query = "
			UPDATE `users`
			SET
			`sessionid` = '". $sessionid ."'
			WHERE
			`uid` =  '". $userid ."';";
			return $query;
		}

		function updateUserAgent($userid, $useragent) {
			$query = "
			UPDATE `users`
			SET
			`useragent` = '". $useragent ."'
			WHERE
			`uid` =  '". $userid ."';";
			return $query;
		}

		function insertAuthlog($userid, $type, $ip, $date) {
			$query = "
			INSERT INTO `auth`
			(`uid`, `type`, `ip`, `date`) 
			VALUES
			('". $userid ."', '". $type ."', '" . $ip . "', '" . $date . "');";
			return $query;
		}

		function getFailedAuthlog($uid) {
			$query = "
			SELECT * from `auth`
			 WHERE
			`uid` = '". $uid ."'
			AND
			`type` = '0'";
			return $query;
		}

		function getCustomerList() {
			$query = "
			SELECT * FROM `customers`
			ORDER BY customername ASC";
			return $query;
		}

		function getActivityList() {
			$query = "
			SELECT * FROM `activitylog`
			ORDER BY aid ASC";
			return $query;
		}
		
		function getJobList() {
			$query = "
			SELECT * FROM `jobs`
			WHERE
			`closed` = '0'
			AND `repairtype` != '6'
			AND `repairtype` != '7'";
			return $query;
		}

                function getTradeinJobList() {
                        $query = "
                        SELECT * FROM `jobs`
                        WHERE
                        `closed` = '0'
			AND `repairtype` = '6' OR `repairtype` = '7'";
                        return $query;
                }
		
		function getArchiveJobList() {
			$query = "
			SELECT * FROM `jobs`
			WHERE
			`closed` = '1'";
			return $query;
		}
				
		function getCustomer($customerid) {
			$query = "
			SELECT * FROM `customers`
			WHERE `id` = '". $customerid ."' ";
			return $query;
		}
		
		function getJob($jobid) {
			$query = "
			SELECT * FROM `jobs`
			WHERE `id` = '". $jobid ."' ";
			return $query;
		}

		function getCustomerName($customerid) {
			$query = "
			SELECT `customername` FROM `customers`
			WHERE `id` = '". $customerid ."' ";
			return $query;
		}
		
		function getEquipmentName($equipmentid) {
			$query = "
			SELECT `equipment` FROM `equipment`
			WHERE `id` = '". $equipmentid ."' ";
			return $query;
		}
		
		function getUserName($userid) {
			$query = "
			SELECT `firstname`, `lastname` FROM `users`
			WHERE `uid` = '". $userid ."' ";
			return $query;
		}
		
		function getEquipment($equipmentid) {
			$query = "
			SELECT * FROM `equipment`
			WHERE `id` = '". $equipmentid ."' ";
			return $query;
		}

		function getUser($puserid) {
			$query = "
			SELECT * FROM `users`
			WHERE `uid` = '". $puserid ."' ";
			return $query;
		}

		function updateCustomer($customerid, $customername, $customerno, $customercontact, $customerphone, $customeremail, $notes) {
			$query = "
			UPDATE `customers`
			SET
			`customername` = '". $customername ."',
			`customerno` = '". $customerno ."',
			`customercontact` = '". $customercontact ."',
			`customerphone` = '". $customerphone ."',
			`customeremail` = '". $customeremail ."',
			`notes` = '". $notes ."'
			WHERE
			`id` = '". $customerid ."';";
			return $query;
		}
		
		function updateJob($jobid, $repairloan, $repairtype, $repairstatus, $engineer, $notes, $jobclosed, $updateflag) {
			$query = "
			UPDATE `jobs`
			SET
			`repairloan` = '". $repairloan ."',
			`repairtype` = '". $repairtype ."',
			`repairstatus` = '". $repairstatus ."',
			`uid` = '". $engineer ."',
			`notes` = '". $notes ."',
			`closed` = '". $jobclosed ."',
			`updateflag` = '". $updateflag ."'
			WHERE
			`id` = '". $jobid ."';";
			return $query;
		}
		
		function updateJobUpdated($jobid, $today) {
			$query = "
			UPDATE `jobs`
			SET
			`custupdated` = '". $today ."'
			WHERE
			`id` = '". $jobid ."';";
			return $query;
		}
		
		function updateJobClosuredate($jobid, $today) {
			$query = "
			UPDATE `jobs`
			SET
			`closeddate` = '". $today ."'
			WHERE
			`id` = '". $jobid ."';";
			return $query;
		}
		
		function updateUserWOPass($userid, $firstname, $lastname, $email) {
			$query = "
			UPDATE `users`
			SET
			`firstname` = '". $firstname ."',
			`lastname` = '". $lastname ."',
			`email` = '". $email ."'
			WHERE
			`uid` = '". $userid ."';";
			return $query;
		}
		
		function updateUser($userid, $firstname, $lastname, $email, $password) {
			$query = "
			UPDATE `users`
			SET
			`firstname` = '". $firstname ."',
			`lastname` = '". $lastname ."',
			`email` = '". $email ."',
			`password` = '". $password ."'
			WHERE
			`uid` = '". $userid ."';";
			return $query;
		}
		
		function updateEquipment($equipmentid, $equipmentname) {
			$query = "
			UPDATE `equipment`
			SET
			`equipment` = '". $equipmentname ."'
			WHERE
			`id` = '". $equipmentid ."';";
			return $query;
		}
		
		function updateUserLastlog($userid, $lastloginip, $lastlogin) {
			$query = "
			UPDATE `users`
			SET
			`lastloginip` = '". $lastloginip ."',
			`lastlogin` = '". $lastlogin ."'
			WHERE
			`uid` = '". $userid ."';";
			return $query;
		}
		
		function getEquipmentList() {
			$query = "
			SELECT * FROM `equipment`
			ORDER BY equipment ASC";
			return $query;
		}
          
		function getPaginationTable($dbtable, $orderby, $ascdesc, $limit) {
			$query = "
			SELECT * FROM `".$dbtable."`
			ORDER BY `".$orderby."` ".$ascdesc." ".$limit.";";
			return $query;
		}

		function getPaginationTableArc($dbtable, $orderby, $ascdesc, $limit) {
			$query = "
			SELECT * FROM `".$dbtable."`
			WHERE `closed` = '1'
			ORDER BY `".$orderby."` ".$ascdesc." ".$limit.";";
			return $query;
		}
		
		function getPaginationTableOpen($dbtable, $orderby, $ascdesc, $limit) {
			$query = "
			SELECT * FROM `".$dbtable."`
			WHERE `closed` = '0'
			AND `repairtype` != '6'
			AND `repairtype` != '7'
			ORDER BY `".$orderby."` ".$ascdesc." ".$limit.";";
			return $query;
		}
		
                function getPaginationTableOpenTradeIN($dbtable, $orderby, $ascdesc, $limit) {
                        $query = "
                        SELECT * FROM `".$dbtable."`
                        WHERE `closed` = '0'
			AND `repairtype` = '6' OR `repairtype` = '7'
                        ORDER BY `".$orderby."` ".$ascdesc." ".$limit.";";
                        return $query;
                }

		function removeCustomer($customerid) {
			$query = "
			DELETE FROM `customers`
			WHERE
			`id` = '".$customerid."';";
			return $query;
		}
		
		function removeJob($jobid) {
			$query = "
			DELETE FROM `jobs`
			WHERE
			`id` = '".$jobid."';";
			return $query;
		}
		
		function insertCustomer($customername, $customerno, $customercontact, $customerphone, $customeremail, $notes) {
			$query = "
			INSERT INTO `customers`
			(`customername`, `customerno`, `customercontact`, `customerphone`, `customeremail`, `notes`)
			VALUES
			('". $customername ."', '". $customerno ."', '" . $customercontact . "', '" . $customerphone . "', '" . $customeremail. "', '" . $notes. "');";
			return $query;
		}
		
		function insertJob($customer, $equipment, $serialno, $serviceorder, $daterecv, $repairtype, $repairstatus, $engineer, $notes, $closed, $externalno, $repairloan) {
			$query = "
			INSERT INTO `jobs`
			(`cid`, `iid`, `serialno`, `serviceord`, `daterecv`, `repairtype`, `repairstatus`, `uid`, `notes`, `closed`, `externalno`, `repairloan`)
			VALUES
			('". $customer ."', '". $equipment ."', '" . $serialno . "', '" . $serviceorder . "', '" . $daterecv. "', '" . $repairtype ."', '" . $repairstatus ."', '" . $engineer ."', '" . $notes ."', '" . $closed ."', '" . $externalno ."', '" . $repairloan ."');";
			return $query;
		}

		function removeEquipment($equipmentid) {
			$query = "
			DELETE FROM `equipment`
			WHERE
			`id` = '".$equipmentid."';";
			return $query;
		}

		function insertEquipment($equipment) {
			$query = "
			INSERT INTO `equipment`
			(`equipment`)
			VALUES
			('". $equipment ."');";
			return $query;
		}

		function insertActivity($action, $date, $uid) {
			$query = "
			INSERT INTO `activitylog`
			(`action`, `date`, `uid`)
			VALUES
			('". $action ."', '". $date ."', '". $uid ."');";
			return $query;
		}

		function getUserList() {
			$query = "
			SELECT * FROM `users`
                        ";
			return $query;
		}

		function removeUser($puserid) {
			$query = "
			DELETE FROM `users`
			WHERE
			`uid` = '".$puserid."';";
			return $query;
		}

		function insertUser($username, $password, $firstname, $lastname, $email, $userlevel) {
			$query = "
			INSERT INTO `users`
			(`username`, `password`, `firstname`, `lastname`, `email`, `userlevel`)
			VALUES
			('". $username ."', '". $password ."', '". $firstname ."', '". $lastname ."', '". $email ."', '". $userlevel ."');";
			return $query;
		}
		
		function searchJobSN($searchitem, $jobstatus) {
			$query = "
			SELECT * FROM `jobs`
			WHERE `serialno` = '". $searchitem ."'
			AND `closed` = '". $jobstatus ."'";
			return $query;
		}
		
		function searchJobSO($searchitem, $jobstatus) {
			$query = "
			SELECT * FROM `jobs`
			WHERE `serviceord` = '". $searchitem ."'
			AND `closed` = '". $jobstatus ."'";
			return $query;
		}
		
		function searchJobAccNo($searchitem, $jobstatus) {
			$query = "
			SELECT * FROM `jobs`
			WHERE `cid` = '". $searchitem ."'
			AND `closed` = '". $jobstatus ."'";
			return $query;
		}

                function searchJobAccNoGetCID($searchitem) {
                        $query = "
                        SELECT `id` FROM `customers`
                        WHERE `customerno` LIKE '%". $searchitem ."%'";
                        return $query;
                }

		
                function searchJobCustomerNameGetCID($searchitem) {
                        $query = "
			SELECT `id` FROM `customers`
                        WHERE `customername` LIKE '%". $searchitem ."%'";
                        return $query;
                }

                function searchJobCustomerNameCID($cid, $jobstatus) {
                        $query = "
                        SELECT * FROM `jobs`
                        WHERE `cid` = '". $cid ."'
                        AND `closed` = '". $jobstatus ."'";
                        return $query;
                }
		
		
		function searchJobEngineerName($searchitem, $jobstatus) {
			$query = "
			SELECT * FROM `jobs`
			JOIN users ON jobs.uid = users.uid
			WHERE `username` = '". $searchitem ."'
			AND `closed` = '". $jobstatus ."'";
			return $query;
		}
		
		function searchCustomerAccNo($searchitem) {
			$query = "
			SELECT * FROM `customers`
			WHERE `customerno` = '". $searchitem ."'";
			return $query;
		}

		function searchCustomerName($searchitem) {
			$query = "
			SELECT * FROM `customers`
			WHERE `customername` LIKE '%". $searchitem ."%'";
			return $query;
		}

		function searchEquipmentName($searchitem) {
			$query = "
			SELECT * FROM `equipment`
			WHERE `equipment` LIKE '%". $searchitem ."%'";
			return $query;
		}
		
		function searchUserName($searchitem) {
			$query = "
			SELECT * FROM `users`
			WHERE `username` = '". $searchitem ."'";
			return $query;
		}
		
		function searchUserFirstName($searchitem) {
			$query = "
			SELECT * FROM `users`
			WHERE `firstname` LIKE '%". $searchitem ."%'";
			return $query;
		}
}
?>
