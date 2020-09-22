<?php

	class User
	{
        // Factories ################################################################################################
        private function signin($data){
            if(isset($data['user'], $data['company'], $data['password'])){
                include_once '../scripts/conn.php';
                // Search for user in database (as read only db user)
                $conn = createConn($data['company'], 'login', '123');
                $sql = "SELECT `id`,`user`,`theme`,`type` FROM `users` WHERE `user`='" . $data['user'] . "'";
                $sql = $conn->prepare($sql);
                $sql->execute();
                $users = array();
                // Go through all results
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $users[] = $row;
                }
                if (!$users) {
                    throw new Exception("None user in users");
                }
                // Go through all users and verify if it matches the user input
                foreach ($users as $key => $value){
                    // Make connection with database as root
                    $conn = createConn($data['company'], 'root', '');
                    // Get user password throught his username
                    $sql = "SELECT * FROM `users` WHERE `user`='" . $data['user'] . "'";
                    $sql = $conn->prepare($sql);
                    $sql->execute();
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $results[] = $row;
                    }
                    if (!$results){
                        throw new Exception("Error in database, call the admin");
                    }
                    // Encrypt password input as sha1 (database default)
                    // and verify if user found has the same password as the input
                    if(sha1($data['password']) == $results[0]['password']){					
                        return $results[0];
                    }
                    throw new Exception("Password does not match");		
                }
                throw new Exception("User does not exist in this company");
            }
        }

        private function list($data, $user){
            // Verify if user has permission to get(read) other users information
            if($user['type'] == "admin"){
                include_once '../scripts/conn.php';
                // Make connection to database as only read user for security reassons
                $conn = createConn($data['company'], 'read', '123');
                // Get specif user information, except password
                $sql = "SELECT `id`,`user`,`theme`,`type` FROM `users`";
                if(isset($data['id'])){
                    $sql = "SELECT `id`,`user`,`theme`,`type` FROM `users` WHERE `id`='" . $data['id'] . "'";
                }
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
        
        private function del($data, $user){
            if($user['type'] == "admin" and isset($data['id'])) {
                include_once '../scripts/conn.php';
                // Make connection to database as root to be able to delete users
                $conn = createConn($data['company'], 'root', '');
                // Delete user with the id in parameters
                $sql = "DELETE FROM `users` WHERE id='" . $data['id'] . "'";
                $sql = $conn->prepare($sql);
                $sql->execute();
                return "User with id: " . $data['id'] . " was deleted";
            }
            throw new Exception("User does not have permission to delete other users");
        }

        private function up($data, $user){
            if($user['type'] == "admin" or $user['id'] == $data['id']) {
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
                    // If dont receive any parameters to be updated
                    throw new Exception("What do you want to update? i did not get it");
                }
                // Add to query the user to be updated
                $sql .= " WHERE `id`='" . $data['id'] . "'";
                $sql = $conn->prepare($sql);
                $sql->execute(); 
                return "User with id: " . $data['id'] . " updated";
            }
            throw new Exception("You do not have permission to update any user information except yours");
        }

        // API Calls ################################################################################################
        public function login(){
            // Get URL parameters
            $data = $_GET;
            // Get user and return it as a object
            $user = User::signin($data);
            return $user;
        }

        public function get(){
            // Get URL parameters
            $data = $_GET;
            // Get user object
            $user = User::signin($data);
            // Pass and parameters to List function and return it as a object
            $get = User::list($data, $user);
            return $get;
        }

		public function delete()
		{
            // Get URL parameters
			$data = $_GET;
            // Get user object
            $user = User::signin($data);
            // Call dell function with credentials in user object and with user to be deleted with data
            $deleted = User::del($data, $user);
            return $deleted;
		}

        public function update()
        {
            // Get URL parameters
            $data = $_GET;
            // Get user object
            $user = User::signin($data);
            // Call up function and return results as object
            $updated = User::up($data, $user);
            return $updated;

        }
	}
?>