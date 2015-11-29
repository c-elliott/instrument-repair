<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: globals.php
*
*/
        	// Product Details
            $PRODUCT_HEADER = "Repair Portal";
        	$PRODUCT_FOOTER = "Repair Portal";
            $PRODUCT_VERSION = "Version 1.0";

        	// JQuery toastmessage OK alert
		function alertOK($message) {
			echo "
			<script type=\"text/javascript\">
			$().toastmessage('showSuccessToast', \"$message\");
			</script>
			";
		}

        	// JQuery toastmessage Error alert
		function alertERR($message) {
			echo "
			<script type=\"text/javascript\">
			$().toastmessage('showErrorToast', \"$message\");
			</script>
			";
		}
		
        	// JQuery toastmessage Warning alert
		function alertWARN($message) {
			echo "
			<script type=\"text/javascript\">
			$().toastmessage('showWarningToast', \"$message\");
			</script>
			";
		}

        	// Start the global class
		class globals {		

			private $sql;
			private $querys;
			
        	// Start the SQL query construct
		function __construct($sql, $q) {
			$this->sql = $sql;
			$this->querys = $q;
		}
		             
        	// Populates dropdown menus based on database values
		function dropDownValues($ddvalues, $selected, $selectname) { 
			$countArray = array_map("count", $ddvalues);
			$totalCount = array_sum($countArray);	
			$num =  $totalCount / 2;
			echo '<select name="'.$selectname.'" id="'.$selectname.'" autocomplete="off">';
			$i = "0";
			while( $i < $num) {
				echo '<option value="'.$ddvalues['values'][$i]. '"';
				if ($selected == $ddvalues['values'][$i]) {
					echo ' selected';
				}
				echo '>'.$ddvalues['label'][$i].'</option>';
 				$i++;
			}
			echo '</select>';
			return TRUE;
		}
		
        	// Basic string cleaning function
		function cleanInject( $string ) {
			$string = mysqli_real_escape_string($sql->connect, $string);
		}
		
		// A clean function for form textarea input sanitization
		function textareaClean($string) {
			if(get_magic_quotes_gpc()) {
				$string = stripslashes($string);
			}
			elseif(!get_magic_quotes_gpc()) {
				$string = addslashes(trim($string));
			}
			$string = filter_var($string, FILTER_SANITIZE_STRING);
            return $string;
		}

        	// A clean function for form input sanitization
		function Clean($string) {
			if(get_magic_quotes_gpc()) {
				$string = stripslashes($string);
            		} elseif(!get_magic_quotes_gpc()) {
				$string = addslashes(trim($string));
            		}
            		$string = escapeshellcmd($string);
            		$string = mysqli_real_escape_string($this->sql->passConn(), $string);
                    $string = filter_var($string, FILTER_SANITIZE_STRING);
            		$string = stripslashes(strip_tags(htmlspecialchars($string, ENT_QUOTES)));
            		return $string;
		}
	
		// Encrypt the password via Bcrypt 12-interation hash and random salt	
		function Encrypt($toEncrypt) {
            $salt = '$2a$12$' . substr(md5(uniqid(rand(), true)), 0, 22);
			$enc = crypt($toEncrypt, $salt);
			return $enc;
		}
		
		// Rebuild existing password with input and salt stored in database
		function rebuildEncryption($toEncrypt, $dbSalt) {
            		$salt = substr($dbSalt,0,29);
            		$enc = crypt($toEncrypt, $salt);
            		return $enc;
		}

		// Light hashing function for session data	
		function SessEncrypt($SessString) {
			$enc = md5($SessString);
			return $enc;
		}
		
		// A reliable way to get the clients external IP
		function getIP() {
            		$ip = $_SERVER['REMOTE_ADDR'];
            		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
               			$ip = $_SERVER['HTTP_CLIENT_IP'];
            		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
               			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
            		return $ip;
		}

// End the global class		
}
?>
