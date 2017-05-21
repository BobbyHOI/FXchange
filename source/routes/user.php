<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Get All Users
$app->get("/api/v1/users", function(Request $request, Response $response){
	
	$sql = "SELECT * FROM users";
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		if (!$user){
			echo '{"error":{"error":"no users exist"}';
			return $response->withStatus(404);
		};
		
		echo json_encode($users);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});


//Get Single User with ID
$app->get("/api/v1/user/{user_id}", function(Request $request, Response $response){
	
	$user_id = $request->getAttribute("user_id");
	
	$sql = "SELECT * FROM users WHERE id = $user_id";
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		echo json_encode($user);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});

//GET user with EMAIL && PASSWORD
$app->get("/api/v1/user/{email}/{password}", function(Request $request, Response $response){
	$email = $request->getAttribute("email");
	$password = $request->getAttribute("password");
	
	$sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$user){
			echo '{"error":{"error":"user does not exist"}';
			return $response->withStatus(404);
		};
		
		$db = null;
		
		echo json_encode($user);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});


//Add Single User  AND  CREATE WALLET

$app->post("/api/v1/user", function(Request $request, Response $response){
	
	//$first_name = $request->getParam("first_name");
	//$last_name = $request->getParam("last_name");
	$email = $request->getParam("email");
	$password = $request->getParam("password");
	//$phone_no = $request->getParam("phone_no");
	//$address = $request->getParam("address");
	
	$NGN = "0";
	$USD = "0";
	$GBP = "0";
	
	$sql0 = "SELECT * FROM users";
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt0 = $db->prepare($sql0);
		$stmt0->execute();
		
		$count = $stmt0->rowCount();
		
		if ($count == 0){
			
		$sql = "INSERT INTO users (email,password) VALUES ('$email','$password')";
		}
		else {
			
			$sql = "INSERT INTO users (email,password) 
	SELECT :email,:password FROM users
	WHERE NOT EXISTS (SELECT email FROM users WHERE email = :email)
	LIMIT 1"; 
	};
	
			$stmt = $db->prepare($sql);
	
			$stmt->bindParam(":email", $email);
			$stmt->bindParam(":password", $password);
		//$stmt->bindParam(":phone_no",$phone_no);
		//$stmt->bindParam(":address",$address);
		//$stmt->bindParam(":",$);
		
			$stmt->execute();
		
			$succsesful = $stmt->rowCount();
		
			if ($succsesful == 0){
			echo '{"error":{"user":"user alredy exists"}';
			return $response->withStatus(404);
			};
		
		
		$sql1 = "SELECT user_id FROM users WHERE email = '$email' AND password = '$password'"; 
		
		$stmt1 = $db->query($sql1);
		$user_details = $stmt1->fetchAll(PDO::FETCH_OBJ);
		
		if (!$user_details){
			echo '{"rate":{"null":"user not found"}';
			//return $response->withStatus(404);
		};
		
		$user_id = $user_details[0]->user_id;
		
		$sql3 = "SELECT * FROM wallets";
		
		$stmt3 = $db->prepare($sql3);
		$stmt3->execute();
		
		$walletCount = $stmt3->rowCount();
		
		if ($walletCount == 0){
			$sql2 = "INSERT INTO wallets (user_id,NGN,GBP,USD) VALUES ('$user_id','$NGN','$GBP','$USD')";
		}
		else{
			$sql2 = "INSERT INTO wallets (user_id,NGN,GBP,USD)
	SELECT $user_id,$NGN,$USD,$GBP FROM wallets
	WHERE NOT EXISTS (SELECT user_id FROM wallets WHERE user_id = $user_id)
	LIMIT 1"; 
		};
	
	$stmt2 = $db->prepare($sql2);
	
		$stmt2->execute(); 
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});

//???????????OLD USER CREATION ??????????????//
/*$app->post("/api/v1/user", function(Request $request, Response $response){
	
	//$first_name = $request->getParam("first_name");
	//$last_name = $request->getParam("last_name");
	$email = $request->getParam("email");
	$password = $request->getParam("password");
	//$phone_no = $request->getParam("phone_no");
	//$address = $request->getParam("address");
	//$ = $request->getParam{""};
	
	$sql = "INSERT INTO users (email,password) 
	SELECT :email,:password FROM users
	WHERE NOT EXISTS (SELECT email FROM users WHERE email = :email)
	LIMIT 1"; 
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->prepare($sql);
	
		$stmt->bindParam(":email", $email);
		$stmt->bindParam(":password", $password);
		//$stmt->bindParam(":phone_no",$phone_no);
		//$stmt->bindParam(":address",$address);
		//$stmt->bindParam(":",$);
		
		$stmt->execute();
		
		$succsesful = $stmt->rowCount();
		
		if ($succsesful == 0){
			echo '{"error":{"user":"user alredy exists"}';
			return $response->withStatus(404);
		};
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});*/

