<?php
//Database Connection
require_once 'databaseInterfaceService.php';
//require_once('XMLToArray.php');



function getQuestionsForHealth($objDB){
	
	$BASE_URL = "https://query.yahooapis.com/v1/public/yql";
	
	// Form YQL query and build URI to YQL Web service
	$yql_query = "select * from answers.getbycategory(0,50) where category_id=396545018 and type=\"resolved\"";
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
			echo '<br>'.$idCurr->id.'<br>';
			getAllAnswers($objDB, $idCurr->id);
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
    
    echo '<br><br>------------New values--------------------<br><br>';
	
    //Confirm that results were returned before parsing
    if(!is_null($phpObj->query->results)){
    	echo 'true OR fALSE';
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
	    		echo "<br>".$objCurr->Content."<br><br>".$objCurr->Reference."<br><br>".
	    				$objCurr->Best."<br><br>".
	    				$objCurr->UserId."<br><br>".
	    				$objCurr->UserNick."<br><br>".
	    				$objCurr->Date."<br><br>".
	    				$objCurr->Timestamp."<br><br>=======================================<br>";
	    		
	    		echo '<br>';
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
    		echo "<br>".$objCurr->Content."<br><br>".$objCurr->Reference."<br><br>".
    				$objCurr->Best."<br><br>".
    				$objCurr->UserId."<br><br>".
    				$objCurr->UserNick."<br><br>".
    				$objCurr->Date."<br><br>".
    				$objCurr->Timestamp."<br><br>=======================================<br>";   
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

set_time_limit(300);
$YahooDB=new InterfaceService();
//$qid=$_POST["q_id"];
//echo $qid;
//echo "<br><br>";
//$qContent= $_POST["q_content"];
//echo $qContent;
//echo "<br><br>";
//$qSubject= $_POST["q_subject"];
//echo $qSubject;
//echo "<br><br>";
//$qdate = $_POST["q_date"];
//echo $qdate;
//echo "<br><br>";
//$qTimestamp = $_POST["q_timestamp"];
//echo $qTimestamp;
//echo "<br><br>";
//$qlink=$_POST["q_link"];
//echo $qlink;
//echo "<br><br>";
//$quserId= $_POST["q_userId"];
//echo $quserId;
//echo "<br><br>";
//$quserNick=$_POST["q_userNick"];
//echo $quserNick;
//echo "<br><br>";
//$qnumAnswers= $_POST["q_numAnswers"];
//echo $qnumAnswers;
//echo "<br><br>";
//$qnumComments= $_POST["q_numComment"];
//echo $qnumComments;
//$YahooDB->insertRowInQuestionTable($qid,$qSubject,$qContent,$qdate,$qTimestamp,$qlink,$quserId,$quserNick,$qnumAnswers,$qnumComments);
//$YahooDB->getAllInterface();
//get User Data
//getAllAnswers($YahooDB,$qid);
getQuestionsForHealth($YahooDB);
$YahooDB->closeConnection();

?>

