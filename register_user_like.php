<?php

	require 'vendor/autoload.php';
	use Parse\ParseClient;
	use Parse\ParseObject;
 
	ParseClient::initialize('Oi06rcMuTImq7ZolKPfanXUZTZBDhl23a91xvEQR', 'ne6sFrfymIHSdCuclROMUKYvh6bw2sO3AA1SeKPU', 'RUaxhmj7wXa7WoOlMtHOHPtiwnGQVJKHTwRcYHPq');


	$servername = "127.0.0.1";
	$username = "root";
	$password = "";
	$dbname = "dating_application";
	//reading the request body
	$entityBody = file_get_contents('php://input');
	//decoding the request body and returning json as php array using true as second argument
	$json_request = json_decode($entityBody, true);
	$json_response = array();

	$user_id_1 = $json_request["user_id_1"]; //string type
	$user_id_2 = $json_request["user_id_2"]; //string type

	//debug code

	//$json_response['user_id_1'] = $user_id_1;
	//$json_response['user_id_2'] = $user_id_2;

	///////////
	

	//connecting with database
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);


	$chatData = new ParseObject("ChatData");
	
	if( ($user_id_1 !== NULL) && ($user_id_2 !== NULL)  )
	{
		//insert
		$like_insert_sql = "INSERT INTO likes (user_id_1,user_id_2) VALUES ('$user_id_1','$user_id_2');";
		if ($conn->query($like_insert_sql) == true) {
			$json_response["success"] = true;

			//checking for a new match
			$sql = "SELECT * FROM likes WHERE user_id_1 = '$user_id_2' AND user_id_2 = '$user_id_1';";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {

				try {
  					$chatData->setArray("messages", array());
  					$chatData->save();
  					$chat_id = $chatData->getObjectId();

  					$match_sql_1 = "INSERT INTO matches (user_id_1,user_id_2,chat_id) VALUES ('$user_id_1','$user_id_2','$chat_id');";
					$match_sql_2 = "INSERT INTO matches (user_id_1,user_id_2,chat_id) VALUES ('$user_id_2','$user_id_1','$chat_id');";

					$conn->query($match_sql_1);
					$conn->query($match_sql_2);
				} catch (ParseException $ex) {  
  					// Execute any logic that should take place if the save fails.
  					// error is a ParseException object with an error code and message.
  					echo 'Failed to create new object, with error message: ' . $ex->getMessage();
					}
			}
		}
	}
	else
	{
		$json_response["success"] = false;
		echo mysql_error();
	}

	

	$conn->close();

	//encoding json to response body
	$json_response_encoded = json_encode($json_response);
	//setting the content type tp json object
	header('Content-type: application/json');
	exit($json_response_encoded);

?>