// Get currencies
$app->get("/api/v1/currencies",function(Request $request ,Response $response){
	
	try{
		// currencies
		echo '{"currencies": {"united_states_dollars": "USD,"british_pound_sterling":"GBP","nigerian_naira":"NGN"}';
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});

	//GET all rates
$app->get("/api/v1/rates", function(Request $request, Response $response){
	
	$sql = "SELECT * FROM rates";
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$rates = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rates){
			echo '{"rates":{"null":"no rates found"}';
			//return $response->withStatus(404);
		};
		
		$db = null;
		
		echo json_encode($rates);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});


// get world wide current xchage rate
$app->get("/api/v1/rate/current/{initial_currency}/{final_currency}", function(Request $request, Response $response){	
	$initial_currency = $request->getAttribute("initial_currency");
	$final_currency = $request->getAttribute("final_currency");
	
		$convert_rates = "$initial_currency$final_currency";
		
		$url = "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22$convert_rates%22)&format=json&diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=";
		$ch = curl_init();
			// Disable SSL verification
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Will return the response, if false it print the response
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Set the url
				curl_setopt($ch, CURLOPT_URL,$url);
			// Execute
				$result=curl_exec($ch);
			// Closing
				curl_close($ch);
	
		$rates = (json_decode($result,true));
		$rate = $rates["query"]["results"]["rate"];
		
		echo json_encode($rate);
		
	//$latest_rates = file_get_contents('https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22NGNGBP%22)&format=json&diagnostics=false&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=');
	
	});

// Post a Rate
$app->post("/api/v1/rate", function(Request $request, Response $response){
	
	$user_id = $request->getParam("user_id");
	$rate = $request->getParam("rate");
	$amount = $request->getParam("amount");
	$initial_currency = $request->getParam("initial_currency");
	$final_currency = $request->getParam("final_currency");
	$availability = "1";
	$transfer_completion = "0";
	
	/*if (strlen($first_name)<4){
		echo "NUMBER LESS THAN 4";
		return $response->withStatus(400);
	}; */
	
	if (($initial_currency != "USD" ) && ($initial_currency != "GBP") && ($initial_currency != "NGN")) {
		echo '{"error": {"initial_currency":"value not recognised"}';
		return $response->withStatus(404);
	}
	else if (($final_currency != "USD" ) && ($final_currency != "GBP") && ($final_currency != "NGN")) {
		echo '{"error": {"final_currency":"value not recognised"}';
		return $response->withStatus(404);
	};
	
	if ($initial_currency == $final_currency) {
		echo '{"error": {"currencies":"initial_currency and final_currency cannot be the same"}';
		return $response->withStatus(404);
	};
	if ($amount == "0") {
		echo '{"error": {"amount":"amount cannot be 0"}';
		return $response->withStatus(404);
	};
	
	
/*$sql = "INSERT INTO rates (customer_id,rate,amount,initial_currency,final_currency,availability,transfer_completion) SELECT
							:customer_id,:rate,:amount,:initial_currency,:final_currency,:availability,:transfer_completion FROM rates 
							WHERE NOT EXISTS (SELECT $initial_currency, customer_id FROM wallets 
							WHERE $initial_currency < :amount AND customer_id = :customer_id)
							LIMIT 1";	*/// WORKING
							  
$sql = "INSERT INTO rates (user_id,rate,amount,initial_currency,final_currency,availability,transfer_completion) VALUES
							(:user_id,:rate,:amount,:initial_currency,:final_currency,:availability,:transfer_completion)";	
	
$sql2 = "SELECT $initial_currency FROM wallets WHERE user_id = $user_id";

	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		// SELECT AMOUNT FROM WALLET
		
		$stmt2 = $db->query($sql2);
		$wallet_amount = $stmt2->fetchAll(PDO::FETCH_OBJ);
		
		if (!$wallet_amount){
			echo '{"error":{"wallet":"User has no wallet"}';
			return $response->withStatus(404);
		};
		$key_amount = $wallet_amount[0]->$initial_currency;
		
		// SUBTRACT FROM WALLET AMOUNT
		$new_amount = $key_amount - $amount;
		
		if ($key_amount < $amount){
			echo '{"error":{"wallet":"User has insuffient funds to set a rate"}';
			return $response->withStatus(404);
		};
		
		// POST RATE TO DB
		
		$stmt = $db->prepare($sql);
	
		$stmt->bindParam(":user_id", $user_id);
		$stmt->bindParam(":rate", $rate);
		$stmt->bindParam(":amount", $amount);
		$stmt->bindParam(":initial_currency", $initial_currency);
		$stmt->bindParam(":final_currency", $final_currency);
		$stmt->bindParam(":availability", $availability);
		$stmt->bindParam(":transfer_completion", $transfer_completion);
		
		$stmt->execute();
		/*
		$succsesful = $stmt->rowCount();
		
		if ($succsesful == 0){
			echo '{"error":{"wallet":"not enough funds in wallet"}';
			return $response->withStatus(404);
		} */
		
		//$db = null;
		
		// UPDATE VALUE IN WALLET
		
	$sql3 = "UPDATE wallets SET $initial_currency = $new_amount WHERE user_id = $user_id";
		
		$stmt3 = $db->prepare($sql3);
		
		$stmt3->execute();
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});

