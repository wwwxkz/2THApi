<?php

	class Report
	{
        public function login($parameters)
        {
            $companies = json_decode(file_get_contents("../../../../secure/companies.txt"), true);
            $data = $_GET;
            if(array_key_exists($data['company'], $companies['companies'])) {
                if($data['user'] == "connector"){
                    if($data['password'] == $companies['companies'][$data['company']]['api_connector_password']){
                        return 'connector'; 
                    } else {
                        throw new Exception("This password does not match");
                    }
                } 
                elseif($data['user'] == "admin"){
                    if($data['password'] == $companies['companies'][$data['company']]['api_admin_password']){
                        return 'admin';
                    } else {
                        throw new Exception("This password does not match");
                    }
                } else {
                    throw new Exception("Please, specify a user");
                }
            } else {
                throw new Exception("This company does not exist");
            }
        }
		public function get($parameters)
		{
            if(Report::login($parameters) == 'admin') {
                include_once '../scripts/conn.php';

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
                
                return $results;
            } else {
                throw new Exception("You do not have permission to use this method");
            }
        }
        public function send($parameters)
		{
            $user = Report::login($parameters);
            if($user == 'connector' or $user == 'admin'){
                $data = $_GET;
                if(strlen($data['mac']) == 12) {
                    include_once '../scripts/conn.php';

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
            if(Report::login($parameters) == 'admin'){
                $data = $_GET;
                
                include_once '../scripts/conn.php';

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