<?php
	function createConn($company, $user, $password){
	    return new PDO("mysql: host=localhost; dbname=" . $company . ";", $user, $password);
	}
?>