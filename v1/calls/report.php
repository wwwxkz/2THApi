<?php
    
	class Report
	{
        // Factories ################################################################################################
        private function list($data, $user){
            if($user['type'] == 'admin') {
                // Make connection with db as read only
                include_once '../scripts/conn.php';
                $conn = createConn($data['company'], 'read', '123');
                // Select all reports and order
                $sql = "SELECT * FROM reports ORDER BY id ASC";
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
                $conn = createConn($data['company'], 'root', '');
                try {
                    $sql = "UPDATE `reports` SET `name`=\"" . $data['name'] . "\",`tag`=\"" . $data['tag'] . "\",`groups`=\"" . $data['groups'] . "\" WHERE id =" . $data['id'];   
                    $conn->exec($sql);
                } catch(PDOException $e) {
                    echo $sql . "<br>" . $e->getMessage();
                }
            } else {
                throw new Exception("You do not have permission to use this method");
            }
        }

        private function receive($data, $user){
            if($user['type'] == 'connector' or $user['type'] == 'admin'){
                if(strlen($data['mac']) == 12) {
                    include_once '../scripts/conn.php';
                    $conn = createConn($data['company'], 'connector', '123');

                    $sql = "SELECT * FROM `reports` WHERE `mac`=\"" . $data['mac'] . "\"";
                    $sql = $conn->prepare($sql);
                    $sql->execute();

                    if ($sql->fetch(PDO::FETCH_ASSOC) == true){
                        echo 'Exist';
                        try {   
                            $sql = "UPDATE `reports` SET `lat`=" . $data['lat'] . ",`lon`=" . $data['lon'] . ",`date`='" . date("Y-m-d") . "' WHERE `mac`='" . $data['mac'] . "'";
                            $conn->exec($sql);
                            echo "Record updated successfully";
                        } catch(PDOException $e) {
                            echo $sql . "<br>" . $e->getMessage();
                        }
                    } else {   
                        echo 'Does not exist';
                        try {
                            $sql = "INSERT INTO `reports`(`mac`, `lat`, `lon`, `date`) VALUES (\"" . $data['mac'] . "\"," . $data['lat'] . "," . $data['lon'] . ",'" . date("Y-m-d") . "')";
                            $conn->exec($sql);
                            echo "New report created successfully";
                        } catch(PDOException $e) {
                            echo $sql . "<br>" . $e->getMessage();
                        }
                    }
                    $conn = null;
                } else {
                    throw new Exception("This is not a MAC Address");
                }
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

	}

?>