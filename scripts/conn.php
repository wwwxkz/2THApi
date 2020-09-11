<?php
	function createConn($company){
	    return new PDO("mysql: host=localhost; dbname=" . $company . ";", "root", "");
	}
?>