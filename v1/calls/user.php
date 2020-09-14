<?php

	class User
	{

        public function login($parameters)
        {
        	$data = $_GET;

        	$users = User::get($parameters);
        	foreach ($users as $key => $value) {
        		if ($data['name'] == $value['name']){
        			if($data['password'] == $value['password']){
						return $value['type'];
        			}
        			throw new Exception("Password does not match");
        		}
        	}
        	throw new Exception("User does not exist in this company");
        }

        public function get($parameters)
        {
        	// wHAT Type of user are requesting the method?
        	$data = $_GET;

        	include_once '../scripts/conn.php';
            $conn = createConn($data['company']);

            $sql = "SELECT * FROM `users`";
            $sql = $conn->prepare($sql);
            $sql->execute();

            $results = array();

            while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
            if (!$results) {
                throw new Exception("None user in users");
            }
                
           	return $results;
    	}

		public function delete($parameters)
		{
			$data = $_GET;

			include_once '../scripts/conn.php';
			$conn = createConn($data['company']);

			$sql = "DELETE FROM `users` WHERE id='" . $data['id'] . "'";
            $sql = $conn->prepare($sql);
            $sql->execute();
		}

	}

?>