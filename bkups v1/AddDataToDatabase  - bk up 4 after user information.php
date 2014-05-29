<?php
//Database Connection
require_once 'databaseInterfaceService.php';



function getQuestionsForHealth($objDB,$offsetStart,$maxOffset){
	
	$BASE_URL = "https://query.yahooapis.com/v1/public/yql";
	
	for($offsetCurr=$offsetStart;$offsetCurr<$maxOffset;$offsetCurr+=50)
	{
		// Form YQL query and build URI to YQL Web service
		//$yql_query = "select * from answers.getbycategory(0,50) where category_id=396545018 and type=\"resolved\"";
		$yql_query = "select * from answers.getbycategory(".$offsetCurr.",50) where category_id=396545018 and type=\"resolved\"";
		//echo $yql_query."<br>";
		$yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";	
		
		// Make call with cURL
		$session = curl_init($yql_query_url);
		curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($session, CURLOPT_SSL_VERIFYHOST,  2);
		$json = curl_exec($session);
		
		// Convert JSON to PHP object
		$phpObj =  json_decode($json);
		//echo 'true or FAlse';
		if(!is_null($phpObj->query->results)){
			//echo $phpObj->query->results->Question->id;
			foreach ($phpObj->query->results->Question as $idCurr){
				//getAllAnswers($objDB, $idCurr);
				//echo '<br>'.$idCurr->id.'<br>';
				getAllAnswers($objDB, $idCurr->id);
			}		
		}
	}	
}

