<?php
		require_once 'config.php';
		
		class InterfaceService{
			private $table_ques='questiontable';
			private $table_ans='answerstable';
			private $table_user='usertable';
			private $connection;
			
			public function __construct(){
				$this->connection=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
				$this->throwExceptionOnError($this->connection);
				if($this->connection){
 					//echo'u r connected';
				}		
			}
			
			//insert row queries 
			public function insertRowInQuestionTable($q_id, $q_subject, $q_content, $q_date, $q_timestamp, $q_link, $q_userId,$q_userNick,$q_numAnswers,$q_numComments,$q_ChosenAnswer,$q_AnswererId,$q_AnswererNick,$q_AnswerTimestamp,$q_AnswerAwardTimestamp)
			{
				//echo "<br><br>";
				//echo $q_content;
				$badChar=array("'");
				$emptyStr="";
				$q_content1=str_replace($badChar,$emptyStr, $q_content);
				$q_subject1=str_replace($badChar,$emptyStr, $q_subject);
				$q_ChosenAnswer1=str_replace($badChar,$emptyStr, $q_ChosenAnswer);
				$q_AnswererNick1=str_replace($badChar,$emptyStr, $q_AnswererNick);
				$q_userNick1=str_replace($badChar,$emptyStr, $q_userNick);
				//echo $q_content1;
				//str_replace("'", "", $q_subject);
				//echo "<br><br>";
				$result=mysqli_query($this->connection,"Insert into questiontable values('$q_id', '$q_subject1', '$q_content1', '$q_date', '$q_timestamp','$q_link','$q_userId','$q_userNick1',$q_numAnswers,$q_numComments,'$q_ChosenAnswer1','$q_AnswererId','$q_AnswererNick1','$q_AnswerTimestamp','$q_AnswerAwardTimestamp')");
				if ( false===$result ) {
					printf("error: %s\n", mysqli_error($this->connection));
					echo $q_id."<br>";
				}
				else {
					//echo 'question done.';
				}
			}
			
			public function insertRowInAnswersTable($q_id, $ans_content, $ans_reference, $ans_best, $ans_userId, $ans_userNick,$ans_date, $ans_timestamp)
			{
				//echo "<br><br>";
				//echo $q_content;
				$badChar=array("'");
				$emptyStr="";
				$ans_content1=str_replace($badChar,$emptyStr, $ans_content);
				$ans_userNick1=str_replace($badChar,$emptyStr, $ans_userNick);
				$ans_reference1=str_replace($badChar,$emptyStr, $ans_reference);
				//echo $q_content1;
				//echo "<br><br>";
				$result=mysqli_query($this->connection,"Insert into answerstable values('$q_id', '$ans_content1', '$ans_reference1', '$ans_best', '$ans_userId','$ans_userNick1','$ans_date','$ans_timestamp')");
				if ( false===$result ) {
					printf("error: %s\n", mysqli_error($this->connection));
					echo $q_id."<br>";
				}
				else {
					//echo 'answers done.';
				}
			}
			
			public function insertRowInUserTable($q_id,$user_profile_id,$user_level,$user_points,$user_percentageBestAnswers,$user_noAnswers,$user_noQuestions){
				$result=mysqli_query($this->connection,"Insert into usertable values('$q_id','$user_profile_id', '$user_level','$user_points','$user_percentageBestAnswers','$user_noAnswers','$user_noQuestions')");
				if ( false===$result ) {
					printf("error: %s\n", mysqli_error($this->connection));
					echo $q_id."<br>";
				}
				else {
					//echo 'answers done.';
				}
			}
			
			
			public function getAllUserIdsFromQuestionTable(){
				$query="SELECT q_id from questiontable";
				$stmt=mysqli_prepare($this->connection,$query);
				$this->throwExceptionOnError();
				
				mysqli_stmt_execute($stmt);
				$this->throwExceptionOnError();
				
				mysqli_stmt_bind_result($stmt,$userId->q_id);
								
				//mysqli_stmt_fetch($stmt);
				while(mysqli_stmt_fetch($stmt)){
					$rows[]=$userId;
					$userId=new stdClass();
					mysqli_stmt_bind_result($stmt,$userId->q_id);					
				}
				
				mysqli_stmt_free_result($stmt);
				//print_r($rows);
				return $rows;			
				
				
				
				/*if ( false===$result ) {
					printf("error: %s\n", mysqli_error($this->connection));					
				}
				else{
					
				}*/
			}
			
			
			//show all rows
			public function getAllInterface(){
				$stmt=mysqli_prepare($this->connection,"SELECT * from $this->table_ques");
				$this->throwExceptionOnError();
			}
			
			//close db connection
			public function closeConnection(){
				mysqli_close($this->connection);
			}
			
			//session time
			/*public function insertSessionTime($eid,$startTimeValue,$field3){
// 				echo $field3;
// 				echo $startTimeValue;
// 				echo $eid;
// 				mysqli_query($this->connection,"UPDATE basetable SET global_clk='5.4' WHERE Email='pop'");
				mysqli_query($this->connection,"UPDATE basetable SET $field3='$startTimeValue' WHERE Email='$eid'");
			}
			
			//insert global and stofhla times
			public function insertTimeFields($eid,$TimeValue,$field3){
 				$minutes=floor($TimeValue/60);
 				$hours=floor($TimeValue/3600);
 				$seconds=$TimeValue-($hours*3600+$minutes*60);
 				$TimeTotal="$hours:$minutes:$seconds";
 				mysqli_query($this->connection,"UPDATE basetable SET $field3='$TimeTotal' WHERE Email='$eid'");
 			}
			
			
			//insert email field
			public function insertEmail($emailId){
				mysqli_query($this->connection,"INSERT INTO $this->tablename (Email)
					VALUES ('$emailId')");
			}
			
			//insert stofhla fields
			public function insertStofhlaResponses($field1,$eid,$field3){
				mysqli_query($this->connection,"UPDATE basetable SET $field3='$field1' WHERE Email='$eid'");				
			}
			
			//insert ss fields
			public function insertSsResponses($field1,$eid,$field3){
				mysqli_query($this->connection,"UPDATE basetable SET $field3='$field1' WHERE Email='$eid'");
			}
			
			//insert bph fields
			public function insertBPHResponses($field1,$eid,$field3){
				mysqli_query($this->connection,"UPDATE basetable SET $field3='$field1' WHERE Email='$eid'");
			}*/
			
			private function throwExceptionOnError($link=null){
				if($link==null){
					$link=$this->connection;
				}
				if(mysqli_error($link)){
					$msg=mysqli_errno($link).":".mysqli_error($link);
					throw new Exception('MySql Error - '.$msg);
				}
			}
		}
	?>