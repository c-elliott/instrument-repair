<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: dbconnect.php
*
*/

    	// Create a MySQL class including MySQLi and all the functions we need
	class mysql extends mysqli
	{
		var $MYSQL_user;
		var $MYSQL_pass;
		var $MYSQL_host;
		var $MYSQL_db;
		var $MYSQL_port;
		var $MYSQL_socket;
		
		var $connect;

		function __construct(   ) {
		
			$this->Connect(   );
		}
		
		function Connect($host = NULL, $user = NULL, $password = NULL, $database = NULL, $port = NULL, $socket = NULL) {
			$this->MYSQL_user = "username";
			$this->MYSQL_pass = "password";
			$this->MYSQL_host = "localhost";
			$this->MYSQL_db = "database";
			$this->connect = mysqli_connect($this->MYSQL_host, $this->MYSQL_user, $this->MYSQL_pass, $this->MYSQL_db)or die(mysql_error(   ));
		}
	
		function passConn() {
			return $this->connect;
		}	
				
		function __destruct(   ) {
			mysqli_close(   $this->connect   );
		}
		
		public function runQuery( $query ) {
			$q = $this->connect->query($query) or die ("Couldn't execute query: ".mysqli_error($this->connect));
			$res = $q->fetch_assoc();
			return $res;
		}
		
		function removeQuery( $query ) {
			$q = $this->connect->query($query) or die ("Couldn't execute query: ".mysqli_error($this->connect));
			if ($q) {
			return TRUE;
			}
			else {
			return FALSE;
			}
		}

		function runArrayQuery( $query ) {
			$q = $this->connect->query($query) or die ("Couldn't execute query: ".mysqli_error($this->connect));
			$res = $q->fetch_array();
			return $res;
		}

		function runMultipleArrayQuery( $query ) {
			$q = $this->connect->query($query) or die ("Couldn't execute query: ".mysqli_error($this->connect));
			$res = $q->fetch_all();
			return $res;
		}

		function runNumRowsQuery( $query ) {
			$q = $this->connect->query($query) or die ("Couldn't execute query: ".mysqli_error($this->connect));
			$res = $q->num_rows;
			return $res;
		}

		function insertQuery( $query ) {
			$q = $this->connect->query($query) or die ("Couldn't execute query: ".mysqli_error($this->connect));
			if ($q)
			{
				return TRUE;
			} else {
				return FALSE;
			}
		}

		function fetchWhileRows($query) {
			$result = $this->connect->query($query) or die ("Couldn't execute query: ".mysqli_error($this->connect));
			$rows = array();
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		}

		function updateQuery( $query ) {
			$q = $this->connect->query($query) or die ("Couldn't execute query: ".mysqli_error($this->connect));
		}

	}
?>