// Get customer rate history
$app->get("/api/v1/rate" , function(Request $request , Response $response) {
		$user_id = $request->getParam("user_id");
		
		$sql = "SELECT * FROM rates WHERE user_id = $user_id";
		
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$rates = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rates){
			echo '{"error": {"null": "user has no rates"}';
			//return $response->withStatus(404);
		};
		
		$db = null;
		
		echo json_encode($rates);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
	});
	
	//GET availabile rates for single customer
	$app->get("/api/v1/rate/availability/{user_id}" , function(Request $request , Response $response) {
		$user_id = $request->getAttribute("user_id");
		
		$sql = "SELECT * FROM rates WHERE user_id = $user_id AND availability = 1";
		
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$rates = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rates){
			echo '{"error": {"null": "user has no availabile rates"}';
			//return $response->withStatus(404);
		};
		
		$db = null;
		
		echo json_encode($rates);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
	});
	
	//GET all availability rates by availability
	$app->get("/api/v1/rate/availability" , function(Request $request , Response $response) {
	
		$sql = "SELECT * FROM rates WHERE availability = 1";
		
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$rates = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rates){
			echo '{"rate":{"null":"no rates found"}';
			//return $response->withStatus(404);
		};
		
		$db = null;
		
		echo json_encode($rates);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
	});
	

	//GET all availability rates by availability and initial and final currency
	$app->get("/api/v1/rate/availability/{initial_currency}/{final_currency}" , function(Request $request , Response $response) {
		
		$initial_currency = $request->getAttribute("initial_currency");
		$final_currency = $request->getAttribute("final_currency");
		
		if (($initial_currency != "USD" ) && ($initial_currency != "GBP") && ($initial_currency != "NGN") && ($initial_currency != "0")) {
		echo '{"error": {"initial_currency":"value not recognised"}';
		return $response->withStatus(404);
		}
		else if (($final_currency != "USD" ) && ($final_currency != "GBP") && ($final_currency != "NGN") && ($final_currency != "0")) {
		echo '{"error": {"final_currency":"value not recognised"}';
		return $response->withStatus(404);
		};
	
		
		if (($initial_currency == "0") && ($final_currency == "0")){
			$sql = "SELECT * FROM rates WHERE availability = 1";
		} elseif (($initial_currency != "0") && ($final_currency != "0")) {
			$sql = $sql = "SELECT * FROM rates WHERE availability = 1 AND initial_currency = '$initial_currency' AND final_currency = '$final_currency'";
		}; 
		
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$rates = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rates){
			echo '{"rate":{"null":"no rates found"}';
			//return $response->withStatus(404);
		};
		
		$db = null;
		
		echo json_encode($rates);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
	});
	
