<?php
/*
*
* Instrument Repair Portal - A simple repair management system
* Developed by Chris Elliott -- https://github.com/c-elliott
* Filename: logout.php
*
*/

// Destroy the session
session_start();
session_unset();
session_destroy();

// Redirect to login
header('Location: login.php?logout');

?>
