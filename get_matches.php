<?php 

	$servername = "127.0.0.1";
	$username = "root";
	$password = "";
	$dbname = "dating_application";


	//reading the request body
	$entityBody = file_get_contents('php://input');
	//decoding the request body and returning json as php array using true as second argument
	$json_request = json_decode($entityBody, true);
	$json_response = array();


	$fetch_matches = $json_request["fetch_matches"];
	$user_id = $json_request["user_id"];

	//connecting with database
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	$match_sql = "SELECT user_id_2 FROM matches WHERE user_id_1 = \'" . $user_id . "\';";
	$match_data_sql = "SELECT * FROM users WHERE user_id IN (" . $match_sql . ");";


	$match_data_list = array();

	if ($fetch_matches === true) {
		$result = $conn->query($match_data_sql);
		if ($result->rows > 0) {
			$json_response["has_matches"] = true;
			$i = 0;
			while ($row = $result->fetch_assoc()) {
				//initializing json object
				$match_data = array();
				
				//poppulating json object
				$match_data["user_id"] = $row["user_id"];
				$match_data["name"] = $row["name"];
				$match_data["gender"] = $row["gender"];
				$match_data["age"] = $row["age"];
				$match_data["url"] = $row["url"];

				$match_data_list[$i] = $match_data;

				$i++;
			}
		} else {
			$json_response["has_matches"] = false;	
		}
	} else {
		$json_response["has_matches"] = false;
	}

	$json_response["matches"] = $match_data_list;

	

	$conn->close();

	//encoding json to response body
	$json_response_encoded = json_encode($json_response);
	//setting the content type tp json object
	header('Content-type: application/json');
	exit($json_response_encoded);

?>