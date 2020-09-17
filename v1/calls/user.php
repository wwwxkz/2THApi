<?php

	class User
	{

        public function login($parameters)
        {
            include_once '../scripts/conn.php';
            // Get URL parameters
        	$data = $_GET;
            // Search for user in database (as read only db user)
            $conn = createConn($data['company'], 'login', '123');
            $sql = "SELECT `id`,`name`,`theme`,`type` FROM `users`";
            $sql = $conn->prepare($sql);
            $sql->execute();
            $users = array();
            // Go through all users
            while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                $users[] = $row;
            }
            if (!$users) {
                throw new Exception("None user in users");
            }
            // Go through all users and verify if it matches the user input
        	foreach ($users as $key => $value) {
        		if ($data['name'] == $value['name']){
                    // Make connection with database as root
                    $conn = createConn($data['company'], 'root', '');
                    // Get user password throught his name
                    $sql = "SELECT * FROM `users` WHERE name='" . $data['name'] . "'";
                    $sql = $conn->prepare($sql);
                    $sql->execute();
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                        $results[] = $row;
                    }
                    if (!$results) {
                        throw new Exception("Error in database, call the admin");
                    }
                    // Encrypt password input as sha1 (database default)
                    // and verify if user found has the same password as the input
        			if(sha1($data['password']) == $results[0]['password']){
						return $results[0]['type'];
        			}
        			throw new Exception("Password does not match");
        		}
        	}
        	throw new Exception("User does not exist in this company");
        }

        public function get($parameters)
        {
            // Get URL parameters
        	$data = $_GET;
            // Get user permission and verify if user has permission to get(read) other users information
            $permission = User::login($parameters);
            if($permission == "admin"){
                include_once '../scripts/conn.php';
                // Make connection to database as only read user for security reassons
                $conn = createConn($data['company'], 'read', '123');
                // Get specif user information, except password
                $sql = "SELECT `id`,`name`,`theme`,`type` FROM `users`";
                $sql = $conn->prepare($sql);
                $sql->execute();
                $results = array();
                // Go through all users and return as result
                while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $results[] = $row;
                }
                if (!$results) {
                    throw new Exception("None user in users");
                }  
                return $results;
            }
        	throw new Exception("User does not have permission to get(read) other users information");
    	}

		public function delete($parameters)
		{
            // Get URL parameters
			$data = $_GET;
            // Get user permission and verify if user has permission to delete other users
            $permission = User::login($parameters);
            if($permission == "admin") {
                include_once '../scripts/conn.php';
                // Make connection to database as root to be able to delete users
                $conn = createConn($data['company'], 'root', '');
                // Delete user with the id in parameters
                $sql = "DELETE FROM `users` WHERE id='" . $data['id'] . "'";
                $sql = $conn->prepare($sql);
                $sql->execute();
                return "User with id: " . $data['id'] . " deleted";
            }
            throw new Exception("User does not have permission to delete other users");
		}

        public function update($parameters)
        {
            // Get URL parameters
            $data = $_GET;
            // Get user permission and verify if user has permission to update other users information
            // or if it is your own user information
            $users = User::get($parameters);
            if(array_key_exists('id', $data)){
                $permission = User::login($parameters);
                if($users[$data['index']]['id'] == $data['id']){
                    if($permission == "admin" or $data['name'] == $data['index']['name']) {
                        // Make connection to database as root to be able to update users information
                        include_once '../scripts/conn.php';
                        $conn = createConn($data['company'], 'root', '');
                        // Create base string for query
                        $sql = "UPDATE `users` SET";
                        // Add query actions as url needs
                        if(array_key_exists('new-password', $data)){
                            $sql .= "`password`='" . sha1($data['new-password']) . "'";
                        }
                        elseif(array_key_exists('new-theme', $data)){
                            $sql .= "`theme`='" . $data['new-theme'] . "'";
                        } else {
                            // If dont receive any parameters to update
                            throw new Exception("What do you want to update? i did not get it");
                        }
                        // Add to query the user to be updated
                        $sql .= " WHERE `id`='" . $data['id'] . "'";
                        $sql = $conn->prepare($sql);
                        $sql->execute(); 
                        return "User with name: " . $data['id'] . " updated";
                    }
                    throw new Exception("You do not have permission to update any user information except yours");
                }
                throw new Exception("This id or index does not point to any");
            }
            throw new Exception("Did not received obligatory parameters");
        }
	}

?>