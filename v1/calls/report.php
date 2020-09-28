<?php
    
	class Report
	{
        // Factories ################################################################################################
        private function list($data, $user){
            if($user['type'] == 'admin') {
                // Make connection with db as read only
                include_once '../scripts/conn.php';
                $conn = createConn($data['company'], 'read', '123');         
                // Verify if the request specify an id
                // If do not, select all reports and order
                $sql = "SELECT * FROM reports ORDER BY id ASC";
                // Use id if exist
                if(isset($data['id'])){
                    $sql = "SELECT * FROM reports WHERE `id`=" . $data['id'];
                }
                $sql = $conn->prepare($sql);
                $sql->execute();
                $results = array();
                while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $results[] = $row;
                }
                if (!$results) {
                    throw new Exception("None report in reports");
                }
                // Return reports if there are reports
                return $results;
            } else {
                throw new Exception("You do not have permission to use this method");
            }
        }

        private function up($data, $user){
            if($user['type'] == 'admin'){
                include_once '../scripts/conn.php';
                // Make connection as root to be able to update reports
                $conn = createConn($data['company'], 'root', '');
                if(isset($data['name'], $data['tag'], $data['groups'], $data['id'])){
                    try {
                        $sql = "UPDATE `reports` SET `name`=\"" . $data['name'] . "\",`tag`=\"" . $data['tag'] . "\",`groups`=\"" . $data['groups'] . "\" WHERE id =" . $data['id'];   
                        $conn->exec($sql);
                        return "User with id: " . $data['id'] . " updated";
                    } catch(PDOException $e) {
                        throw new Exception("Database error, contact the administrator");
                    }
                }
                throw new Exception("ID is not set");
            }
            throw new Exception("You do not have permission to use this method");
        }

        private function receive($data, $user){
            if($user['type'] == 'connector' or $user['type'] == 'admin'){
                if(strlen($data['mac']) == 12) {
                    include_once '../scripts/conn.php';
                    $conn = createConn($data['company'], 'connector', '123');
                    $sql = "SELECT * FROM `reports` WHERE `mac`=\"" . $data['mac'] . "\"";
                    $sql = $conn->prepare($sql);
                    $sql->execute();
                    $results = array();
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                        $results[] = $row;
                    }
                    // Report exist
                    if ($results){
                        $arr = json_decode($results[0]['locations']);
                        $arr[] = ['date' => date("Y-m-d"), 'lat' => $data['lat'], 'lon'  => $data['lon']];
                        $locations = json_encode($arr);
                        try {
                            $sql = "UPDATE `reports` SET `locations`='" . $locations . "' WHERE `mac`='" . $data['mac'] . "'";
                            $conn->exec($sql);
                            return $sql;
                            return "Record updated successfully";
                        } catch(PDOException $e) {
                            throw new Exception("Database error, contact the administrator");
                        }
                    }
                    // Report does not exist, create one instead
                    if (!$results) {
                        $arr = array(
                            array(
                                'date' => date("Y-m-d"),
                                'lat' => $data['lat'],
                                'lon' => $data['lon']
                            )
                        );
                        $locations = json_encode($arr);
                        try {
                            $sql = "INSERT INTO `reports`(`mac`, `locations`) VALUES (\"" . $data['mac'] . "\",'" . $locations . "')";
                            $conn->exec($sql);
                            return "New report created successfully";
                        } catch(PDOException $e) {
                            throw new Exception("Database error, contact the administrator");
                        }
                    }
                    $conn = null;
                } else {
                    throw new Exception("This is not a MAC Address");
                }
            }
        }

        private function del($data, $user){
            if($user['type'] == 'admin'){
                include_once '../scripts/conn.php';
                // Make connection as root to be able to delete reports
                $conn = createConn($data['company'], 'root', '');
                if(isset($data['id'])){
                    try {
                        $sql = "DELETE FROM `reports` WHERE id=" . $data['id'];
                        $conn->exec($sql);
                        return "User with id: " . $data['id'] . " deleted";
                    } catch(PDOException $e) {
                        throw new Exception("Database error, contact the administrator");
                    }
                }
                throw new Exception("Id is not set");
            } else {
                throw new Exception("You do not have permission to use this method");
            }
        }

        // API Calls ################################################################################################
		public function get(){
            // Get URL parameters
            $data = $_GET;
            // Get user.php to use login function and verify user permissions
            include_once 'user.php';
            // Get user object
            $user = User::login($data);
            // Call list function to get reports and return it as a object
            $reports = Report::list($data, $user);
            return $reports;
        }

        public function send()
		{
            // Get URL parameters
            $data = $_GET;
            // Get user.php to use login function and verify user permissions
            include_once 'user.php';
            // Get user object
            $user = User::login($data);
            // Call receive function and return it as a object
            $received = Report::receive($data, $user);
            return $received;
        }

        public function update()
		{
            // Get URL parameters
            $data = $_GET;
            // Get user.php to use login function and verify user permissions
            include_once 'user.php';
            // Get user object
            $user = User::login($data);
            // Call up function and return it as a object
            $updated = Report::up($data, $user);
            return $updated;

        }

        public function delete()
        {
            // Get URL parameters
            $data = $_GET;
            // Get user.php to use login function and verify user permissions
            include_once 'user.php';
            // Get user object
            $user = User::login($data);
            // Call dell function to delete user and return it as a object
            $deleted = Report::del($data, $user);
            return $deleted;
        }

	}

?>