<?php 

	$servername = "127.0.0.1";
	$username = "root";
	$password = "";
	$dbname = "dating_application";


	//connecting with database
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}


	//reading the request body
	$entityBody = file_get_contents('php://input');

	//decoding the request body and returning json as php array using true as second argument
	$json_request = json_decode($entityBody, true);
	$json_response = array();






	$user_id = $json_request["user_id"]; //string type
	$name = $json_request["name"]; //string type
	$gender = $json_request["gender"]; //boolean type
	$age = $json_request["age"]; //integer type
	$url = $json_request["url"]; //string type

	$registered = False;

	$query = "SELECT * FROM users WHERE user_id = '$user_id';";

	$result = $conn->query($query);

	if($result->num_rows > 0)
	{
		//update
		$registered = True;
		$update_query = "UPDATE users SET name = '$name', gender = '$gender', age = '$age', url = '$url' WHERE user_id = '$user_id';";
		$conn->query($update_query);

		$like_query = "SELECT user_id_2 FROM likes WHERE user_id_1='$user_id';";
		$like_query_result = $conn->query($like_query);
		if ($like_query_result->num_rows > 0) {
			$like_array = array();
			$i = 0;
			while ($row = $like_query_result->fetch_assoc()) {
				$like_array[$i++] = $row['user_id_2'];			
			}
			$json_response["likes"] = $like_array;
		}
	}
	else if ($row === False)
	{
		//insert
		$registered = False;
		$queryNew = "INSERT INTO users (user_id,name,gender,age,url) VALUES ('$user_id','$name','$gender','$age','$url');";
		$conn->query($queryNew);
	}

	$json_response["registered"] = $registered;


	$conn->close();

	//encoding json to response body
	$json_response_encoded = json_encode($json_response);
	//setting the content type tp json object
	header('Content-type: application/json');
	exit($json_response_encoded);
?>