<?php
    
	class Report
	{

		public function get($parameters)
		{
            // Get user.php to use login function and verify user permissions
            include_once 'user.php';
            $permissions = User::login($parameters);
            if($permissions == 'admin') {
                $data = $_GET;
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

        public function send($parameters)
		{
            // Get user.php to use login function and verify user permissions
            include_once 'user.php';
            $permissions = User::login($parameters);
            if($permissions == 'connector' or $permissions == 'admin'){
                $data = $_GET;
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

        public function update($parameters)
		{
            // Get user.php to use login function and verify user permissions
            include_once 'user.php';
            $permissions = User::login($parameters);
            if($permissions == 'admin'){
                $data = $_GET;
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

	}

?>