// sTOP a rate availability
$app->post("/api/v1/rate/availability/{rate_id}" , function(Request $request , Response $response) {
	
	$rate_id = $request->getAttribute("rate_id");
	$user_id = $request->getParam("user_id");
	$availability = "0";
		
	$sql = "UPDATE rates SET availability = :availability
	WHERE rate_id = $rate_id";
	
	$sql1 = "SELECT availability,initial_currency FROM rates WHERE user_id = $user_id AND rate_id = $rate_id";

	$sql2 = "SELECT amount FROM rates WHERE user_id = $user_id AND rate_id = $rate_id";
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		// CHECK DATABASE FOR AVALABILITY
		$stmt1 = $db->query($sql1);
		$rate_details = $stmt1->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rate_details){
			echo '{"error":{"error":"rate does not exist"}';
			return $response->withStatus(404);
		};
		$key_availability = $rate_details[0]->availability;
		$initial_currency = $rate_details[0]->initial_currency;
		
		if ($key_availability == "0"){
			echo '{"error":{"error":"rate is unavailable"}';
			return $response->withStatus(404);
		};
		
		// SELECT RATE AMOUNT
		$stmt2 = $db->query($sql2);
		$rate_amount = $stmt2->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rate_amount){
			echo '{"error":{"error":"rate does not exist"}';
			return $response->withStatus(404);
		};
		$key_amount = $rate_amount[0]->amount;
		
		// SUBTRACT FROM rate AMOUNT
		$amount_deducted = $key_amount - ($key_amount/33.3333333333); // CHANGE DEDUCTION BASED ON RATE SET BY ADMIN
		
		// SELECT AMOUNT FROM WALLET
		
		$sql3 = "SELECT $initial_currency FROM wallets WHERE user_id = $user_id";
		
		$stmt3 = $db->query($sql3);
		$wallet_details = $stmt3->fetchAll(PDO::FETCH_OBJ);
		      
		if (!$wallet_details){
			echo '{"error":{"wallet":"User has no wallet"}';
			return $response->withStatus(404);
		};
		$wallet_amount = $wallet_details[0]->$initial_currency;
		
		// SUBTRACT FROM WALLET AMOUNT
		$new_amount = $wallet_amount + $amount_deducted;
		
		$sql4 = "UPDATE wallets SET $initial_currency = $new_amount WHERE user_id = $user_id" ;
		
		$stmt4 = $db->prepare($sql4);
		
		$stmt4->execute();
		
		// UPDATE THE AVAILABILITY OF A RATE
		$stmt = $db->prepare($sql);
	
		$stmt->bindParam(":availability", $availability);
		
		$stmt->execute();
		
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
	}); 
	
	// Transfer money + accept rate
	$app->post("/api/v1/rate/transfer/{rate_id}" , function(Request $request , Response $response) {
	
	$rate_id = $request->getAttribute("rate_id");
	$user_id = $request->getParam("user_id");
	
	$availability = "0";
	$transfer_completion = "1";
	
	$sql = "UPDATE rates SET availability = :availability,transfer_completion = :transfer_completion WHERE rate_id = $rate_id";
	
	$sql1 = "SELECT user_id,availability,initial_currency,final_currency,rate,amount,transfer_completion FROM rates WHERE rate_id = $rate_id";

	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		// CHECK DATABASE FOR AVALABILITY
		$stmt1 = $db->query($sql1);
		$rate_details = $stmt1->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rate_details){
			echo '{"error":{"error":"rate does not exist"}';
			return $response->withStatus(404);
		};
		$key_availability = $rate_details[0]->availability;
		$initial_currency = $rate_details[0]->initial_currency;
		$final_currency =  $rate_details[0]->final_currency;
		$rate = $rate_details[0]->rate;
		$rate_amount = $rate_details[0]->amount;
		$key_transfer_completion = $rate_details[0]->transfer_completion;
		$rate_user_id = $rate_details[0]->user_id;
		
		$deduction_amount = $rate_amount * $rate;
		
		//echo "deduction_amount = $deduction_amount";
		

		if ($key_availability == "0"){
			echo '{"error":{"error":"rate is unavailable"}';
			return $response->withStatus(404);
		};
		
		if ($key_transfer_completion == "1"){
			echo '{"error":{"error":"rate is unavailable. transfer already completed"}';
			return $response->withStatus(404);
		};
		
		// FIND AMOUNT CUSTOMER HAS IN WALLET
		$sql2 = "SELECT $final_currency, $initial_currency FROM wallets WHERE user_id = $user_id";
		
		$stmt2 = $db->query($sql2);
		$wallet_details = $stmt2->fetchAll(PDO::FETCH_OBJ);
		
		if (!$wallet_details){
			echo '{"error":{"wallet":"user does not have a wallet"}';
			return $response->withStatus(404);
		};
		
		$wallet_amount = $wallet_details[0]->$final_currency;
		$wallet_amount_2 = $wallet_details[0]->$initial_currency;
		
		//echo "wallet amount 2 = $wallet_amount_2";
		
		if ($wallet_amount < $deduction_amount){
			echo '{"error":{"wallet":"User has insuffient funds to accept rate"}';
			return $response->withStatus(404);
		};
		
		$new_amount = $wallet_amount - $deduction_amount;
		
		$new_amount_2 = $wallet_amount_2 + $rate_amount;
		
		//echo "new amount 2 = $new_amount_2";
		
		// UPDATE RATE TRANSFER AND AVAILABILITY
		
		$stmt = $db->prepare($sql);
	
		$stmt->bindParam(":availability", $availability);
		$stmt->bindParam(":transfer_completion", $transfer_completion);
		
		$stmt->execute(); 
		
		// UPDATE AMOUNT IN USER WALLET
		
		
		$sql3 = "UPDATE wallets SET $final_currency = $new_amount,
									$initial_currency = $new_amount_2
									WHERE user_id = $user_id";
		
		$stmt3 = $db->prepare($sql3);
		
		$stmt3->execute(); 
		
		//SELECT AMOUNT IN RATE SETTERS WALLET
		$sql4 = "SELECT $final_currency FROM wallets WHERE user_id = $rate_user_id";
		
		$stmt4 = $db->query($sql4);
		$setter_wallet_details = $stmt4->fetchAll(PDO::FETCH_OBJ);
		
		if (!$setter_wallet_details){
			echo '{"error":{"wallet":"user does not have a wallet"}';
			return $response->withStatus(404);
		};
		
		$setter_wallet_amount = $setter_wallet_details[0]->$final_currency;
		
		$setter_Wallet_new_amount = $setter_wallet_amount + $deduction_amount;
		
		// UPDATE AMOUNT IN rate setter USER WALLET
		$sql5 = "UPDATE wallets SET $final_currency = $setter_Wallet_new_amount WHERE user_id = $rate_user_id";
		
		$stmt5 = $db->prepare($sql5);
		
		$stmt5->execute();
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
	});
	
	//create a wallet
	$app->post("/api/v1/wallet/{customer_id}", function(Request $request, Response $response){
	
	$customer_id = $request->getAttribute("customer_id");
	$NGN = "0";
	$USD = "0";
	$GBP = "0";
	
	$sql = "INSERT INTO wallets (customer_id,NGN,GBP,USD)
	SELECT :customer_id,:NGN,:USD,:GBP FROM wallets
	WHERE NOT EXISTS (SELECT customer_id FROM wallets WHERE customer_id = :customer_id)
	LIMIT 1"; 
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->prepare($sql);
	
		$stmt->bindParam(":customer_id", $customer_id);
		$stmt->bindParam(":NGN", $NGN);
		$stmt->bindParam(":USD", $USD);
		$stmt->bindParam(":GBP", $GBP);
		
		$stmt->execute();
		
		$succsesful = $stmt->rowCount();
		
		if ($succsesful == 0){
			echo '{"error":{"wallet":"user alreay has a wallet"}';
			return $response->withStatus(404);
		}
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
	});
	
	// get users wallet amount
	$app->get("/api/v1/wallet/{user_id}", function(Request $request, Response $response){
	$user_id = $request->getAttribute("user_id");
	
	$sql = "SELECT * FROM wallets WHERE user_id = $user_id";
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		$stmt = $db->query($sql);
		$wallet = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$wallet){
			echo '{"error":{"user":"user does not have a wallet"}';
			return $response->withStatus(404);
		};
		
		$db = null;
		
		echo json_encode($wallet);  
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
});
	
	// add money to wallet
	$app->post("/api/v1/wallet/add/{user_id}", function(Request $request, Response $response){
	
	$user_id = $request->getAttribute("user_id");
	$amount = $request->getParam("amount");
	$currency = $request->getParam("currency");
	
	if (($currency != "USD" ) && ($currency != "GBP") && ($currency != "NGN")){
		echo '{"error": {"currency":"currency not recognised"}';
		return $response->withStatus(404);
	};
	
	$sql1 = "SELECT $currency FROM wallets WHERE user_id = $user_id";
	
	try{
		// Get database object
		$db = new db();
		//connection
		$db = $db->connect();
		
		// get current wallet amount
		$stmt1 = $db->query($sql1);
		$wallet_details = $stmt1->fetchAll(PDO::FETCH_OBJ);
		
		if (!$wallet_details){
			echo '{"error":{"wallet":"User has no wallet"}';
			return $response->withStatus(404);
		};
		
		$wallet_amount = $wallet_details[0]->$currency;
		
		$new_wallet_amount = $wallet_amount + $amount;
		
		//update wallet amount
		
		$sql = "UPDATE wallets SET $currency = $new_wallet_amount WHERE user_id = $user_id";
		
		$stmt = $db->prepare($sql);
		
		$stmt->execute();
		
		$succsesful = $stmt->rowCount();
		
		if ($succsesful == 0){
			echo '{"error":{"wallet":"user alreay has a wallet"}';
			return $response->withStatus(404);
		}
		
	} catch(PDOExecption $e){
		
		echo '{"error": {"text": ".$e->getMessage()."}';
	}
	
	});
	
 ?>