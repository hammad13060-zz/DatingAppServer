<?php 

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

	$dbconection = mysql_connect("127.0.0.1", "root", "");
	mysql_select_db("dating_application",$dbconection);

	$query = mysql_query("SELECT * FROM users WHERE user_id = '$user_id'", $dbconection);

	$row = mysql_fetch_array($query);

	if($row !== False)
	{
		//update
		$registered = True;
		$queryNew = mysql_query("UPDATE users SET name = '$name', gender = '$gender', age = '$age', url = '$url' WHERE user_id = '$user_id'");
	}
	else if ($row === False)
	{
		//insert
		$registered = False;
		$queryNew = mysql_query("INSERT INTO users (user_id,name,gender,age,url) VALUES ('$user_id','$name','$gender','$age','$url')");
	}
	else
	{
		echo mysql_error();
	}

	$json_response["registered"] = $registered;


	mysql_close($dbconection);

	//encoding json to response body
	$json_response_encoded = json_encode($json_response);
	//setting the content type tp json object
	header('Content-type: application/json');
	exit($json_response_encoded);

?>