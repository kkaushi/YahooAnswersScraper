<?php
//Database Connection
require_once 'databaseInterfaceService.php';



function getQuestionsForHealth($objDB,$offsetStart,$maxOffset){
	
	$BASE_URL = "https://query.yahooapis.com/v1/public/yql";
	
	for($offsetCurr=$offsetStart;$offsetCurr<$maxOffset;$offsetCurr+=50)
	{
		// Form YQL query and build URI to YQL Web service
		//$yql_query = "select * from answers.getbycategory(0,50) where category_id=396545018 and type=\"resolved\"";
		$yql_query = "select * from answers.getbycategory(".$offsetCurr.",50) where category_id=396545018 and type=\"resolved\" and sort=\"ans_count_desc\"";
		//select * from answers.getbycategory(10000,50) where category_id=396545018 and type="resolved" and sort="date_asc"
		
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
				//getAllAnswers($objDB, "20140523144037AApXbqy");
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
    			$phpObj->query->results->Question->type,
    			$phpObj->query->results->Question->Subject,
    			$phpObj->query->results->Question->Content,
    			$phpObj->query->results->Question->Date,
    			$phpObj->query->results->Question->Timestamp,
    			$phpObj->query->results->Question->Link,
    			$phpObj->query->results->Question->Category->id,
    			$phpObj->query->results->Question->Category->content,
    			$phpObj->query->results->Question->UserId,
    			$phpObj->query->results->Question->UserNick,
    			$phpObj->query->results->Question->UserPhotoURL,
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
function getUserDetails($objDB){
	
	$var=$objDB->getAllUserIdsFromQuestionTable();
	echo "Questions Id:<br>= = = = = = = = =<br>";
	
	$max=sizeof($var);
	for($ctr=1295;$ctr<$max;$ctr++){
		//echo "max is: ".$max."<br>";
		//echo $var[0]->q_id."<br>";
		
		$httpQuery="https://answers.yahoo.com/question/index?qid=".$var[$ctr]->q_id;
		echo $httpQuery."<br>";
		//}
		
		// Make call with cURL
		$sess = curl_init($httpQuery);
		curl_setopt($sess, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($sess, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($sess, CURLOPT_SSL_VERIFYHOST,  2);
		$html = curl_exec($sess);
		
		$dom = new DOMDocument();
		# Parse the HTML 
		# The @ before the method call suppresses any warnings that
		# loadHTML might throw because of invalid HTML in the page.
		@$dom->loadHTML($html);
		
		# Iterate over all the <a> tags
		foreach($dom->getElementsByTagName('div') as $currDiv) {
			# Show the <a href>
			if($currDiv->getAttribute('class')=="profile"){
				//echo $currDiv->getAttribute('id')."<br>";
				$userId = $currDiv->getAttribute('id');
				break;
			}	
		}
		/*
		echo "<br>User Id:<br>= = = = = = = = =<br>";
		echo $userId."<br>";	
		echo "<br>Data:<br>= = = = = = = = =";
		*/	
		
		//Create query
		//$yql_query_url="https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22https%3A%2F%2Fanswers.yahoo.com%2Factivity%3Fshow%3DWAJQZ466237DDAH64PWA36KYOE%26t%3Dg%22%20and%20compat%3D%22html5%22&format=json&callback=";
		$yql_query_url="https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22https%3A%2F%2Fanswers.yahoo.com%2Factivity%3Fshow%3D".$userId."%26t%3Dg%22%20and%20compat%3D%22html5%22&format=json&callback=";
		//echo $yql_query_url."<br>";
		
		// Make call with cURL
		$session = curl_init($yql_query_url);
		curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($session, CURLOPT_SSL_VERIFYHOST,  2);
		$json = curl_exec($session);
		
		// Convert JSON to PHP object
		$phpObj =  json_decode($json);
		//$elemFind = $json->getElementById('level-text');
		if(!is_null($phpObj->query->results)){
			/*
			echo "<br>Level: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[0]->div[1]->span[2]->p->content."<br>";
			echo "Questions: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[0]->div->a->span[0]->content."<br>";
			echo "Answers: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[1]->div->a->span[0]->content."<br>";
			echo "Best Answers: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[2]->div->a->span[0]->content."<br>";
			echo "Total Points: ".$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[3]->div->span[0]->content."<br>";
			*/
			
			
			$user_level= $phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[0]->div[1]->span[2]->p->content;
			$user_points=$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[3]->div->span[0]->content;
			$user_percentageBestAnswers=$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[2]->div->a->span[0]->content;
			$user_noAnswers=$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[1]->div->a->span[0]->content;
			$user_noQuestions=$phpObj->query->results->body->div[0]->div->div[1]->div->div->div->div[0]->div[0]->div[1]->div->table->tbody->tr->td[0]->div->a->span[0]->content;
						
			echo "<br>Level: ".$user_level."<br>";
			echo "Questions: ".$user_noQuestions."<br>";
			echo "Answers: ".$user_noAnswers."<br>";
			echo "Best Answers: ".$user_percentageBestAnswers."<br>";
			echo "Total Points: ".$user_points."<br>";
			echo "ctr is:".$ctr."<br>";
			
			$objDB->insertRowInUserTable($var[$ctr]->q_id,$userId,$user_level,$user_points,$user_percentageBestAnswers,$user_noAnswers,$user_noQuestions);
		}		
		//echo "===================================================================================================<br>";
	}
}

$offsetStart=$_POST["offsetBegin"];
$noOfQuestions=$_POST["noOfQuestions"];
$phpTimeOut=$_POST["phpTimeOut"];
//echo $offsetStart."<br>".$noOfQuestions."<br>".$phpTimeOut;

set_time_limit($phpTimeOut);
$YahooDB=new InterfaceService();
getQuestionsForHealth($YahooDB,$offsetStart,$offsetStart+$noOfQuestions);
//$userId="6BZ4QZE6ZEAXED4NXLXR6HSS7A";
//getUserDetails($YahooDB);
$YahooDB->closeConnection();

?>