function getAllAnswers($objDB,$qid){
	
	//$qid = "20140523121925AAnK483";//TEST QUESTION ID
	//$qid = "20140523144037AApXbqy";

	$BASE_URL = "https://query.yahooapis.com/v1/public/yql";
				
	// Form YQL query and build URI to YQL Web service
    $yql_query = "select * from answers.getquestion where question_id=\"".$qid."\"";
	$yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";
    
    // Make call with cURL
    $session = curl_init($yql_query_url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($session, CURLOPT_SSL_VERIFYHOST,  2);
    $json = curl_exec($session);
    
    // Convert JSON to PHP object
    $phpObj =  json_decode($json);
    
    //echo '<br><br>------------New values--------------------<br><br>';
	
    //Confirm that results were returned before parsing
    if(!is_null($phpObj->query->results)){
    	//echo 'true OR fALSE';
    	//echo $phpObj->query->results->Question->Answers->Answer[0]->Content;
    	$objDB->insertRowInQuestionTable(
    			$phpObj->query->results->Question->id,
    			$phpObj->query->results->Question->Subject,
    			$phpObj->query->results->Question->Content,
    			$phpObj->query->results->Question->Date,
    			$phpObj->query->results->Question->Timestamp,
    			$phpObj->query->results->Question->Link,
    			$phpObj->query->results->Question->UserId,
    			$phpObj->query->results->Question->UserNick,
    			$phpObj->query->results->Question->NumAnswers,
    			$phpObj->query->results->Question->NumComments,
    			$phpObj->query->results->Question->ChosenAnswer,
    			$phpObj->query->results->Question->ChosenAnswererId,
    			$phpObj->query->results->Question->ChosenAnswererNick,
    			$phpObj->query->results->Question->ChosenAnswerTimestamp,
    			$phpObj->query->results->Question->ChosenAnswerAwardTimestamp
    	);
    	/*echo '<br><br>'.
      	$phpObj->query->results->Question->id.'<br><br>'.
      	$phpObj->query->results->Question->Subject.'<br><br>'.
      	$phpObj->query->results->Question->Content.'<br><br>'.
      	$phpObj->query->results->Question->Date.'<br><br>'.
      	$phpObj->query->results->Question->Timestamp.'<br><br>'.
      	$phpObj->query->results->Question->Link.'<br><br>'.
      	$phpObj->query->results->Question->UserId.'<br><br>'.
      	$phpObj->query->results->Question->UserNick.'<br><br>'.
      	$phpObj->query->results->Question->NumAnswers.'<br><br>'.
      	$phpObj->query->results->Question->NumComments.'<br><br>'.
      	$phpObj->query->results->Question->ChosenAnswer.'<br><br>'.
      	$phpObj->query->results->Question->ChosenAnswererId.'<br><br>'.
      	$phpObj->query->results->Question->ChosenAnswererNick.'<br><br>'.
      	$phpObj->query->results->Question->ChosenAnswerTimestamp.'<br><br>'.
      	$phpObj->query->results->Question->ChosenAnswerAwardTimestamp.'<br><br>==================';*/
    	
      	$ctr=0;
      	if($phpObj->query->results->Question->NumAnswers>1){
	      	foreach ($phpObj->query->results->Question->Answers->Answer as $objCurr){
	    		//echo $objCurr->Content."<br><br>";
	    		/*echo "<br>".$objCurr->Content."<br><br>".$objCurr->Reference."<br><br>".
	    				$objCurr->Best."<br><br>".
	    				$objCurr->UserId."<br><br>".
	    				$objCurr->UserNick."<br><br>".
	    				$objCurr->Date."<br><br>".
	    				$objCurr->Timestamp."<br><br>=======================================<br>";*/
	    		
	    		//echo '<br>';
	    		//echo $objCurr->UserId.'<br>';
	    		//echo $objCurr->UserId;
	    		//echo $ctr;
	    		//$ctr++;
	    		$objDB->insertRowInAnswersTable(
	    				$phpObj->query->results->Question->id,
	    				$objCurr->Content,
	    				$objCurr->Reference,
	    				$objCurr->Best,
	    				$objCurr->UserId,
	    				$objCurr->UserNick,
	    				$objCurr->Date,
	    				$objCurr->Timestamp
	    				);
    		}
    	}
    	else{
    		
    		$objCurr=$phpObj->query->results->Question->Answers->Answer;
    		/*echo "<br>".$objCurr->Content."<br><br>".$objCurr->Reference."<br><br>".
    				$objCurr->Best."<br><br>".
    				$objCurr->UserId."<br><br>".
    				$objCurr->UserNick."<br><br>".
    				$objCurr->Date."<br><br>".
    				$objCurr->Timestamp."<br><br>=======================================<br>";*/   
    		$objDB->insertRowInAnswersTable(
    				$phpObj->query->results->Question->id,
    				$objCurr->Content,
    				$objCurr->Reference,
    				$objCurr->Best,
    				$objCurr->UserId,
    				$objCurr->UserNick,
    				$objCurr->Date,
    				$objCurr->Timestamp
    		);
    	}    	
    }
}
function getUserDetails($userId){
	
	$BASE_URL = "https://query.yahooapis.com/v1/public/yql";
	
	// Form YQL query and build URI to YQL Web service
	//$yql_query = "select * from answers.getbycategory(".$offsetCurr.",50) where category_id=396545018 and type=\"resolved\"";
	$yql_query = "select * from html where url=\"https://answers.yahoo.com/activity?show=KYTLNNG2IXPRXDFMSNIWT32HGQ&t=g\" and compat=\"html5\"";
	//echo $yql_query."<br>";
	//$yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json".urlencode("env=store://datatables.org/alltableswithkeys");
	//$yql_query_url="https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22https%3A%2F%2Fanswers.yahoo.com%2Factivity%3Fshow%KYTLNNG2IXPRXDFMSNIWT32HGQ%26t%3Dg%22%20and%20compat%3D%22html5%22&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=";
	//$yql_query_url="https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22https%3A%2F%2Fanswers.yahoo.com%2Factivity%3Fshow%3DKYTLNNG2IXPRXDFMSNIWT32HGQ%26t%3Dg%22%20and%20compat%3D%22html5%22&format=json&callback=";
	$yql_query_url="https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22https%3A%2F%2Fanswers.yahoo.com%2Factivity%3Fshow%3DWAJQZ466237DDAH64PWA36KYOE%26t%3Dg%22%20and%20compat%3D%22html5%22&format=json&callback=";
	//xml query
	//$yql_query_url="https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22https%3A%2F%2Fanswers.yahoo.com%2Factivity%3Fshow%3DKYTLNNG2IXPRXDFMSNIWT32HGQ%26t%3Dg%22%20and%20compat%3D%22html5%22";
	echo $yql_query_url."<br>";
	// Make call with cURL
	$session = curl_init($yql_query_url);
	curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($session, CURLOPT_SSL_VERIFYHOST,  2);
	$json = curl_exec($session);
	
	// Convert JSON to PHP object
	$phpObj =  json_decode($json);
	
	
	
	//$xmlObj=simplexml_load_file(file_get_contents($yql_query_url));
	
	
	//$elemFind = $json->getElementById('level-text');
	if(!is_null($phpObj->query->results)){
		echo "<br>Level: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[0]->div[1]->span[2]->p->content."<br>";
		echo "Questions: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[0]->div->a->span[0]->content."<br>";
		echo "Answers: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[1]->div->a->span[0]->content."<br>";
		echo "Best Answers: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[2]->div->a->span[0]->content."<br>";
		echo "Total Points: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[3]->div->span[0]->content."<br>";
		//$elemFind = $phpObj->query->results->getElementById('level-text');
		
		//echo $phpObj->query->results->Question->id;
		//foreach ($phpObj->query->results->body->div->div as $divCurr){
			//if($divCurr->id=="member-details"){
				echo 'got something!';
			//}
			/*if($spanCurr->id == "level-text")
			{
				echo $spanCurr->p->content;
			}*/
		//}
	}
		
}

$offsetStart=$_POST["offsetBegin"];
$noOfQuestions=$_POST["noOfQuestions"];
$phpTimeOut=$_POST["phpTimeOut"];
//echo $offsetStart."<br>".$noOfQuestions."<br>".$phpTimeOut;

set_time_limit($phpTimeOut);
$YahooDB=new InterfaceService();
//getQuestionsForHealth($YahooDB,$offsetStart,$offsetStart+$noOfQuestions);
$userId="6BZ4QZE6ZEAXED4NXLXR6HSS7A";
getUserDetails($userId);
$YahooDB->closeConnection();

?>

