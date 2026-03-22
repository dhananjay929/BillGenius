<?php
#Base Functions
function electricity_bill_include($file_name){	return include_once($file_name);}

function electricity_bill_debug($line_number,$file_name,$function,$var = NULL)
{
	/*	if(isset($_SESSION)){
	    	$somecontent = "[".gmdate('Y-m-d H:i:s')."]  \t ".$file_name."\t".$function."\t Line_number:".$line_number."\t".$var."\t".electricity_bill_json_converter($_SESSION)."\r\n";
		}else{
			$somecontent = "[".gmdate('Y-m-d H:i:s')."]  \t ".$file_name."\t".$function."\t Line_number:".$line_number."\t".$var."\t\r\n";
		}
		$filename = DEFINE_PATH_LOGS . date('Y-m-d').'.log';
		if (!$handle = fopen($filename, 'a')) { echo "Cannot open file ($filename)"; exit; }
		if (fwrite($handle, $somecontent) === FALSE) { echo "Cannot write to file ($filename)"; exit; }*/

}

function electricity_bill_print_query($line_number,$file_name,$query,$msc)
{
	
		$somecontent = "[".date('Y-m-d H:i:s')."]  \t ".$file_name."\t Line_number:".$line_number."\t Time:".$msc."\t".$query."\r\n";
		$filename = dirname(__FILE__) . '/query_log/'.date('Y-m-d').'.txt';
		if (!$handle = fopen($filename, 'a')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
	
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
	
}

function electricity_bill_json_converter() {
		$numargs = func_num_args();
		$parameters = array();
		for($index=0;$index < $numargs;$index++) { $parameters['parameter_'.($index+1)] = func_get_arg($index); }
		return json_encode($parameters);;
	}
	

#Base Functions

#Session Functions
function electricity_bill_get_session($line,$file,$session_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($session_name));	return $_SESSION[$session_name];}
function electricity_bill_session_isset($line,$file,$session_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($session_name));	return isset($_SESSION[$session_name]);}
function electricity_bill_unset_session($line,$file,$session_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($session_name));unset($_SESSION[$session_name]);}
function electricity_bill_set_session( $line,$file,$session_name,$session_value ){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($session_name,$session_value)); $_SESSION[$session_name] = $session_value;}
function electricity_bill_all_unset_session( $line,$file ){electricity_bill_debug($line,$file,__FUNCTION__); session_unset();}
function electricity_bill_destroy_session( $line,$file ){electricity_bill_debug($line,$file,__FUNCTION__); session_destroy();}
#Session Functions

#Database Related Functions
function electricity_bill_commit_off($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);global $con;mysqli_autocommit($con, FALSE); }
function electricity_bill_commit_on($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);global $con;mysqli_autocommit($con, TRUE); }
function electricity_bill_commit($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);global $con;mysqli_commit($con);}
function electricity_bill_rollback($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);global $con;mysqli_rollback($con);}

function electricity_bill_query($line,$file,$query){
		if($_SERVER['REMOTE_ADDR'] == '122.163.75.216'){
			//echo "<br/>".$file."------".$line."------".$query;
		}
		electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($query));
		 global $con;
		 $msc = microtime(true);
		 $result = mysqli_query($con,$query);
		 $msc = microtime(true)-$msc;
		 electricity_bill_print_query($line,$file,$query,$msc);
		 if($result == false){
		    $t = microtime(true);
		    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
		    $datetime = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
			$template_data_array = array('DATE_TIME','Query_EXEC_TIME','FILE_NAME','LINE_NUMBER','ERROR_TYPE','QUERY','ERROR');
			$template_value_array = array(electricity_bill_date_format_with_formatted(__LINE__,__FILE__,$datetime->format("Y-m-d H:i:s")),$datetime->format("Y-m-d H:i:s"),$file,$line,'QUERY FAILED',$query,mysqli_error($con)); 
			global $mailTempalte;
			electricity_bill_send_mail(__LINE__,__FILE__,$mailTempalte['query_failed_error_content'],$template_data_array,$template_value_array,QUERY_FAILED_RECEIVER,$mailTempalte['query_failed_error_subject']);
		 }
		 return $result;
}
function electricity_bill_num_rows($line,$file,$result){electricity_bill_debug($line,$file,__FUNCTION__);global $con; $rows = mysqli_num_rows($result); return $rows;}
function electricity_bill_fetch_assoc($line,$file,$result){electricity_bill_debug($line,$file,__FUNCTION__);global $con;return mysqli_fetch_assoc($result);}
function electricity_bill_fetch_array($line,$file,$value){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($value));return mysqli_fetch_array($value);}
function electricity_bill_affected_rows($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);global $con;$rows = mysqli_affected_rows($con); return $rows;}
function electricity_bill_last_inserted($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);global $con;return mysqli_insert_id($con);}

#Database Related Functions

#Post Related Functions
function electricity_bill_print_post_data($line,$file,$content,$page)
{
	    electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($content,$page));
		$somecontent = "[".date('Y-m-d H:i:s')."]  \n".print_r($content,true)."\r\n";
		
		$filename = dirname(__FILE__) . '/app_post_data/'.$page.date('Y-m-d').'.txt';
		if (!$handle = fopen($filename, 'a')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
	
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
	
}
function electricity_bill_post_isset($line,$file,$post_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($post_name)); return isset($_POST[$post_name]);}
function electricity_bill_get_post_escape($line,$file,$post_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($post_name));global $con;return mysqli_real_escape_string($con,trim($_POST[$post_name]," "));	}
function electricity_bill_real_escape($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));global $con;return mysqli_real_escape_string($con,trim($string," "));	}
function electricity_bill_get_post($line,$file,$post_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($post_name));return $_POST[$post_name];	}
#Post Related Functions

#Send Related Functions
function electricity_bill_print_send_data($line,$file,$content,$page)
{
	    electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($content,$page));
		$somecontent = "[".date('Y-m-d H:i:s')."]  \n".print_r($content,true)."\r\n" ;
		$filename = dirname(__FILE__) . '/app_send_data/'.$page.date('Y-m-d').'.txt';
		if (!$handle = fopen($filename, 'a')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
	
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
	
}
#Send Related Functions

#Get Related Functions
function electricity_bill_get_get($line,$file,$get_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($get_name)); global $con; return mysqli_real_escape_string($con,trim($_GET[$get_name]));	}
function electricity_bill_get_isset($line,$file,$get_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($get_name));	return isset($_GET[$get_name]);}
#Get Related Functions

#Validation Related Functions
function electricity_bill_validation($line,$file,$string,$blank,$max,$min,$nospace,$nospecialch,$alphaonly,$numberonly,$field_name){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string,$blank,$max,$min,$nospace,$nospecialch,$alphaonly,$numberonly,$field_name));
	if($blank){
		if($string == ""){
			return BLANK_FIELDS.$field_name;
		}
	}
	if(strlen($string) <$min){
			return "Please enter at least ".$min." characters in ".$field_name;
	}
	if(strlen($string) >$max){
			return "Please enter no more than ".$max." characters in ".$field_name;
	}
	if($nospecialch){
		if(electricity_bill_hsaspecialcharacter($string)){
			return SPECIAL_CHARS. $field_name;
		}
	}
	if($nospace){
		if(preg_match('/\s/',$string)){
			return "No space please and don't leave it empty in ".$field_name;	
		}
	}
	if($alphaonly){
		if (!ctype_alpha($string)) {
			return "Please enter only letters in ".$field_name;	
		}
	}
	if($numberonly){
		if (!is_numeric($string)) {
			return "Please enter only numeric value in ".$field_name;	
		}
	}
	return "";
	}
function electricity_bill_no_single_quotes($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));if (preg_match("/[']/", $string)){return true;}else{return false;}}
function electricity_bill_no_double_quotes($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));if (preg_match('/["]/', $string)){return true;}else{return false;}}
function electricity_bill_validate_mobile_number($line,$file,$phone)
{
	if($phone === '0000000000')
	{
		electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($phone));
		return false;
	}
	if( !preg_match("/^[7-9][0-9]{9}$/i", $phone))
	{
		electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($phone));
		return false;
	}
	else
	{
		electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($phone));
	 	return true;
	}
}
function electricity_bill_filter_var($line,$file,$email){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($email)); return filter_var($email, FILTER_VALIDATE_EMAIL);  };
function electricity_bill_is_alphabet_consist($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string)); return preg_match("/[a-z]/i", $string);  };
#Validation Related Functions

#Encode-Decode Related Functions
function electricity_bill_hash($line,$file,$content){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($content));$output = sha1($content);	if(!$output) {electricity_bill_display(__LINE__,__FILE__,"error in hashing sha1 input "."\r\n"); } else { return $output;}}
function electricity_bill64_encode($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));return base64_encode($string);}
function electricity_bill64_decode($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));return base64_decode($string);}
function encrypt_password($line,$file,$password) {
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($password));
	$length = strlen($password);
	$encyPassword = "";
	for($i=0;$i<$length;$i++) {
		$utf8Character = substr($password,$i,1);
		list(, $ord) = unpack('N', mb_convert_encoding($utf8Character, 'UCS-4BE', 'UTF-8'));
		$temp = (int)$ord + 74;
		$encyPassword .= pad(__LINE__,__FILE__,$temp,4);
	}
	return $encyPassword;
}
#Encode-Decode Related Functions

#General Functions
function electricity_bill_file_open($line,$file,$filename,$mode){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($filename,$mode)); return fopen($filename, $mode);}
function electricity_bill_file_write($line,$file,$resource,$text){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($resource,$text)); return fwrite($resource, $text);}
function electricity_bill_array_merge(){
	$args = func_get_args();
	$temp_args = array();
	electricity_bill_debug($args[0],$args[1],__FUNCTION__);	
	for($index=2;$index<sizeof($args)-1;$index++){
		$temp_args = array_merge($temp_args,$args[$index]);			
	}
	return $temp_args;
}
function electricity_bill_display($line,$file,$content){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($content));echo $content;}
function electricity_bill_unset($line,$file,& $arguement){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($arguement));unset($arguement); }
function electricity_bill_unset_assoc($line,$file,& $arguement_array,$arguement_list){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($arguement_array,$arguement_list));
	for($index=0;$index<sizeof($arguement_list);$index++){
		unset($arguement_array[$arguement_list[$index]]); 
	}
}
function electricity_bill_get_request($line,$file,$request_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($request_name));return $_REQUEST[$request_name];	}
function electricity_bill_request_isset($line,$file,$request_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($request_name));return isset($_REQUEST[$request_name]);}
function electricity_bill_array_key_exists($line,$file,$search_str, $array){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($search_str,$array));return array_key_exists($search_str, $array);}
function electricity_bill_string_replace($line,$file,$find,$replace,$string){
	//electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($find,$replace,$string));
	return str_replace($find,$replace,$string);
}
function electricity_bill_lower($line,$file,$str){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($str)); return strtolower($str);}
function electricity_bill_ucwords($line,$file,$value){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($value));return  ucwords($value); }
function electricity_bill_explode($line,$file,$string_to_explode, $string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string_to_explode, $string));return explode($string_to_explode, $string);}
function electricity_bill_upper($line,$file,$value){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($value));return strtoupper($value);}
function electricity_bill_get_user_browser($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);return $_SERVER['HTTP_USER_AGENT'];}
function electricity_bill_in_array($line,$file,$value, $arr){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($value, $arr));return in_array($value, $arr);}

function electricity_bill_find_string_length($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));return strlen($string);}

function electricity_bill_find_position($line,$file,$string, $findme){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string, $findme));return strpos($string, $findme);}
function electricity_bill_http_build_query($line,$file,$data_array){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($data_array));return http_build_query($data_array);}
function electricity_bill_file_put_contents($line,$file,$filename,$content){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($filename,$content));return file_put_contents($filename,$content);}
function electricity_bill_file_content_by_line($line,$file,$myFile){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($myFile));return file($myFile);}
function electricity_bill_trim($line,$file,$value){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($value));return trim($value);}
function electricity_bill_string_length($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));return strlen($string);}
function electricity_bill_substring_two_params($line,$file,$string,$value_1,$value_2){ electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string,$value_1,$value_2));return substr($string,$value_1,$value_2);}
function electricity_bill_substring_one_params($line,$file,$string,$value_1){ electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string,$value_1));return substr($string,$value_1);}
function electricity_bill_sleep($line,$file,$time){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($time));sleep($time);}
function electricity_bill_is_numeric($line,$file,$value){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($value));return is_numeric($value);}
function electricity_bill_json_decode($line,$file,$data){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($data));return json_decode($data,true);}
function electricity_bill_json_decode_1($line,$file,$data){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($data));return json_decode($data);}
function electricity_bill_preg_split($line,$file,$split_by,$data){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($split_by,$data));return preg_split($split_by,$data);}
function electricity_bill_preg_split_2($line,$file,$split_by,$data){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($split_by,$data));return preg_split($split_by,$data,-1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);}
function electricity_bill_sizeof($line,$file,$data){/*electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($data));*/return sizeof($data);}
function electricity_preg_replace($line,$file,$pattern,$replacement,$data){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($pattern,$replacement,$data));return preg_replace($pattern,$replacement,$data);}
function electricity_urlencode($line,$file,$data){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($data));return urlencode($data);}
function electricity_strrchr($line,$file,$data,$find){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($data,$find));return strrchr($data,$find);}
function get_line_number($line,$file,$lines,$search){
	//electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($lines,$search));
	$line_number = false;
	while (list($key, $line) = each($lines) and !$line_number) {
	   $line_number = (strpos($line, $search) !== FALSE) ? $key : $line_number;
	}
	return $line_number;
}
function get_line_number_occurrence($line,$file,$lines,$search,$occurrence =1){
	//electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($lines,$search));
	$line_number = false;
	$find_occurrence=0;
	while (list($key, $line) = each($lines) and !$line_number) {
	   $line_number = (strpos($line, $search) !== FALSE) ? $key : $line_number;
	   if($line_number != false){
	   	$find_occurrence++;
	   }
	   if($find_occurrence != $occurrence){
	   	$line_number = false;
	   }
	}
	return $line_number;
}
function electricity_get_total_occurance_of_substring($line,$file,$to_find,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($to_find,$string));return substr_count($string, $to_find);}
function get_string_between($line,$file,$string, $start, $end){
	
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string, $start, $end));
	if(strpos($string,$start) !== false ){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		$temp_value = substr($string, $ini, $len);
		if(($temp_value !== false) && ($temp_value != '') ) {
			return trim($temp_value);
		}else{
			return false;	
		}
	}else{
		return false;	
	}
}
function electricity_bill_generate_bill_id($line,$file,$board,$ai_id){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($board,$ai_id));
	$bill_id = $board.'_'.date('y').'_'.date('m').'_'.electricity_bill_left_pad(__LINE__,__FILE__,$ai_id);
	return $bill_id;	
}
function get_line_number_by_regexp($line,$file,$pattern,$arr) {
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($pattern,$arr));
	$mixed  = preg_grep ($pattern, $arr);
	$line_number = false;
	foreach($mixed as $line_num => $value) {
		$line_number = $line_num;
	}
	return $line_number;
}	
function pad($line,$file,$num, $size) {
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($num, $size));
	$s = $num."";
	while (strlen($s) < $size) $s = "0".$s;
	return $s;
}
function mb_stripos_all($line,$file,$haystack, $needle) {
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($haystack, $needle));
 	$s = 0;
 	$i = 0;
 	while(is_integer($i)) {
 		$i = mb_stripos($haystack, $needle, $s);
 		if(is_integer($i)) {
      		$aStrPos[] = $i;
      		$s = $i + mb_strlen($needle);
    	}
  	}
 
  if(isset($aStrPos)) {
    return $aStrPos;
  } else {
    return false;
  }
}
function electricity_bill_unlink($line,$file,$file_name){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($file_name));unlink($file_name);}
function electricity_bill_left_pad($line,$file,$input){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($input));return str_pad($input, 11, "0", STR_PAD_LEFT);}
function electricity_bill_count($line,$file,$value){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($value));return count($value);}
function electricity_bill_array_empty($line,$file,$array){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($array)); return empty($array);}
function electricity_bill_isset($line,$file,$var){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($var)); return isset($var);}
function electricity_bill_preg_match($line,$file,$search_char,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($search_char,$string));return preg_match($search_char,$string);}
function electricity_bill_disk_total_space($line,$file,$variable){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($variable));return disk_total_space($variable);}
function electricity_bill_disk_free_space($line,$file,$variable){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($variable));return disk_free_space($variable);}
function electricity_bill_floatval($line,$file,$variable){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($variable));return floatval($variable);}
function electricity_bill_intval($line,$file,$variable){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($variable));return intval($variable);}
function electricity_bill_number_format($line,$file,$num,$decimal_point){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($num,$decimal_point));
	
	$explrestunits = "" ;
	$minus = '';
	$after_point = '00';
	if(floatval($num) < 0){
		$minus = substr($num,0,1);
		$num = substr($num,1,strlen($num));
	}
	
	if(strpos($num,'.') !== false){
		$temp_val = number_format($num,$decimal_point).'';
		$temp_val_array_point = explode('.',$temp_val);
		$after_point = $temp_val_array_point[1];
		$temp_val_array = explode('.',$num);
		$num = 	$temp_val_array[0];
		
	} 
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $minus.$explrestunits.$lastthree.'.'.$after_point;
    } else {
        $thecash = $minus.$num.'.'.$after_point;
    }
    return $thecash;
	//return number_format($num,$decimal_point);
	}
	
function electricity_bill_number_format_php($line,$file,$num,$decimal_point){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($num,$decimal_point));return number_format($num,$decimal_point);}
	
function electricity_bill_number_preg_match_all($line,$file,$explode,$data){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($explode,$data));
	preg_match_all($explode, $data,$matches, PREG_OFFSET_CAPTURE);
	return $matches;	
}
function electricity_bill_json_encode($line,$file,$array){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($array));return json_encode($array,JSON_PRETTY_PRINT);}
function electricity_bill_json_encode_1($line,$file,$array){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($array));return json_encode($array);}
function electricity_bill_hsaspecialcharacter($string){if (preg_match('/[\'^£!$%&*()}{@#~?><>,|=_+¬-]/', $string)){return true;}else{return false;}}
function electricity_bill_get_file_ext($line,$file,$filename){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($filename));return strtolower(pathinfo($filename, PATHINFO_EXTENSION));}
function electricity_bill_file_get_contents($line,$file,$data){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($data)); return file_get_contents($data);}
function electricity_bill_array_push($line,$file,& $array,$data){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($array,$data)); array_push($array, $data);}
function electricity_bill_move_uploaded_file($line,$file,$filename,$save_dir){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($filename,$save_dir));return move_uploaded_file($filename,$save_dir);}
function electricity_bill_array_to_string($line,$file,$lower_limit,$upper_limit,$array){
	 electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($lower_limit,$upper_limit,$array));
	$retuen_value = "";
	for($index = $lower_limit;$index<=$upper_limit;$index++){
		$retuen_value .= " ".$array[$index];
	}
	return trim($retuen_value);
}	
function electricity_bill_round($line,$file,$value,$dec){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($value,$dec));return round($value,$dec) ;}
function electricity_bill_strtotime($line,$file,$time){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($time));return strtotime($time);}

function electricity_bill_filename($line,$file,$filename){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($filename));
	$file_name_temp = preg_replace('/[^a-zA-Z0-9_.]/', '', $filename);
	$file_name_temp = str_replace(' ','_',$file_name_temp);
	return strtolower($file_name_temp);
}
function electricity_bill_array_slice($line,$file,& $array,$index){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($array,$index));return array_slice($array,$index);}
function electricity_bill_implode($line,$file,$implode_by,& $array){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($implode_by,$array));return implode($implode_by,$array);}
function electricity_bill_stripslashes($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));return stripslashes($string);}
function electricity_bill_abs($line,$file,$string){electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string));return abs($string);}

#General Functions

#Date-Time Related Functinos
function electricity_bill_datetime_date_year_month_date_time_IST($line,$file,$datetime){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($datetime));	
	$temp_timestamp = strtotime($datetime);
	//date_default_timezone_set("Asia/Calcutta");	
	return date('Y-m-d H:i:s',strtotime('+330 minutes',($temp_timestamp)));
}
function electricity_bill_date_format_with_formatted($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	if($date != '0000-00-00 00:00:00'){
		return date("j M, Y",strtotime($date));
	}else{
		return "0000-00-00 00:00:00";	
	}
}
function electricity_bill_date_format_with_formatted_2($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	if($date != '0000-00-00 00:00:00'){
		return date("j-M-Y",strtotime($date));
	}else{
		return "0000-00-00 00:00:00";	
	}
}
function electricity_bill_date_format_with_formatted_3($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	if($date != '0000-00-00 00:00:00'){
		return date("j-m-Y",strtotime($date));
	}else{
		return "0000-00-00 00:00:00";	
	}
}
function electricity_bill_month_formatted_2($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	if($date != '0000-00-00 00:00:00'){
		return date("M",strtotime($date));
	}else{
		return "0000-00-00 00:00:00";	
	}
}
function electricity_bill_custom_date_time_format_2($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	return date("j-M-y (g:i A)", strtotime($date));
}
function electricity_bill_datapicker_date_format($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	return date("m/d/Y", strtotime($date));
}
function electricity_bill_database_date_format($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));
	return date("Y-m-d", strtotime($date));
}
function electricity_bill_custom_date_format($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	if($date == "0000-00-00" || $date == "0000-00-00 00:00:00"){
		return " ";	
	}else{
	return date("dS F, Y", strtotime($date));
	}
}
function electricity_bill_custom_date_time_format($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	return date("dS F, Y (g:i A)", strtotime($date));
}
function electricity_bill_time_format($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));	
	return date("g:i A ",strtotime($date));
}
function get_full_month($line,$file,$month){
	
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($month));
	switch(strtolower($month)){
			case "jan":
				return "JANUARY";
				break;
			case "feb":
				return "FEBRUARY";
				break;
			case "mar":
				return "MARCH";
				break;
			case "apr":
				return "APRIL";
				break;
			case "may":
				return "MAY";
				break;
			case "jun":
				return "JUNE";
				break;
			case "jul":
				return "JULY";
				break;
			case "aug":
				return "AUGUST";
				break;
			case "sep":
				return "SEPTEMBER";
				break;
			case "sept":
				return "SEPTEMBER";
				break;
			case "oct":
				return "OCTOBER";
				break;
			case "nov":
				return "NOVEMBER";
				break;
			case "dec":
				return "DECEMBER";
				break;
			default:
				return $month;
				break;
		}
}
function electricity_bill_current_year($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);return date("Y");}
function electricity_bill_current_month($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);return date("F");}
function electricity_bill_DGVCL_date($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);return date('F', strtotime('-1 month'));}
function electricity_bill_get_time_in_seconds($line,$file){electricity_bill_debug($line,$file,__FUNCTION__); return time(); }
function electricity_bill_date_to_database_format($line,$file,$date,$explodewith){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date,$explodewith));
	$temp_arr = explode($explodewith,trim($date));
	return $temp_arr[2].'-'.$temp_arr[1].'-'.$temp_arr[0];	
}
function get_month_number($line,$file,$month){
	
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($month));
switch(strtolower($month)){
		case "jan":
		case "january":
			return "01";
			break;
		case "feb":
		case "february":
			return "02";
			break;
		case "mar":
		case "march":
			return "03";
			break;
		case "apr":
		case "april":
			return "04";
			break;
		case "may":
			return "05";
			break;
		case "jun":
		case "june":
			return "06";
			break;
		case "jul":
		case "july":
			return "07";
			break;
		case "aug":
		case "august":
			return "08";
			break;
		case "sep":
		case "september":
			return "09";
			break;
		case "sept":
			return "09";
			break;
		case "oct":
		case "october":
			return "10";
			break;
		case "nov":
		case "november":
			return "11";
			break;
		case "dec":
		case "december":
			return "12";
			break;	
	}		
}
function get_full_year($line,$file,$year){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($year));
	$dt = DateTime::createFromFormat('y', $year);
	return $dt->format('Y');
}
function electricity_bill_date_to_database_format_2($line,$file,$date,$explodewith){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date,$explodewith));
	$temp_arr = explode($explodewith,trim($date));
	if(strlen($temp_arr[2]) == 2){
		return get_full_year(__LINE__,__FILE__,$temp_arr[2]).'-'.get_month_number(__LINE__,__FILE__,$temp_arr[1]).'-'.$temp_arr[0];	
	}else{
		return $temp_arr[2].'-'.get_month_number(__LINE__,__FILE__,$temp_arr[1]).'-'.$temp_arr[0];	
	}
}
function electricity_bill_date_to_database_format_3($line,$file,$date,$explodewith){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date,$explodewith));
	$temp_arr = explode($explodewith,trim($date));
	if(strlen($temp_arr[2]) == 4){
		return $temp_arr[2].'-'.get_month_number(__LINE__,__FILE__,$temp_arr[1]).'-'.$temp_arr[0];
	}else{
		return get_full_year(__LINE__,__FILE__,$temp_arr[2]).'-'.get_month_number(__LINE__,__FILE__,$temp_arr[1]).'-'.$temp_arr[0];	
	}	
}
function electricity_bill_date_to_database_format_4($line,$file,$date,$explodewith){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date,$explodewith));
	$temp_arr = explode($explodewith,trim($date));
	if(strlen($temp_arr[2]) == 2){
		return get_full_year(__LINE__,__FILE__,$temp_arr[2]).'-'.$temp_arr[1].'-'.$temp_arr[0];	
	}else{
		return $temp_arr[2].'-'.$temp_arr[1].'-'.$temp_arr[0];
	}
}
function electricity_bill_get_timestamp($line,$file,$user_time){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($user_time));
	$date = new DateTime($user_time);
	$date = $date->getTimestamp();
	return $date;
}
function electricity_bill_add_days_to_date($line,$file,$date,$days){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date,$days));
	$date=date_create($date);
	date_add($date,date_interval_create_from_date_string($days." days"));
	return date_format($date,"Y-m-d");
}
function electricity_bill_yesterday_date($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);return date('Y-m-d',strtotime("-1 days"));}

function electricity_bill_get_formatted_date_from_given_date($line,$file,$date){electricity_bill_debug($line,$file,__FUNCTION__,$date);return date('Y-m-d H:i:s',strtotime($date));}

function electricity_bill_today_date($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);return date('Y-m-d H:i:s');}
function electricity_bill_today_date_year_month_date($line,$file){electricity_bill_debug($line,$file,__FUNCTION__);return date('Y-m-d');}

function convert_ascii($line,$file,$string) { 
  electricity_bill_debug($line,$file,__FUNCTION__,$string);
  // Replace Single Curly Quotes
  $search[]  = chr(226).chr(128).chr(152);
  $replace[] = "'";
  $search[]  = chr(226).chr(128).chr(153);
  $replace[] = "'";
  // Replace Smart Double Curly Quotes
  $search[]  = chr(226).chr(128).chr(156);
  $replace[] = '"';
  $search[]  = chr(226).chr(128).chr(157);
  $replace[] = '"';
  // Replace En Dash
  $search[]  = chr(226).chr(128).chr(147);
  $replace[] = '--';
  // Replace Em Dash
  $search[]  = chr(226).chr(128).chr(148);
  $replace[] = '---';
  // Replace Bullet
  $search[]  = chr(226).chr(128).chr(162);
  $replace[] = '*';
  // Replace Middle Dot
  $search[]  = chr(194).chr(183);
  $replace[] = '*';
  // Replace Ellipsis with three consecutive dots
  $search[]  = chr(226).chr(128).chr(166);
  $replace[] = '...';
  // Apply Replacements
  $string = str_replace($search, $replace, $string);
  // Remove any non-ASCII Characters
  $string = preg_replace("/[^\x01-\x7F]/","", $string);
  return $string; 
}
function electricity_bill_check_valid_month ($line,$file,$month) { 
  	electricity_bill_debug($line,$file,__FUNCTION__,$month);
  	$x = DateTime::createFromFormat('M', $month);
   	if(!$x){
		return false;	
	}else{
		return true;
	}
}
#Date-Time Related Functinos

#Mail Related Functions
function electricity_bill_send_mail_attachment($line,$file,$template,$template_date_array,$template_value_array,$receiver,$subject,$filename,$path)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($template,$template_date_array,$template_value_array,$receiver,$subject,$filename,$path));
	$template_date_array = array_map("add_email_template_code", $template_date_array);
	$email_data = str_replace($template_date_array, $template_value_array, $template);
	$subject = str_replace($template_date_array, $template_value_array, $subject);
	$email_id = "newbills@altius.billpro.online";
	$file = $filename;

    $message = $email_data."
    File link : https://altius.billpro.online/".$file;
    $headers = 'From: info@altius.billpro.online' . "\r\n" .
    'Reply-To: info@altius.billpro.online' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
   	if(! mail($email_id, $subject, $message, $headers))
	{
		electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__);
		return "FAIL";
	}
	else
	{
		electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__);
		return "SUCCESS";
	}
/*
	$file_size = filesize($file);
	$handle = fopen($file, "r");
	$content = fread($handle, $file_size);
	fclose($handle);
	$content = chunk_split(base64_encode($content));
	$uid = md5(uniqid(time()));
	$from = "arup@codez.in";
	$email_id =	$receiver;

	$email_id = "rupak@codez.in";
	$message = $email_data;
	
    $headers = "Reply-To: ".$from."\r\n"; 
    $headers .= "Return-Path: ".$from."\r\n"; 
    $headers .= "From: ".$from."\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	//$headers .= "Content-type: text/html; charset=iso-8859-1\r\nX-Priority: 3\r\nX-Mailer: PHP". phpversion() ."\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
	$headers .= "This is a multi-part message in MIME format.\r\n";
	$headers .= "--".$uid."\r\n";
	$headers .= "Content-type:text/plain; charset=iso-8859-1\r\n";
	$headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
	$headers .= $message."\r\n\r\n";
	$headers .= "--".$uid."\r\n";
	$headers .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
	$headers .= "Content-Transfer-Encoding: base64\r\n";
	$headers .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
	$headers .= $content."\r\n\r\n";
	$headers .= "--".$uid."--";
	
	
	if(!mail($email_id,$subject,$message,$headers, 'O DeliveryMode=b'))
	{
		electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__);
		return "FAIL";
	}
	else
	{
		electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__);
		return "SUCCESS";
	}*/
}
function electricity_bill_send_mail($line,$file,$template,$template_date_array,$template_value_array,$receiver,$subject)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($template,$template_date_array,$template_value_array,$receiver,$subject));
	$template_date_array = array_map("add_email_template_code", $template_date_array);
	$email_data = str_replace($template_date_array, $template_value_array, $template);
	$subject = str_replace($template_date_array, $template_value_array, $subject);
	$email_id =	$receiver;
	$_MAIL = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
			   "http://www.w3.org/TR/html4/loose.dtd">
			<html lang="en">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<meta name="viewport" content="initial-scale=1.0">    <!-- So that mobile webkit will display zoomed in -->
				<meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->
			
				<title>BillPro</title>
				<style type="text/css">
			
					/* Resets: see reset.css for details */
					.ReadMsgBody { width: 100%; background-color:#d8d7d5;}
					.ExternalClass {width: 100%; background-color:#d8d7d5;}
					.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100%;}
					body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}
					body {margin:0; padding:0;}
					table {border-spacing:0;}
					table td {border-collapse:collapse;}
					.yshortcuts a {border-bottom: none !important;}
			
			
					/* Constrain email width for small screens */
					@media screen and (max-width: 600px) {
						table[class="container"] {
							width: 95% !important;
						}
					}
			
					/* Give content more room on mobile */
					@media screen and (max-width: 480px) {
						td[class="container-padding"] {
							padding-left: 12px !important;
							padding-right: 12px !important;
						}
					}
			
			
					/* Styles for forcing columns to rows */
					@media only screen and (max-width : 600px) {
			
						/* force container columns to (horizontal) blocks */
						td[class="force-col"] {
							display: block;
							padding-right: 0 !important;
						}
						table[class="col-3"] {
							/* unset table align="left/right" */
							float: none !important;
							width: 100% !important;
			
							/* change left/right padding and margins to top/bottom ones */
							margin-bottom: 12px;
							padding-bottom: 12px;
							border-bottom: 1px solid #eee;
						}
			
						/* remove bottom border for last column/row */
						table[id="last-col-3"] {
							border-bottom: none !important;
							margin-bottom: 0;
						}
			
						/* align images right and shrink them a bit */
						img[class="col-3-img"] {
							float: right;
							margin-left: 6px;
							max-width: 130px;
						}
					}
			
				</style>
			</head>
			<body style="margin:0; padding:10px 0;" bgcolor="#d8d7d5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
			
			
			<!-- 100% wrapper (grey background) -->
			<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#d8d7d5" >
			  <tr>
				<td align="center" valign="top" bgcolor="#ebebeb" style="background-color:#d8d7d5;">
			
				  <!-- 600px container (white background) -->
				  <table border="0" width="100%" cellpadding="0" cellspacing="0" class="container" bgcolor="#ffffff" style="border:1px solid #d8d7d5;">
					<tr>
					  <td class="container-padding" bgcolor="#ffffff" style="background-color: #ffffff; padding-left: 30px; padding-right: 30px; font-size: 13px; line-height: 20px; font-family: Helvetica, sans-serif; color: #333;" align="left">
						<br>
			
						<!-- ### BEGIN CONTENT ### -->
			
						<div>
						<img src="https://altius.billpro.online/assets/images/logo.png" style="padding-top:10px; padding-bottom:10px; width:100px" align="BillPro Logo">
						</div>
						<br>
			
						<!--/ end .columns-container-->
			
					</td>
				</tr>
				<tr>
					<td class="container-padding" bgcolor="#ffffff" style="background-color: #ffffff; padding-left: 30px; padding-right: 30px; font-size: 13px; line-height: 20px; font-family: Helvetica, sans-serif; color: #333;" align="left">
					   
			
			
			
						<div style="font-weight: bold; font-size: 18px; line-height: 24px; color: #000; border-top: 1px solid #ddd;"><br>
						Hi Team,</div>
						<br>'.$email_data.'
           
            <br>
            <br/>
Sincerely,<br>
                  The BillPro Team
            </div>

            <!-- ### END CONTENT ### -->
            <br><br>

          </td>
        </tr>
        
      </table>
      <!--/600px container -->

    </td>
  </tr>

</table>
<!--/100% wrapper-->
</body>
</html>
';
			
	$from = "BillPro Notifications <noreply@altius.billpro.online>";
	$headers = "Reply-To: ".$from."\r\n";
    $headers .= "Return-Path: ".$from."\r\n"; 
    $headers .= "From: ".$from."\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\nX-Priority: 3\r\nX-Mailer: PHP". phpversion() ."\r\n";

	//mail("kousik@codez.in","My subject","hello world");
	
	if(!mail($email_id,$subject,$_MAIL,$headers, 'O DeliveryMode=b'))
	{
		electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__);
		return "FAIL";
	}
	else
	{
		//echo("Mail Sent");
		electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__);
		return "SUCCESS";
	}
}

function electricity_bill_send_mail_exception($line,$file,$template,$template_date_array,$template_value_array,$receiver,$subject)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($template,$template_date_array,$template_value_array,$receiver,$subject));
	
	$template_date_array = array_map("add_email_template_code", $template_date_array);
	$email_data = str_replace($template_date_array, $template_value_array, $template);
	$subject = str_replace($template_date_array, $template_value_array, $subject);
	$email_id =	$receiver;
	$_MAIL = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="initial-scale=1.0">    <!-- So that mobile webkit will display zoomed in -->
    <meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->

    <title>BillPro</title>
    <style type="text/css">

        /* Resets: see reset.css for details */
        .ReadMsgBody { width: 100%; background-color:#d8d7d5;}
        .ExternalClass {width: 100%; background-color:#d8d7d5;}
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100%;}
        body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}
        body {margin:0; padding:0;}
        table {border-spacing:0;}
        table td {border-collapse:collapse;}
        .yshortcuts a {border-bottom: none !important;}


        /* Constrain email width for small screens */
        @media screen and (max-width: 600px) {
            table[class="container"] {
                width: 95% !important;
            }
        }

        /* Give content more room on mobile */
        @media screen and (max-width: 480px) {
            td[class="container-padding"] {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }
        }


        /* Styles for forcing columns to rows */
        @media only screen and (max-width : 600px) {

            /* force container columns to (horizontal) blocks */
            td[class="force-col"] {
                display: block;
                padding-right: 0 !important;
            }
            table[class="col-3"] {
                /* unset table align="left/right" */
                float: none !important;
                width: 100% !important;

                /* change left/right padding and margins to top/bottom ones */
                margin-bottom: 12px;
                padding-bottom: 12px;
                border-bottom: 1px solid #eee;
            }

            /* remove bottom border for last column/row */
            table[id="last-col-3"] {
                border-bottom: none !important;
                margin-bottom: 0;
            }

            /* align images right and shrink them a bit */
            img[class="col-3-img"] {
                float: right;
                margin-left: 6px;
                max-width: 130px;
            }
        }

    </style>
</head>
<body style="margin:0; padding:10px 0;" bgcolor="#d8d7d5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">


<!-- 100% wrapper (grey background) -->
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#d8d7d5" >
  <tr>
    <td align="center" valign="top" bgcolor="#ebebeb" style="background-color:#d8d7d5;">

      <!-- 600px container (white background) -->
      <table border="0" width="100%" cellpadding="0" cellspacing="0" class="container" bgcolor="#ffffff" style="border:1px solid #d8d7d5;">
        <tr>
          <td class="container-padding" bgcolor="#ffffff" style="background-color: #ffffff; padding-left: 30px; padding-right: 30px; font-size: 13px; line-height: 20px; font-family: Helvetica, sans-serif; color: #333;" align="left">
            <br>

            <!-- ### BEGIN CONTENT ### -->

            <div>
            <img src="https://www.codez.in/billpro/white_billpro_neosigma.png" style="padding-top:10px; padding-bottom:10px;" align="BillPro Logo">
            </div>
            <br>

            <!--/ end .columns-container-->

        </td>
    </tr>
    <tr>
        <td class="container-padding" bgcolor="#ffffff" style="background-color: #ffffff; padding-left: 30px; padding-right: 30px; font-size: 13px; line-height: 20px; font-family: Helvetica, sans-serif; color: #333;" align="left">
           

            <div style="font-weight: bold; font-size: 18px; line-height: 24px; color: #000; border-top: 1px solid #ddd;"><br>
            Hi Team,</div>
            <br>'.$email_data.'            
  <table cellspacing="0" cellpadding="0" border="1" style="background:#2E57A7;border:solid 1px #5ea352;border-radius:3px 3px 3px 3px; clear:both;" align="center">
<tbody>
	<tr>
    	<td style="border:none;padding:5px 45px;">
					<b><span style="font-size:14px;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                    	<a target="_blank" href="https://altius.billpro.online" style="text-decoration:none;">
                        	<span style="color:white;text-decoration:none">Login to BILLPRO</span></a><u></u><u></u></span></b>
		</td>
    </tr>
</tbody>
</table>
           
            <br>
            <br/>
Sincerely,<br>
                  The BillPro Team
            </div>

            <!-- ### END CONTENT ### -->
            <br><br>

          </td>
        </tr>
        
      </table>
      <!--/600px container -->

    </td>
  </tr>

</table>
<!--/100% wrapper-->
</body>
</html>

';
	
	$from = "BillPro System <noreply@altius.billpro.online>";
	$headers = "Reply-To: ".$from."\r\n";
    $headers .= "Return-Path: ".$from."\r\n"; 
    $headers .= "From: ".$from."\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\nX-Priority: 3\r\nX-Mailer: PHP". phpversion() ."\r\n";

	//mail("kousik@codez.in","My subject","hello world");
	
	if(!mail($email_id,$subject,$_MAIL,$headers, 'O DeliveryMode=b'))
	{
		electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__);
		return "FAIL";
	}
	else
	{
		//echo("Mail Sent");
		electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__);
		return "SUCCESS";
	}
}


function add_email_template_code($n)
{
	electricity_bill_debug(__LINE__,__FILE__,__FUNCTION__,electricity_bill_json_converter($n));
    return("%%".$n."%%");
}
#Mail Related Functions

#Notification Related Functions
function electricity_bill_single_notification_add($line,$file,$message,$board_id,$user_id)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($message,$board_id,$user_id));	
	$err_flag = 0;
	$insert_into_notification_query = "INSERT INTO tbl_notification (fld_notification_type,fld_notification_message,fld_board_id,fld_by_user,fld_count) VALUES ('general','".$message."','".$board_id."','".$user_id."','1')";
	$insert_into_notification_query_result = electricity_bill_query(__LINE__,__FILE__,$insert_into_notification_query);
	if(electricity_bill_affected_rows(__LINE__,__FILE__)>0){
		
	}else{
		$err_flag = 1;
	}
	return $err_flag;
}
function electricity_bill_combined_notification_add($line,$file,$enum_value,$board_id){
		electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($enum_value,$board_id));	
		$insert_or_update_query = "";
		$count = "";
		$ai_id = "";
		$error_flag = 0;
		$check_for_already_inserted_notificatino_query = "SELECT tbl_notification.fld_ai_id,tbl_notification.fld_count FROM tbl_notification WHERE tbl_notification.fld_board_id = '".$board_id."' AND DATE(tbl_notification.fld_timestamp) = DATE(NOW()) AND tbl_notification.fld_notification_type = '".$enum_value."';";
		if($check_for_already_inserted_notificatino_query_result = electricity_bill_query(__LINE__,__FILE__,$check_for_already_inserted_notificatino_query)){
			if(electricity_bill_num_rows(__LINE__,__FILE__,$check_for_already_inserted_notificatino_query_result) > 0){
					$row = electricity_bill_fetch_assoc(__LINE__,__FILE__,$check_for_already_inserted_notificatino_query_result);
					$count = $row['fld_count'] + 1;
					$ai_id = $row['fld_ai_id'];
					
					$insert_or_update_query = "UPDATE tbl_notification SET tbl_notification.fld_count = '".$count."',tbl_notification.fld_timestamp = NOW() WHERE tbl_notification.fld_ai_id = '".$ai_id."'";
			}else{
				$insert_or_update_query = "INSERT INTO tbl_notification(fld_notification_type,fld_count,fld_board_id) VALUES('".$enum_value."','1','".$board_id."');";	
			}
			if($insert_or_update_query != ''){
				$insert_or_update_query_result = electricity_bill_query(__LINE__,__FILE__,$insert_or_update_query);
				if(electricity_bill_affected_rows(__LINE__,__FILE__)>0){
					
				}else{
					$error_flag = 1;
				}
			}else{
				$error_flag = 1;
			}
		}else{
			$error_flag = 1;
		}
		return $error_flag;
}
#Notification Related Functions
#Log Change Related Functions
function electricity_bill_change_status_log($line,$file,$bill_id, $status, $user_id) {
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($bill_id, $status, $user_id));
	$status_array = array(
		'' => 'Select a Status',
		'generated' => 'Generated',
		'approved_for_payment' => 'Approved for Payment',
		'processing_for_payment' => 'Processing Payment',
		'paid' => 'Paid',
		'on_hold' => 'On Hold'
	);
	
	$board_id_list_array = array();
	$board_id_list_query = 'SELECT fld_ai_id, fld_discom_id AS fld_board_id , fld_electricity_board, fld_internalsite_id, fld_bill_id AS fld_internal_bill_no FROM ebill_overview WHERE fld_ai_id IN ('.$bill_id.');';
	if($result = electricity_bill_query(__LINE__,__FILE__,$board_id_list_query)) {
		if(electricity_bill_num_rows(__LINE__,__FILE__,$result)) {
			while($row = electricity_bill_fetch_assoc(__LINE__,__FILE__,$result)) {
				$board_id_list_array[$row['fld_ai_id'].'_']['fld_board_id'] = $row['fld_board_id'];
				$board_id_list_array[$row['fld_ai_id'].'_']['fld_electricity_board'] = $row['fld_electricity_board'];
				$board_id_list_array[$row['fld_ai_id'].'_']['fld_internalsite_id'] = $row['fld_internalsite_id'];
				$board_id_list_array[$row['fld_ai_id'].'_']['fld_internal_bill_no'] = $row['fld_internal_bill_no'];
			}
		}
	}
	
	$success = TRUE;
	$bill_id_array = explode(',', $bill_id);
	foreach($bill_id_array as $key => $value) {
		$change_status_log_query = 'INSERT INTO tbl_bills_log(fld_bill_id, fld_internalsite_id, fld_status, fld_edited_by) VALUES (' . $value . ',' . $board_id_list_array[$value.'_']['fld_board_id'] . ', "'.$status.'", '.$user_id.');';
		if(!electricity_bill_query(__LINE__,__FILE__,$change_status_log_query)) {
			$success = FALSE;
		} else {
			$message = str_replace("%%status%%", $status_array[$status], STATUS_CHANGE_NOTIFICATION_MESSAGE);
			$message = str_replace("%%internalsite_id%%", $board_id_list_array[$value.'_']['fld_internalsite_id'], $message);
			$message = str_replace("%%board%%", $board_id_list_array[$value.'_']['fld_electricity_board'], $message);
			$message = str_replace("%%internal_bill_no%%", $board_id_list_array[$value.'_']['fld_internal_bill_no'], $message);
			if(electricity_bill_single_notification_add(__LINE__,__FILE__,$message,$board_id_list_array[$value.'_']['fld_board_id'],$user_id) == 1) {
				$success = FALSE;
			}
		}
	}
	return $success;
}
#Log Change Related Functions
#Bill Check Related Functions
function electricity_bill_last_bill_check($line,$file, $consumer_id,$board){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($consumer_id,$board));
	$select_consumer_exist_query = "SELECT fld_ai_internalsite_id,fld_organizationsite_id,fld_consumer_id,`fld_last_bill_checked`,`fld_last_bill_date`,`fld_last_bill_amount`,`fld_last_bill_duedate` FROM tbl_sites WHERE fld_consumer_id = '".$consumer_id."' AND fld_discom_id = '".$board."'"	;
	if($select_consumer_exist_query_result = electricity_bill_query(__LINE__,__FILE__,$select_consumer_exist_query)){
		if(electricity_bill_num_rows(__LINE__,__FILE__,$select_consumer_exist_query_result) > 0){
			$row = electricity_bill_fetch_assoc(__LINE__,__FILE__,$select_consumer_exist_query_result);
			$ai_id = $row['fld_ai_internalsite_id'];
			$site_id = $row['fld_organizationsite_id'];
			$last_bill_fetch_date = $row['fld_last_bill_checked'];
			$bill_date = $row['fld_last_bill_date'];
			$due_date = $row['fld_last_bill_duedate'];
			$total_amount = $row['fld_last_bill_amount'];

			$insert_query = "INSERT INTO tbl_fetch_bills(fld_internal_site_id,fld_circle_id,fld_discom_id,fld_zone_id,fld_message,fld_last_success_time,fld_last_bill_fetch_date,
			fld_bill_date,tbl_fetch_bills.fld_due_date,fld_expected_bill_generated_date,fld_saved_credential,fld_status,tbl_fetch_bills.fld_amount,
			tbl_fetch_bills.fld_message_count,fld_timestamp,fld_ip) VALUES 
			('".$ai_id."','777','".$board."','777','CHECKING STARTED','0000-00-00 00:00:00','".$last_bill_fetch_date."','".$bill_date."','".$due_date."','0000-00-00','','1','".$total_amount."','1',NOW(),'cron');";
			electricity_bill_query(__LINE__,__FILE__,$insert_query);
			
			$update_user_last_checked_time_query = "UPDATE tbl_sites SET fld_last_bill_checked = NOW() ,fld_cron_count = 0 WHERE  fld_ai_internalsite_id = ".$ai_id.";";
			$update_user_last_checked_time_result = electricity_bill_query(__LINE__,__FILE__,$update_user_last_checked_time_query);
			if(electricity_bill_affected_rows(__LINE__,__FILE__)>0){
				
			}else{
				
			}
		}
	}
}
#Bill Check Related Functions
#Fetch Page Related Function
function fetch_captcha_file($api_parse_array){
	$api_parse_array['fileContent'] = file_get_contents($api_parse_array['file']);
	
	$ch = curl_init();
	$url = 'http://185.170.212.30/solveCaptcha.php';

	// Set cURL options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($api_parse_array));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Execute the request
	$response = curl_exec($ch);

	// Check for errors
	if (curl_errno($ch)) {
		$error_msg = 'Error:' . curl_error($ch);
	} 
	// Close cURL session
	curl_close($ch);

	if($error_msg == ""){
		$jsonString = str_replace("\f", " ", $response);
		$data = json_decode($jsonString, true);
		return preg_replace('/[\f\r\n\t]/', '',$data['captcha']);
	}else{
		return $error_msg;
	}
}
function fetch_parse_file($api_parse_array){

	$api_parse_array['file_content'] = file_get_contents($api_parse_array['file']);

	$ch = curl_init();

	$url = 'http://central-app1.myelectricity.co.in:26558/parse_bill.php';

	// Set cURL options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($api_parse_array));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Execute the request
	$response = curl_exec($ch);

	// Check for errors
	if (curl_errno($ch)) {
		$error_msg = 'Error:' . curl_error($ch);
	} 
	// Close cURL session
	curl_close($ch);

	if($error_msg == ""){
		return $response;
	}else{
		return false;
	}
}
function fetch_page($line,$file,$pass_discom_id,$url, $referer, $header, $post, $current_attempt = 0, $with_header = 1,$allow_302 = 0,$is_redirecturl=0,$allow_303 = 0) {
	
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($url, $referer, $header, $post, $current_attempt, $with_header,$allow_302));
	//echo "Calling: ".$url. $post."////Attempt: ".$current_attempt;
	$timeout = 120;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	
	if($pass_discom_id == 81){
		//curl_setopt($ch, CURLOPT_POST, 1);
	}
	if(!is_null($post)) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
	}
	
	if($url == 'https://billing.mpez.co.in/' || strchr($url,'https://portal.guvnl.in/services/validateConsumer.php')){
		// echo $url;
		$proxy_var = '159.89.167.246:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	$discoms_enable_proxy = array(46,29);
	if(electricity_bill_in_array(__LINE__,__FILE__,$pass_discom_id,$discoms_enable_proxy)) {
		//echo 'setting proxy : ' . $url;
		$proxy_var = '172.105.50.48:8086';
		//$proxy_var = '150.129.3.195:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	
	$discoms_enable_proxy = array(83,117,127,107,140,31,38,39);
	if(electricity_bill_in_array(__LINE__,__FILE__,$pass_discom_id,$discoms_enable_proxy)) {
		echo 'setting proxy : ' . $url;
		$proxy_var = '159.65.149.250:8086';
		//$proxy_var = '150.129.3.195:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	
	}

	if ( (24 == $pass_discom_id) && (electricity_bill_find_position(__LINE__,__FILE__,$url,'kescocp.eeslsmartmeter.in') !== false) ) {
		$proxy_var = '159.89.167.246:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	if($pass_discom_id == 81 || $pass_discom_id == 117 || $pass_discom_id == 143){
		$proxy_var = '172.105.50.48:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	if($url == 'https://billing.mpez.co.in/') {
		$proxy_var = '159.65.149.250:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	$discoms_enable_proxy_246 = array(30,34,35,36,74,77,99,103,27,37,43,42,148);
	if(electricity_bill_in_array(__LINE__,__FILE__,$pass_discom_id,$discoms_enable_proxy_246)) {
		//$proxy_var = '159.89.167.246:8086';
		$proxy_var = '159.65.149.250:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	$odisha_proxy = array(77);
	if(electricity_bill_in_array(__LINE__,__FILE__,$pass_discom_id,$odisha_proxy)){
		$proxy_var = '45.79.125.51:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	$discoms_enable_proxy_246 = array(28,146,102,105);
	if(electricity_bill_in_array(__LINE__,__FILE__,$pass_discom_id,$discoms_enable_proxy_246)) {
		$proxy_var = '159.89.167.246:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	if($pass_discom_id == 89) {
		$proxy_var = '159.65.149.250:8086';
		//$proxy_var = '139.59.66.158:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	if($pass_discom_id == 40 || $pass_discom_id == 59){
		$proxy_var = '159.65.149.250:8086';
		curl_setopt($ch, CURLOPT_PROXY, $proxy_var);
	}
	// echo $proxy_var;
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	if ($with_header == 1) {
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
	}
	if($pass_discom_id != 143){
		if($pass_discom_id == 8 || $pass_discom_id == 101){
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0');
		}else{
	
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)');
		}
	}
	if($pass_discom_id == 40){
		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); //saved cookies
	}
	if($pass_discom_id == 77){
		curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie_cesu.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie_cesu.txt'); //saved cookies
	}
	curl_setopt($ch, CURLOPT_REFERER, $referer);
	if (!is_null($header) && $header != '') {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	if($is_redirecturl == 1){
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);	
	}

	$data = curl_exec($ch);	
	
	if (false === $data) { 
		//Check if Curl Responds Properly
		sleep(10);
		if (2 == $current_attempt) {
			//echo "False: ".$url. "/////Curl Failure////Attempt: ".$current_attempt." ".curl_error($ch);;				
			return false;
		}
		return fetch_page(__LINE__,__FILE__,$pass_discom_id,$url, $referer, $header, $post, $current_attempt + 1,$allow_302,$is_redirecturl);
	}
	
	$response = curl_getinfo( $ch );
	
	if (1 == $allow_302) {
		if ($response['http_code'] == 302) { 
			return $data;
		}		
	}
	if ($response['http_code'] != 200) { 
	if ((303 == $response['http_code']) && (1 == $allow_303) ) { 
			return $response;		
	}
	//Check if Curl wsa able to fetch the page properly
		sleep(10);
		if (2 == $current_attempt) {
		//	echo "False: ".$url. "/////Code: ".$response['http_code']."////Attempt: ".$current_attempt;
			return false;
		}
		return fetch_page(__LINE__,__FILE__,$pass_discom_id,$url, $referer, $header, $post, $current_attempt + 1);
	}
	return $data;
}
#Fetch Page Related Function
#CESC Consumer Related Functions
function get_consumer_parameters_CESC($line,$file,$string,$fixed_data){
	
	//electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string,$fixed_data));
	$data_assoc = array();			
	for($index =0;$index<sizeof($fixed_data);$index++){
			$data = '';
			if(strpos($string,'<input type="hidden" name="'.$fixed_data[$index].'" value="') !== false ){
				$data = get_string_between(__LINE__,__FILE__,$string,'<input type="hidden" name="'.$fixed_data[$index].'" value="','"');
			}
			else {
				return false;
			}
			$data_assoc[$fixed_data[$index]] = $data;
	}
	return 	$data_assoc;		
}
#CESC Consumer Related Functions
#WBSEDCL Consumer Related Functions
function get_parameters_WBSEDCL($line,$file,$string,$fixed_data){
	
	//electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string,$fixed_data));
	$data_assoc = array();			
	for($index =0;$index<sizeof($fixed_data);$index++){
			$data = '';
			if(strpos($string,'<input type="hidden" name="'.$fixed_data[$index].'" value="') !== false ){
				$data = get_string_between(__LINE__,__FILE__,$string,'<input type="hidden" name="'.$fixed_data[$index].'" value="','"');
			}
			else {
				return false;
			}
			$data_assoc[$fixed_data[$index]] = $data;
	}
	return 	$data_assoc;		
}
#WBSEDCL Consumer Related Functions
#NDMC Consumer Related Functions
function get_parameters_NDMC($line,$file,$string,$fixed_data){
	
	//electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string,$fixed_data));
	$data_assoc = array();			
	for($index =0;$index<sizeof($fixed_data);$index++){
			$data = '';
			if(strpos($string,'<input type="hidden" id="'.$fixed_data[$index].'" name="'.$fixed_data[$index].'" value="') !== false ){
				$data = get_string_between(__LINE__,__FILE__,$string,'<input type="hidden" id="'.$fixed_data[$index].'" name="'.$fixed_data[$index].'" value="','"');
			}else{
				return false;
			}
			$data_assoc[$fixed_data[$index]] = $data;
	}
	return 	$data_assoc;		
}
#NDMC Consumer Related Functions
#AVVNL Consumer Related Functions
function get_consumer_parameters_AVVNL($line,$file,$string,$fixed_data){
	   // electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($string,$fixed_data));
		$data_assoc = array();			
		for($index =0;$index<sizeof($fixed_data);$index++){
				$data = '';
				if(strpos($string,'<input type="hidden" name="'.$fixed_data[$index].'" id="'.$fixed_data[$index].'" value="') !== false ){
					$data = get_string_between(__LINE__,__FILE__,$string,'<input type="hidden" name="'.$fixed_data[$index].'" id="'.$fixed_data[$index].'" value="','"');
					if($data == false){
						$data = '';	
					}
				}
				else {
					return false;
				}
				$data_assoc[$fixed_data[$index]] = $data;
		}
		return 	$data_assoc;		
	}
#AVVNL Consumer Related Functions
#Exception Related Function
function get_exception_by_customer($line,$file,$site_ai_id){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($site_ai_id));
	$exception_text = '';
	$meter_details_substring = '';
	$board_short_name = '';
	$board_full_name ='';
	$circle_name ='';
	$consumer_id ='';
	$consumer_name = '';
	$bill_link = '';
	$site_id = '';
	$insert_substring = '';
	$exception_generated_flag = false;
	$customer_assoc = array();
	$template_data_array = array();
	$template_value_array = array();
	$current_date = electricity_bill_today_date(__LINE__,__FILE__);
	$subject = electricity_bill_date_format_with_formatted(__LINE__,__FILE__,$current_date);
	$check_for_valid_consumer_query = "SELECT fld_ai_internalsite_id,fld_discom_id,fld_is_automated,fld_consumer_id,tbl_sites.fld_organizationsite_id FROM tbl_sites WHERE fld_ai_internalsite_id = '".$site_ai_id."';";
	$check_for_valid_consumer_query_result = electricity_bill_query(__LINE__,__FILE__,$check_for_valid_consumer_query);
	if(electricity_bill_num_rows(__LINE__,__FILE__,$check_for_valid_consumer_query_result)>0)
	{
		$row_data = electricity_bill_fetch_assoc(__LINE__,__FILE__,$check_for_valid_consumer_query_result);
		$customer_ai_id = $row_data['fld_ai_internalsite_id'];
		$customer_is_automated = $row_data['fld_is_automated'];
		$customer_id = $row_data['fld_consumer_id'];
		$board_id = $row_data['fld_discom_id'];
		$site_id = $row_data['fld_organizationsite_id'];
		if($customer_is_automated == '1'){
			$get_bills_of_current_and_previous_month_query = "SELECT tbl_discoms.fld_ai_id AS `electricity_board_id`, tbl_sites.fld_organizationsite_id, tbl_bills.fld_file_name, tbl_circles.fld_ai_id AS `circle_id`, tbl_sites.fld_zone_id, tbl_bills.fld_generated_date, tbl_bills.fld_due_date, tbl_bills.fld_ai_id, tbl_bills.fld_energy_charges, tbl_bills.fld_tarrif_code, tbl_bills.fld_penalty, tbl_bills.fld_arrears, tbl_bills.fld_amount, tbl_bills.fld_bill_month, tbl_bills.fld_bill_year,tbl_bills.fld_payableamount_current_month,fld_rebate,fld_current_month_dps,fld_previous_month_dps,fld_payment_after_due_date,fld_late_payment_charge,fld_total_current_amount FROM tbl_bills LEFT JOIN tbl_discoms ON tbl_discoms.fld_ai_id = tbl_bills.fld_discom_id LEFT JOIN tbl_circles ON tbl_circles.fld_ai_id = tbl_discoms.fld_circle_id LEFT JOIN tbl_sites ON tbl_sites.fld_ai_internalsite_id = tbl_bills.fld_internalsite_id WHERE tbl_sites.fld_ai_internalsite_id = '".$site_ai_id."' ORDER BY tbl_bills.fld_datetime DESC LIMIT 0,2; ";
			$get_bills_of_current_and_previous_month_query_result = electricity_bill_query(__LINE__,__FILE__,$get_bills_of_current_and_previous_month_query);
			if(electricity_bill_num_rows(__LINE__,__FILE__,$get_bills_of_current_and_previous_month_query_result)>0){			
				$inner_index = 0;	
				$result_count = electricity_bill_num_rows(__LINE__,__FILE__,$get_bills_of_current_and_previous_month_query_result);
				while($row_data = electricity_bill_fetch_assoc(__LINE__,__FILE__,$get_bills_of_current_and_previous_month_query_result)){
					if($inner_index == 0){
						$insert_substring .= "'".$site_ai_id."'";
						
						$circle_id = $row_data['circle_id'];
						if($insert_substring != ''){
							$insert_substring .=",";
						}
						$insert_substring .= "'".$circle_id."'";
						$board_id = $row_data['electricity_board_id'];
						if($insert_substring != ''){
							$insert_substring .=",";
						}
						$insert_substring .= "'".$board_id."'";
						
						$zone_id = $row_data['fld_zone_id'];
						if($insert_substring != ''){
							$insert_substring .=",";
						}
						$insert_substring .= "'".$zone_id."'";
						
						$bill_id = $row_data['fld_ai_id'];
						if($insert_substring != ''){
							$insert_substring .=",";
						}
						$insert_substring .= "'".$bill_id."'";
						
						if($result_count == 1){
							$previous_bill_link  = 'NULL';
							if($insert_substring != ''){
								$insert_substring .=",";
							}
							$insert_substring .= $previous_bill_link;							
						}															
					}		
					
					if($inner_index == 1){
						$previous_bill_id  = $row_data['fld_ai_id'];
						if($insert_substring != ''){
							$insert_substring .=",";
						}
						$insert_substring .= "'".$previous_bill_id."'";
						
					}		
																
					if($meter_details_substring != ''){
						$meter_details_substring .=' OR ';	
					}
					$meter_details_substring .= 'tbl_bill_meter_details.fld_bill_id="'.$row_data['fld_ai_id'].'"';
					$customer_assoc[$inner_index] = array();
					$customer_assoc[$inner_index]['bill_ai_id'] = $row_data['fld_ai_id'];
					$customer_assoc[$inner_index]['amount'] = $row_data['fld_payment_after_due_date'];
					$customer_assoc[$inner_index]['bill_date'] = electricity_bill_date_format_with_formatted(__LINE__,__FILE__,$row_data['fld_generated_date']);					
					$customer_assoc[$inner_index]['due_date'] = electricity_bill_date_format_with_formatted(__LINE__,__FILE__,$row_data['fld_due_date']);
					$customer_assoc[$inner_index]['energy_charges'] = $row_data['fld_energy_charges'];
					$customer_assoc[$inner_index]['tarrif_code'] = $row_data['fld_tarrif_code'];
					$customer_assoc[$inner_index]['penalty'] = $row_data['fld_late_payment_charge'];
					$customer_assoc[$inner_index]['arrears'] = $row_data['fld_arrears'];
					$customer_assoc[$inner_index]['bill_month'] = $row_data['fld_bill_month'];
					$customer_assoc[$inner_index]['bill_year'] = $row_data['fld_bill_year'];
					$customer_assoc[$inner_index]['payableamount_current_month'] = $row_data['fld_total_current_amount'];
					$customer_assoc[$inner_index]['rebate'] = $row_data['fld_rebate'];
					$customer_assoc[$inner_index]['current_month_dps'] = $row_data['fld_current_month_dps'];
					$customer_assoc[$inner_index]['previous_month_dps'] = $row_data['fld_previous_month_dps'];
					$customer_assoc[$inner_index++]['meter'] = array();
				}	
				if($meter_details_substring != ''){
					$inner_index = 0;
					$meter_details_assoc = array();
					$get_meter_details_query = "SELECT tbl_bill_meter_details.fld_billed_unit,tbl_bill_meter_details.fld_meter_no, tbl_bill_meter_details.fld_bill_id, tbl_bill_meter_details.fld_previous_reading, tbl_bill_meter_details.fld_meter_current_reading, tbl_bill_meter_details.fld_meter_load, tbl_bills.fld_bill_month, tbl_bills.fld_energy_charges,tbl_bill_meter_details.fld_past_reading_date,tbl_bill_meter_details.fld_present_reading_date FROM tbl_bill_meter_details LEFT JOIN tbl_bills ON tbl_bills.fld_ai_id = tbl_bill_meter_details.fld_bill_id WHERE (".$meter_details_substring.") ORDER BY tbl_bill_meter_details.fld_bill_id DESC;";
					$get_meter_details_query_result = electricity_bill_query(__LINE__,__FILE__,$get_meter_details_query);
					if(electricity_bill_num_rows(__LINE__,__FILE__,$get_meter_details_query_result)>0){
						while($row_data = electricity_bill_fetch_assoc(__LINE__,__FILE__,$get_meter_details_query_result)){
							$meter_details_assoc = array();
							$meter_details_assoc['meter_no'] = $row_data['fld_meter_no'];
							$meter_details_assoc['previous_reading'] = $row_data['fld_previous_reading'];
							$meter_details_assoc['current_reading'] = $row_data['fld_meter_current_reading'];
							$meter_details_assoc['energy_charges'] = $row_data['fld_energy_charges'];	
							$meter_details_assoc['meter_load'] = $row_data['fld_meter_load'];
							$meter_details_assoc['billed_unit'] = $row_data['fld_billed_unit'];	
							$meter_details_assoc['past_reading_date'] = $row_data['fld_past_reading_date'];
							$meter_details_assoc['present_reading_date'] = $row_data['fld_present_reading_date'];					
							if($customer_assoc[$inner_index]['bill_ai_id'] != $row_data['fld_bill_id']){
								$inner_index = $inner_index+1;										
							}
							$customer_assoc[$inner_index]['meter'][]=$meter_details_assoc;
						}
						$exception_generated_substring = '';
						$bill_amount_bill_date_substring = '';
						$exception_array = array();
						$send_mail_flag = false;
						$send_mail_text = "";
						for($index=0;$index <= $inner_index;$index++){
							if($index == 0){
								
								//============================================================================AMOUNT=============================================================================
								if($customer_assoc[$index]['amount'] > 500000){
									$exception_generated_flag = true;
									$exception_generated_substring = "High Bill Amount: Rs. ".electricity_bill_number_format(__LINE__,__FILE__,$customer_assoc[$index]['amount'],2);	
									$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"large-amount");								
								}
								if($customer_assoc[$index]['amount'] < 0){
									$exception_generated_flag = true;
									$exception_generated_substring = "Negative Bill Amount: Rs. ".electricity_bill_number_format(__LINE__,__FILE__,$customer_assoc[$index]['amount'],2);	
									$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"negative-amount");								
								}									
								//============================================================================PENALTY=============================================================================

								if(floatval($customer_assoc[$index]['penalty']) > 1000){
									$exception_generated_flag = true;
									$exception_generated_substring ="Non-zero Penalty: Rs. ".electricity_bill_number_format(__LINE__,__FILE__,$customer_assoc[$index]['penalty'],2);									
									$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"non-zero-penalty");
								}
								//============================================================================ARREARS=============================================================================
								if(floatval($customer_assoc[$index]['arrears']) > 5000){
									$exception_generated_flag = true;
									$exception_generated_substring ="Non-zero Arrears: Rs. ".electricity_bill_number_format(__LINE__,__FILE__,$customer_assoc[$index]['arrears'],2);									
									$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"non-zero-arrears");
								}
								//============================================================================Energy Charges=============================================================================
								if($inner_index == 1){
									if($customer_assoc[$index+1]['energy_charges'] > 0){
										if(((($customer_assoc[$index]['energy_charges'] - $customer_assoc[$index+1]['energy_charges'])/$customer_assoc[$index+1]['energy_charges'])*100) > 15){
											$exception_generated_flag = true;
											$exception_generated_substring ="Energy Charges Variance wrt previous month bill: ".electricity_bill_number_format_php(__LINE__,__FILE__,((($customer_assoc[$index]['energy_charges'] - $customer_assoc[$index+1]['energy_charges'])/$customer_assoc[$index+1]['energy_charges'])*100),2)."%";											
											$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"energy-charges-variance");
										}
									}else{
										if(((($customer_assoc[$index]['energy_charges'] - $customer_assoc[$index+1]['energy_charges']))*100) > 15){
											$exception_generated_flag = true;
											$exception_generated_substring ="Energy Charges Variance wrt previous month bill: ".electricity_bill_number_format_php(__LINE__,__FILE__,((($customer_assoc[$index]['energy_charges'] - $customer_assoc[$index+1]['energy_charges']))*100),2)."%";											
											$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"energy-charges-variance");
										}	
									}
								
//============================================================================TARRIF=============================================================================
									if($customer_assoc[$index]['tarrif_code'] != $customer_assoc[$index+1]['tarrif_code']){
										$exception_generated_flag = true;
										$exception_generated_substring ="Tariff Code Mismatch.Present: ".$customer_assoc[$index]['tarrif_code'].", Previous: ".$customer_assoc[$index+1]['tarrif_code'];										
										$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"tarrif-mismatch");
									}																																													
									
//============================================================================Meter reading&Load=============================================================================
									if(sizeof($customer_assoc[$index]['meter']) == sizeof($customer_assoc[$index+1]['meter'])){
										for($inner_loop_index=0;$inner_loop_index<sizeof($customer_assoc[$index]['meter']);$inner_loop_index++){
											$malfunction_billdata_flag = false;
											if($customer_assoc[$index]['meter'][$inner_loop_index]['past_reading_date'] == '0000-00-00' || $customer_assoc[$index]['meter'][$inner_loop_index]['present_reading_date'] == '0000-00-00'){
												$exception_generated_flag = true;
												$malfunction_billdata_flag = true;
												if($customer_assoc[$index]['meter'][$inner_loop_index]['past_reading_date'] == '0000-00-00'){
													$exception_generated_substring ="Malfunction Bill Data. Previous Meter Reading Date : N/A";											
												}else{
													$exception_generated_substring ="Malfunction Bill Data. Current Meter Reading Date : N/A";
												}
												$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"malfunction-bill-data");
											}	
											
											if($customer_assoc[$index]['meter'][$inner_loop_index]['previous_reading'] != $customer_assoc[$index+1]['meter'][$inner_loop_index]['current_reading']){
												$exception_generated_flag = true;
												$exception_generated_substring ="Mismatch in Meter Readings, Current Bill Opening: ".$customer_assoc[$index]['meter'][$inner_loop_index]['previous_reading'].", Previous Bill Closing: ".$customer_assoc[$index+1]['meter'][$inner_loop_index]['current_reading'];												
												$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"meter-reading-mismatch");
											}
											
											if($customer_assoc[$index]['meter'][$inner_loop_index]['meter_load'] != $customer_assoc[$index+1]['meter'][$inner_loop_index]['meter_load']){
												$exception_generated_flag = true;
												$exception_generated_substring ="Mismatch in Meter Load, Previous Meter Load: ".$customer_assoc[$index+1]['meter'][$inner_loop_index]['meter_load'].", Current Meter Load: ".$customer_assoc[$index]['meter'][$inner_loop_index]['meter_load'];												
												$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"meter-load-mismatch");
											}
											
											if($customer_assoc[$index]['meter'][$inner_loop_index]['meter_no'] != $customer_assoc[$index+1]['meter'][$inner_loop_index]['meter_no']){
												$exception_generated_flag = true;
												$exception_generated_substring ="Mismatch in Meter Number, Previous Meter Number: ".$customer_assoc[$index+1]['meter'][$inner_loop_index]['meter_no'].", Current Meter Number: ".$customer_assoc[$index]['meter'][$inner_loop_index]['meter_no'];												
												$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"meter-mismatch");
											}
											if($malfunction_billdata_flag == false){
												if($customer_assoc[$index]['meter'][$inner_loop_index]['past_reading_date'] != $customer_assoc[$index+1]['meter'][$inner_loop_index]['present_reading_date']){
													$exception_generated_flag = true;
													$exception_generated_substring ="Missing Bill, Previous Bill Current Meter Reading Date: ".$customer_assoc[$index+1]['meter'][$inner_loop_index]['present_reading_date'].", Current Bill Past Reading Date: ".$customer_assoc[$index]['meter'][$inner_loop_index]['past_reading_date'];												
													$exception_array[] = array("text"=>$exception_generated_substring,"type"=>"missing-bill");
												}
											}
										}
									}else{	
										$exception_strin_segment .= "mismatch in total no of meters";	
									}
								}
								
								//============================================================================END=============================================================================	
								
								if($customer_assoc[$index]['energy_charges'] == "0.00"){
									$send_mail_flag = true;
									$send_mail_text .= '<li>Energy Charge: '.$customer_assoc[$index]['energy_charges'].'</li>';	
								}
								if($customer_assoc[$index]['payableamount_current_month'] == "0.00"){
									$send_mail_flag = true;
									$send_mail_text .= '<li>Payable Amount for Current Month: '.$customer_assoc[$index]['payableamount_current_month'].'</li>';	
								}
								for($inner_loop_index=0;$inner_loop_index<sizeof($customer_assoc[$index]['meter']);$inner_loop_index++){
									if($customer_assoc[$index]['meter'][$inner_loop_index]['current_reading'] == 0){
										$send_mail_flag = true;
										$send_mail_text .= '<li>Current Meter Reading: '.$customer_assoc[$index]['meter'][$inner_loop_index]['current_reading'].'</li>';
									}
									if($customer_assoc[$index]['meter'][$inner_loop_index]['billed_unit'] == 0.00){
										$send_mail_flag = true;
										$send_mail_text .= '<li>Billed Unit: '.$customer_assoc[$index]['meter'][$inner_loop_index]['billed_unit'].'</li>';
									}	
								}
								if($customer_assoc[$index]['rebate'] <= 0){
									$send_mail_flag = true;
									$send_mail_text .= '<li>Rebate: '.$customer_assoc[$index]['rebate'].'</li>';	
								}
								if($customer_assoc[$index]['current_month_dps'] <= 0){
									$send_mail_flag = true;
									$send_mail_text .= '<li>Current Month DPS: '.$customer_assoc[$index]['current_month_dps'].'</li>';	
								}
								if($customer_assoc[$index]['previous_month_dps'] <= 0){
									$send_mail_flag = true;
									$send_mail_text .= '<li>DPS: '.$customer_assoc[$index]['previous_month_dps'].'</li>';	
								}
							}																										
						}
						if($send_mail_flag == true){
							$template_data_array = array('CONSUMER_ID','SITE_ID','ERROR_DATA');
							$template_value_array = array($customer_id,$site_id,$send_mail_text);
							global $mailTempalte;
							electricity_bill_send_mail(__LINE__,__FILE__,$mailTempalte['newbill_zero_content'],$template_data_array,$template_value_array,QUERY_FAILED_RECEIVER,$mailTempalte['newbill_zero_subject']);
						}
						if($exception_generated_flag === true){
							$insert_query_string = "";
							foreach($exception_array as $value){
								if($insert_query_string != ""){
										$insert_query_string .= ",";
								}
								$insert_query_string .= "(".$insert_substring.",'".$value['type']."','".$value['text']."')";
							}
							electricity_bill_commit_off(__LINE__,__FILE__);
							$insert_query = "INSERT INTO tbl_exception(fld_internalsite_id,fld_circle_id,fld_discom_id,fld_zone_id,fld_present_bill_internalid,fld_previous_bill_internalid,fld_exception_type,fld_exception_text) VALUES ".$insert_query_string.";";
							$insert_query_result =  electricity_bill_query(__LINE__,__FILE__,$insert_query);
							if(electricity_bill_affected_rows(__LINE__,__FILE__) > 0){
								electricity_bill_commit(__LINE__,__FILE__);
							}
							electricity_bill_commit_on(__LINE__,__FILE__);
						}
						
					}else{
						return false;	
					}
				}else{
					return false;	
				}					
			}else{
				return false;	
			}
		}else{
			return false;	
		}
	}else{
		return false;	
	}
}
#Exception Related Function
#Exception For Previous Month Data
function data_previous_bill($line,$file,$consumer_id,$board_id,$due_date,$bill_month,$bill_year,$file_name){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($consumer_id,$board_id,$due_date,$file_name));
	$receiver = "arup@codez.in,kousik@codez.in";
	$error_message = "";
	$saved_bill_ai_id = "";
	$exception_text_substring = '';
	$return_flag = 0;
	$return_array = array();
	$select_board_deatils_query = "SELECT fld_electricity_board,fld_circle,fld_short_form FROM tbl_electricity_board LEFT JOIN tbl_circles ON tbl_circles.fld_ai_id = tbl_electricity_board.fld_circle_id WHERE tbl_electricity_board.fld_ai_id = '".$board_id."'";
	$select_board_deatils_query_result = electricity_bill_query(__LINE__,__FILE__,$select_board_deatils_query);
	if(electricity_bill_num_rows(__LINE__,__FILE__,$select_board_deatils_query_result) > 0){
		$row = electricity_bill_fetch_assoc(__LINE__,__FILE__,$select_board_deatils_query_result);
		$circle = $row['fld_circle'];
		$board_name = $row['fld_electricity_board'];
		$board_short_form = $row['fld_short_form'];
	}
	
	$check_already_exist_or_not_query = "SELECT fld_ai_id FROM tbl_customer_details WHERE fld_consumer_id = '".$consumer_id."' AND fld_bill_month = '".$bill_month."' AND fld_bill_year = '".$bill_year."' AND fld_board = '".$board_id."'";
	$check_already_exist_or_not_query_result = electricity_bill_query(__LINE__,__FILE__,$check_already_exist_or_not_query);
	if(electricity_bill_num_rows(__LINE__,__FILE__,$check_already_exist_or_not_query_result) > 0){	
		$row = electricity_bill_fetch_assoc(__LINE__,__FILE__,$check_already_exist_or_not_query_result);
		$saved_bill_ai_id = $row['fld_ai_id'];
		//$error_message = "Data already stored into database";
	}
	$select_top_consumer_ai_id_query = "SELECT  fld_ai_id,fld_due_date,fld_file_name,fld_name FROM tbl_customer_details WHERE fld_ai_id = (SELECT MAX(fld_ai_id) FROM tbl_customer_details WHERE fld_consumer_id = '".$consumer_id."' AND  fld_board = '".$board_id."');";
	$select_top_consumer_ai_id_query_result = electricity_bill_query(__LINE__,__FILE__,$select_top_consumer_ai_id_query);
	if(electricity_bill_num_rows(__LINE__,__FILE__,$select_top_consumer_ai_id_query_result) > 0){
			$row = electricity_bill_fetch_assoc(__LINE__,__FILE__,$select_top_consumer_ai_id_query_result);
			$flag = false;
			$top_ai_id = $row['fld_ai_id'];
			$top_due_date = $row['fld_due_date'];
			$file_name_saved = $row['fld_file_name'];
			$consumer_name = $row['fld_name'];
			if($saved_bill_ai_id != ""){
				if($saved_bill_ai_id == $top_ai_id){
					$error_message = "Data already stored into database";
					$return_flag = 1;
				}else if($saved_bill_ai_id < $top_ai_id){
					$error_message = "Getting bill with previously saved data";
					$flag = true;
					$exception_text_substring = '<ul style="padding-left:15px; padding-bottom:20px;">
													<li><strong>A New Bill from '.$board_short_form.' was found with Previously Saved Data</strong></li>
												</ul>';
				}
			}else{
				if(electricity_bill_strtotime(__LINE__,__FILE__,$due_date) < electricity_bill_strtotime(__LINE__,__FILE__,$top_due_date)){
					$error_message = "Getting bill with expired due date";
					$flag = true;
					$exception_text_substring = '<ul style="padding-left:15px; padding-bottom:20px;">
													<li><strong>A New Bill from '.$board_short_form.' was found with Expired Due Date</strong></li>
												</ul>';
				}
			}
			if($flag == true){
				$return_flag = 2;
				$table_data_1 = '<tr>
									<td>'.electricity_bill_date_format_with_formatted(__LINE__,__FILE__,$due_date).'</td>
									<td>&nbsp;</td>
									<td><a href="'.ROOT_PATH.'/download/'.$file_name.'" style="color:#2E57A7;text-decoration:underline">Click to View</a></strong></td>
								</tr>';	
								
				$table_data_2 = '<tr>
									<td>'.electricity_bill_date_format_with_formatted(__LINE__,__FILE__,$top_due_date).'</td>
									<td>&nbsp;</td>
									<td><a href="'.ROOT_PATH.'/download/'.$file_name_saved.'" style="color:#2E57A7;text-decoration:underline">Click to View</a></strong></td>
								</tr>';	
				$template_data_array = array('EXCEPTION_HEADING','BOARD_SHORT_NAME','CIRCLE_NAME','BOARD_FULL_NAME','CONSUMER_ID','CONSUMER_NAME','TABLE_CONTENT_1','TABLE_CONTENT_2');
				$template_value_array = array($exception_text_substring,$board_short_form,$circle,$board_name,$consumer_id,$consumer_name,$table_data_1,$table_data_2);	
				global $mailTempalte;	
				electricity_bill_send_mail(__LINE__,__FILE__,$mailTempalte['newbill_exception_2'], $template_data_array,$template_value_array,$receiver,$mailTempalte['newbill_exception_2_subject']);
			}
	}
	$return_array['flag'] = $return_flag;
	$return_array['error_message'] = $error_message;
	return $return_array;
}
function save_file_dir($line,$file,$dir_name='download/',$from_flag=0){
	electricity_bill_debug($line,$file,__FUNCTION__);
	$temp_folder_name = date("Y-m-d");
	if($from_flag == "0"){
		$file_destination = "";
	}else{
		$file_destination = "../";
	}
	if(!file_exists($file_destination.$dir_name.$temp_folder_name)) {
    	 mkdir($file_destination.$dir_name.$temp_folder_name, 0777, true);
    }
	return $file_destination.$dir_name.$temp_folder_name;
}
function electricity_bill_date_diff($line,$file,$date_1,$date_2){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date_1,$date_2));
	$date1=date_create($date_1);
	$date2=date_create($date_2);
	$diff=date_diff($date1,$date2);
	return $diff->format("%a");
}
function electricity_bill_date_diff_2($line,$file,$date_1,$date_2){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date_1,$date_2));
	$date1=date_create($date_1);
	$date2=date_create($date_2);
	$diff=date_diff($date1,$date2,false);
	return $diff->format("%r%a");
}

function electricity_bill_file_exist($line,$file,$filename){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($filename));
	if(file_exists($filename)) {
		return true;
	}else{
		return false;	
	}
}
function electricity_bill_mppdf($line,$file,$discom_id,$filename){
require_once '/home/atc/public_html/vendor/autoload.php';
	$mpdf = new \Mpdf\Mpdf(['format' =>[297, 420]]);
	$temp_file_name = electricity_bill_string_replace(__LINE__,__FILE__,'.html','.pdf',$filename);
	// Set some flags for mPDF
	$mpdf->autoScriptToLang = true;
	$mpdf->autoLangToFont = true;
	$temp_arr = electricity_bill_explode(__LINE__,__FILE__,'/',$filename);
	// Read and Parse HTML Content
	$mpdf->WriteHTML(file_get_contents($filename));
	// Display PDF to Browser
	$mpdf->Output($temp_file_name,"F");
	return $temp_file_name;
}
function electricity_bill_html_to_pdf($line,$file,$discom_id,$filename){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($discom_id,$filename));
	include_once 'dompdf/autoload.inc.php';
	$temp_arr = electricity_bill_explode(__LINE__,__FILE__,'/',$filename);
	$dompdf = new Dompdf\Dompdf();
	$paper_size = "A4";
	$get_html = file_get_contents($filename);
	if( $discom_id == '8'){
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'<script type="text/javascript" src="./URJA MITRA APPLICATION _ JHARKHAND BIJLI VITRAN NIGAM LTD._files/ajax.js.download"></script>','',$get_html);	
		
		$between_string = get_string_between(__LINE__,__FILE__,$get_html,'<script','/script>');
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'<script'.$between_string.'/script>','',$get_html);				
		
		$get_logo_class_occurance = electricity_get_total_occurance_of_substring(__LINE__,__FILE__,'class="logo"',$get_html);
		for($index=0;$index<$get_logo_class_occurance;$index++){
			$between_string = get_string_between(__LINE__,__FILE__,$get_html,'<div class="logo"','/div>');
			$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'<div class="logo"'.$between_string.'/div>','',$get_html);	
		}
		$between_string = get_string_between(__LINE__,__FILE__,$get_html,'<style','/style>');
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'<style'.$between_string.'/style>','',$get_html);	
		
		$between_string = get_string_between(__LINE__,__FILE__,$get_html,'<script language="javascript" type="text/','/script>');
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'<script language="javascript" type="text/'.$between_string.'/script>','',$get_html);
			
	}else if($discom_id == '6'){
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'http://cpmuz.esselutilities.com:86/images/Essel.png','images/Essel.png',$get_html);
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'http://cpmuz.esselutilities.com:86/images/sulogo.png','images/sulogo.png',$get_html);
		//$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'</head>','images/sulogo.png',$get_html);
		
	}else if($discom_id == '9'){
		$get_logo_class_occurance = electricity_get_total_occurance_of_substring(__LINE__,__FILE__,'rowspan="7"',$get_html);
		for($index=0;$index<$get_logo_class_occurance;$index++){
			$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'rowspan="7"','rowspan="6"',$get_html);	
		}
		
		$get_logo_class_occurance = electricity_get_total_occurance_of_substring(__LINE__,__FILE__,'<table border="1" cellpadding="0" cellspacing="0" width="100%" style="height: 45px">',$get_html);
		for($index=0;$index<$get_logo_class_occurance;$index++){
			$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'<table border="1" cellpadding="0" cellspacing="0" width="100%" style="height: 45px">','<table border="1" cellpadding="2" cellspacing="2" width="100%" style="height: 45px">',$get_html);	
		}
	}else if($discom_id == '24'){
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'onKeyDown=\'return DisableControlKey(event)\' onMouseDown=\'return DisableControlKey(event)\'     onKeyPress="return disableCtrlKeyCombination(event);"onKeyDown="return disableCtrlKeyCombination(event); "onload=\'Onload_All(this.frm)\' bgColor=#ffffff style="background-image:url(/wss/images/main_bg.jpg); margin-top:0; padding-top:20px;margin-left:0; text-align:center"','',$get_html);			
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'विद्युत बिल एवं आपूर्ति शिकायत हेतु टोल फ्री 1912 डायल करें।','',$get_html);			
		$between_string = get_string_between(__LINE__,__FILE__,$get_html,'<table width="940" align="center" cellpadding="0" cellspacing="0" style="position:relative;left:-10px;"','/table>');
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'<table width="940" align="center" cellpadding="0" cellspacing="0" style="position:relative;left:-10px;"'.$between_string.'/table>','',$get_html);	
		$paper_size = "A3";
	}else if($discom_id == '25' || $discom_id == '26' || $discom_id == '73' || $discom_id == '79'|| $discom_id == '71'){
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'onKeyDown=\'return DisableControlKey(event)\' onMouseDown=\'return DisableControlKey(event)\'     onKeyPress="return disableCtrlKeyCombination(event);"onKeyDown="return disableCtrlKeyCombination(event); "onload=\'Onload_All(this.frm)\' bgColor=#ffffff style="background-image:url(/wss/images/main_bg.jpg); margin-top:0; padding-top:20px;margin-left:0; text-align:center"','',$get_html);			
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'विद्युत बिल एवं आपूर्ति शिकायत हेतु टोल फ्री 1912 डायल करें।','',$get_html);			
		$between_string = get_string_between(__LINE__,__FILE__,$get_html,'<table width="940" align="center" cellpadding="0" cellspacing="0" style="position:relative;left:-10px;"','/table>');
		$get_html = electricity_bill_string_replace(__LINE__,__FILE__,'<table width="940" align="center" cellpadding="0" cellspacing="0" style="position:relative;left:-10px;"'.$between_string.'/table>','',$get_html);	
		$paper_size = "A2";
	}
	
	$dompdf->load_html($get_html);
	if($discom_id == '25' || $discom_id == '26' || $discom_id == '73' || $discom_id == '79' || $discom_id == '71'){
		$dompdf->setPaper($paper_size, 'potrait');
	}else{
		$dompdf->setPaper($paper_size, 'landscape');
	}
	$dompdf->set_option('defaultFont', 'Courier');
	$options = new  \Dompdf\Options();
	$options->setIsRemoteEnabled(true);
	$dompdf->setOptions($options);
	$context = stream_context_create([ 
		'ssl' => [ 
			'verify_peer' => FALSE, 
			'verify_peer_name' => FALSE,
			'allow_self_signed'=> TRUE 
		] 
	]);
	$temp_file_name = electricity_bill_string_replace(__LINE__,__FILE__,'.html','.pdf',$filename);
	$file_full_path ='html_to_pdf/'.$temp_arr[1].'/'.$temp_file_name; 
	$dompdf->setHttpContext($context);
	$dompdf->render();
	$output = $dompdf->output();
	file_put_contents($temp_file_name, $output);
	return $temp_file_name;
}
function electricity_bill_database_to_bill_check_format($line,$file,$date)
{
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($date));
	return date("d/m", strtotime($date));
}
function gzCompressFile($line,$file,$source, $level = 9){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($source, $level)); 
	$dest = $source . '.gz'; 
	$mode = 'wb' . $level; 
	$error = false; 
	if ($fp_out = gzopen($dest, $mode)) { 
		if ($fp_in = fopen($source,'rb')) { 
			while (!feof($fp_in)) 
				gzwrite($fp_out, fread($fp_in, 1024 * 512)); 
			fclose($fp_in); 
		} else {
			$error = true; 
		}
		gzclose($fp_out); 
	} else {
		$error = true; 
	}
	if ($error)
		return false; 
	else
	   return $dest; 
}
	function reposition_negative_sign($str) {
		if(strpos($str,'-') !== FALSE) {
			$str = electricity_bill_string_replace(__LINE__,__FILE__,'-','',$str);
			$str = -1 * $str;
		}
		return $str;
	}
	
	if (!function_exists('getallheaders')) {
		function getallheaders() {
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', strtolower(str_replace('_', '-', substr($name, 5))))] = $value;
			}
		}
		return $headers;
		}
	}

function atc_api_mail($to, $subject, $body) {

		$message = "
		<html>
		<head>
		<title>Exception Email</title>
		<style>
		#customers {
		font-family: Arial, Helvetica, sans-serif;
		border-collapse: collapse;
		width: 100%;
		}

		#customers td, #customers th {
		border: 1px solid #ddd;
		padding: 8px;
		}

		#customers th {
		padding-top: 12px;
		padding-bottom: 12px;
		text-align: left;
		background-color: #4CAF50;
		color: white;
		}
		</style>
		</head>
		<body>
		$body
		</body>
		</html>
		";

		// Always set content-type when sending HTML email
		$from = "Altius BillPro Exception <noreply@altius.billpro.online>";
		$headers = "Reply-To: ".$from."\r\n";
		$headers .= "Return-Path: ".$from."\r\n"; 
		$headers .= "From: ".$from."\r\n"; 
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\nX-Priority: 3\r\nX-Mailer: PHP". phpversion() ."\r\n";

		mail($to,$subject,$message,$headers);
	}
	
	
function electricity_bill_generate_excel($line,$file,$bill_array,$username,$usercircle,$excel=0,$excel_with_files=0,$excel_billpro_format=0,$excel_atc_format=0,$excel_mpg_epr_format=0,$is_mail_manually=0,$file_name = '',$atc_excel_kl_format = 0,$atc_excel_jh_format = 0){
	electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($bill_array,$usercircle,$excel,$excel_with_files,$excel_billpro_format,$excel_atc_format,$excel_mpg_epr_format));
	if($username == 'anant' ){
			
	/*	ini_set('display_errors', 1);
		var_dump($line,$file);
		echo '<pre>';print_r($bill_array);echo '</pre>';
		var_dump($username,$usercircle);
	*/
	}
	$arr_return = array();
	if($excel == 1 || $excel_with_files == '1' || $excel_billpro_format == 1){
		$fn = "BillPro_Energy".date('d_m_Y');
		if($is_mail_manually == 1){
			$fn = $file_name;
		}
		$file_name_array = array();
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Team Codez")
						   ->setLastModifiedBy("Team Codez")
						   ->setTitle("Office 2007 XLSX Test Document")
						   ->setSubject("Office 2007 XLSX Test Document")
						   ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
						   ->setKeywords("office 2007 openxml php")
						   ->setCategory("Test result file");
				
		if(electricity_bill_sizeof(__LINE__,__FILE__,$bill_array)) {
			if($username == 'anant' ){
				//**************************new add start on  2023-02-22***************************//
						
				foreach($bill_array as $row){
					//$bill_array[$row['fld_bill_id']] = $row;
					$bill_array_dup[$row['fld_organizationsite_id'].$row['fld_bill_month'].$row['fld_bill_year']][$row['fld_bill_id']] =array($row['fld_due_date'],$row['fld_generated_date']);
					$bill_array_revi_bill[$row['fld_organizationsite_id'].$row['fld_generated_date']][$row['fld_bill_id']] =array('due_date' => $row['fld_due_date'],'bill_date' =>$row['fld_generated_date']);
					//$bill_array_overlap[$row['fld_organizationsite_id'].date('F', strtotime($row['fld_process_date'])).'_'.date('Y', strtotime($row['fld_process_date']))][$row['fld_bill_id']] =array($row['fld_due_date'],$row['fld_generated_date']);
					$bill_array_same_bill_due_bill[$row['fld_organizationsite_id'].$row['fld_generated_date'].$row['fld_due_date']][$row['fld_bill_id']] =array('due_date' => $row['fld_due_date'],'bill_date' =>$row['fld_generated_date']);
				}
					
				foreach($bill_array_dup as $due_date_array){
					$temp=$due_date_array;
					end($temp);
					$due_date1 = key($temp);
					foreach($due_date_array as $ai_id_value => $bill_due_date){
						$bill_array[$ai_id_value]['duedate_difference'] = electricity_bill_date_diff(__LINE__,__FILE__,$due_date_array[$due_date1][0],$bill_due_date[0]);
						$bill_array[$ai_id_value]['billdate_difference'] = electricity_bill_date_diff(__LINE__,__FILE__,$due_date_array[$due_date1][1],$bill_due_date[1]);
					}
				}
							
				/*
				foreach($bill_array_overlap as $due_date_array){
					$temp=$due_date_array;
					end($temp);
					$due_date1 = key($temp);
					foreach($due_date_array as $ai_id_value => $bill_due_date){
						if($ai_id_value != $due_date1){
							$bill_array[$ai_id_value]['duedate_difference_1'] = electricity_bill_date_diff(__LINE__,__FILE__,$due_date_array[$due_date1][0],$bill_due_date[0]);
							$bill_array[$ai_id_value]['billdate_difference_1'] = electricity_bill_date_diff(__LINE__,__FILE__,$due_date_array[$due_date1][1],$bill_due_date[1]);
							if(abs($bill_array[$ai_id_value]['billdate_difference_1']) <= 7){
								$bill_array[$due_date1]['compare_row'] = 1;
							}
						}
					}
				}
				*/
				
				
				foreach($bill_array_revi_bill as $bill_data ){
						
					$temp=$bill_data;
					end($temp);
					$bill_date1 = key($temp);
					if(sizeof($bill_data) > 1){
						foreach($bill_data as $ai_id_value => $bill_due_date){
							//if($ai_id_value == $bill_date1){
							if($bill_data[$ai_id_value]['bill_date'] == $bill_data[$bill_date1]['bill_date']){
								$bill_array[$ai_id_value]['same_bill_date_colour'] = 1;
							}else{
							
								$bill_array[$ai_id_value]['same_bill_date_colour'] = 0;
							}
						}
					}
				}
							
				foreach($bill_array_same_bill_due_bill as $bill_data ){
						
					$temp=$bill_data;
					end($temp);
					$bill_date1 = key($temp);
					if(sizeof($bill_data) > 1){
						foreach($bill_data as $ai_id_value => $bill_due_date){
							//if($ai_id_value == $bill_date1){
							if(($bill_data[$ai_id_value]['bill_date'] == $bill_data[$bill_date1]['bill_date']) || $bill_data[$ai_id_value]['due_date'] == $bill_data[$bill_date1]['due_date']){
								$bill_array[$ai_id_value]['same_bill_due_date_colour'] = 1;
							}else{
							
								$bill_array[$ai_id_value]['same_bill_due_date_colour'] = 0;
							}
						}
					}
						
				}
							
				//echo '<pre>';print_r($bill_array_same_bill_due_bill);echo '</pre>';
						
				//echo '<pre>';print_r($bill_array_dup);echo '</pre>';
				//echo '<pre>';print_r($bill_array_revi_bill);echo '</pre>';
				//echo '<pre>';print_r($bill_array_overlap);echo '</pre>';
				//echo '<pre>';print_r($bill_array);echo '</pre>';
				//exit;
			}
			//**************************new add end on  2023-02-22***************************//
			if($usercircle == 12 || $usercircle == 22){
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', 'Global Site ID')
							->setCellValue('B1', 'Trading Partner')
							->setCellValue('C1', 'Bill name of')
							->setCellValue('D1', 'Type of Connection')
							->setCellValue('E1', 'Tarrif Class')
							->setCellValue('F1', 'Sanctioned Load')
							->setCellValue('G1', 'Meter No.')
							->setCellValue('H1', 'Security Deposit Amount')
							->setCellValue('I1', 'Customer Id-CESC')
							->setCellValue('J1', 'Consumer No-WBSEDCL/CESC')
							->setCellValue('K1', 'Bill Issue Date')
							->setCellValue('L1', '1st Bill due Date')
							->setCellValue('M1', 'EB Reading Start (Cumulative fig. of all meter)')
							->setCellValue('N1', 'EB Reading End (Cumulative fig. of all meter)')
							->setCellValue('O1', 'No. of Units')
							->setCellValue('P1', 'Power Start Date')
							->setCellValue('Q1', 'Power End Date')
							->setCellValue('R1', 'Billing month')
							->setCellValue('S1', 'Energy Charges')
							->setCellValue('T1', 'Fixed/Demand Charge')
							->setCellValue('U1', 'Meter Rent')
							->setCellValue('V1', 'MVCA')
							->setCellValue('W1', 'Electricity Duty')
							->setCellValue('X1', 'Arrear MVCA Charge')
							->setCellValue('Y1', 'Arrear Energy Charge')
							->setCellValue('Z1','Arrear Fixed Charge')
							->setCellValue('AA1','LPSC')
							->setCellValue('AB1','Arrear ED Charge')
							->setCellValue('AC1','Arrear Rebate')
							->setCellValue('AD1','FPPCA')
							->setCellValue('AE1','ARREARS_AMT')
							->setCellValue('AF1','Adjustment')
							->setCellValue('AG1','SD INTEREST RECEVED')
							->setCellValue('AH1','Total Gross Amt')
							->setCellValue('AI1','Special Rebate')
							->setCellValue('AJ1','Monthly Rebate')
							->setCellValue('AK1','E- Payment Rebate')
							->setCellValue('AL1','Rounded Net Amount Payable as per BILL DETAILS')
							->setCellValue('AM1','Amount due within due dates 1 St Payment')
							->setCellValue('AN1','Amount due within due dates 2Nd Payment')
							->setCellValue('AO1','Amount due within due dates 3Rd Payment')
							->setCellValue('AP1','Net Amt. Payable for e-payment/Amount payable at a time through e-Payment within 1st Due date')
							->setCellValue('AQ1',' LESS INCOME TAX ON ABOVE INTEREST')
							->setCellValue('AR1','SECURITY DEPOSIT MAINTAINABLE')
							->setCellValue('AS1', 'Bill added to bill pro portal date')
							->setCellValue('AT1', 'Bill Basis');
							//if($username == 'energy.wb' || $username =='pritam.ghosh'){
							if(1){
								$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('AU1', 'TCS Charges')
									->setCellValue('AV1', 'Arrear Min. Charge')
									->setCellValue('AW1', 'Minimum Charge')
									->setCellValue('AX1', 'Bill Number')
									->setCellValue('AY1', 'A/C No.') 
									->setCellValue('AZ1', 'IFSC Code');
							}
			}else if ($usercircle == 20 || $usercircle == 25){
				$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', 'Site ID')
						->setCellValue('B1', 'Consumer ID')
						->setCellValue('C1', 'Meter No (KWH)')
						->setCellValue('D1', 'Old Account No')
						->setCellValue('E1', 'Knumber')
						->setCellValue('F1', 'Phase')
						->setCellValue('G1', 'Sanctioned Load')
						->setCellValue('H1', 'Tariff')
						->setCellValue('I1', 'Board Name')
						->setCellValue('J1', 'Sub Division')
						->setCellValue('K1', 'Mdi')
						->setCellValue('L1', 'Name On Bill')
						->setCellValue('M1', 'Address')
						->setCellValue('N1', 'Expense Period')
						->setCellValue('O1', 'Bill No')
						->setCellValue('P1', 'Bill Issue Date')
						->setCellValue('Q1', 'Bill Receive Date')
						->setCellValue('R1', 'Bill Due Date')
						->setCellValue('S1', 'Opening Reading (KWH)')
						->setCellValue('T1', 'Closing Reading (KWH)')
						->setCellValue('U1', 'EB Start Date')
						->setCellValue('V1', 'EB End Date')
						->setCellValue('W1', 'Adjustment Unit')
						->setCellValue('X1', 'Total Unit')
						->setCellValue('Y1', 'Unit Rate')
						->setCellValue('Z1', 'Disconnection Date')
						->setCellValue('AA1', 'Energy Arrear')
						->setCellValue('AB1', 'Current Interest')
						->setCellValue('AC1', 'Interest Arrears')
						->setCellValue('AD1', 'Principle Arrears')
						->setCellValue('AE1', 'Fixed Charges')
						->setCellValue('AF1', 'Other Charges')
						->setCellValue('AG1', 'Energy Duty ED Charges')
						->setCellValue('AH1', 'Meter Rent')
						->setCellValue('AI1', 'Fuel Surcharge')
						->setCellValue('AJ1', 'Late Payment Charges')
						->setCellValue('AK1', 'Admin Charges')
						->setCellValue('AL1', 'Basic Eb Charges')
						->setCellValue('AM1', 'P.F. Penal Charges/P.F. Inc.')
						->setCellValue('AN1', 'Wheeling Charge')
						->setCellValue('AO1', 'Charges For Excess Demand')
						->setCellValue('AP1', 'Debit Bill Adjustment / Adjusted amount')
						->setCellValue('AQ1', 'Prompt Payment Discount')
						->setCellValue('AR1', 'Security Deposit Paid')
						->setCellValue('AS1', 'Security Deposit Refund')
						->setCellValue('AT1', 'Additional S.D. Demanded')
						->setCellValue('AU1', 'Rebate Early Payment')
						->setCellValue('AV1', 'Amount Before Due Date')
						->setCellValue('AW1', 'Amount After Due Date')
						->setCellValue('AX1', 'Last Amount Paid')
						->setCellValue('AY1', 'Last Payment Reference')
						->setCellValue('AZ1', 'Last Payment Date')
						->setCellValue('BA1', 'Connection Date')
						->setCellValue('BB1', 'mFactor (KVAH)')
						->setCellValue('BC1', 'Power Factor')
						->setCellValue('BD1', 'Total Bill Amount')
						->setCellValue('BE1', 'Current Bill Amount')
						->setCellValue('BF1', 'Contract OnDemand')
						->setCellValue('BG1', 'Remarks')
						->setCellValue('BH1', 'PowerFactor Surcharge')
						->setCellValue('BI1', 'Minus the average payment amount')
						->setCellValue('BJ1', 'Electricity sales tax');
						if ($usercircle == 25){
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('BK1', 'DTC')
								->setCellValue('BL1', 'Category')
								->setCellValue('BM1', 'Digital Payment discount')
								->setCellValue('BN1', 'Supply Date')
								->setCellValue('BO1', 'Discount Date')
								->setCellValue('BP1', 'Discount Date Amount')
								->setCellValue('BQ1', 'TOD 9 Hrs to 12 Hrs energy charge')
								->setCellValue('BR1', 'TOD 18 Hrs to 22 Hrs energy charge')
								->setCellValue('BS1', 'TOD 22 Hrs to 06 Hrs energy charge')
								->setCellValue('BT1', 'DIGITAL PAYMENT DISCOUNT DATE')
								->setCellValue('BU1', 'Prompt Payment Discount Date')
								->setCellValue('BV1', 'S.D. Arrears');
						}
			}else if ($usercircle == 21){
				$objPHPExcel->setActiveSheetIndex(0)
				                        ->setCellValue('A1', 'Site Id')
				                        ->setCellValue('B1', 'Site name')
				                        ->setCellValue('C1', 'Circle')
				                        ->setCellValue('D1', 'Cluster')
				                        ->setCellValue('E1', 'Discom ')
				                        ->setCellValue('F1', 'Consumer No.')
				                        ->setCellValue('G1', 'Consumer Name')
				                        ->setCellValue('H1', 'Address')
				                        ->setCellValue('I1', 'Bill no.')
				                        ->setCellValue('J1', 'Bill Month')
				                        ->setCellValue('K1', 'Bill Date On Bill ')
				                        ->setCellValue('L1', 'Bill Due Date')
				                        ->setCellValue('M1', 'Bill Process Date')
				                        ->setCellValue('N1', 'Tariff')
				                        ->setCellValue('O1', 'Phase')
				                        ->setCellValue('P1', 'Contract Demand/Sanctioned Load')
				                        ->setCellValue('Q1', 'Maximum Demand/Recorded Demand')
				                        ->setCellValue('R1', 'Security Deposit')
				                        ->setCellValue('S1', 'Additional S.D. Demanded')
				                        ->setCellValue('T1', 'Meter No.')
				                        ->setCellValue('U1', 'If meter change in current')
				                        ->setCellValue('V1', 'Meter Status')
				                        ->setCellValue('W1', 'MR/NR ')
				                        ->setCellValue('X1', 'Power Factor')
				                        ->setCellValue('Y1', 'Start Reading Date')
				                        ->setCellValue('Z1','End Reading Date')
				                        ->setCellValue('AA1','Start Reading(KWH)')
				                        ->setCellValue('AB1','End Reading(KWH)')
				                        ->setCellValue('AC1','Unit Difference(KWH)')
				                        ->setCellValue('AD1','Start Reading(KVAH)')
				                        ->setCellValue('AE1','End Reading (KVAH)')
				                        ->setCellValue('AF1','Unit Difference(KVAH)')
				                        ->setCellValue('AG1','Multiplying Factor')
				                        ->setCellValue('AH1','KVAH/KWH')
				                        ->setCellValue('AI1','Final Opening Reading')
				                        ->setCellValue('AJ1','Final Closing Reading')
				                        ->setCellValue('AK1','Final Unit Difference')
				                        ->setCellValue('AL1','Adjustment Unit')
				                        ->setCellValue('AM1','Billed Unit')
				                        ->setCellValue('AN1','Arrear')
				                        ->setCellValue('AO1','LPSC On Arrear Amount')
				                        ->setCellValue('AP1','Total Arrear')
				                        ->setCellValue('AQ1','Energy Charges')
				                        ->setCellValue('AR1','Fixed Charges')	
				                        ->setCellValue('AS1','Electricity Duty')
				                        ->setCellValue('AT1','DPS current month')
				                        ->setCellValue('AU1','Excess Demand Penalty')
				                        ->setCellValue('AV1','Fuel Surcharge')
				                        ->setCellValue('AW1','Capacitor Surcharge')
				                        ->setCellValue('AX1','Power Factor Sucharge')
				                        ->setCellValue('AY1','Metering Charges/Rent')
				                        ->setCellValue('AZ1','Metering Charges (CGST)')
				                        ->setCellValue('BA1','Metering Charges (SGST)')
				                        ->setCellValue('BB1','Other Charges')
				                        ->setCellValue('BC1','Government Subsidy')
				                        ->setCellValue('BD1','Interest on SD amount')
				                        ->setCellValue('BE1','Tax')
				                        ->setCellValue('BF1','Bill Correction/Adjustment amount')
				                        ->setCellValue('BG1','Any Other Positive Entry 1')
				                        ->setCellValue('BH1','Any Other Positive Entry 2')
				                        ->setCellValue('BI1','Any Other Positive Entry 3')
				                        ->setCellValue('BJ1','Any Other Negetive Entry 1')
				                        ->setCellValue('BK1','Any Other Negetive Entry 2')
				                        ->setCellValue('BL1','Remarks For BG Column')
				                        ->setCellValue('BM1','Remarks For BH Column')
				                        ->setCellValue('BN1','Remarks For BI Column')
				                        ->setCellValue('BO1','Remarks For BJ Column')
				                        ->setCellValue('BP1','Remarks For BK Column')
				                        ->setCellValue('BQ1','Total Current Amount')
				                        ->setCellValue('BR1','Rebate')
				                        ->setCellValue('BS1','Total amount payable before due date')
				                        ->setCellValue('BT1','Penalty for Late Payment')
				                        ->setCellValue('BU1','Total amount payable after due date')
				                        ->setCellValue('BV1','E-Rebate')
				                        ->setCellValue('BW1','Last Amount paid')
				                        ->setCellValue('BX1','Last Amount Paid Date')
				                        ->setCellValue('BY1','Last Amount Paid recipt no.')
				                        ->setCellValue('BZ1','Feeder Code')
				                        ->setCellValue('CA1','Exception in Bill')
				                        ->setCellValue('CB1','Exception Code')
				                        ->setCellValue('CC1','Exception in Bill')
				                        ->setCellValue('CD1','Mobile No')
				                        ->setCellValue('CE1','Email ID')
				                        ->setCellValue('CF1','Minimum Site Cost per day (Units)')
				                        ->setCellValue('CG1','Variation Percentage')
				                        ->setCellValue('CH1','Sub-Division')
							->setCellValue('CI1','PF Penalty/Rebate');
			}else{
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', 'Site Id')
							->setCellValue('B1', 'Site name')
							->setCellValue('C1', 'Circle')
							->setCellValue('D1', 'Cluster')
							->setCellValue('E1', 'Discom ')
							->setCellValue('F1', 'Consumer No.')
							->setCellValue('G1', 'Consumer Name')
							->setCellValue('H1', 'Address')
							->setCellValue('I1', 'Bill no.')
							->setCellValue('J1', 'Bill Month')
							->setCellValue('K1', 'Bill Date On Bill ')
							->setCellValue('L1', 'Bill Due Date')
							->setCellValue('M1', 'Bill Process Date')
							->setCellValue('N1', 'Tariff')
							->setCellValue('O1', 'Phase')
							->setCellValue('P1', 'Contract Demand/Sanctioned Load')
							->setCellValue('Q1', 'Maximum Demand/Recorded Demand')
							->setCellValue('R1', 'Security Deposit')
							->setCellValue('S1', 'Additional S.D. Demanded')
							->setCellValue('T1', 'Meter No.')
							->setCellValue('U1', 'If meter change in current')
							->setCellValue('V1', 'Meter Status')
							->setCellValue('W1', 'MR/NR ')
							->setCellValue('X1', 'Power Factor')
							->setCellValue('Y1', 'Start Reading Date')
							->setCellValue('Z1','End Reading Date')
							->setCellValue('AA1','Start Reading(KWH)')
							->setCellValue('AB1','End Reading(KWH)')
							->setCellValue('AC1','Unit Difference(KWH)')
							->setCellValue('AD1','Start Reading(KVAH)')
							->setCellValue('AE1','End Reading (KVAH)')
							->setCellValue('AF1','Unit Difference(KVAH)')
							->setCellValue('AG1','Multiplying Factor')
							->setCellValue('AH1','KVAH/KWH')
							->setCellValue('AI1','Final Opening Reading')
							->setCellValue('AJ1','Final Closing Reading')
							->setCellValue('AK1','Final Unit Difference')
							->setCellValue('AL1','Adjustment Unit')
							->setCellValue('AM1','Billed Unit')
							->setCellValue('AN1','Arrear')
							->setCellValue('AO1','LPSC On Arrear Amount')
							->setCellValue('AP1','Total Arrear')
							->setCellValue('AQ1','Energy Charges')
							->setCellValue('AR1','Fixed Charges')	
							->setCellValue('AS1','Electricity Duty')
							->setCellValue('AT1','DPS current month')
							->setCellValue('AU1','Excess Demand Penalty')
							->setCellValue('AV1','Fuel Surcharge')
							->setCellValue('AW1','Capacitor Surcharge')
							->setCellValue('AX1','Power Factor Sucharge')
							->setCellValue('AY1','Metering Charges/Rent')
							->setCellValue('AZ1','Metering Charges (CGST)')
							->setCellValue('BA1','Metering Charges (SGST)')
							->setCellValue('BB1','Other Charges')
							->setCellValue('BC1','Government Subsidy')
							->setCellValue('BD1','Interest on SD amount')
							->setCellValue('BE1','Tax')
							->setCellValue('BF1','Bill Correction/Adjustment amount')
							->setCellValue('BG1','Any Other Positive Entry 1')
							->setCellValue('BH1','Any Other Positive Entry 2')
							->setCellValue('BI1','Any Other Positive Entry 3')
							->setCellValue('BJ1','Any Other Negetive Entry 1')
							->setCellValue('BK1','Any Other Negetive Entry 2')
							->setCellValue('BL1','Remarks For BG Column')
							->setCellValue('BM1','Remarks For BH Column')
							->setCellValue('BN1','Remarks For BI Column')
							->setCellValue('BO1','Remarks For BJ Column')
							->setCellValue('BP1','Remarks For BK Column')
							->setCellValue('BQ1','Total Current Amount')
							->setCellValue('BR1','Rebate')
							->setCellValue('BS1','Total amount payable before due date')
							->setCellValue('BT1','Penalty for Late Payment')
							->setCellValue('BU1','Total amount payable after due date')
							->setCellValue('BV1','E-Rebate')
							->setCellValue('BW1','Last Amount paid')
							->setCellValue('BX1','Last Amount Paid Date')
							->setCellValue('BY1','Last Amount Paid recipt no.')
							->setCellValue('BZ1','Feeder Code')
							->setCellValue('CA1','Exception in Bill')
							->setCellValue('CB1','Exception Code')
							->setCellValue('CC1','Exception in Bill')
							->setCellValue('CD1','Mobile No')
							->setCellValue('CE1','Email ID')
							->setCellValue('CF1','Minimum Site Cost per day (Units)')
							->setCellValue('CG1','Variation Percentage')
							->setCellValue('CH1','Sub-Division');
							if($usercircle == 3 || $usercircle == 4){
								$discom_name_arr = array();
								$get_discom_name = "SELECT `fld_consumer_details_id`,`fld_registration_field_id`,`fld_registration_field_value`,fld_organizationsite_id,fld_consumer_id 
										FROM tbl_site_additonal_details 
										LEFT JOIN tbl_registration_fields ON tbl_site_additonal_details.`fld_registration_field_id` =  `tbl_registration_fields`.`fld_ai_id`
										LEFT JOIN tbl_sites ON tbl_sites.`fld_ai_internalsite_id` = tbl_site_additonal_details.fld_consumer_details_id
										WHERE `tbl_registration_fields`.`fld_field_id_name` = 'discom_name' AND `tbl_registration_fields`.`fld_board_id` IN (25,26,32,33);";
								if($get_discom_name_result = electricity_bill_query(__LINE__,__FILE__,$get_discom_name)) {
									if(electricity_bill_num_rows(__LINE__,__FILE__,$get_discom_name_result) > 0) {
										while($row_data = electricity_bill_fetch_assoc(__LINE__,__FILE__,$get_discom_name_result)) {
											$discom_name_arr[$row_data['fld_organizationsite_id']]['discom_name'] = $row_data['fld_registration_field_value'];
										}
									}
								}
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CI1', 'EB Connection Date ');
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CJ1', 'Division ');
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CK1', 'Discom Name ');
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CL1', 'Progressive Subsidy ');
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CM1', 'Period Months');
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CN1', 'Meter Remark');
							}
							if($usercircle == 19){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CI1', 'Loss and Gain ');
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CJ1', 'True-up charges ');
							}
							if($usercircle == 15){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CI1', 'Progressive Subsidy ');
							}
							if($username == 'navin' ){
								$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('CI1','BillPro Unique ID')
								->setCellValue('CJ1','File_Name');
							}
										
			}
			$bill_counter = 2;	
			$bill_loader_counter = 2;
			foreach($bill_array as $key => $row){
				if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
					$past_reading_date = "";
				}else{
					$past_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_past_reading_date']);
				}
							
				if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
					$present_reading_date = "";
				}else{
					$present_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_present_reading_date']);
				}
							
				if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
					$fld_generated_date = "";
				}else{
					$fld_generated_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_generated_date']);
				}
							
				if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
					$fld_due_date = "";
				}else{
					$fld_due_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_due_date']);
				}
				if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
					$fld_process_date = "";
				}else{
					$fld_process_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_process_date']);
				}
				if($row['fld_last_amount_paid_date'] == "" || $row['fld_last_amount_paid_date'] == "0000-00-00" || $row['fld_last_amount_paid_date'] == "0000-00-00 00:00:00"){
					$fld_last_amount_paid_date = "";
				}else{
					$fld_last_amount_paid_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_last_amount_paid_date']);
				}
				if($row['fld_connection_date'] == "" || $row['fld_connection_date'] == "0000-00-00"){
					$fld_connection_date = "";
				}else{
					$fld_connection_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_connection_date']);
				}
				if($row['fld_is_meter_changed'] ==0){
					$fld_is_meter_changed = "No";
				}else{
					$fld_is_meter_changed = "Yes";
				}
				#New added start on 17-05-2023
				$avg_minimum_unit = $avg_day_of_bill = 0;
				if($row['fld_present_reading_date'] != "0000-00-00" && $row['fld_past_reading_date'] != "0000-00-00"){
					$avg_day_of_bill = electricity_bill_date_diff(__LINE__,__FILE__,$row['fld_past_reading_date'],$row['fld_present_reading_date']);
				}
				if($avg_day_of_bill != 0 && $avg_day_of_bill != 0 && $row['fld_billed_unit'] != ''){
					$avg_minimum_unit = $row['fld_billed_unit'] / $avg_day_of_bill;
				}
				if($row['fld_master_minimum_site_cost_per_day'] == ''){$row['fld_master_minimum_site_cost_per_day'] = 0;}
				$final_avg_minimum_unit = 'Undefined';
				if($avg_minimum_unit != 0){
					$final_avg_minimum_unit = (($avg_minimum_unit - $row['fld_master_minimum_site_cost_per_day']) / $row['fld_master_minimum_site_cost_per_day']) * 100;
					$final_avg_minimum_unit = electricity_bill_round(__LINE__,__FILE__,$final_avg_minimum_unit,1);
				}
				#New added end on 17-05-2023	
				$bill_month = electricity_bill_ucwords(__LINE__,__FILE__,electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_month'],0,3))."-".electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_year'],2,2) ;
				if($usercircle == 12 || $usercircle == 22){
					$consumer_id_WBSEDCL = $consumer_id_CESC = '';
					if($row['fld_discom_id'] == 46){
						$consumer_id_CESC = $row['fld_consumer_id'];
						$consumer_id_WBSEDCL = $row['fld_bill_consumer_no'];
					}else if($row['fld_discom_id'] == 49 || $row['fld_discom_id'] == 115){
						$consumer_id_WBSEDCL = $row['fld_consumer_id'];
						$consumer_id_CESC = '';
					}
					if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
						$past_reading_date = "";
					}else{
						$past_reading_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_past_reading_date']);
					}
							
					if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
						$present_reading_date = "";
					}else{
						$present_reading_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_present_reading_date']);
					}
							
					if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
						$fld_generated_date = "";
					}else{
						$fld_generated_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_generated_date']);
					}
							
					if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
						$fld_due_date = "";
					}else{
						$fld_due_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_due_date']);
					}
					if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
						$fld_process_date = "";
					}else{
						$fld_process_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_process_date']);
					}
					$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.$bill_counter, $row['fld_organizationsite_id'])
							->setCellValue('B'.$bill_counter, $row['fld_short_form'])
							->setCellValue('C'.$bill_counter, $row['fld_name'])
							->setCellValue('D'.$bill_counter,$row['fld_bill_category'])
							->setCellValue('E'.$bill_counter, $row['fld_tarrif_code'])
							->setCellValueExplicit('F'.$bill_counter,$row['fld_meter_load'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('G'.$bill_counter, $row['fld_meter_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('H'.$bill_counter, $row['fld_security_deposit_amount'])
							->setCellValueExplicit('I'.$bill_counter, $consumer_id_CESC,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('J'.$bill_counter, $consumer_id_WBSEDCL,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('K'.$bill_counter, $fld_generated_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('L'.$bill_counter, $fld_due_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('M'.$bill_counter, $row['fld_previous_reading'])
							->setCellValue('N'.$bill_counter, $row['fld_meter_current_reading'])
							->setCellValue('O'.$bill_counter, $row['unit_consumed'])
							->setCellValueExplicit('P'.$bill_counter, $past_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('Q'.$bill_counter, $present_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('R'.$bill_counter, $row['fld_months_bill'])
							->setCellValue('S'.$bill_counter, $row['fld_energy_charges'])
							->setCellValue('T'.$bill_counter, $row['fld_fix_charges'])
							->setCellValue('U'.$bill_counter, $row['fld_meter_charges'])
							->setCellValue('V'.$bill_counter, $row['fld_mvca_charge'])	
							->setCellValueExplicit('W'.$bill_counter, $row['fld_eb_duty'])
							->setCellValue('X'.$bill_counter, $row['fld_arrear_mvca_charge'])
							->setCellValue('Y'.$bill_counter, $row['fld_energy_arrears'])
							->setCellValueExplicit('Z'.$bill_counter, $row['fld_fix_charges_arrear'])
							->setCellValue('AA'.$bill_counter, $row['fld_late_payment_charge'])
							->setCellValue('AB'.$bill_counter, $row['fld_arrears_duty'])
							->setCellValue('AC'.$bill_counter, '')
							->setCellValue('AD'.$bill_counter, $row['fld_fuel_charges'])
							->setCellValue('AE'.$bill_counter, $row['fld_total_arrears'])
							->setCellValue('AF'.$bill_counter, $row['fld_adjustment_amount'])
							->setCellValue('AG'.$bill_counter, $row['fld_interestsecurity_deposit_amount'])
							->setCellValue('AH'.$bill_counter, $row['fld_gross_total'])
							->setCellValue('AI'.$bill_counter, $row['fld_special_rebate'])
							->setCellValue('AJ'.$bill_counter, $row['fld_rebate'])
							->setCellValue('AK'.$bill_counter, $row['fld_erebate'])
							->setCellValue('AL'.$bill_counter, $row['fld_payment_after_due_date'])
							->setCellValue('AM'.$bill_counter, $row['fld_amount_on_first_due_date'])
							->setCellValue('AN'.$bill_counter, $row['fld_amount_on_second_due_date'])
							->setCellValue('AO'.$bill_counter, $row['fld_amount_on_third_due_date'])
							->setCellValue('AP'.$bill_counter, $row['fld_online_netpayable_amount'])
							->setCellValue('AQ'.$bill_counter, $row['fld_interest_on_tax'])
							->setCellValue('AR'.$bill_counter, $row['fld_sd_amt_required'])
							->setCellValueExplicit('AS'.$bill_counter, $fld_process_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('AT'.$bill_counter, $row['fld_bill_basis'],PHPExcel_Cell_DataType::TYPE_STRING);
							//if($username == 'energy.wb' || $username =='pritam.ghosh'){
							if(1){
								$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('AU'.$bill_counter, $row['fld_tax_on_sale'])
									->setCellValue('AV'.$bill_counter, $row['fld_arrear_min_charge'])
									->setCellValue('AW'.$bill_counter, $row['fld_minimum_charge'])
									->setCellValueExplicit('AX'.$bill_counter, $row['fld_discombill_no'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('AY'.$bill_counter, $row['fld_beneficiary_ac_no'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('AZ'.$bill_counter, $row['fld_IFSC_code']);
									
							}
				}else if($usercircle == 20 || $usercircle == 25){
					if($row['fld_present_reading_date'] != "0000-00-00" && $row['fld_past_reading_date'] != "0000-00-00"){
						$fld_day_of_bill = electricity_bill_date_diff(__LINE__,__FILE__,$row['fld_past_reading_date'],$row['fld_present_reading_date']);
					}
					$fld_previous_reading = $row['fld_previous_reading'];
					$fld_meter_current_reading = $row['fld_meter_current_reading'];
					$fld_billed_unit = $row['fld_billed_unit'];
					if($row['fld_billed_unit_type'] == 'KVAH'){
						$fld_previous_reading = $row['fld_start_reading_KWH'];
						$fld_meter_current_reading = $row['fld_end_reading_KWH'];
						$fld_billed_unit = $row['fld_unit_diff_KWH'];
					}
					$eb_rate = 0;
					if($row['unit_consumed'] != 0){
						$eb_rate = electricity_bill_string_replace(__LINE__,__FILE__,',','',number_format(($row['fld_total_current_amount']/$row['unit_consumed']),4));
					}
					$disconnection_date = date('j-M-Y', strtotime($fld_due_date. ' + 15 days'));
					if($row['fld_supply_date'] == "" || $row['fld_supply_date'] == "0000-00-00"){
						$fld_supply_date = "";
					}else{
						$fld_supply_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_supply_date']);
					}
					if($row['fld_discount_date'] == "" || $row['fld_discount_date'] == "0000-00-00"){
						$fld_discount_date = "";
					}else{
						$fld_discount_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_discount_date']);
					}
					if($row['fld_digital_payment_discount_date'] == "" || $row['fld_digital_payment_discount_date'] == "0000-00-00"){
						$fld_digital_payment_discount_date = "";
					}else{
						$fld_digital_payment_discount_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_digital_payment_discount_date']);
					}
					if($row['fld_prompt_payment_discount_date'] == "" || $row['fld_prompt_payment_discount_date'] == "0000-00-00"){
						$fld_prompt_payment_discount_date = "";
					}else{
						$fld_prompt_payment_discount_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_prompt_payment_discount_date']);
					}
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$bill_counter, $row['fld_organizationsite_id'])
						->setCellValueExplicit('B'.$bill_counter, $row['fld_consumer_id'],PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('C'.$bill_counter, $row['fld_meter_no'],PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValue('D'.$bill_counter, '')
						->setCellValueExplicit('E'.$bill_counter, $row['fld_knumber'],PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValue('F'.$bill_counter, $row['fld_phase'])
						->setCellValue('G'.$bill_counter, $row['fld_meter_load'])
						->setCellValue('H'.$bill_counter, $row['fld_tarrif_code'])
						->setCellValue('I'.$bill_counter, $row['fld_electricity_board'])
						->setCellValue('J'.$bill_counter, $row['fld_bill_sub_division'])
						->setCellValue('K'.$bill_counter, $row['fld_maximum_demand'])
						->setCellValue('L'.$bill_counter, $row['fld_name'])
						->setCellValue('M'.$bill_counter, $row['fld_address'])
						->setCellValue('N'.$bill_counter, $fld_day_of_bill)
						->setCellValueExplicit('O'.$bill_counter, $row['fld_discombill_no'],PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValue('P'.$bill_counter, $fld_generated_date)
						->setCellValue('Q'.$bill_counter, $fld_process_date)
						->setCellValue('R'.$bill_counter, $fld_due_date)
						->setCellValue('S'.$bill_counter, $fld_previous_reading)
						->setCellValue('T'.$bill_counter, $fld_meter_current_reading)
						->setCellValue('U'.$bill_counter, $past_reading_date)
						->setCellValue('V'.$bill_counter, $present_reading_date)
						->setCellValue('W'.$bill_counter, $row['fld_connected_load'])
						->setCellValue('X'.$bill_counter, $fld_billed_unit)
						->setCellValueExplicit('Y'.$bill_counter, $eb_rate,PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValue('Z'.$bill_counter, $disconnection_date)
						->setCellValue('AA'.$bill_counter, $row['fld_total_arrears'])
						->setCellValue('AB'.$bill_counter, $row['fld_current_month_dps'])
						->setCellValue('AC'.$bill_counter, $row['fld_previous_month_dps'])
						->setCellValue('AD'.$bill_counter, $row['fld_arrears'])
						->setCellValue('AE'.$bill_counter, $row['fld_fix_charges'])
						->setCellValue('AF'.$bill_counter, $row['fld_other_charges'])
						->setCellValue('AG'.$bill_counter, $row['fld_eb_duty'])
						->setCellValue('AH'.$bill_counter, $row['fld_meter_charges'])
						->setCellValue('AI'.$bill_counter, $row['fld_fuel_charges'])
						->setCellValue('AJ'.$bill_counter, $row['fld_late_payment_charge'])
						->setCellValue('AK'.$bill_counter, $row['fld_green_cess_or_admin_ch'])
						->setCellValue('AL'.$bill_counter, $row['fld_energy_charges'])
						->setCellValue('AM'.$bill_counter, $row['fld_pf_penal_charges'])
						->setCellValue('AN'.$bill_counter, $row['fld_wheeling_charges'])
						->setCellValue('AO'.$bill_counter, $row['fld_excess_demand_penalty'])
						->setCellValue('AP'.$bill_counter, $row['fld_adjustment_amount'])
						->setCellValue('AQ'.$bill_counter, $row['fld_rebate'])
						->setCellValue('AR'.$bill_counter, $row['fld_security_deposit_amount'])
						->setCellValue('AS'.$bill_counter, 0)
						->setCellValue('AT'.$bill_counter, $row['fld_sd_amt_required'])
						->setCellValue('AU'.$bill_counter, $row['fld_rebate_early_payment'])
						->setCellValue('AV'.$bill_counter, $row['fld_payment_after_due_date'])
						->setCellValue('AW'.$bill_counter, $row['fld_gross_total'])
						->setCellValue('AX'.$bill_counter, $row['fld_last_amount_paid'])
						->setCellValue('AY'.$bill_counter, $row['fld_last_amount_reciept_no'])
						->setCellValue('AZ'.$bill_counter, $fld_last_amount_paid_date)
						->setCellValue('BA'.$bill_counter, $fld_connection_date)
						->setCellValueExplicit('BB'.$bill_counter, electricity_bill_round(__LINE__,__FILE__,$row['fld_mf'],0),PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValue('BC'.$bill_counter, $row['fld_power_factor'])
						->setCellValue('BD'.$bill_counter, $row['fld_payment_after_due_date'])
						->setCellValue('BE'.$bill_counter, $row['fld_total_current_amount'])
						->setCellValue('BF'.$bill_counter, $row['fld_meter_load'])
						->setCellValue('BG'.$bill_counter, $row['fld_bill_base'])
						->setCellValue('BH'.$bill_counter, $row['fld_subsidy_load_factor'])
						->setCellValue('BI'.$bill_counter, $row['fld_minus_the_avg_paymnet_amount'])
						->setCellValue('BJ'.$bill_counter, $row['fld_tax_on_sale']);
						if ($usercircle == 25){
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('BK'.$bill_counter, $row['fld_distribution_channel'])
								->setCellValue('BL'.$bill_counter, $row['fld_bill_category'])
								->setCellValue('BM'.$bill_counter, $row['fld_erebate'])
								->setCellValue('BN'.$bill_counter, $fld_supply_date)
								->setCellValue('BO'.$bill_counter, $fld_discount_date)
								->setCellValue('BP'.$bill_counter, $row['fld_discount_date_amount'])
								->setCellValue('BQ'.$bill_counter, $row['fld_energy_amount_9_to_12_hrs'])
								->setCellValue('BR'.$bill_counter, $row['fld_energy_amount_18_to_22_hrs'])
								->setCellValue('BS'.$bill_counter, $row['fld_energy_amount_22_to_6_hrs'])
								->setCellValue('BT'.$bill_counter, $fld_digital_payment_discount_date)
								->setCellValue('BU'.$bill_counter, $fld_prompt_payment_discount_date)
								->setCellValue('BV'.$bill_counter, $row['fld_arrears_duty']);
						}
				}else if($usercircle == 21){
					$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.$bill_counter, $row['fld_organizationsite_id'])
							->setCellValue('B'.$bill_counter, $row['fld_site_name'])
							->setCellValue('C'.$bill_counter, $row['fld_circle_name'])
							->setCellValue('D'.$bill_counter, $row['fld_cluster'])
							->setCellValue('E'.$bill_counter, $row['fld_short_form'])
							->setCellValueExplicit('F'.$bill_counter,$row['fld_consumer_id'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('G'.$bill_counter, $row['fld_name'])
							->setCellValue('H'.$bill_counter, $row['fld_address'])
							->setCellValueExplicit('I'.$bill_counter, $row['fld_discombill_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('J'.$bill_counter, electricity_bill_ucwords(__LINE__,__FILE__,$row['fld_bill_month']))
							->setCellValueExplicit('K'.$bill_counter, $fld_generated_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('L'.$bill_counter, $fld_due_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('M'.$bill_counter, $fld_process_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('N'.$bill_counter, $row['fld_tarrif_code'])
							->setCellValueExplicit('O'.$bill_counter, $supply_type,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('P'.$bill_counter, $row['fld_meter_load'])
							->setCellValue('Q'.$bill_counter, $row['fld_maximum_demand'])
							->setCellValue('R'.$bill_counter, $row['fld_security_deposit_amount'])
							->setCellValue('S'.$bill_counter, $row['fld_sd_amt_required'])
							->setCellValueExplicit('T'.$bill_counter, $row['fld_meter_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('U'.$bill_counter, $row['fld_is_meter_changed' ])
							->setCellValue('V'.$bill_counter, $row['fld_meter_status'])
							->setCellValueExplicit('W'.$bill_counter, $row['fld_mr_nr'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('X'.$bill_counter, $row['fld_power_factor'])
							->setCellValueExplicit('Y'.$bill_counter, $past_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('Z'.$bill_counter, $present_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('AA'.$bill_counter, $row['fld_start_reading_KWH'])
							->setCellValue('AB'.$bill_counter, $row['fld_end_reading_KWH'])
							->setCellValue('AC'.$bill_counter, $row['fld_unit_diff_KWH'])
							->setCellValue('AD'.$bill_counter, $row['fld_start_reading_KVAH'])
							->setCellValue('AE'.$bill_counter, $row['fld_end_reading_KVAH'])
							->setCellValue('AF'.$bill_counter, $row['fld_unit_diff_KVAH'])
							->setCellValue('AG'.$bill_counter, electricity_bill_round(__LINE__,__FILE__,$row['fld_mf'],0))
							->setCellValue('AH'.$bill_counter, $row['fld_billed_unit_type'])
							->setCellValue('AI'.$bill_counter, $row['fld_previous_reading'])
							->setCellValue('AJ'.$bill_counter, $row['fld_meter_current_reading'])
							->setCellValue('AK'.$bill_counter, $row['unit_consumed'])
							->setCellValue('AL'.$bill_counter, $row['fld_connected_load'])
							->setCellValue('AM'.$bill_counter, $row['fld_billed_unit'])
							->setCellValue('AN'.$bill_counter, $row['fld_arrears'])
							->setCellValue('AO'.$bill_counter, $row['fld_previous_month_dps'])
							->setCellValue('AP'.$bill_counter, $row['fld_total_arrears'])
							->setCellValue('AQ'.$bill_counter, $row['fld_energy_charges'])
							->setCellValue('AR'.$bill_counter, $row['fld_fix_charges'])
							->setCellValue('AS'.$bill_counter, $row['fld_eb_duty'])
							->setCellValue('AT'.$bill_counter, $row['fld_current_month_dps'])
							->setCellValue('AU'.$bill_counter, $row['fld_excess_demand_penalty'])
							->setCellValue('AV'.$bill_counter, $row['fld_fuel_charges'])
							->setCellValue('AW'.$bill_counter, $row['fld_capacitor_charges'])
							->setCellValue('AX'.$bill_counter, $row['fld_subsidy_load_factor'])
							->setCellValue('AY'.$bill_counter, $row['fld_meter_charges'])
							->setCellValue('AZ'.$bill_counter, $row['fld_meter_charges_CGST'])
							->setCellValue('BA'.$bill_counter, $row['fld_meter_charges_SGST'])
							->setCellValue('BB'.$bill_counter, $row['fld_other_charges'])
							->setCellValue('BC'.$bill_counter, $row['fld_subsidy'])
							->setCellValue('BD'.$bill_counter, $row['fld_interestsecurity_deposit_amount'])
							->setCellValue('BE'.$bill_counter, $row['fld_tax'])
							->setCellValue('BF'.$bill_counter, $row['fld_adjustment_amount'])
							->setCellValue('BG'.$bill_counter, $row['fld_other_possitive_entry1'])
							->setCellValue('BH'.$bill_counter, $row['fld_other_possitive_entry2'])
							->setCellValue('BI'.$bill_counter, $row['fld_other_possitive_entry3'])
							->setCellValue('BJ'.$bill_counter, $row['fld_other_negative_entry1'])
							->setCellValue('BK'.$bill_counter, $row['fld_other_negative_entry2'])
							->setCellValue('BL'.$bill_counter, $row['fld_remarks_possitive_entry1'])
							->setCellValue('BM'.$bill_counter, $row['fld_remarks_possitive_entry2'])
							->setCellValue('BN'.$bill_counter, $row['fld_remarks_possitive_entry3'])
							->setCellValue('BO'.$bill_counter, $row['fld_remarks_negative_entry1'])
							->setCellValue('BP'.$bill_counter, $row['fld_remarks_negative_entry2'])
							->setCellValue('BQ'.$bill_counter, $row['fld_total_current_amount'])
							->setCellValue('BR'.$bill_counter, $row['fld_rebate'])
							->setCellValue('BS'.$bill_counter, $row['fld_payment_after_due_date'])
							->setCellValue('BT'.$bill_counter, $row['fld_late_payment_charge'])
							->setCellValue('BU'.$bill_counter, $row['fld_gross_total'])
							->setCellValue('BV'.$bill_counter, $row['fld_erebate'])
							->setCellValueExplicit('BW'.$bill_counter, $row['fld_last_amount_paid'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('BX'.$bill_counter,$fld_last_amount_paid_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('BY'.$bill_counter, $row['fld_last_amount_reciept_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('BZ'.$bill_counter, $row['fld_feeder'])
							->setCellValueExplicit('CA'.$bill_counter,$row['exception'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CB'.$bill_counter,$row['atc_exception_type_error_code'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CC'.$bill_counter,$row['atc_exception'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CD'.$bill_counter,$row['fld_mobile_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CE'.$bill_counter,$row['fld_email'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CF'.$bill_counter,$row['fld_master_minimum_site_cost_per_day'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CG'.$bill_counter,$final_avg_minimum_unit,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CH'.$bill_counter,$row['fld_bill_sub_division'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CI'.$bill_counter,$row['fld_pf_penalty_rebate'],PHPExcel_Cell_DataType::TYPE_STRING);
				}else{
					if($row['fld_circle_id'] == '6'){
						$supply_type = $row['fld_supply_voltage'];
					}else{
						$supply_type = $row['fld_phase'];
					}
					if(isset($row['duedate_difference']) && $row['duedate_difference'] != 0 ){
						$duedate_difference = abs($row['duedate_difference']);
					}else{
						$duedate_difference = "0";
					}
					if(isset($row['billdate_difference']) && $row['billdate_difference'] != 0){
						$billdate_difference = abs($row['billdate_difference']);
					}else{
						$billdate_difference = "0";
					}
							
					if(isset($row['duedate_difference_1']) && $row['duedate_difference_1'] != 0 ){
						$duedate_difference_1 = abs($row['duedate_difference_1']);
					}else{
						$duedate_difference_1 = "0";
					}
					if(isset($row['billdate_difference_1']) && $row['billdate_difference_1'] != 0){
						$billdate_difference_1 = abs($row['billdate_difference_1']);
					}else{
						$billdate_difference_1 = "0";
					}
							
					if(isset($row['compare_row']) && $row['compare_row'] == 1 ){
						$comare_row = 1;
					}else{
						$comare_row = 0;
					}
					if($row['fld_discom_id'] == 28){
						$row['fld_address'] = '';
					}
					$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A'.$bill_counter, $row['fld_organizationsite_id'])
							->setCellValue('B'.$bill_counter, $row['fld_site_name'])
							->setCellValue('C'.$bill_counter, $row['fld_circle_name'])
							->setCellValue('D'.$bill_counter, $row['fld_cluster'])
							->setCellValue('E'.$bill_counter, $row['fld_short_form'])
							->setCellValueExplicit('F'.$bill_counter,$row['fld_consumer_id'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('G'.$bill_counter, $row['fld_name'])
							->setCellValue('H'.$bill_counter, $row['fld_address'])
							->setCellValueExplicit('I'.$bill_counter, $row['fld_discombill_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('J'.$bill_counter, electricity_bill_ucwords(__LINE__,__FILE__,$row['fld_bill_month']))
							->setCellValueExplicit('K'.$bill_counter, $fld_generated_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('L'.$bill_counter, $fld_due_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('M'.$bill_counter, $fld_process_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('N'.$bill_counter, $row['fld_tarrif_code'])
							->setCellValueExplicit('O'.$bill_counter, $supply_type,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('P'.$bill_counter, $row['fld_meter_load'])
							->setCellValue('Q'.$bill_counter, $row['fld_maximum_demand'])
							->setCellValue('R'.$bill_counter, $row['fld_security_deposit_amount'])
							->setCellValue('S'.$bill_counter, $row['fld_sd_amt_required'])
							->setCellValueExplicit('T'.$bill_counter, $row['fld_meter_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('U'.$bill_counter, $row['fld_is_meter_changed' ])
							->setCellValue('V'.$bill_counter, $row['fld_meter_status'])
							->setCellValueExplicit('W'.$bill_counter, $row['fld_mr_nr'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('X'.$bill_counter, $row['fld_power_factor'])
							->setCellValueExplicit('Y'.$bill_counter, $past_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('Z'.$bill_counter, $present_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('AA'.$bill_counter, $row['fld_start_reading_KWH'])
							->setCellValue('AB'.$bill_counter, $row['fld_end_reading_KWH'])
							->setCellValue('AC'.$bill_counter, $row['fld_unit_diff_KWH'])
							->setCellValue('AD'.$bill_counter, $row['fld_start_reading_KVAH'])
							->setCellValue('AE'.$bill_counter, $row['fld_end_reading_KVAH'])
							->setCellValue('AF'.$bill_counter, $row['fld_unit_diff_KVAH'])
							->setCellValue('AG'.$bill_counter, electricity_bill_round(__LINE__,__FILE__,$row['fld_mf'],0))
							->setCellValue('AH'.$bill_counter, $row['fld_billed_unit_type'])
							->setCellValue('AI'.$bill_counter, $row['fld_previous_reading'])
							->setCellValue('AJ'.$bill_counter, $row['fld_meter_current_reading'])
							->setCellValue('AK'.$bill_counter, $row['unit_consumed'])
							->setCellValue('AL'.$bill_counter, $row['fld_connected_load'])
							->setCellValue('AM'.$bill_counter, $row['fld_billed_unit'])
							->setCellValue('AN'.$bill_counter, $row['fld_arrears'])
							->setCellValue('AO'.$bill_counter, $row['fld_previous_month_dps'])
							->setCellValue('AP'.$bill_counter, $row['fld_total_arrears'])
							->setCellValue('AQ'.$bill_counter, $row['fld_energy_charges'])
							->setCellValue('AR'.$bill_counter, $row['fld_fix_charges'])
							->setCellValue('AS'.$bill_counter, $row['fld_eb_duty'])
							->setCellValue('AT'.$bill_counter, $row['fld_current_month_dps'])
							->setCellValue('AU'.$bill_counter, $row['fld_excess_demand_penalty'])
							->setCellValue('AV'.$bill_counter, $row['fld_fuel_charges'])
							->setCellValue('AW'.$bill_counter, $row['fld_capacitor_charges'])
							->setCellValue('AX'.$bill_counter, $row['fld_subsidy_load_factor'])
							->setCellValue('AY'.$bill_counter, $row['fld_meter_charges'])
							->setCellValue('AZ'.$bill_counter, $row['fld_meter_charges_CGST'])
							->setCellValue('BA'.$bill_counter, $row['fld_meter_charges_SGST'])
							->setCellValue('BB'.$bill_counter, $row['fld_other_charges'])
							->setCellValue('BC'.$bill_counter, $row['fld_subsidy'])
							->setCellValue('BD'.$bill_counter, $row['fld_interestsecurity_deposit_amount'   ])
							->setCellValue('BE'.$bill_counter, $row['fld_tax'])
							->setCellValue('BF'.$bill_counter, $row['fld_adjustment_amount'])
							->setCellValue('BG'.$bill_counter, $row['fld_other_possitive_entry1'])
							->setCellValue('BH'.$bill_counter, $row['fld_other_possitive_entry2'])
							->setCellValue('BI'.$bill_counter, $row['fld_other_possitive_entry3'])
							->setCellValue('BJ'.$bill_counter, $row['fld_other_negative_entry1'])
							->setCellValue('BK'.$bill_counter, $row['fld_other_negative_entry2'])
							->setCellValue('BL'.$bill_counter, $row['fld_remarks_possitive_entry1'])
							->setCellValue('BM'.$bill_counter, $row['fld_remarks_possitive_entry2'])
							->setCellValue('BN'.$bill_counter, $row['fld_remarks_possitive_entry3'])
							->setCellValue('BO'.$bill_counter, $row['fld_remarks_negative_entry1'])
							->setCellValue('BP'.$bill_counter, $row['fld_remarks_negative_entry2'])
							->setCellValue('BQ'.$bill_counter, $row['fld_total_current_amount'])
							->setCellValue('BR'.$bill_counter, $row['fld_rebate'])
							->setCellValue('BS'.$bill_counter, $row['fld_payment_after_due_date'  ])
							->setCellValue('BT'.$bill_counter, $row['fld_late_payment_charge'])
							->setCellValue('BU'.$bill_counter, $row['fld_gross_total'])
							->setCellValue('BV'.$bill_counter, $row['fld_erebate'])
							->setCellValueExplicit('BW'.$bill_counter, $row['fld_last_amount_paid'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('BX'.$bill_counter,$fld_last_amount_paid_date,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('BY'.$bill_counter, $row['fld_last_amount_reciept_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue('BZ'.$bill_counter, $row['fld_feeder'])
							->setCellValueExplicit('CA'.$bill_counter,$row['exception'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CB'.$bill_counter,$row['atc_exception_type_error_code'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CC'.$bill_counter,$row['atc_exception'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CD'.$bill_counter,$row['fld_mobile_no'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CE'.$bill_counter,$row['fld_email'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CF'.$bill_counter,$row['fld_master_minimum_site_cost_per_day'],PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CG'.$bill_counter,$final_avg_minimum_unit,PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValueExplicit('CH'.$bill_counter,$row['fld_bill_sub_division'],PHPExcel_Cell_DataType::TYPE_STRING);
							if($usercircle == 3 || $usercircle == 4){
								$discom_name = '';
								if(isset($discom_name_arr[$row['fld_organizationsite_id']]['discom_name'])){
									$discom_name = $discom_name_arr[$row['fld_organizationsite_id']]['discom_name'];
								}
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CI'.$bill_counter, $fld_connection_date);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CJ'.$bill_counter, $row['fld_bill_division']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CK'.$bill_counter, $discom_name);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CL'.$bill_counter, $row['fld_progressive_subsidy']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CM'.$bill_counter, $row['fld_period_months']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CN'.$bill_counter, $row['fld_meter_remark']);
							}
							if($usercircle == 19){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF'.$bill_counter, '');
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CI'.$bill_counter, $row['fld_adjustment_amount']);
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CJ'.$bill_counter, $row['fld_misc_charges']);
							}
							if($usercircle == 15){
								$objPHPExcel->setActiveSheetIndex(0)->setCellValue('CI'.$bill_counter, $row['fld_progressive_subsidy']);
							}
							if($username == 'navin' ){
								$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('CI'.$bill_counter,$row['fld_ai_id'])
								->setCellValue('CJ'.$bill_counter,$row['fld_file_name']);
							}						
							if($username == 'anant' ){
								//if($billdate_difference > 7 || $duedate_difference > 7){
									//cellColor('A'.$bill_counter.':BQ'.$bill_counter, '87CEEB');
								//}
										
									
								if($billdate_difference >5 || $duedate_difference >5){
										$objPHPExcel->getActiveSheet()
										->getStyle('A' . $bill_counter . ':CE' . $bill_counter)
										->getFill()
										->applyFromArray(
											array(
									        		'type' => PHPExcel_Style_Fill::FILL_SOLID,
									        		'fill' => array(
													'color' => array('rgb' => '87CEEB')
												)
									    	));
											
								}
										
										
										
								if(isset($row['same_bill_date_colour']) && $row['same_bill_date_colour']){
									$objPHPExcel->getActiveSheet()
										->getStyle('A' . $bill_counter . ':CE' . $bill_counter)
										->getFill()
										->applyFromArray(
											array(
									        		'type' => PHPExcel_Style_Fill::FILL_SOLID,
												        		
												'startcolor' => array(
											             'rgb' => '87CEEB'
											        )
									    	));
											
								}
											
								if(isset($row['same_bill_due_date_colour']) && $row['same_bill_due_date_colour']){
									$objPHPExcel->getActiveSheet()
										->getStyle('A' . $bill_counter . ':CE' . $bill_counter)
										->getFill()
										->applyFromArray(
											array(
									        		'type' => PHPExcel_Style_Fill::FILL_SOLID,
												        		
												'startcolor' => array(
											             'rgb' => '90EE90'
											        )
									    	));
											
								}
											
							}
				}		
				$bill_counter++;
				$file_name_array[] = $row['fld_file_name'];
			}
		}else{
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'No Bill Found') ;
		}
				
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		if($excel_with_files == '1'){
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('download/'.$fn.'.xls');
			$archive_file_name=time().'_'.$fn.'.zip';
					
			$genarated_pdf_file_array = array();
			$file_path = "zip";
			$zip = new ZipArchive(); // Load zip library
			if($zip->open($file_path."/".$archive_file_name, ZIPARCHIVE::CREATE)!==TRUE){
				// Opening zip file to load files
				$error = "* Sorry ZIP creation failed at this time";
			}
			$zip->addFile("download/".$fn.'.xls',$fn.'.xls'); // Adding files into zip
			foreach($file_name_array as $value){
				$temp_arr = electricity_bill_explode(__LINE__,__FILE__,'/',$value);
				if($zip->locateName($temp_arr['1']) === false){
					$zip->addEmptyDir($temp_arr['1']);
				}
				if(file_exists($value)){
					$zip->addFile($value,$temp_arr[1].'/'.$temp_arr[2]);
				}else{
				
					$zip->addFile('oldbills/download/'.$temp_arr[1].'/'.$temp_arr[2],$temp_arr[1].'/'.$temp_arr[2]);
				}
			}
			$zip->close();
			if(file_exists($file_path."/".$archive_file_name)){
				// push to download the zip
				header("Content-type: application/zip");
				header("Content-Disposition: attachment; filename=$archive_file_name");
				header("Pragma: no-cache");
				header("Expires: 0");
				readfile($file_path."/".$archive_file_name); 
				exit();
			}
		}else {
			if($is_mail_manually != 1){
				header('Content-Type: application/vnd.ms-excel');
				header("Content-Disposition: attachment; filename=$fn.xls");
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');
					 
				// If you're serving to IE over SSL, then the following may be needed
				header ('Expires: Mon, 26 Jul 2020 05:00:00 GMT'); // Date in the past
				header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header ('Pragma: public'); // HTTP/1.0
					
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
					
				$objWriter->save('php://output');	
			}else{
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('download/'.$fn.'.xls');	
			}
		}
	}else if ($atc_excel_kl_format == 1){
		$fn = "Altius_Kerala_Energy_".date('d_m_Y');
		if($is_mail_manually == 1){
			$fn = $file_name;
		}
		$file_name_array = array();
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Team Codez")
						   ->setLastModifiedBy("Team Codez")
						   ->setTitle("Office 2007 XLSX Test Document")
						   ->setSubject("Office 2007 XLSX Test Document")
						   ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
						   ->setKeywords("office 2007 openxml php")
						   ->setCategory("Test result file");
			
			if(electricity_bill_sizeof(__LINE__,__FILE__,$bill_array)) {
				$objPHPExcel->getActiveSheet()->setTitle("EPR Uploder Format");
				$objPHPExcel->createSheet();
				$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'ENTITY')
								->setCellValue('B1', 'GLOBAL_SITE_ID')
								->setCellValue('C1', 'Consumer_Number')
								->setCellValue('D1', 'METER_NUMBER')
								->setCellValue('E1', 'EB_BILL_NO')
								->setCellValue('F1', 'BILL_RECEIVED_DATE')
								->setCellValue('G1', 'BILL_ISSUE_DATE')
								->setCellValue('H1', 'BILL_DUE_DATE')
								->setCellValue('I1', 'EB_START_DATE')
								->setCellValue('J1', 'EB_END_DATE')
								->setCellValue('K1', 'OPENING_METER_READING')
								->setCellValue('L1', 'CLOSING_METER_READING')
								->setCellValue('M1', 'Multiplication_Factor')
								->setCellValue('N1', 'UNIT_CONSUMED')
								->setCellValue('O1', 'UNIT_RATE')
								->setCellValue('P1', 'CURRENT_ENERGY_CHARGES')
								->setCellValue('Q1', 'METER_RENT')
								->setCellValue('R1', 'FIXED_CHARGES')
								->setCellValue('S1', 'ADMIN_CHARGES')
								->setCellValue('T1', 'CESS')
								->setCellValue('U1', 'BASIC_EB_CHARGES')
								->setCellValue('V1', 'FUEL_SURCHARGE')
								->setCellValue('W1', 'CUSTOMER_CC_CHARGES')
								->setCellValue('X1', 'ENERGY_DUTY_ED_CHARGES')
								->setCellValue('Y1', 'GOVT_TAX_OTHER_CHARGES')
								->setCellValue('Z1', 'ADD_CHARGES_BY_EB_BOARD')
								->setCellValue('AA1', 'MUNICIPALITY_CHARGES')
								->setCellValue('AB1', 'OTHER_CHARGES')
								->setCellValue('AC1', 'ARREARS_START_DATE')
								->setCellValue('AD1', 'ARREARS_END_DATE')
								->setCellValue('AE1', 'ARREARS_AMT')
								->setCellValue('AF1', 'Disconnection_Date')
								->setCellValue('AG1', 'RE_DIS_CONNECTION_CHARGES')
								->setCellValue('AH1', 'LATE_PAYMENT_CHARGES')
								->setCellValue('AI1', 'PENALTY_OTHER_LATE_PAYMENT')
								->setCellValue('AJ1', 'SECURITY_DEPOSIT_PAID')
								->setCellValue('AK1', 'SECURITY_DEPOSIT_REFUND')
								->setCellValue('AL1', 'INTEREST_ON_SECURITY_DEPOSIT')
								->setCellValue('AM1', 'REBATE_EARLY_PAYMENT')
								->setCellValue('AN1', 'ADVANCE_PAID_AMOUNT')
								->setCellValue('AO1', 'ADVANCE_RECEIVED_AMOUNT')
								->setCellValue('AP1', 'PAYMENT MODE')
								->setCellValue('AQ1', 'DD_CHARGES')
								->setCellValue('AR1', 'REMARKS')	
								->setCellValue('AS1', 'SPECIAL_CASE')
								->setCellValue('AT1', 'AGENCY')
								->setCellValue('AU1', 'INFAVOUR OF')
								->setCellValue('AV1', 'EXPENSE_PERIOD')
								->setCellValue('AW1', 'APPROVED_EB_BILL_AMOUNT')
								->setCellValue('AX1', 'BILL_TYPE ( Regular, Avg, Reconcile)')
								->setCellValue('AY1', 'ADVANCE_PAID for prepaid meter case')
								->setCellValue('AZ1', 'SDO Code');
								if(1){
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA1', 'SECURITY_DEPOSIT')
									->setCellValue('BB1', 'SECURITY_DEPOSIT_REMARK')
									->setCellValue('BC1', 'TCS')
									->setCellValue('BD1', 'IndorCom_Credit')
									->setCellValue('BE1', 'Gross bill amount')
									->setCellValue('BF1', 'Last Paid Amount')
									->setCellValue('BG1', 'Last Payment Date');
								}
				$bill_counter = 2;	
				$bill_loader_counter = 2;
				foreach($bill_array as $key => $row){
					$ADVANCE_RECEIVED_AMOUNT = '';
					$remark_bill_base = $row['fld_bill_base'];
					$approve_amount = $row['fld_payment_after_due_date'];
					$difference_approve_amount = $row['fld_other_charges'];
					$fld_agency = $row['fld_short_form'];
					$fld_day_of_bill = $govt_tax_other_charges = 0;
					$fld_sd_amt_required = $row['fld_security_deposit_amount'];
					$current_lpsc = $row['fld_late_payment_charge'];
					$excess_demand_penalty = $row['fld_capacitor_charges'];
					$total_eb_charges = $row['fld_eb_duty'];
					$fld_infavour_of = $row['fld_bill_sub_division'];
					$bill_type = $row['fld_bill_base'];
					$payment_mode = 'BNP';
					$special_case = '';
					$fld_rebate = $row['fld_rebate'];
					$fld_security_deposit_remark = $row['fld_security_deposit_remark'];
					#For Kerala 10
					if($row['fld_circle_id'] == 10){
						$excess_demand_penalty = $row['fld_late_payment_charge'];
						$current_lpsc = $row['fld_capacitor_charges'];
						$govt_tax_other_charges = $row['fld_tax'];
						$ADVANCE_RECEIVED_AMOUNT = $row['fld_arrears'] - $row['fld_total_arrears']; 
						$ADVANCE_RECEIVED_AMOUNT = round($ADVANCE_RECEIVED_AMOUNT,2) * -1;
					}
					if($fld_security_deposit_remark == ''){
						if($row['fld_security_deposit_amount'] == 0){
							$fld_security_deposit_remark = 'Zero SD amount As per Bill';
						}else if($row['fld_security_deposit_amount'] == ''){
							$fld_security_deposit_remark = 'SD not print in Bill';
						}else{
							$fld_security_deposit_remark = 'SD uploaded';
						}
					}
					if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
						$past_reading_date = "";
					}else{
						$past_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_past_reading_date']);
					}
						
					if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
						$present_reading_date = "";
					}else{
						$present_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_present_reading_date']);
					}
						
					if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
						$fld_generated_date = "";
					}else{
						$fld_generated_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_generated_date']);
					}
						
					if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
						$fld_due_date = "";
					}else{
						$fld_due_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_due_date']);
					}
					if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
						$fld_process_date = "";
					}else{
						$fld_process_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_process_date']);
					}
					if($row['fld_last_amount_paid_date'] == "" || $row['fld_last_amount_paid_date'] == "0000-00-00" || $row['fld_last_amount_paid_date'] == "0000-00-00 00:00:00"){
						$fld_last_amount_paid_date = "";
					}else{
						$fld_last_amount_paid_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_last_amount_paid_date']);
					}
	
					if($row['fld_is_meter_changed'] == 0){
						$fld_is_meter_changed = "No";
					}else{
						$fld_is_meter_changed = "Yes";
					}
					$bill_month = electricity_bill_ucwords(__LINE__,__FILE__,electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_month'],0,3))."-".electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_year'],2,2) ;
					$disconnection_date = date('j-M-Y', strtotime($fld_due_date. ' + 15 days'));
					$eb_rate = 0;
					if($row['unit_consumed'] != 0){
						$eb_rate = electricity_bill_string_replace(__LINE__,__FILE__,',','',number_format(($row['fld_total_current_amount']/$row['unit_consumed']),4));
					}
					//$fld_ca_no = $row['fld_ca_no'];
					//if($row['fld_discom_id'] == '90'){
						$fld_ca_no = $row['fld_consumer_id'];
					//}
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A'.$bill_counter, $row['fld_entity_name'])
								->setCellValue('B'.$bill_counter, $row['fld_organizationsite_id'])
								->setCellValueExplicit('C'.$bill_counter, $fld_ca_no,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('D'.$bill_counter, $row['fld_meter_no'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('E'.$bill_counter, $row['fld_discombill_no'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('F'.$bill_counter, $fld_process_date)
								->setCellValue('G'.$bill_counter, $fld_generated_date)
								->setCellValue('H'.$bill_counter, $fld_due_date)
								->setCellValue('I'.$bill_counter, $past_reading_date)
								->setCellValue('J'.$bill_counter, $present_reading_date)
								->setCellValue('K'.$bill_counter, $row['fld_previous_reading'])
								->setCellValue('L'.$bill_counter, $row['fld_meter_current_reading'])
								->setCellValueExplicit('M'.$bill_counter, $row['fld_mf'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('N'.$bill_counter, $row['unit_consumed'])
								->setCellValueExplicit('O'.$bill_counter, $eb_rate,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('P'.$bill_counter, 0)
								->setCellValue('Q'.$bill_counter, ($usercircle == 6) ? round($row['fld_meter_charges']) : $row['fld_meter_charges'])
								->setCellValue('R'.$bill_counter, ($usercircle == 6) ? round($row['fld_fix_charges']) : $row['fld_fix_charges'])
								->setCellValue('S'.$bill_counter, $row['fld_green_cess_or_admin_ch'])
								->setCellValue('T'.$bill_counter, $row['fld_energy_cess'])
								->setCellValue('U'.$bill_counter, ($usercircle == 6) ? round($row['fld_energy_charges']) : $row['fld_energy_charges'])
								->setCellValue('V'.$bill_counter, ($usercircle == 6) ? round($row['fld_fuel_charges']) : $row['fld_fuel_charges'])
								->setCellValue('W'.$bill_counter, 0)
								->setCellValue('X'.$bill_counter, ($usercircle == 6) ? round($total_eb_charges) : $total_eb_charges)
								->setCellValue('Y'.$bill_counter, ($usercircle == 6) ? round($govt_tax_other_charges) : $govt_tax_other_charges)
								->setCellValue('Z'.$bill_counter, 0)
								->setCellValue('AA'.$bill_counter, 0)
								->setCellValue('AB'.$bill_counter, ($usercircle == 6) ? round($difference_approve_amount) : $difference_approve_amount)
								->setCellValue('AC'.$bill_counter, '')
								->setCellValue('AD'.$bill_counter, '')
								->setCellValue('AE'.$bill_counter, ($usercircle == 6) ? round($row['fld_arrears']) : $row['fld_arrears'])
								->setCellValue('AF'.$bill_counter, $disconnection_date)
								->setCellValue('AG'.$bill_counter, 0)
								->setCellValue('AH'.$bill_counter, ($usercircle == 6) ? round($current_lpsc) : $current_lpsc)
								->setCellValue('AI'.$bill_counter, ($usercircle == 6) ? round($excess_demand_penalty) : $excess_demand_penalty)
								->setCellValue('AJ'.$bill_counter, $row['fld_security_deposit_paid'])
								->setCellValue('AK'.$bill_counter, $row['fld_security_deposit_refund'])
								->setCellValue('AL'.$bill_counter, $row['fld_interestsecurity_deposit_amount'])
								->setCellValue('AM'.$bill_counter, ($usercircle == 6) ? round($fld_rebate) : abs($fld_rebate))
								->setCellValue('AN'.$bill_counter, 0)
								->setCellValue('AO'.$bill_counter, $ADVANCE_RECEIVED_AMOUNT)
								->setCellValue('AP'.$bill_counter, $payment_mode)
								->setCellValue('AQ'.$bill_counter, 0)
								->setCellValue('AR'.$bill_counter, $remark_bill_base)
								->setCellValue('AS'.$bill_counter, $special_case)
								->setCellValue('AT'.$bill_counter, $fld_agency)
								->setCellValue('AU'.$bill_counter, $fld_infavour_of)
								->setCellValue('AV'.$bill_counter, $fld_day_of_bill)
								->setCellValue('AW'.$bill_counter, $approve_amount)
								->setCellValue('AX'.$bill_counter, $bill_type)
								->setCellValue('AY'.$bill_counter, 0)
								->setCellValue('AZ'.$bill_counter, $row['fld_sdo_code']);
								//if ($usercircle == 12) {	
								if(1){
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA'.$bill_counter, $row['fld_security_deposit_amount']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB'.$bill_counter, $fld_security_deposit_remark);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC'.$bill_counter, $row['fld_tax_on_sale']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD'.$bill_counter, $row['fld_indorcom_credit']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BE'.$bill_counter, $row['fld_total_current_amount']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF'.$bill_counter, $row['fld_last_amount_paid']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BG'.$bill_counter, $fld_last_amount_paid_date);
								}
									
					$bill_counter++;
				}
				
				$objPHPExcel->setActiveSheetIndex(1)->setTitle("Site Master Format");
				$objPHPExcel->setActiveSheetIndex(1)
								->setCellValue('A1', 'Sr no. ')
								->setCellValue('B1', 'Entity ')
								->setCellValue('C1', 'EPR No. ')
								->setCellValue('D1', 'SAP ID ')
								->setCellValue('E1', 'Site ID ')
								->setCellValue('F1', 'Globle ID ')
								->setCellValue('G1', 'Site Name ')
								->setCellValue('H1', 'Cluster ')
								->setCellValue('I1', 'Dist. ')
								->setCellValue('J1', 'Sanction Load (KW) ')
								->setCellValue('K1', 'No of BTS ')
								->setCellValue('L1', 'EB Cycle ')
								->setCellValue('M1', 'O&M Agency ')
								->setCellValue('N1', 'Old Consumer No. ')
								->setCellValue('O1', 'New Consumer No. ')
								->setCellValue('P1', 'K no. ')
								->setCellValue('Q1', 'Meter No ')
								->setCellValue('R1', 'Type of Supply (Phase) ')
								->setCellValue('S1', 'City Classification(Rural/Urban) ')
								->setCellValue('T1', 'Period Start ')
								->setCellValue('U1', 'Period End ')
								->setCellValue('V1', 'EB Bill Date ')
								->setCellValue('W1', 'Due Date ')
								->setCellValue('X1', 'Vendor DD Request Date ')
								->setCellValue('Y1', 'Disconnection Date ')
								->setCellValue('Z1', 'Arrears Starting Date ')
								->setCellValue('AA1', 'Arrears End Date ')
								->setCellValue('AB1', 'EB Reading Start ')
								->setCellValue('AC1', 'EB Reading End ')
								->setCellValue('AD1', 'Unit Multiplying Factor (M.F.) ')
								->setCellValue('AE1', 'No. Units ')
								->setCellValue('AF1', 'Arrears No. Units ')
								->setCellValue('AG1', 'Days ')
								->setCellValue('AH1', 'EB Rate ')
								->setCellValue('AI1', 'EB Bill type (Regular/Avg) ')
								->setCellValue('AJ1', 'Energy Charges ')
								->setCellValue('AK1', 'Fixed Charge ')
								->setCellValue('AL1', 'Meter Rent ')
								->setCellValue('AM1', 'Fuel S/C ')
								->setCellValue('AN1', 'Shunt Capacitor Charges ')
								->setCellValue('AO1', 'Eletricity Duty(40 Paisa) ')
								->setCellValue('AP1', 'WCC (10 Paisa) ')
								->setCellValue('AQ1', 'UC ( 15 Paisa) ')
								->setCellValue('AR1', 'Others negam Dues ')	
								->setCellValue('AS1', 'Arrear LPC ')
								->setCellValue('AT1', 'Arrear Amt. if any ')
								->setCellValue('AU1', 'Adv. Paid ')
								->setCellValue('AV1', 'Interest Received ')
								->setCellValue('AW1', 'Rebate(-) Voltage ')
								->setCellValue('AX1', 'GST ')
								->setCellValue('AY1', 'Total Amt.( Before Due Date) ')
								->setCellValue('AZ1', 'Late Payment Penalty ')
								->setCellValue('BA1', 'Total Amt.( After Due Date) ')
								->setCellValue('BB1', 'Penalty ')
								->setCellValue('BC1', 'Request DD Amount')
								->setCellValue('BD1', 'DD in Favor of ')
								->setCellValue('BE1', 'Location ')
								->setCellValue('BF1', 'Branch Code No. ')
								->setCellValue('BG1', 'If DD, Drawn on which Bank ')
								->setCellValue('BH1', 'DISCOM ')
								->setCellValue('BI1', 'Remarks ')
								->setCellValue('BJ1', 'Month ')
								->setCellValue('BK1', 'EPR No. ')
								->setCellValue('BL1', 'IOM Date ')
								->setCellValue('BM1', 'Security Amount ')
								->setCellValue('BN1', 'DD No. ')
								->setCellValue('BO1', 'DD Amount ')
								->setCellValue('BP1', 'DD Date ')
								->setCellValue('BQ1', 'Difference of TDS deducted from Interest on security ')
								->setCellValue('BR1', 'SMALL COIN ')
								->setCellValue('BS1', 'Prompt Payment Incentive ')
								->setCellValue('BT1', 'Account No. ');
								if($usercircle == 6){
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BU1', 'Power Factor ')
													->setCellValue('BV1', 'Start Reading(KVAH) ')
													->setCellValue('BW1', 'End Reading (KVAH) ')
													->setCellValue('BX1', 'Unit Difference(KVAH) ');
								}
				$bill_counter = 2;	
				$bill_loader_counter = 2;
				foreach($bill_array as $key => $row){
					if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
						$past_reading_date = "";
					}else{
						$past_reading_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_past_reading_date']);
					}
						
					if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
						$present_reading_date = "";
					}else{
						$present_reading_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_present_reading_date']);
					}
						
					if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
						$fld_generated_date = "";
					}else{
						$fld_generated_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_generated_date']);
					}
						
					if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
						$fld_due_date = "";
					}else{
						$fld_due_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_due_date']);
					}
					if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
						$fld_process_date = "";
					}else{
						$fld_process_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_process_date']);
					}
					if($row['fld_last_amount_paid_date'] == "" || $row['fld_last_amount_paid_date'] == "0000-00-00" || $row['fld_last_amount_paid_date'] == "0000-00-00 00:00:00"){
						$fld_last_amount_paid_date = "";
					}else{
						$fld_last_amount_paid_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_last_amount_paid_date']);
					}
	
					if($row['fld_is_meter_changed'] == 0){
						$fld_is_meter_changed = "No";
					}else{
						$fld_is_meter_changed = "Yes";
					}
					$bill_month = electricity_bill_ucwords(__LINE__,__FILE__,electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_month'],0,3))."-".electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_year'],2,2) ;
					$disconnection_date = date('j-m-Y', strtotime($fld_due_date. ' + 15 days'));
					$eb_rate = 0;
					if($row['unit_consumed'] != 0){
						$eb_rate = number_format(($row['fld_total_current_amount']/$row['unit_consumed']),4);
					}
					$fld_infavour_of = $row['fld_bill_sub_division'];
					#New added
					$fld_service_no = $row['fld_service_no'];
					$fld_consumer_id = $row['fld_consumer_id'];
					$meter_no_xl=$row['fld_meter_no'];
					if($row['fld_discom_id'] == '90'){
						$fld_service_no = $row['fld_consumer_id'];
						$fld_consumer_id = $row['fld_service_no'];
					}
					$supply_type = $row['fld_phase'];
					$fld_payment_after_due_date_sm = $row['fld_payment_after_due_date'];
					if($row['fld_discom_id'] == 103 && ($past_reading_date == '' || $present_reading_date == '')){
						$row['fld_day_of_bill'] = 0;
					}
					$objPHPExcel->setActiveSheetIndex(1)
								->setCellValue('A'.$bill_counter, $bill_counter - 1)
								->setCellValueExplicit('B'.$bill_counter, $row['fld_entity_name'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('C'.$bill_counter, $row['fld_erp_id'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('D'.$bill_counter, $row['fld_sap_id'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('E'.$bill_counter, $row['fld_global_id'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('F'.$bill_counter, $row['fld_organizationsite_id'])
								->setCellValueExplicit('G'.$bill_counter, $row['fld_site_name'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('H'.$bill_counter, $row['fld_cluster'])
								->setCellValueExplicit('I'.$bill_counter, $row['fld_payable_location'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('J'.$bill_counter, $row['fld_meter_load'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('K'.$bill_counter, $row['fld_no_of_bts'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('L'.$bill_counter, $row['fld_eb_cycle'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('M'.$bill_counter, $row['fld_agency'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('N'.$bill_counter, $row['fld_ca_no'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('O'.$bill_counter, $fld_service_no,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('P'.$bill_counter, $fld_consumer_id,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('Q'.$bill_counter, $meter_no_xl,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('R'.$bill_counter, $supply_type,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('S'.$bill_counter, $row['fld_area_type'])
								->setCellValueExplicit('T'.$bill_counter, $past_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('U'.$bill_counter, $present_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('V'.$bill_counter, $fld_generated_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('W'.$bill_counter, $fld_due_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('X'.$bill_counter, $fld_process_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('Y'.$bill_counter, $disconnection_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('Z'.$bill_counter, '')
								->setCellValue('AA'.$bill_counter, '')
								->setCellValue('AB'.$bill_counter, $row['fld_previous_reading'])
								->setCellValue('AC'.$bill_counter, $row['fld_meter_current_reading'])
								->setCellValue('AD'.$bill_counter, $row['fld_mf'])
								->setCellValue('AE'.$bill_counter, $row['unit_consumed'])
								->setCellValue('AF'.$bill_counter, '')
								->setCellValue('AG'.$bill_counter, $row['fld_day_of_bill'])
								->setCellValueExplicit('AH'.$bill_counter, $eb_rate,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('AI'.$bill_counter, $row['fld_bill_base'])
									
								->setCellValue('AJ'.$bill_counter, ($usercircle == 6) ? round($row['fld_energy_charges']) : $row['fld_energy_charges'])
								->setCellValue('AK'.$bill_counter, ($usercircle == 6) ? round($row['fld_fix_charges']) : $row['fld_fix_charges'])
								->setCellValue('AL'.$bill_counter, ($usercircle == 6) ? round($row['fld_meter_charges']) : $row['fld_meter_charges'])
								->setCellValue('AM'.$bill_counter, ($usercircle == 6) ? round($row['fld_fuel_charges']) : $row['fld_fuel_charges'])
								->setCellValue('AN'.$bill_counter, ($usercircle == 6) ? round($row['fld_subsidy_load_factor']) : $row['fld_subsidy_load_factor'])
								->setCellValue('AO'.$bill_counter, ($usercircle == 6) ? round($row['fld_eb_duty']) : $row['fld_eb_duty'])
								->setCellValue('AP'.$bill_counter, ($usercircle == 6) ? round($row['fld_water_cess']) : $row['fld_water_cess'])
								->setCellValue('AQ'.$bill_counter, ($usercircle == 6) ? round($row['fld_urban_cess']) : $row['fld_urban_cess'])
								->setCellValue('AR'.$bill_counter, ($usercircle == 6) ? round($row['fld_other_nigam_dues']) : $row['fld_other_nigam_dues'])
								->setCellValue('AS'.$bill_counter, '')
								->setCellValue('AT'.$bill_counter, ($usercircle == 6) ? round($row['fld_arrears']) : $row['fld_arrears'])
								->setCellValue('AU'.$bill_counter, '')
								->setCellValue('AV'.$bill_counter, '')
								->setCellValue('AW'.$bill_counter, ($usercircle == 6) ? round($row['fld_rebate']) : $row['fld_rebate'])
								->setCellValue('AX'.$bill_counter, '')
								->setCellValue('AY'.$bill_counter, ($usercircle == 6) ? round($fld_payment_after_due_date_sm) : $fld_payment_after_due_date_sm)
								->setCellValue('AZ'.$bill_counter, ($usercircle == 6) ? round($row['fld_late_payment_charge']) : $row['fld_late_payment_charge'])
								->setCellValue('BA'.$bill_counter, ($usercircle == 6) ? round($row['fld_gross_total']) : $row['fld_gross_total'])
									
								->setCellValue('BB'.$bill_counter, 0)
								->setCellValue('BC'.$bill_counter, 0)
								->setCellValue('BD'.$bill_counter, $fld_infavour_of)
								->setCellValue('BE'.$bill_counter, '')
								->setCellValue('BF'.$bill_counter, '')
								->setCellValue('BG'.$bill_counter, '')
								->setCellValue('BH'.$bill_counter, $row['fld_short_form'])
								->setCellValue('BI'.$bill_counter, '')
								->setCellValueExplicit('BJ'.$bill_counter, $bill_month,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('BK'.$bill_counter, '')
								->setCellValue('BL'.$bill_counter, '')
								->setCellValue('BM'.$bill_counter, $row['fld_security_deposit_amount'])
								->setCellValue('BN'.$bill_counter, '')
								->setCellValue('BO'.$bill_counter, '')
								->setCellValue('BP'.$bill_counter, '')
								->setCellValue('BQ'.$bill_counter, $row['fld_tds_deducted_diff'])
								->setCellValue('BR'.$bill_counter, $row['fld_small_coin'])
								->setCellValue('BS'.$bill_counter, $row['fld_prompt_payment_incentive'])
								->setCellValueExplicit('BT'.$bill_counter, $row['fld_service_no'],PHPExcel_Cell_DataType::TYPE_STRING);
								if ($usercircle == 6) {
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BU'.$bill_counter, $row['fld_power_factor']);
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BV'.$bill_counter, $row['fld_start_reading_KVAH']);
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BW'.$bill_counter, $row['fld_end_reading_KVAH']);
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BX'.$bill_counter, $row['fld_unit_diff_KVAH']);
								}
									
					$bill_counter++;
					$file_name_array[] = $row['fld_file_name'];
				}
			}else{
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No Bill  Found') ;
			}
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			if($is_mail_manually != 1){
				header('Content-Type: application/vnd.ms-excel');
				header("Content-Disposition: attachment; filename=$fn.xls");
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');
				 
				// If you're serving to IE over SSL, then the following may be needed
				header ('Expires: Mon, 26 Jul 2020 05:00:00 GMT'); // Date in the past
				header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header ('Pragma: public'); // HTTP/1.0
				
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				
				$objWriter->save('php://output');	
			}else{
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('download/'.$fn.'.xls');	
			}
	}else if ($atc_excel_jh_format == 1){
		$fn = "Altius_Jharkhand_Energy_".date('d_m_Y');
		if($is_mail_manually == 1){
			$fn = $file_name;
		}
		$file_name_array = array();
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Team Codez")
						   ->setLastModifiedBy("Team Codez")
						   ->setTitle("Office 2007 XLSX Test Document")
						   ->setSubject("Office 2007 XLSX Test Document")
						   ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
						   ->setKeywords("office 2007 openxml php")
						   ->setCategory("Test result file");
			
			if(electricity_bill_sizeof(__LINE__,__FILE__,$bill_array)) {
				$objPHPExcel->getActiveSheet()->setTitle("EPR Uploder Format");
				$objPHPExcel->createSheet();
				$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'ENTITY')
								->setCellValue('B1', 'GLOBAL_SITE_ID')
								->setCellValue('C1', 'Consumer_Number')
								->setCellValue('D1', 'METER_NUMBER')
								->setCellValue('E1', 'EB_BILL_NO')
								->setCellValue('F1', 'BILL_RECEIVED_DATE')
								->setCellValue('G1', 'BILL_ISSUE_DATE')
								->setCellValue('H1', 'BILL_DUE_DATE')
								->setCellValue('I1', 'EB_START_DATE')
								->setCellValue('J1', 'EB_END_DATE')
								->setCellValue('K1', 'OPENING_METER_READING')
								->setCellValue('L1', 'CLOSING_METER_READING')
								->setCellValue('M1', 'Multiplication_Factor')
								->setCellValue('N1', 'UNIT_CONSUMED')
								->setCellValue('O1', 'UNIT_RATE')
								->setCellValue('P1', 'CURRENT_ENERGY_CHARGES')
								->setCellValue('Q1', 'METER_RENT')
								->setCellValue('R1', 'FIXED_CHARGES')
								->setCellValue('S1', 'ADMIN_CHARGES')
								->setCellValue('T1', 'CESS')
								->setCellValue('U1', 'BASIC_EB_CHARGES')
								->setCellValue('V1', 'FUEL_SURCHARGE')
								->setCellValue('W1', 'CUSTOMER_CC_CHARGES')
								->setCellValue('X1', 'ENERGY_DUTY_ED_CHARGES')
								->setCellValue('Y1', 'GOVT_TAX_OTHER_CHARGES')
								->setCellValue('Z1', 'ADD_CHARGES_BY_EB_BOARD')
								->setCellValue('AA1', 'MUNICIPALITY_CHARGES')
								->setCellValue('AB1', 'OTHER_CHARGES')
								->setCellValue('AC1', 'ARREARS_START_DATE')
								->setCellValue('AD1', 'ARREARS_END_DATE')
								->setCellValue('AE1', 'ARREARS_AMT')
								->setCellValue('AF1', 'Disconnection_Date')
								->setCellValue('AG1', 'RE_DIS_CONNECTION_CHARGES')
								->setCellValue('AH1', 'LATE_PAYMENT_CHARGES')
								->setCellValue('AI1', 'PENALTY_OTHER_LATE_PAYMENT')
								->setCellValue('AJ1', 'SECURITY_DEPOSIT_PAID')
								->setCellValue('AK1', 'SECURITY_DEPOSIT_REFUND')
								->setCellValue('AL1', 'INTEREST_ON_SECURITY_DEPOSIT')
								->setCellValue('AM1', 'REBATE_EARLY_PAYMENT')
								->setCellValue('AN1', 'ADVANCE_PAID_AMOUNT')
								->setCellValue('AO1', 'ADVANCE_RECEIVED_AMOUNT')
								->setCellValue('AP1', 'PAYMENT MODE')
								->setCellValue('AQ1', 'DD_CHARGES')
								->setCellValue('AR1', 'REMARKS')	
								->setCellValue('AS1', 'SPECIAL_CASE')
								->setCellValue('AT1', 'AGENCY')
								->setCellValue('AU1', 'INFAVOUR OF')
								->setCellValue('AV1', 'EXPENSE_PERIOD')
								->setCellValue('AW1', 'APPROVED_EB_BILL_AMOUNT')
								->setCellValue('AX1', 'BILL_TYPE ( Regular, Avg, Reconcile)')
								->setCellValue('AY1', 'ADVANCE_PAID for prepaid meter case')
								->setCellValue('AZ1', 'SDO Code');
								if(1){
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA1', 'SECURITY_DEPOSIT')
									->setCellValue('BB1', 'SECURITY_DEPOSIT_REMARK')
									->setCellValue('BC1', 'TCS')
									->setCellValue('BD1', 'IndorCom_Credit')
									->setCellValue('BE1', 'Gross bill amount')
									->setCellValue('BF1', 'Last Paid Amount')
									->setCellValue('BG1', 'Last Payment Date')
									->setCellValue('BH1', 'Beneficiary Name')
									->setCellValue('BI1', 'Bank Name')
									->setCellValue('BJ1', 'IFSC') 
									->setCellValue('BK1', 'Beneficiary A/c No'); 
								}
				$bill_counter = 2;	
				$bill_loader_counter = 2;
				foreach($bill_array as $key => $row){
					$ADVANCE_RECEIVED_AMOUNT = '';
					$remark_bill_base = $row['fld_bill_base'];
					$approve_amount = $row['fld_payment_after_due_date'];
					$difference_approve_amount = $row['fld_other_charges'];
					$fld_agency = $row['fld_short_form'];
					$fld_day_of_bill = $govt_tax_other_charges = 0;
					$fld_sd_amt_required = $row['fld_security_deposit_amount'];
					$current_lpsc = $row['fld_late_payment_charge'];
					$excess_demand_penalty = $row['fld_capacitor_charges'];
					$total_eb_charges = $row['fld_eb_duty'];
					$fld_infavour_of = $row['fld_bill_sub_division'];
					$bill_type = $row['fld_bill_base'];
					$payment_mode = 'BNP';
					$special_case = '';
					$fld_rebate = $row['fld_rebate'];
					$fld_security_deposit_remark = $row['fld_security_deposit_remark'];
					#For Kerala 10
					if($row['fld_circle_id'] == 10){
						$excess_demand_penalty = $row['fld_late_payment_charge'];
						$current_lpsc = $row['fld_capacitor_charges'];
						$govt_tax_other_charges = $row['fld_tax'];
						$ADVANCE_RECEIVED_AMOUNT = $row['fld_arrears'] - $row['fld_total_arrears']; 
						$ADVANCE_RECEIVED_AMOUNT = round($ADVANCE_RECEIVED_AMOUNT,2) * -1;
					}
					if($fld_security_deposit_remark == ''){
						if($row['fld_security_deposit_amount'] == 0){
							$fld_security_deposit_remark = 'Zero SD amount As per Bill';
						}else if($row['fld_security_deposit_amount'] == ''){
							$fld_security_deposit_remark = 'SD not print in Bill';
						}else{
							$fld_security_deposit_remark = 'SD uploaded';
						}
					}
					if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
						$past_reading_date = "";
					}else{
						$past_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_past_reading_date']);
					}
						
					if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
						$present_reading_date = "";
					}else{
						$present_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_present_reading_date']);
					}
						
					if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
						$fld_generated_date = "";
					}else{
						$fld_generated_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_generated_date']);
					}
						
					if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
						$fld_due_date = "";
					}else{
						$fld_due_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_due_date']);
					}
					if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
						$fld_process_date = "";
					}else{
						$fld_process_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_process_date']);
					}
					if($row['fld_last_amount_paid_date'] == "" || $row['fld_last_amount_paid_date'] == "0000-00-00" || $row['fld_last_amount_paid_date'] == "0000-00-00 00:00:00"){
						$fld_last_amount_paid_date = "";
					}else{
						$fld_last_amount_paid_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_last_amount_paid_date']);
					}
	
					if($row['fld_is_meter_changed'] == 0){
						$fld_is_meter_changed = "No";
					}else{
						$fld_is_meter_changed = "Yes";
					}
					$bill_month = electricity_bill_ucwords(__LINE__,__FILE__,electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_month'],0,3))."-".electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_year'],2,2) ;
					$disconnection_date = date('j-M-Y', strtotime($fld_due_date. ' + 15 days'));
					$eb_rate = 0;
					if($row['unit_consumed'] != 0){
						$eb_rate = electricity_bill_string_replace(__LINE__,__FILE__,',','',number_format(($row['fld_total_current_amount']/$row['unit_consumed']),4));
					}
					//$fld_ca_no = $row['fld_ca_no'];
					//if($row['fld_discom_id'] == '90'){
						$fld_ca_no = $row['fld_consumer_id'];
					//}
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A'.$bill_counter, $row['fld_entity_name'])
								->setCellValue('B'.$bill_counter, $row['fld_organizationsite_id'])
								->setCellValueExplicit('C'.$bill_counter, $fld_ca_no,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('D'.$bill_counter, $row['fld_meter_no'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('E'.$bill_counter, $row['fld_discombill_no'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('F'.$bill_counter, $fld_process_date)
								->setCellValue('G'.$bill_counter, $fld_generated_date)
								->setCellValue('H'.$bill_counter, $fld_due_date)
								->setCellValue('I'.$bill_counter, $past_reading_date)
								->setCellValue('J'.$bill_counter, $present_reading_date)
								->setCellValue('K'.$bill_counter, $row['fld_previous_reading'])
								->setCellValue('L'.$bill_counter, $row['fld_meter_current_reading'])
								->setCellValueExplicit('M'.$bill_counter, $row['fld_mf'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('N'.$bill_counter, $row['unit_consumed'])
								->setCellValueExplicit('O'.$bill_counter, $eb_rate,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('P'.$bill_counter, 0)
								->setCellValue('Q'.$bill_counter, ($usercircle == 6) ? round($row['fld_meter_charges']) : $row['fld_meter_charges'])
								->setCellValue('R'.$bill_counter, ($usercircle == 6) ? round($row['fld_fix_charges']) : $row['fld_fix_charges'])
								->setCellValue('S'.$bill_counter, $row['fld_green_cess_or_admin_ch'])
								->setCellValue('T'.$bill_counter, $row['fld_energy_cess'])
								->setCellValue('U'.$bill_counter, ($usercircle == 6) ? round($row['fld_energy_charges']) : $row['fld_energy_charges'])
								->setCellValue('V'.$bill_counter, ($usercircle == 6) ? round($row['fld_fuel_charges']) : $row['fld_fuel_charges'])
								->setCellValue('W'.$bill_counter, 0)
								->setCellValue('X'.$bill_counter, ($usercircle == 6) ? round($total_eb_charges) : $total_eb_charges)
								->setCellValue('Y'.$bill_counter, ($usercircle == 6) ? round($govt_tax_other_charges) : $govt_tax_other_charges)
								->setCellValue('Z'.$bill_counter, 0)
								->setCellValue('AA'.$bill_counter, 0)
								->setCellValue('AB'.$bill_counter, ($usercircle == 6) ? round($difference_approve_amount) : $difference_approve_amount)
								->setCellValue('AC'.$bill_counter, '')
								->setCellValue('AD'.$bill_counter, '')
								->setCellValue('AE'.$bill_counter, ($usercircle == 6) ? round($row['fld_arrears']) : $row['fld_arrears'])
								->setCellValue('AF'.$bill_counter, $disconnection_date)
								->setCellValue('AG'.$bill_counter, 0)
								->setCellValue('AH'.$bill_counter, ($usercircle == 6) ? round($current_lpsc) : $current_lpsc)
								->setCellValue('AI'.$bill_counter, ($usercircle == 6) ? round($excess_demand_penalty) : $excess_demand_penalty)
								->setCellValue('AJ'.$bill_counter, $row['fld_security_deposit_paid'])
								->setCellValue('AK'.$bill_counter, $row['fld_security_deposit_refund'])
								->setCellValue('AL'.$bill_counter, $row['fld_interestsecurity_deposit_amount'])
								->setCellValue('AM'.$bill_counter, ($usercircle == 6) ? round($fld_rebate) : abs($fld_rebate))
								->setCellValue('AN'.$bill_counter, 0)
								->setCellValue('AO'.$bill_counter, $ADVANCE_RECEIVED_AMOUNT)
								->setCellValue('AP'.$bill_counter, $payment_mode)
								->setCellValue('AQ'.$bill_counter, 0)
								->setCellValue('AR'.$bill_counter, $remark_bill_base)
								->setCellValue('AS'.$bill_counter, $special_case)
								->setCellValue('AT'.$bill_counter, $fld_agency)
								->setCellValue('AU'.$bill_counter, $fld_infavour_of)
								->setCellValue('AV'.$bill_counter, $fld_day_of_bill)
								->setCellValue('AW'.$bill_counter, $approve_amount)
								->setCellValue('AX'.$bill_counter, $bill_type)
								->setCellValue('AY'.$bill_counter, 0)
								->setCellValue('AZ'.$bill_counter, $row['fld_sdo_code']);
								//if ($usercircle == 12) {	
								if(1){
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA'.$bill_counter, $row['fld_security_deposit_amount']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB'.$bill_counter, $fld_security_deposit_remark);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC'.$bill_counter, $row['fld_tax_on_sale']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD'.$bill_counter, $row['fld_indorcom_credit']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BE'.$bill_counter, $row['fld_total_current_amount']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF'.$bill_counter, $row['fld_last_amount_paid']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BG'.$bill_counter, $fld_last_amount_paid_date);
										
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BH'.$bill_counter, $row['fld_beneficiary_name']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BI'.$bill_counter, $row['fld_bank_name']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BJ'.$bill_counter, $row['fld_IFSC_code']);
									$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK'.$bill_counter, $row['fld_beneficiary_ac_no']);
								}
									
					$bill_counter++;
				}
				
				$objPHPExcel->setActiveSheetIndex(1)->setTitle("Site Master Format");
				$objPHPExcel->setActiveSheetIndex(1)
								->setCellValue('A1', 'Sr no. ')
								->setCellValue('B1', 'Entity ')
								->setCellValue('C1', 'EPR No. ')
								->setCellValue('D1', 'SAP ID ')
								->setCellValue('E1', 'Site ID ')
								->setCellValue('F1', 'Globle ID ')
								->setCellValue('G1', 'Site Name ')
								->setCellValue('H1', 'Cluster ')
								->setCellValue('I1', 'Dist. ')
								->setCellValue('J1', 'Sanction Load (KW) ')
								->setCellValue('K1', 'No of BTS ')
								->setCellValue('L1', 'EB Cycle ')
								->setCellValue('M1', 'O&M Agency ')
								->setCellValue('N1', 'Old Consumer No. ')
								->setCellValue('O1', 'New Consumer No. ')
								->setCellValue('P1', 'K no. ')
								->setCellValue('Q1', 'Meter No ')
								->setCellValue('R1', 'Type of Supply (Phase) ')
								->setCellValue('S1', 'City Classification(Rural/Urban) ')
								->setCellValue('T1', 'Period Start ')
								->setCellValue('U1', 'Period End ')
								->setCellValue('V1', 'EB Bill Date ')
								->setCellValue('W1', 'Due Date ')
								->setCellValue('X1', 'Vendor DD Request Date ')
								->setCellValue('Y1', 'Disconnection Date ')
								->setCellValue('Z1', 'Arrears Starting Date ')
								->setCellValue('AA1', 'Arrears End Date ')
								->setCellValue('AB1', 'EB Reading Start ')
								->setCellValue('AC1', 'EB Reading End ')
								->setCellValue('AD1', 'Unit Multiplying Factor (M.F.) ')
								->setCellValue('AE1', 'No. Units ')
								->setCellValue('AF1', 'Arrears No. Units ')
								->setCellValue('AG1', 'Days ')
								->setCellValue('AH1', 'EB Rate ')
								->setCellValue('AI1', 'EB Bill type (Regular/Avg) ')
								->setCellValue('AJ1', 'Energy Charges ')
								->setCellValue('AK1', 'Fixed Charge ')
								->setCellValue('AL1', 'Meter Rent ')
								->setCellValue('AM1', 'Fuel S/C ')
								->setCellValue('AN1', 'Shunt Capacitor Charges ')
								->setCellValue('AO1', 'Eletricity Duty(40 Paisa) ')
								->setCellValue('AP1', 'WCC (10 Paisa) ')
								->setCellValue('AQ1', 'UC ( 15 Paisa) ')
								->setCellValue('AR1', 'Others negam Dues ')	
								->setCellValue('AS1', 'Arrear LPC ')
								->setCellValue('AT1', 'Arrear Amt. if any ')
								->setCellValue('AU1', 'Adv. Paid ')
								->setCellValue('AV1', 'Interest Received ')
								->setCellValue('AW1', 'Rebate(-) Voltage ')
								->setCellValue('AX1', 'GST ')
								->setCellValue('AY1', 'Total Amt.( Before Due Date) ')
								->setCellValue('AZ1', 'Late Payment Penalty ')
								->setCellValue('BA1', 'Total Amt.( After Due Date) ')
								->setCellValue('BB1', 'Penalty ')
								->setCellValue('BC1', 'Request DD Amount')
								->setCellValue('BD1', 'DD in Favor of ')
								->setCellValue('BE1', 'Location ')
								->setCellValue('BF1', 'Branch Code No. ')
								->setCellValue('BG1', 'If DD, Drawn on which Bank ')
								->setCellValue('BH1', 'DISCOM ')
								->setCellValue('BI1', 'Remarks ')
								->setCellValue('BJ1', 'Month ')
								->setCellValue('BK1', 'EPR No. ')
								->setCellValue('BL1', 'IOM Date ')
								->setCellValue('BM1', 'Security Amount ')
								->setCellValue('BN1', 'DD No. ')
								->setCellValue('BO1', 'DD Amount ')
								->setCellValue('BP1', 'DD Date ')
								->setCellValue('BQ1', 'Difference of TDS deducted from Interest on security ')
								->setCellValue('BR1', 'SMALL COIN ')
								->setCellValue('BS1', 'Prompt Payment Incentive ')
								->setCellValue('BT1', 'Account No. ')
								->setCellValue('BU1', 'Average EB Hr.');
								if($usercircle == 6){
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BU1', 'Power Factor ')
													->setCellValue('BV1', 'Start Reading(KVAH) ')
													->setCellValue('BW1', 'End Reading (KVAH) ')
													->setCellValue('BX1', 'Unit Difference(KVAH) ')
													->setCellValue('BY1', 'Average EB Hr.');
								}
				$bill_counter = 2;	
				$bill_loader_counter = 2;
				foreach($bill_array as $key => $row){
					if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
						$past_reading_date = "";
					}else{
						$past_reading_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_past_reading_date']);
					}
						
					if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
						$present_reading_date = "";
					}else{
						$present_reading_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_present_reading_date']);
					}
						
					if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
						$fld_generated_date = "";
					}else{
						$fld_generated_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_generated_date']);
					}
						
					if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
						$fld_due_date = "";
					}else{
						$fld_due_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_due_date']);
					}
					if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
						$fld_process_date = "";
					}else{
						$fld_process_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_process_date']);
					}
					if($row['fld_last_amount_paid_date'] == "" || $row['fld_last_amount_paid_date'] == "0000-00-00" || $row['fld_last_amount_paid_date'] == "0000-00-00 00:00:00"){
						$fld_last_amount_paid_date = "";
					}else{
						$fld_last_amount_paid_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_last_amount_paid_date']);
					}
	
					if($row['fld_is_meter_changed'] == 0){
						$fld_is_meter_changed = "No";
					}else{
						$fld_is_meter_changed = "Yes";
					}
					$bill_month = electricity_bill_ucwords(__LINE__,__FILE__,electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_month'],0,3))."-".electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_year'],2,2) ;
					$disconnection_date = date('j-m-Y', strtotime($fld_due_date. ' + 15 days'));
					$eb_rate = 0;
					if($row['unit_consumed'] != 0){
						$eb_rate = number_format(($row['fld_total_current_amount']/$row['unit_consumed']),4);
					}
					$fld_infavour_of = $row['fld_bill_sub_division'];
					#New added
					$fld_service_no = $row['fld_service_no'];
					$fld_consumer_id = $row['fld_consumer_id'];
					$meter_no_xl=$row['fld_meter_no'];
					if($row['fld_discom_id'] == '90'){
						$fld_service_no = $row['fld_consumer_id'];
						$fld_consumer_id = $row['fld_service_no'];
					}
					$supply_type = $row['fld_phase'];
					$fld_payment_after_due_date_sm = $row['fld_payment_after_due_date'];
					if($row['fld_discom_id'] == 103 && ($past_reading_date == '' || $present_reading_date == '')){
						$row['fld_day_of_bill'] = 0;
					}
					
					$objPHPExcel->setActiveSheetIndex(1)
								->setCellValue('A'.$bill_counter, $bill_counter - 1)
								->setCellValueExplicit('B'.$bill_counter, $row['fld_entity_name'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('C'.$bill_counter, $row['fld_erp_id'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('D'.$bill_counter, $row['fld_sap_id'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('E'.$bill_counter, $row['fld_global_id'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('F'.$bill_counter, $row['fld_organizationsite_id'])
								->setCellValueExplicit('G'.$bill_counter, $row['fld_site_name'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('H'.$bill_counter, $row['fld_cluster'])
								->setCellValueExplicit('I'.$bill_counter, $row['fld_payable_location'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('J'.$bill_counter, $row['fld_meter_load'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('K'.$bill_counter, $row['fld_no_of_bts'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('L'.$bill_counter, $row['fld_eb_cycle'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('M'.$bill_counter, $row['fld_agency'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('N'.$bill_counter, $row['fld_ca_no'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('O'.$bill_counter, $fld_service_no,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('P'.$bill_counter, $fld_consumer_id,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('Q'.$bill_counter, $meter_no_xl,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('R'.$bill_counter, $supply_type,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('S'.$bill_counter, $row['fld_area_type'])
								->setCellValueExplicit('T'.$bill_counter, $past_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('U'.$bill_counter, $present_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('V'.$bill_counter, $fld_generated_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('W'.$bill_counter, $fld_due_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('X'.$bill_counter, $fld_process_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValueExplicit('Y'.$bill_counter, $disconnection_date,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('Z'.$bill_counter, '')
								->setCellValue('AA'.$bill_counter, '')
								->setCellValue('AB'.$bill_counter, $row['fld_previous_reading'])
								->setCellValue('AC'.$bill_counter, $row['fld_meter_current_reading'])
								->setCellValue('AD'.$bill_counter, $row['fld_mf'])
								->setCellValue('AE'.$bill_counter, $row['unit_consumed'])
								->setCellValue('AF'.$bill_counter, '')
								->setCellValue('AG'.$bill_counter, $row['fld_day_of_bill'])
								->setCellValueExplicit('AH'.$bill_counter, $eb_rate,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('AI'.$bill_counter, $row['fld_bill_base'])
									
								->setCellValue('AJ'.$bill_counter, ($usercircle == 6) ? round($row['fld_energy_charges']) : $row['fld_energy_charges'])
								->setCellValue('AK'.$bill_counter, ($usercircle == 6) ? round($row['fld_fix_charges']) : $row['fld_fix_charges'])
								->setCellValue('AL'.$bill_counter, ($usercircle == 6) ? round($row['fld_meter_charges']) : $row['fld_meter_charges'])
								->setCellValue('AM'.$bill_counter, ($usercircle == 6) ? round($row['fld_fuel_charges']) : $row['fld_fuel_charges'])
								->setCellValue('AN'.$bill_counter, ($usercircle == 6) ? round($row['fld_subsidy_load_factor']) : $row['fld_subsidy_load_factor'])
								->setCellValue('AO'.$bill_counter, ($usercircle == 6) ? round($row['fld_eb_duty']) : $row['fld_eb_duty'])
								->setCellValue('AP'.$bill_counter, ($usercircle == 6) ? round($row['fld_water_cess']) : $row['fld_water_cess'])
								->setCellValue('AQ'.$bill_counter, ($usercircle == 6) ? round($row['fld_urban_cess']) : $row['fld_urban_cess'])
								->setCellValue('AR'.$bill_counter, ($usercircle == 6) ? round($row['fld_other_nigam_dues']) : $row['fld_other_nigam_dues'])
								->setCellValue('AS'.$bill_counter, '')
								->setCellValue('AT'.$bill_counter, ($usercircle == 6) ? round($row['fld_arrears']) : $row['fld_arrears'])
								->setCellValue('AU'.$bill_counter, '')
								->setCellValue('AV'.$bill_counter, '')
								->setCellValue('AW'.$bill_counter, ($usercircle == 6) ? round($row['fld_rebate']) : $row['fld_rebate'])
								->setCellValue('AX'.$bill_counter, '')
								->setCellValue('AY'.$bill_counter, ($usercircle == 6) ? round($fld_payment_after_due_date_sm) : $fld_payment_after_due_date_sm)
								->setCellValue('AZ'.$bill_counter, ($usercircle == 6) ? round($row['fld_late_payment_charge']) : $row['fld_late_payment_charge'])
								->setCellValue('BA'.$bill_counter, ($usercircle == 6) ? round($row['fld_gross_total']) : $row['fld_gross_total'])
									
								->setCellValue('BB'.$bill_counter, 0)
								->setCellValue('BC'.$bill_counter, 0)
								->setCellValue('BD'.$bill_counter, $fld_infavour_of)
								->setCellValue('BE'.$bill_counter, '')
								->setCellValue('BF'.$bill_counter, '')
								->setCellValue('BG'.$bill_counter, '')
								->setCellValue('BH'.$bill_counter, $row['fld_short_form'])
								->setCellValue('BI'.$bill_counter, '')
								->setCellValueExplicit('BJ'.$bill_counter, $bill_month,PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('BK'.$bill_counter, '')
								->setCellValue('BL'.$bill_counter, '')
								->setCellValue('BM'.$bill_counter, $row['fld_security_deposit_amount'])
								->setCellValue('BN'.$bill_counter, '')
								->setCellValue('BO'.$bill_counter, '')
								->setCellValue('BP'.$bill_counter, '')
								->setCellValue('BQ'.$bill_counter, $row['fld_tds_deducted_diff'])
								->setCellValue('BR'.$bill_counter, $row['fld_small_coin'])
								->setCellValue('BS'.$bill_counter, $row['fld_prompt_payment_incentive'])
								->setCellValueExplicit('BT'.$bill_counter, $row['fld_service_no'],PHPExcel_Cell_DataType::TYPE_STRING)
								->setCellValue('BU'.$bill_counter, $row['fld_average_eb_hr']);
								if ($usercircle == 6) {
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BU'.$bill_counter, $row['fld_power_factor']);
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BV'.$bill_counter, $row['fld_start_reading_KVAH']);
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BW'.$bill_counter, $row['fld_end_reading_KVAH']);
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BX'.$bill_counter, $row['fld_unit_diff_KVAH']);
									$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BZ'.$bill_counter, $row['fld_average_eb_hr']);
								}
									
					$bill_counter++;
					$file_name_array[] = $row['fld_file_name'];
				}
			}else{
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No Bill  Found') ;
			}
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			if($is_mail_manually != 1){
				header('Content-Type: application/vnd.ms-excel');
				header("Content-Disposition: attachment; filename=$fn.xls");
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');
				 
				// If you're serving to IE over SSL, then the following may be needed
				header ('Expires: Mon, 26 Jul 2020 05:00:00 GMT'); // Date in the past
				header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header ('Pragma: public'); // HTTP/1.0
				
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				
				$objWriter->save('php://output');	
			}else{
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('download/'.$fn.'.xls');	
			}
	}else if ($excel_mpg_epr_format == 1){
			$fn = "MPCG";
			if($is_mail_manually == 1){
				$fn = $file_name;
			}
			$file_name_array = array();
			require_once 'Classes/PHPExcel.php';
			require_once 'Classes/PHPExcel/IOFactory.php';
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Team Codez")
			->setLastModifiedBy("Team Codez")
			->setTitle("Office 2007 XLSX Test Document")
			->setSubject("Office 2007 XLSX Test Document")
			->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory("Test result file");
			if(electricity_bill_sizeof(__LINE__,__FILE__,$bill_array)) {
				$objPHPExcel->getActiveSheet()->setTitle("MPCG Internal & EPR Forma");
				$objPHPExcel->createSheet();
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'Bill Scan Copy Name')
				->setCellValue('B1', 'Invoice No')
				->setCellValue('C1', 'Sr. No.')
				->setCellValue('D1', 'Circle')
				->setCellValue('E1', 'ENTITY')
				->setCellValue('F1', 'Month')
				->setCellValue('G1', 'Global/ISL ID')
				->setCellValue('H1', 'Global ID')
				->setCellValue('I1', 'RA Status')
				->setCellValue('J1', 'Site ID')
				->setCellValue('K1', 'Vendor Code')
				->setCellValue('L1', 'Site Name')
				->setCellValue('M1', 'Cluster')
				->setCellValue('N1', 'O&M Agency')
				->setCellValue('O1', 'Connected Load in KW')
				->setCellValue('P1', 'Maximum Demand')
				->setCellValue('Q1', 'Consumer No/Servise No')
				->setCellValue('R1', 'IVRS No.')
				->setCellValue('S1', 'Online location')
				->setCellValue('T1', 'Non_R-APDRP/R-APDRP')
				->setCellValue('U1', 'Rural/Urban')
				->setCellValue('V1', 'Power_ Start Date')
				->setCellValue('W1', 'Power_ Start Date+1')
				->setCellValue('X1', 'Power_ End Date')
				->setCellValue('Y1', 'No Of Days')
				->setCellValue('Z1', 'Bill For the Month')
				->setCellValue('AA1', 'Date of Bill')
				->setCellValue('AB1', 'Due Date')
				->setCellValue('AC1', 'Bill No.')
				->setCellValue('AD1', 'Starting Meter Reading')
				->setCellValue('AE1', 'Ending Meter Reading')
				->setCellValue('AF1', 'Multiplication_Factor')
				->setCellValue('AG1', 'Power Factor')
				->setCellValue('AH1', 'Units')
				->setCellValue('AI1', 'MWMF Reading')
				->setCellValue('AJ1', 'MWMF Reading Date')
				->setCellValue('AK1', 'Def. Unit')
				->setCellValue('AL1', 'EB Bill Vs MWMF')
				->setCellValue('AM1', 'EB Meter Status')
				->setCellValue('AN1', 'Site Configuration')
				->setCellValue('AO1', 'Current Bill Amount')
				->setCellValue('AP1', 'Current_LPC')
				->setCellValue('AQ1', 'Previous_LPC')
				->setCellValue('AR1', 'Total Late Penalty')
				->setCellValue('AS1', 'Previous Dues')
				->setCellValue('AT1', 'Arrear Start Date')
				->setCellValue('AU1', 'Arrear End Date')
				->setCellValue('AV1', 'Final Amount')
				->setCellValue('AW1', 'Total Printed Amt In Bill')
				->setCellValue('AX1', 'Finance Reimbursable Amount')
				->setCellValue('AY1', 'Diff')
				->setCellValue('AZ1', 'Rebate')
				->setCellValue('BA1', 'Penalty Other then LPC')
				->setCellValue('BB1', 'TCL')
				->setCellValue('BC1', 'GOVT_TAX_OTHER_CHARGES')
				->setCellValue('BD1', 'Reimbursable Amount')
				->setCellValue('BE1', 'Total Paybel Amount')
				->setCellValue('BF1', 'Def')
				->setCellValue('BG1', 'Unit Charges')
				->setCellValue('BH1', 'SD')
				->setCellValue('BI1', 'SD Refund')
				->setCellValue('BJ1', 'Intrest On SD')
				->setCellValue('BK1', 'Printed SD Deposit in Bill')
				->setCellValue('BL1', 'Receipt No.')
				->setCellValue('BM1', 'Division Name')
				->setCellValue('BN1', 'Consumer Name In Bill')
				->setCellValue('BO1', 'DD to be taken in Favour of')
				->setCellValue('BP1', 'DD Payable At - Location')
				->setCellValue('BQ1', 'Payment Mode (JPM /Paytm/ NEFT/DD)')
				->setCellValue('BR1', 'Connection Type')
				->setCellValue('BS1', 'Teriff Plan')
				->setCellValue('BT1', 'Given date to Altius by Agency')
				->setCellValue('BU1', 'Given date to CI by Altius Circle')
				->setCellValue('BV1', 'Given date to Accounts')
				->setCellValue('BW1', 'Request Approval Date & No')
				->setCellValue('BX1', 'DD/Cheque No')
				->setCellValue('BY1', 'DD/Cheque Received date')
				->setCellValue('BZ1', 'DD/Cheque Received date')
				->setCellValue('CA1', 'DD/Cheque Handed over to')
				->setCellValue('CB1', 'DD/Cheque Submission date')
				->setCellValue('CC1', 'Fixed Charge')
				->setCellValue('CD1', 'Fixed Charge per KW')
				->setCellValue('CE1', 'Energy Charges')
				->setCellValue('CF1', 'Per Unit Charges')
				->setCellValue('CG1', 'Meter Rent')
				->setCellValue('CH1', 'Tax & Levy')
				->setCellValue('CI1', '%Energy Charges')
				->setCellValue('CJ1', 'CESS')
				->setCellValue('CK1', 'FCA & VCA Charges')
				->setCellValue('CL1', 'TCS Charge')
				->setCellValue('CM1', 'TCS %')
				->setCellValue('CN1', 'GOVT_TAX_OTHER_CHARGES')
				->setCellValue('CO1', 'Positive Welding /Capacitor/PF Charges')
				->setCellValue('CP1', 'Peenal Charges')
				->setCellValue('CQ1', 'Others')
				->setCellValue('CR1', 'If EB Bill High/Extra Amount added Remarks')
				->setCellValue('CS1', 'Negative Welding /Capacitor/PF Charges')
				->setCellValue('CT1', 'CCB/Other Rebates')
				->setCellValue('CU1', 'Load Factor Rebate')
				->setCellValue('CV1', 'Online Rebate')
				->setCellValue('CW1', 'Total')
				->setCellValue('CX1', 'Def')
				->setCellValue('CY1', 'Countif')
				->setCellValue('CZ1', 'As per Due Date Aging')
				->setCellValue('DA1', 'Bill Due Date to Receiving Date Aging')
				->setCellValue('DB1', 'Paid Status')
				->setCellValue('DC1', 'NFA No')
				->setCellValue('DD1', 'LPC Accepted /Not Accepted')
				->setCellValue('DE1', 'ENTITY')
				->setCellValue('DF1', 'GLOBAL_SITE_ID')
				->setCellValue('DG1', 'Consumer_Number')
				->setCellValue('DH1', 'METER_NUMBER')
				->setCellValue('DI1', 'EB_BILL_NO')
				->setCellValue('DJ1', 'BILL_RECEIVED_DATE')
				->setCellValue('DK1', 'BILL_ISSUE_DATE')
				->setCellValue('DL1', 'BILL_DUE_DATE')
				->setCellValue('DM1', 'EB_START_DATE')
				->setCellValue('DN1', 'EB_END_DATE')
				->setCellValue('DO1', 'OPENING_METER_READING')
				->setCellValue('DP1', 'CLOSING_METER_READING')
				->setCellValue('DQ1', 'Multiplication_Factor')
				->setCellValue('DR1', 'UNIT_CONSUMED')
				->setCellValue('DS1', 'UNIT_RATE')
				->setCellValue('DT1', 'CURRENT_ENERGY_CHARGES')
				->setCellValue('DU1', 'METER_RENT')
				->setCellValue('DV1', 'FIXED_CHARGES')
				->setCellValue('DW1', 'ADMIN_CHARGES')
				->setCellValue('DX1', 'CESS')
				->setCellValue('DY1', 'BASIC_EB_CHARGES')
				->setCellValue('DZ1', 'FUEL_SURCHARGE')
				->setCellValue('EA1', 'CUSTOMER_CC_CHARGES')
				->setCellValue('EB1', 'ENERGY_DUTY_ED_CHARGES')
				->setCellValue('EC1', 'GOVT_TAX_OTHER_CHARGES')
				->setCellValue('ED1', 'ADD_CHARGES_BY_EB_BOARD')
				->setCellValue('EE1', 'MUNICIPALITY_CHARGES')
				->setCellValue('EF1', 'OTHER_CHARGES')
				->setCellValue('EG1', 'ARREARS_START_DATE')
				->setCellValue('EH1', 'ARREARS_END_DATE')
				->setCellValue('EI1', 'ARREARS_AMT')
				->setCellValue('EJ1', 'Disconnection_Date')
				->setCellValue('EK1', 'RE_DIS_CONNECTION_CHARGES')
				->setCellValue('EL1', 'LATE_PAYMENT_CHARGES')
				->setCellValue('EM1', 'PENALTY_OTHER_LATE_PAYMENT')
				->setCellValue('EN1', 'SECURITY_DEPOSIT_PAID')
				->setCellValue('EO1', 'SECURITY_DEPOSIT_REFUND')
				->setCellValue('EP1', 'INTEREST_ON_SECURITY_DEPOSIT')
				->setCellValue('EQ1', 'REBATE_EARLY_PAYMENT')
				->setCellValue('ER1', 'ADVANCE_PAID_AMOUNT')
				->setCellValue('ES1', 'ADVANCE_RECEIVED_AMOUNT')
				->setCellValue('ET1', 'PAYMENT MODE')
				->setCellValue('EU1', 'DD_CHARGES')
				->setCellValue('EV1', 'REMARKS')
				->setCellValue('EW1', 'SPECIAL_CASE')
				->setCellValue('EX1', 'AGENCY')
				->setCellValue('EY1', 'INFAVOUR OF')
				->setCellValue('EZ1', 'EXPENSE_PERIOD')
				->setCellValue('FA1', 'APPROVED_EB_BILL_AMOUNT')
				->setCellValue('FB1', 'Component Total')
				->setCellValue('FC1', 'Def.')
				->setCellValue('FD1', 'BILL_TYPE')
				->setCellValue('FE1', 'ADVANCE_PAID')
				->setCellValue('FF1', 'SECURITY_DEPOSIT')
				->setCellValue('FG1', 'SECURITY_DEPOSIT_REMARK')
				->setCellValue('FH1', 'TCS')
				->setCellValue('FI1', 'IndorCom_Credit');
			if($_SERVER['REMOTE_ADDR'] == '103.211.20.242' || $_SERVER['REMOTE_ADDR'] == '49.37.9.31' || $_SERVER['REMOTE_ADDR'] == '116.203.214.120' || $_SERVER['REMOTE_ADDR'] == '49.37.36.188'){
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('FJ1', 'SEB calculation method for MDI')
					->setCellValue('FK1', 'SEB calculation method for No MDI')
					->setCellValue('FL1', 'MDI Penalty');
			}
				$bill_counter = 2;	
				$bill_loader_counter = 2;
				foreach($bill_array as $key => $row){
				if($row['fld_process_date'] != "0000-00-00" && $row['fld_due_date'] != "0000-00-00"){
					$calculate_days = electricity_bill_date_diff(__LINE__,__FILE__,$row['fld_process_date'],$row['fld_due_date']);
				}
				if($calculate_days >= 2){
					$row['fld_late_payment_charge'] = 0;
				}
				if($row['fld_discom_id'] == 28 || $row['fld_discom_id'] == 27 || $row['fld_discom_id'] == 29){
					$row['fld_subsidy_load_factor'] = $row['fld_lock_credit'];
				}
				//========================================
				$countif = 1; 
				$row['count_if'] = $count_if = 1; 
				foreach($bill_array as $value){
					if($value['fld_organizationsite_id'] == $row['fld_organizationsite_id']){
						$row['count_if'] = $count_if++;
					}
				}
				$scan_copy_name=$row['fld_organizationsite_id']."_".date('d_M_y', strtotime($row['fld_generated_date']));
				$static_consumer_no = $row['fld_static_consumer_no'];
				if($row['fld_discom_id'] == '103'){
					$static_consumer_no = $row['fld_consumer_id'];
				}
				$aq_previous_lPC = $row['fld_previous_month_dps'];
				$extra_component_total = 0;
				$total_late_payment_dps = $row['fld_late_payment_charge'] + $row['fld_previous_month_dps'];
				if(abs($row['fld_arrears']) > 10 && abs($row['fld_arrears']) <= 1500){
					$total_late_payment_dps = $row['fld_arrears'] + $row['fld_previous_month_dps'];
					$aq_previous_lPC = $row['fld_arrears'];
					$row['fld_arrears'] = 0;
					$extra_component_total = electricity_bill_round(__LINE__,__FILE__,$aq_previous_lPC,0);
				}
				$date_difference='';
				$past_reading_date_next = '';
				if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
					$past_reading_date = "";
				}else{
					$past_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_past_reading_date']);
					$past_reading_date_next=date('Y-m-d', strtotime('+1 day',strtotime($past_reading_date)));
					if("0000-00-00" != $row['fld_present_reading_date']){
						$date_difference = electricity_bill_date_diff(__LINE__,__FILE__,$past_reading_date_next,$row['fld_present_reading_date']);
					}
				}
							
				if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
					$present_reading_date = "";
				}else{
					$present_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_present_reading_date']);
				}
				$fld_generated_date_plus_one = '';
				if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
					$fld_generated_date = "";
				}else{
					$fld_generated_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_generated_date']);
					$fld_generated_date_plus_one = date('j-M-Y', strtotime($fld_generated_date. ' + 1 days'));
				}
							
				if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
					$fld_due_date = "";
				}else{
					$fld_due_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_due_date']);
				}
				$eb_rate = 0;
				if($row['fld_billed_unit'] != 0){
					$eb_rate = number_format(($row['fld_total_current_amount']/$row['fld_billed_unit']),4);
				}
				if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
					$fld_process_date = "";
				}else{
					$fld_process_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_process_date']);
				}
				$disconnection_date = date('j-M-Y', strtotime($fld_due_date. ' + 15 days'));
				$excess_demand_penalty = abs($row['fld_capacitor_charges']);
				$row['fld_interestsecurity_deposit_amount'] = -1 * abs($row['fld_interestsecurity_deposit_amount']);
				$payment_mode = $row['fld_payment_mode'];
				$bill_type = 'Actual';
				$bill_status_act = 'Actual';
				if($row['fld_previous_reading'] == $row['fld_meter_current_reading']){
					$bill_type = 'Average';
					$bill_status_act = 'Average';
				}
				$tcs_per = '';
				if($row['fld_discom_id'] == 103){
					$chk_fld_other_charges = $row['fld_arrears'];
					$chk_fld_energy_charges = $row['fld_energy_charges'];
					$row['fld_tax_on_sale'] = 0;
					$tcs_per = 0;
					if($row['fld_energy_charges'] != 0){
						$per_chk_other = ($row['fld_arrears'] / $row['fld_energy_charges'])*100;
						$per_chk_other_15 = ($row['fld_arrears'] / $row['fld_energy_charges'])*100;
						if($per_chk_other >= 0.1 && $per_chk_other_15 <= 0.15){
							$row['fld_tax_on_sale'] = $chk_fld_other_charges;
							$tcs_per = ($row['fld_arrears']/$row['fld_energy_charges'])*100;;
						}
					}
				}else{
					$row['fld_subsidy_load_factor'] = '-'.abs($row['fld_subsidy_load_factor']);
				}
				$pos_fld_capacitor_charges = $neg_fld_capacitor_charges = $pos_fld_ccb_calculation = $neg_fld_ccb_calculation = $calculate_days = 0;
				if($row['fld_capacitor_charges'] >= 0){
					$pos_fld_capacitor_charges = $row['fld_capacitor_charges'];
				}else{
					$neg_fld_capacitor_charges = $row['fld_capacitor_charges'];
				}
				if($row['fld_discom_id'] == 103){
					$neg_fld_ccb_calculation =  $row['fld_subsidy'];
					$pos_fld_ccb_calculation =  $row['fld_adjustment_amount'];
					if($row['fld_other_charges'] >= 0){
						$pos_fld_ccb_calculation += $row['fld_other_charges'];
					}else{
						$neg_fld_ccb_calculation += $row['fld_other_charges'];
					}
				}
				if($row['fld_discom_id'] == 29){
					$pos_fld_ccb_calculation = $neg_fld_ccb_calculation = 0;
					$neg_fld_ccb_calculation -=  abs($row['fld_other_charges']);
					if($row['fld_ccb_calculation'] >= 0){
						$pos_fld_ccb_calculation += $row['fld_ccb_calculation'];
					}else{
						$neg_fld_ccb_calculation += $row['fld_ccb_calculation'];
					}
					if(abs($row['fld_other_rebate']) == 20){
						$row['fld_erebate'] = $row['fld_other_rebate'];
					}else{
						$neg_fld_ccb_calculation -= $row['fld_other_rebate'];
					}
				}
				if($row['fld_discom_id'] == 28 || $row['fld_discom_id'] == 27 || $row['fld_discom_id'] == 107){
					$pos_fld_ccb_calculation = $neg_fld_ccb_calculation = 0;
					if($row['fld_ccb_calculation'] >= 0){
						$pos_fld_ccb_calculation = $row['fld_ccb_calculation'];
					}else{
						$neg_fld_ccb_calculation = $row['fld_ccb_calculation'];
					}
					if(abs($row['fld_other_rebate']) == 20){
						$row['fld_erebate'] = $row['fld_other_rebate'];
					}else{
						$neg_fld_ccb_calculation -= $row['fld_other_rebate'];
					}
				}
				$total_rebate = $neg_fld_capacitor_charges + $neg_fld_ccb_calculation + $row['fld_subsidy_load_factor'] + $row['fld_erebate'];
				$unit_charges = $per_unit_charges = 0;
				if($row['fld_billed_unit'] != 0){
					$per_unit_charges = $row['fld_energy_charges'] /  $row['fld_billed_unit'];
					$unit_charges = $row['fld_payment_after_due_date'] /  $row['fld_billed_unit'];
				}
				$excess_demand_penalty = $fld_rebate = 0;
				if($row['fld_capacitor_charges'] <= 0 ){
					$fld_rebate = $row['fld_rebate_early_payment'];
				}else{
					$excess_demand_penalty = $row['fld_rebate_early_payment'];
				}
					
				$reimbursable_amount = $row['fld_energy_charges'] + $row['fld_fuel_charges'] + $row['fld_eb_duty'] + $row['fld_meter_charges'] + $row['fld_fix_charges'] + $row['fld_energy_cess'] + $row['fld_arrears'];
				$fixed_charge_per_kw = $row['fld_fix_charges'] / (float) filter_var( $row['fld_meter_load'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) ;
				$per_energy_charge = 0;
				if($row['fld_energy_charges'] != 0){
					$per_energy_charge = $row['fld_eb_duty'] / $row['fld_energy_charges'];
				}
				$diff = $row['fld_payment_after_due_date'] - abs($total_rebate);
				$cw_total = - $row['fld_erebate'] + $row['fld_subsidy_load_factor'] + $neg_fld_ccb_calculation + $pos_fld_ccb_calculation + $row['fld_penal_charge']+ $pos_fld_capacitor_charges + $row['fld_fuel_charges']+ $row['fld_energy_cess']+ $row['fld_eb_duty']+ $row['fld_meter_charges']+ $row['fld_energy_charges']+ $row['fld_fix_charges']+ $row['fld_interestsecurity_deposit_amount']+ $row['fld_security_deposit_refund']+ $row['fld_security_deposit_paid']+ $row['fld_arrears']+ $total_late_payment_dps+ $neg_fld_capacitor_charges+ $row['fld_other_charges'] + $row['fld_tax_on_sale'];
				if($row['fld_discom_id'] == 103){
					$cw_total = abs($row['fld_erebate']) +
					$row['fld_subsidy_load_factor'] +
					$neg_fld_ccb_calculation +
					electricity_bill_round(__LINE__,__FILE__,$pos_fld_ccb_calculation,2) +
					$row['fld_penal_charge'] +
					$pos_fld_capacitor_charges +
					$row['fld_fuel_charges'] +
					$row['fld_energy_cess'] +
					$row['fld_eb_duty'] +
					$row['fld_meter_charges'] +
					$row['fld_energy_charges'] +
					$row['fld_fix_charges'] +
					$row['fld_interestsecurity_deposit_amount'] +
					$row['fld_security_deposit_refund'] +
					$row['fld_security_deposit_paid'] +
					$row['fld_arrears'] +
					$total_late_payment_dps +
					$neg_fld_capacitor_charges+
					$row['fld_tax_on_sale'];
				}else if($row['fld_discom_id'] == 28 || $row['fld_discom_id'] == 27 || $row['fld_discom_id'] == 29 || $row['fld_discom_id'] == 107){
					$cw_total = - abs($row['fld_erebate']) +
					$row['fld_subsidy_load_factor'] +
					$neg_fld_ccb_calculation +
					electricity_bill_round(__LINE__,__FILE__,$pos_fld_ccb_calculation,2) +
					$row['fld_penal_charge'] +
					$pos_fld_capacitor_charges +
					$row['fld_fuel_charges'] +
					$row['fld_energy_cess'] +
					$row['fld_eb_duty'] +
					$row['fld_meter_charges'] +
					$row['fld_energy_charges'] +
					$row['fld_fix_charges'] +
					$row['fld_interestsecurity_deposit_amount'] +
					$row['fld_security_deposit_refund'] +
					$row['fld_security_deposit_paid'] +
					$row['fld_arrears'] +
					$total_late_payment_dps +
					$neg_fld_capacitor_charges+
					$row['fld_tax_on_sale'];	
				}
				$cx_diff = $row['fld_payment_after_due_date'] - abs($cw_total);
				if($cx_diff < 0){
					//$neg_fld_ccb_calculation = $cx_diff - abs($neg_fld_ccb_calculation);
					$neg_fld_ccb_calculation = abs($neg_fld_ccb_calculation);
				}else if($cx_diff > 0){
					//$pos_fld_ccb_calculation = $cx_diff - abs($pos_fld_ccb_calculation);
					$pos_fld_ccb_calculation = abs($pos_fld_ccb_calculation);
				}
				$az_column = $neg_fld_capacitor_charges + $neg_fld_ccb_calculation + $row['fld_subsidy_load_factor'] - abs($row['fld_erebate']); 
				$penalty_other_then_lpc = $pos_fld_capacitor_charges + $row['fld_penal_charge'] + $pos_fld_ccb_calculation;
				$ax_column = $row['fld_payment_after_due_date'] - $az_column - $penalty_other_then_lpc - $total_late_payment_dps - $row['fld_interestsecurity_deposit_amount'] -$row['fld_security_deposit_paid'] - $row['fld_security_deposit_refund'] - $row['fld_other_charges'] - $row['fld_tax_on_sale'] - abs($cx_diff);
				$excess_fixed_cost = $expected_fixed_cost = $calculated_fixed_cost_diff = 0;
				$load_per_kill_watt = '';
				if($row['fld_discom_id'] == 28 || $row['fld_discom_id'] == 27 || $row['fld_discom_id'] == 103 || $row['fld_discom_id'] == 29 || $row['fld_discom_id'] == 107){
					$ax_column = $row['fld_payment_after_due_date'] - 
						$az_column - 
						abs(electricity_bill_round(__LINE__,__FILE__,$penalty_other_then_lpc,2)) -
						$total_late_payment_dps - 
						$row['fld_interestsecurity_deposit_amount'] - 
						$row['fld_security_deposit_paid'] -
						$row['fld_security_deposit_refund'];
					
					$fld_meter_load = (float) filter_var( $row['fld_meter_load'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
					$fld_maximum_demand = (float) filter_var( $row['fld_maximum_demand'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
					$calculated_connected_load  =  $fld_meter_load + ($fld_meter_load*0.2) ;
					$excess_mdi  = $fld_maximum_demand - $calculated_connected_load;
					//echo "<br> fld_meter_load : ".$fld_meter_load;
					//echo "<br> fld_maximum_demand : ".$fld_maximum_demand;
					//echo "<br> calculated_connected_load : ".$calculated_connected_load;
					//echo "<br> excess_mdi : ".$excess_mdi;
					if($fld_meter_load <= 10){
						$load_per_kill_watt = '';
						if(($row['fld_meter_current_reading'] - $row['fld_previous_reading']) <= 50){
							if($row['fld_area_type'] == 'URBAN'){
								$load_per_kill_watt = 82;
							}else if($row['fld_area_type'] == 'RURAL'){
								$load_per_kill_watt = 67;
							}
						}else{
							if($row['fld_area_type'] == 'URBAN'){
								$load_per_kill_watt = 138;
							}else if($row['fld_area_type'] == 'RURAL'){
								$load_per_kill_watt = 117;
							}
						}
					}else{
						$load_per_kill_watt = '';
						if($row['fld_area_type'] == 'URBAN'){
							$load_per_kill_watt = 296;
						}else if($row['fld_area_type'] == 'RURAL'){
							$load_per_kill_watt = 214;
						}
					}
					if($load_per_kill_watt != ''){
						if($fld_meter_load < $fld_maximum_demand && ($fld_meter_load*1.2) < $fld_maximum_demand){
							$fixed_cost_1 = $calculated_connected_load * $load_per_kill_watt;
							$fixed_cost_2 = $excess_mdi * $load_per_kill_watt * 2;
							$excess_fixed_cost = $fixed_cost_2 + $fixed_cost_1;
							$expected_fixed_cost = $fld_maximum_demand * $load_per_kill_watt;
							$calculated_fixed_cost_diff = $excess_fixed_cost - $expected_fixed_cost;
							if($neg_fld_ccb_calculation >= 100 && $fld_maximum_demand > $fld_meter_load){
								$calculated_fixed_cost_diff = $neg_fld_ccb_calculation;
							}
						}
					}
					//echo "<br> load_per_kill_watt : ".$load_per_kill_watt;
					//echo "<br> fixed_cost_1 : ".$fixed_cost_1;
					//echo "<br> fixed_cost_2 : ".$fixed_cost_2;
					//exit;
				}
				if($row['fld_discom_id'] == 103){
					$calculated_connected_load = $fld_meter_load = check_format($row['fld_meter_load']);
					$fld_maximum_demand = $row['fld_maximum_demand'];
					$excess_mdi  = ceil($fld_maximum_demand - $calculated_connected_load);
					$fld_meter_current_reading = $row['fld_meter_current_reading'];
					$fld_previous_reading = $row['fld_previous_reading'];
					$temp_calculated_unit_consumed = $calculated_unit_consumed = $fld_meter_current_reading - $fld_previous_reading;
					
					$unit_devided_as_per_mdi = $calculated_unit_consumed / ceil($fld_maximum_demand);
					$temp_unit_as_per_connected_load = $unit_as_per_connected_load = $unit_devided_as_per_mdi * $calculated_connected_load;
					$unit_as_per_mdi_load = $unit_devided_as_per_mdi * $excess_mdi;
					
					
					$per_kw_cost = '';
					if($calculated_connected_load <= 5){
						$per_kw_cost = 50;
					}else if($calculated_connected_load > 5 && $calculated_connected_load <= 15){
						$per_kw_cost = 120;
					}else if($calculated_connected_load > 15){
						$per_kw_cost = 200;
					}
					$first_fixed_chg = 0;
					$slot_1_energy_chrg = $slot_2_energy_chrg = $slot_3_energy_chrg = 0;
					$slot_1_energy_chrg_unit = $slot_2_energy_chrg_unit = $slot_3_energy_chrg_unit = 0;
					
					if($per_kw_cost != '' && $fld_maximum_demand > 0 && $fld_maximum_demand > $fld_meter_load){
						$first_fixed_chg = $calculated_connected_load * $per_kw_cost;
						$multiple_fixed_chg = $excess_mdi * ($per_kw_cost * 2);
						$with_mdi_fixed_chg = $multiple_fixed_chg + $first_fixed_chg;
						if($per_kw_cost == 50){
							$slot_1_energy_chrg_unit = 5.85;
							$slot_2_energy_chrg_unit = 6.85;
							$slot_3_energy_chrg_unit = 8.25;
							if($temp_unit_as_per_connected_load < 100){
								$slot_1_energy_chrg = $temp_unit_as_per_connected_load;
							}else{
								$slot_1_energy_chrg = 100;
							}
							$temp_unit_as_per_connected_load = $temp_unit_as_per_connected_load - $slot_1_energy_chrg;
							if($temp_unit_as_per_connected_load > 100 && $temp_unit_as_per_connected_load < 300){
								$slot_2_energy_chrg = $temp_unit_as_per_connected_load;
							}else{
								$slot_2_energy_chrg = 300;
							}
							$temp_unit_as_per_connected_load = $temp_unit_as_per_connected_load - $slot_2_energy_chrg;
							if($temp_unit_as_per_connected_load > 400){
								$slot_3_energy_chrg = $temp_unit_as_per_connected_load;
							}
						}else if($per_kw_cost == 120){
							if($_SERVER['REMOTE_ADDR'] == '103.211.20.242' || $_SERVER['REMOTE_ADDR'] =='49.37.9.31' || $_SERVER['REMOTE_ADDR'] =='116.203.214.120'){
								//echo '<br> temp_calculated_unit_consumed : '.$temp_calculated_unit_consumed;
							}
							
							$slot_1_energy_chrg_unit = 6.85;
							$slot_2_energy_chrg_unit = 8.25;
							if($temp_unit_as_per_connected_load < 400){
								$slot_1_energy_chrg = $temp_unit_as_per_connected_load;
							}else{
								$slot_1_energy_chrg = 400;
							}
							$temp_unit_as_per_connected_load = $temp_unit_as_per_connected_load - $slot_1_energy_chrg;
							if($_SERVER['REMOTE_ADDR'] == '103.211.20.242' || $_SERVER['REMOTE_ADDR'] =='49.37.9.31'){
								//echo '<br> temp_unit_as_per_connected_load : '.$temp_unit_as_per_connected_load;
							}
							if($temp_unit_as_per_connected_load > 400){
								$slot_2_energy_chrg = $temp_unit_as_per_connected_load;
							}
						}
						$unit_devided_as_per_mdi = $calculated_unit_consumed / ceil($fld_maximum_demand);
						$unit_as_per_connected_load = $unit_devided_as_per_mdi * $calculated_connected_load;
						$unit_as_per_mdi_load = $unit_devided_as_per_mdi * $excess_mdi;
					
					
						$energy_charge_1 = $slot_1_energy_chrg * $slot_1_energy_chrg_unit;
						$energy_charge_2 = $slot_2_energy_chrg * $slot_2_energy_chrg_unit;
						$energy_charge_3 = $slot_3_energy_chrg * $slot_3_energy_chrg_unit;
						
						
						$multiple_energy_chg = 	$unit_as_per_mdi_load * ($slot_2_energy_chrg_unit * 2);
						if($slot_3_energy_chrg_unit != 0){
							$multiple_energy_chg = 	$unit_as_per_mdi_load * ($slot_3_energy_chrg_unit * 2);
						}
						$total_energy_chr = $energy_charge_1 + $energy_charge_2 + $energy_charge_3 + $multiple_energy_chg;
					
					
						$excess_fixed_cost = $total_energy_chr + $with_mdi_fixed_chg;
						
						$no_mdi_fixed_cost = $fld_maximum_demand * $per_kw_cost;
						
						
						
						if($per_kw_cost == 50){
							$slot_1_energy_chrg_unit = 5.85;
							$slot_2_energy_chrg_unit = 6.85;
							$slot_3_energy_chrg_unit = 8.25;
							if($temp_calculated_unit_consumed < 100){
								$slot_1_energy_chrg = $temp_calculated_unit_consumed;
							}else{
								$slot_1_energy_chrg = 100;
							}
							$temp_calculated_unit_consumed = $temp_calculated_unit_consumed - $slot_1_energy_chrg;
							if($temp_calculated_unit_consumed > 100 && $temp_calculated_unit_consumed < 400){
								$slot_2_energy_chrg = $temp_calculated_unit_consumed;
							}else{
								$slot_2_energy_chrg = 300;
							}
							$temp_calculated_unit_consumed = $temp_calculated_unit_consumed - $slot_2_energy_chrg;
							if($temp_calculated_unit_consumed > 400){
								$slot_3_energy_chrg = $temp_calculated_unit_consumed;
							}
						}else if($per_kw_cost == 120){
							$slot_1_energy_chrg_unit = 6.85;
							$slot_2_energy_chrg_unit = 8.25;
							if($temp_calculated_unit_consumed < 400){
								$slot_1_energy_chrg = $temp_calculated_unit_consumed;
							}else{
								$slot_1_energy_chrg = 400;
							}
							$temp_calculated_unit_consumed = $temp_calculated_unit_consumed - $slot_1_energy_chrg;
							if($temp_calculated_unit_consumed > 400){
								$slot_2_energy_chrg = $temp_calculated_unit_consumed;
							}
						}
						
						
						
						
						$no_mdi_energy_charge_1 = $slot_1_energy_chrg * $slot_1_energy_chrg_unit;
						$no_mdi_energy_charge_2 = $slot_2_energy_chrg * $slot_2_energy_chrg_unit;
						$no_mdi_energy_charge_3 = $slot_3_energy_chrg * $slot_3_energy_chrg_unit;
						$no_mdi_energy_cost = $no_mdi_energy_charge_1 + $no_mdi_energy_charge_2 + $no_mdi_energy_charge_3;
						$expected_fixed_cost = $no_mdi_energy_cost + $no_mdi_fixed_cost;
						
						
						$calculated_fixed_cost_diff = $excess_fixed_cost - $expected_fixed_cost;
						
						if($_SERVER['REMOTE_ADDR'] == '103.211.20.242' || $_SERVER['REMOTE_ADDR'] =='49.37.9.31' || $_SERVER['REMOTE_ADDR'] =='116.203.214.120' || $_SERVER['REMOTE_ADDR'] == '192.73.236.160'){
							/*
							echo '<br> fld_meter_load : '.$fld_meter_load;
							echo '<br> fld_maximum_demand : '.$fld_maximum_demand;
							echo '<br> excess_mdi : '.$excess_mdi;
							echo '<br> fld_meter_current_reading : '.$fld_meter_current_reading;
							echo '<br> fld_previous_reading : '.$fld_previous_reading;
							
							
							
							echo '<br> calculated_connected_load : '.$calculated_connected_load;
							echo '<br> per_kw_cost : '.$per_kw_cost;							
							echo '<br> first_fixed_chg : '.$first_fixed_chg;
							echo '<br> multiple_fixed_chg : '.$multiple_fixed_chg;
							echo '<br> with_mdi_fixed_chg : '.$with_mdi_fixed_chg;
							
							
							echo '<br> per_kw_cost : '.$per_kw_cost;
							
							echo '<br> slot_1_energy_chrg : '.$slot_1_energy_chrg;
							echo '<br> slot_2_energy_chrg : '.$slot_2_energy_chrg;
							echo '<br> slot_3_energy_chrg : '.$slot_3_energy_chrg;
							echo '<br> multiple_energy_chg : '.$multiple_energy_chg;
							echo '<br> total_energy_chr : '.$total_energy_chr;
							
							
							echo '<br> no_mdi_energy_charge_1 : '.$no_mdi_energy_charge_1;
							echo '<br> no_mdi_energy_charge_2 : '.$no_mdi_energy_charge_2;
							echo '<br> no_mdi_energy_charge_3 : '.$no_mdi_energy_charge_3;
							
							
							echo '<br> slot_1_energy_chrg_unit : '.$slot_1_energy_chrg_unit;
							echo '<br> slot_2_energy_chrg_unit : '.$slot_2_energy_chrg_unit;
							echo '<br> slot_3_energy_chrg_unit : '.$slot_3_energy_chrg_unit;
							
							
							echo '<br> energy_charge_1 : '.$energy_charge_1;
							echo '<br> energy_charge_2 : '.$energy_charge_2;
							echo '<br> energy_charge_3 : '.$energy_charge_3;
							echo '<br> temp_calculated_unit_consumed : '.$temp_calculated_unit_consumed;
							
							exit;
							
							*/
							
						}
					}
					
				}
				$ay_column = round($ax_column,2) - round($reimbursable_amount,2);
				$fld_security_deposit_remark = '';
				if($row['fld_security_deposit_amount'] == 0){
					$fld_security_deposit_remark = 'Zero SD as per bill';
				}else if($row['fld_security_deposit_amount'] == ''){
					$fld_security_deposit_remark = 'SD not print in Bill';
				}else{
					$fld_security_deposit_remark = 'SD Uploaded';
				}
				$fb_col_component_total = electricity_bill_round(__LINE__,__FILE__,($row['fld_meter_charges'] +
					$row['fld_fix_charges'] +
					$row['fld_green_cess_or_admin_ch'] +
					$row['fld_energy_cess'] +
					$row['fld_energy_charges'] +
					$row['fld_fuel_charges'] +
					$row['fld_eb_duty'] +
					$row['fld_other_charges'] +
					$row['fld_arrears'] +
					$row['fld_late_payment_charge'] +
					abs($excess_demand_penalty) +
					$row['fld_security_deposit_paid'] +
					$row['fld_security_deposit_refund'] +
					$row['fld_interestsecurity_deposit_amount'] +
					$fld_rebate+
					$row['fld_tax_on_sale']),0);
					//========================================
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$bill_counter,$scan_copy_name)
						->setCellValue('B'.$bill_counter,'')
						->setCellValue('C'.$bill_counter,$bill_counter-1)
						->setCellValue('D'.$bill_counter,$row['fld_circle_name'])
						->setCellValue('E'.$bill_counter,$row['fld_entity_name'])
						->setCellValue('F'.$bill_counter,date("M-y"))
						->setCellValue('G'.$bill_counter,$row['fld_global_isl_id'])
						->setCellValue('H'.$bill_counter,$row['fld_organizationsite_id'])
						->setCellValue('I'.$bill_counter,$row['fld_ra_status'])
						->setCellValue('J'.$bill_counter,$row['fld_global_id'])
						->setCellValue('K'.$bill_counter,$row['fld_vendor_code'])
						->setCellValue('L'.$bill_counter,$row['fld_site_name'])
						->setCellValue('M'.$bill_counter,$row['fld_cluster'])
						->setCellValue('N'.$bill_counter,$row['fld_o_and_m_agency'])
						->setCellValue('O'.$bill_counter,$row['fld_meter_load'])
						->setCellValue('P'.$bill_counter,$row['fld_maximum_demand'])
						->setCellValue('Q'.$bill_counter,$static_consumer_no)
						->setCellValue('R'.$bill_counter,$row['fld_consumer_id'])
						->setCellValue('S'.$bill_counter,$row['fld_online_location'])
						->setCellValue('T'.$bill_counter,$row['fld_bill_type'])
						->setCellValue('U'.$bill_counter,$row['fld_area_type'])
						->setCellValue('V'.$bill_counter,$past_reading_date)
						->setCellValue('W'.$bill_counter,$past_reading_date_next)
						->setCellValue('X'.$bill_counter,$present_reading_date)
						->setCellValue('Y'.$bill_counter,$date_difference)
						->setCellValue('Z'.$bill_counter,(($date_difference+1)/30))
						->setCellValue('AA'.$bill_counter,$fld_generated_date)
						->setCellValue('AB'.$bill_counter,$fld_due_date)
						->setCellValue('AC'.$bill_counter,$row['fld_discombill_no'])
						->setCellValue('AD'.$bill_counter,$row['fld_previous_reading'])
						->setCellValue('AE'.$bill_counter,$row['fld_meter_current_reading'])
						->setCellValue('AF'.$bill_counter,$row['fld_mf'])
						->setCellValue('AG'.$bill_counter,$row['fld_power_factor'])
						->setCellValue('AH'.$bill_counter,$row['fld_billed_unit'])
						->setCellValue('AI'.$bill_counter,'')
						->setCellValue('AJ'.$bill_counter,'')
						->setCellValue('AK'.$bill_counter,'')
						->setCellValue('AL'.$bill_counter,'')
						->setCellValue('AM'.$bill_counter,$bill_status_act)
						->setCellValue('AN'.$bill_counter,'')
						->setCellValue('AO'.$bill_counter,$row['fld_total_current_amount'])
						->setCellValue('AP'.$bill_counter,$row['fld_late_payment_charge'])
						->setCellValue('AQ'.$bill_counter,$aq_previous_lPC)
						->setCellValue('AR'.$bill_counter,$total_late_payment_dps)
						->setCellValue('AS'.$bill_counter,(abs($row['fld_arrears']) > 10 && abs($row['fld_arrears']) <= 1500) ? 0 : $row['fld_arrears'])
						->setCellValue('AT'.$bill_counter, (abs($row['fld_arrears']) < 1500) ? '' : $past_reading_date)
						->setCellValue('AU'.$bill_counter, (abs($row['fld_arrears']) < 1500) ? '' : $present_reading_date)
						->setCellValue('AV'.$bill_counter,$row['fld_payment_after_due_date'])
						->setCellValue('AW'.$bill_counter,$row['fld_gross_total'])
						->setCellValue('AX'.$bill_counter,$ax_column)
						->setCellValue('AY'.$bill_counter,$ay_column)
						->setCellValue('AZ'.$bill_counter,$az_column)
						->setCellValue('BA'.$bill_counter,abs(electricity_bill_round(__LINE__,__FILE__,$penalty_other_then_lpc,2)))
						->setCellValue('BB'.$bill_counter,'')
						->setCellValue('BC'.$bill_counter,0)
						->setCellValue('BD'.$bill_counter,$reimbursable_amount)
						->setCellValue('BE'.$bill_counter,$row['fld_payment_after_due_date'])
						->setCellValue('BF'.$bill_counter,'')
						->setCellValue('BG'.$bill_counter,$unit_charges)
						->setCellValue('BH'.$bill_counter,$row['fld_security_deposit_paid'])
						->setCellValue('BI'.$bill_counter,$row['fld_security_deposit_refund'])
						->setCellValue('BJ'.$bill_counter,$row['fld_interestsecurity_deposit_amount'])
						->setCellValue('BK'.$bill_counter,$row['fld_security_deposit_amount'])
						->setCellValue('BL'.$bill_counter,'')
						->setCellValue('BM'.$bill_counter,$row['fld_bill_division'])
						->setCellValue('BN'.$bill_counter,$row['fld_name'])
						->setCellValue('BO'.$bill_counter,$row['fld_infavour_of'])
						->setCellValue('BP'.$bill_counter,$row['fld_dd_payable_at_location'])
						->setCellValue('BQ'.$bill_counter,$payment_mode)
						->setCellValue('BR'.$bill_counter,$row['fld_connection_type'])
						->setCellValue('BS'.$bill_counter,$row['fld_tarrif_code'])
						->setCellValue('BT'.$bill_counter,$fld_process_date)
						->setCellValue('BU'.$bill_counter,'')
						->setCellValue('BV'.$bill_counter,'')
						->setCellValue('BW'.$bill_counter,'')
						->setCellValue('BX'.$bill_counter,'')
						->setCellValue('BY'.$bill_counter,'')
						->setCellValue('BZ'.$bill_counter,'')
						->setCellValue('CA'.$bill_counter,'')
						->setCellValue('CB'.$bill_counter,'')
						->setCellValue('CC'.$bill_counter,$row['fld_fix_charges'])
						->setCellValue('CD'.$bill_counter,$fixed_charge_per_kw)
						->setCellValue('CE'.$bill_counter,$row['fld_energy_charges'])
						->setCellValue('CF'.$bill_counter,$per_unit_charges)
						->setCellValue('CG'.$bill_counter,$row['fld_meter_charges'])
						->setCellValue('CH'.$bill_counter,$row['fld_eb_duty'])
						->setCellValue('CI'.$bill_counter,$per_energy_charge)
						->setCellValue('CJ'.$bill_counter,$row['fld_energy_cess'])
						->setCellValue('CK'.$bill_counter,$row['fld_fuel_charges'])
						->setCellValue('CL'.$bill_counter,$row['fld_tax_on_sale'])
						->setCellValue('CM'.$bill_counter,$tcs_per)
						->setCellValue('CN'.$bill_counter,0)
						->setCellValue('CO'.$bill_counter,$pos_fld_capacitor_charges)
						->setCellValue('CP'.$bill_counter,$row['fld_penal_charge'])
						->setCellValue('CQ'.$bill_counter,electricity_bill_round(__LINE__,__FILE__,$pos_fld_ccb_calculation,2))
						->setCellValue('CR'.$bill_counter,'')
						->setCellValue('CS'.$bill_counter,$neg_fld_capacitor_charges)
						->setCellValue('CT'.$bill_counter,'-'.abs($neg_fld_ccb_calculation))
						->setCellValue('CU'.$bill_counter,'-'.abs($row['fld_subsidy_load_factor']))
						->setCellValue('CV'.$bill_counter,'-'.abs($row['fld_erebate']))
						->setCellValue('CW'.$bill_counter,round($cw_total))
						->setCellValue('CX'.$bill_counter,round($cx_diff))
						->setCellValue('CY'.$bill_counter,$row['count_if'])
						->setCellValue('CZ'.$bill_counter,'')
						->setCellValue('DA'.$bill_counter,'')
						->setCellValue('DB'.$bill_counter,'')
						->setCellValue('DC'.$bill_counter,'')
						->setCellValue('DD'.$bill_counter,'')
						->setCellValue('DE'.$bill_counter,$row['fld_entity_name'])
						->setCellValue('DF'.$bill_counter,$row['fld_organizationsite_id'])
						->setCellValue('DG'.$bill_counter,$row['fld_static_consumer_no'])
						->setCellValue('DH'.$bill_counter,$row['fld_static_meter_no'])
						->setCellValue('DI'.$bill_counter,$row['fld_discombill_no'])
						->setCellValue('DJ'.$bill_counter,$fld_generated_date_plus_one)
						->setCellValue('DK'.$bill_counter,$fld_generated_date)
						->setCellValue('DL'.$bill_counter,$fld_due_date)
						->setCellValue('DM'.$bill_counter,$past_reading_date)

						->setCellValue('DN'.$bill_counter,$present_reading_date)
						->setCellValue('DO'.$bill_counter,$row['fld_previous_reading'])
						->setCellValue('DP'.$bill_counter,$row['fld_meter_current_reading'])
						->setCellValueExplicit('DQ'.$bill_counter,$row['fld_mf'],PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValue('DR'.$bill_counter,$row['unit_consumed'])
						->setCellValueExplicit('DS'.$bill_counter,round($eb_rate),PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValue('DT'.$bill_counter,0)
						->setCellValue('DU'.$bill_counter,$row['fld_meter_charges'])
						->setCellValue('DV'.$bill_counter,$row['fld_fix_charges'])
						->setCellValue('DW'.$bill_counter,$row['fld_green_cess_or_admin_ch'])
						->setCellValue('DX'.$bill_counter,$row['fld_energy_cess'])
						->setCellValue('DY'.$bill_counter,$row['fld_energy_charges'])
						->setCellValue('DZ'.$bill_counter,$row['fld_fuel_charges'])
						->setCellValue('EA'.$bill_counter,0)
						->setCellValue('EB'.$bill_counter,$row['fld_eb_duty'])
						->setCellValue('EC'.$bill_counter,0)
						->setCellValue('ED'.$bill_counter,0)
						->setCellValue('EE'.$bill_counter,0)
						->setCellValue('EF'.$bill_counter,$row['fld_other_charges'])
						->setCellValue('EG'.$bill_counter, (abs($row['fld_arrears']) > 1500) ? $past_reading_date : '')
						->setCellValue('EH'.$bill_counter, (abs($row['fld_arrears']) > 1500) ? $present_reading_date : '')
						->setCellValue('EI'.$bill_counter,$row['fld_arrears'])
						->setCellValue('EJ'.$bill_counter,'')
						->setCellValue('EK'.$bill_counter,0)
						->setCellValue('EL'.$bill_counter,($extra_component_total == 0) ? $row['fld_late_payment_charge'] : $extra_component_total )
						->setCellValue('EM'.$bill_counter,abs($excess_demand_penalty))
						->setCellValue('EN'.$bill_counter,$row['fld_security_deposit_paid'])
						->setCellValue('EO'.$bill_counter,$row['fld_security_deposit_refund'])
						->setCellValue('EP'.$bill_counter,$row['fld_interestsecurity_deposit_amount'])
						->setCellValue('EQ'.$bill_counter,$fld_rebate)
						->setCellValue('ER'.$bill_counter,0)
						->setCellValue('ES'.$bill_counter,'')
						->setCellValue('ET'.$bill_counter,$payment_mode)
						->setCellValue('EU'.$bill_counter,0)
						->setCellValue('EV'.$bill_counter,$row['fld_bill_base'])
						->setCellValue('EW'.$bill_counter,'No')
						->setCellValue('EX'.$bill_counter,$row['fld_agency'])
						->setCellValue('EY'.$bill_counter,$row['fld_infavour_of'])
						->setCellValue('EZ'.$bill_counter,0)
						->setCellValue('FA'.$bill_counter,$row['fld_payment_after_due_date'])
						->setCellValue('FB'.$bill_counter,$fb_col_component_total+$extra_component_total )
						->setCellValue('FC'.$bill_counter,($row['fld_payment_after_due_date'] == $fb_col_component_total+$extra_component_total) ? 'TRUE' : 'FALSE')
						->setCellValue('FD'.$bill_counter,$bill_type)
						->setCellValue('FE'.$bill_counter,0)
						->setCellValue('FF'.$bill_counter,$row['fld_security_deposit_amount'])
						->setCellValue('FG'.$bill_counter,$fld_security_deposit_remark)
						->setCellValue('FH'.$bill_counter,$row['fld_tax_on_sale'])
						->setCellValue('FI'.$bill_counter,$row['fld_indorcom_credit']);
					if($_SERVER['REMOTE_ADDR'] == '103.211.20.242' || $_SERVER['REMOTE_ADDR'] == '49.37.9.31' || $_SERVER['REMOTE_ADDR'] == '116.203.214.120'  || $_SERVER['REMOTE_ADDR'] == '49.37.36.188'){
						$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('FJ'.$bill_counter,$excess_fixed_cost)
							->setCellValue('FK'.$bill_counter,$expected_fixed_cost)
							->setCellValue('FL'.$bill_counter,$calculated_fixed_cost_diff);
					}
						$bill_counter++;
						$file_name_array[] = $row['fld_file_name'];
				}	
			}else{
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'No Bill  Found') ;
			}
			$objPHPExcel->setActiveSheetIndex(0);
			if($is_mail_manually != 1){
				header('Content-Type: application/vnd.ms-excel');
				header("Content-Disposition: attachment; filename=$fn.xls");
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');
					 
				// If you're serving to IE over SSL, then the following may be needed
				header ('Expires: Mon, 26 Jul 2020 05:00:00 GMT'); // Date in the past
				header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header ('Pragma: public'); // HTTP/1.0
					
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
					
				$objWriter->save('php://output');	
			}else{
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('download/'.$fn.'.xls');	
			}
	}else if($excel_atc_format == 1){
			$fn = "Altius_Energy_".date('d_m_Y');
			if($is_mail_manually == 1){
				$fn = $file_name;
			}
			$file_name_array = array();
			require_once 'Classes/PHPExcel.php';
			require_once 'Classes/PHPExcel/IOFactory.php';
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Team Codez")
							   ->setLastModifiedBy("Team Codez")
							   ->setTitle("Office 2007 XLSX Test Document")
							   ->setSubject("Office 2007 XLSX Test Document")
							   ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							   ->setKeywords("office 2007 openxml php")
							   ->setCategory("Test result file");
				
				if(electricity_bill_sizeof(__LINE__,__FILE__,$bill_array)) {
					$objPHPExcel->getActiveSheet()->setTitle("EPR Uploder Format");
					$objPHPExcel->createSheet();
					$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A1', 'ENTITY')
									->setCellValue('B1', 'GLOBAL_SITE_ID')
									->setCellValue('C1', 'Consumer_Number')
									->setCellValue('D1', 'METER_NUMBER')
									->setCellValue('E1', 'EB_BILL_NO')
									->setCellValue('F1', 'BILL_RECEIVED_DATE')
									->setCellValue('G1', 'BILL_ISSUE_DATE')
									->setCellValue('H1', 'BILL_DUE_DATE')
									->setCellValue('I1', 'EB_START_DATE')
									->setCellValue('J1', 'EB_END_DATE')
									->setCellValue('K1', 'OPENING_METER_READING')
									->setCellValue('L1', 'CLOSING_METER_READING')
									->setCellValue('M1', 'Multiplication_Factor')
									->setCellValue('N1', 'UNIT_CONSUMED')
									->setCellValue('O1', 'UNIT_RATE')
									->setCellValue('P1', 'CURRENT_ENERGY_CHARGES')
									->setCellValue('Q1', 'METER_RENT')
									->setCellValue('R1', 'FIXED_CHARGES')
									->setCellValue('S1', 'ADMIN_CHARGES')
									->setCellValue('T1', 'CESS')
									->setCellValue('U1', 'BASIC_EB_CHARGES')
									->setCellValue('V1', 'FUEL_SURCHARGE')
									->setCellValue('W1', 'CUSTOMER_CC_CHARGES')
									->setCellValue('X1', 'ENERGY_DUTY_ED_CHARGES')
									->setCellValue('Y1', 'GOVT_TAX_OTHER_CHARGES')
									->setCellValue('Z1', 'ADD_CHARGES_BY_EB_BOARD')
									->setCellValue('AA1', 'MUNICIPALITY_CHARGES')
									->setCellValue('AB1', 'OTHER_CHARGES')
									->setCellValue('AC1', 'ARREARS_START_DATE')
									->setCellValue('AD1', 'ARREARS_END_DATE')
									->setCellValue('AE1', 'ARREARS_AMT')
									->setCellValue('AF1', 'Disconnection_Date')
									->setCellValue('AG1', 'RE_DIS_CONNECTION_CHARGES')
									->setCellValue('AH1', 'LATE_PAYMENT_CHARGES')
									->setCellValue('AI1', 'PENALTY_OTHER_LATE_PAYMENT')
									->setCellValue('AJ1', 'SECURITY_DEPOSIT_PAID')
									->setCellValue('AK1', 'SECURITY_DEPOSIT_REFUND')
									->setCellValue('AL1', 'INTEREST_ON_SECURITY_DEPOSIT')
									->setCellValue('AM1', 'REBATE_EARLY_PAYMENT')
									->setCellValue('AN1', 'ADVANCE_PAID_AMOUNT')
									->setCellValue('AO1', 'ADVANCE_RECEIVED_AMOUNT')
									->setCellValue('AP1', 'PAYMENT MODE')
									->setCellValue('AQ1', 'DD_CHARGES')
									->setCellValue('AR1', 'REMARKS')	
									->setCellValue('AS1', 'SPECIAL_CASE')
									->setCellValue('AT1', 'AGENCY')
									->setCellValue('AU1', 'INFAVOUR OF')
									->setCellValue('AV1', 'EXPENSE_PERIOD')
									->setCellValue('AW1', 'APPROVED_EB_BILL_AMOUNT')
									->setCellValue('AX1', 'BILL_TYPE ( Regular, Avg, Reconcile)')
									->setCellValue('AY1', 'ADVANCE_PAID for prepaid meter case')
									->setCellValue('AZ1', 'SDO Code');
									//if($usercircle == 12){
									if(1){
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA1', 'SECURITY_DEPOSIT')
										->setCellValue('BB1', 'SECURITY_DEPOSIT_REMARK')
										->setCellValue('BC1', 'TCS')

										->setCellValue('BD1', 'IndorCom_Credit');
									}
					$bill_counter = 2;	
					$bill_loader_counter = 2;
					foreach($bill_array as $key => $row){
						$ADVANCE_RECEIVED_AMOUNT = '';
						$remark_bill_base = $row['fld_bill_base'];
						$approve_amount = $row['fld_payment_after_due_date'];
						$difference_approve_amount = $row['fld_other_charges'];
						$fld_agency = $row['fld_short_form'];
						$fld_day_of_bill = $govt_tax_other_charges = 0;
						$fld_sd_amt_required = $row['fld_security_deposit_amount'];
						$current_lpsc = $row['fld_late_payment_charge'];
						$excess_demand_penalty = $row['fld_capacitor_charges'];
						$total_eb_charges = $row['fld_eb_duty'];
						$fld_infavour_of = $row['fld_bill_sub_division'];
						$bill_type = $row['fld_bill_base'];
						if($row['fld_circle_id'] == 12 || $row['fld_circle_id'] == 22){
							$bill_type = $row['fld_customer_care'];
						}
						$payment_mode = 'BNP';
						$special_case = '';
						$fld_rebate = $row['fld_rebate'];
						$fld_security_deposit_remark = $row['fld_security_deposit_remark'];
						#For Kerala 10
						if($row['fld_circle_id'] == 10){
							$excess_demand_penalty = $row['fld_late_payment_charge'];
							$current_lpsc = $row['fld_capacitor_charges'];
							$govt_tax_other_charges = $row['fld_tax'];
							$ADVANCE_RECEIVED_AMOUNT = $row['fld_arrears'] - $row['fld_total_arrears']; 
							$ADVANCE_RECEIVED_AMOUNT = round($ADVANCE_RECEIVED_AMOUNT,2) * -1;
						}
						if($fld_security_deposit_remark == ''){
							if($row['fld_security_deposit_amount'] == 0){
								$fld_security_deposit_remark = 'Zero SD amount As per Bill';
							}else if($row['fld_security_deposit_amount'] == ''){
								$fld_security_deposit_remark = 'SD not print in Bill';
							}else{
								$fld_security_deposit_remark = 'SD uploaded';
							}
						}
						#For Madhya Pradesh
						if($usercircle == 5){
							$excess_demand_penalty = abs($excess_demand_penalty);
							$row['fld_interestsecurity_deposit_amount'] = -1 * abs($row['fld_interestsecurity_deposit_amount']);
							$payment_mode = $row['fld_payment_mode'];
							$bill_type = 'Reconcile';
							if($row['fld_previous_reading'] == $row['fld_meter_current_reading']){
								$bill_type = 'Avg';
							}
							if($row['fld_discom_id'] == 103){
								$chk_fld_other_charges = $row['fld_arrears'];
								$chk_fld_energy_charges = $row['fld_energy_charges'];
								$row['fld_tax_on_sale'] = 0;
								$tcs_per = 0;
								if($row['fld_energy_charges'] != 0){
									$per_chk_other = ($row['fld_arrears'] / $row['fld_energy_charges'])*100;
									$per_chk_other_15 = ($row['fld_arrears'] / $row['fld_energy_charges'])*100;
									if($per_chk_other >= 0.1 && $per_chk_other_15 <= 0.15){
										$row['fld_tax_on_sale'] = $chk_fld_other_charges;
										$tcs_per = ($row['fld_arrears']/$row['fld_energy_charges'])*100;;
									}
								}
							}
							$special_case = 'No';
							$fld_agency = $row['fld_agency'];
							$excess_demand_penalty = $fld_rebate = 0;
							if($row['fld_capacitor_charges'] <= 0 ){
								$fld_rebate = $row['fld_rebate_early_payment'];
							}else{
								$excess_demand_penalty = $row['fld_rebate_early_payment'];
							}
							$fld_ca_no = $row['fld_static_consumer_no'];
							$row['fld_meter_no'] = $row['fld_static_meter_no'];
							$fld_infavour_of = $row['fld_infavour_of'];
						}
						if($row['fld_circle_id'] == 6){
							$total_eb_charges = electricity_bill_string_replace(__LINE__,__FILE__,',','',number_format(($row['fld_eb_duty'] + $row['fld_water_cess'] + $row['fld_urban_cess']),4));
							//if($fld_infavour_of == ''){
								$fld_infavour_of = $row['fld_bill_sub_division'];
							//}
						}
						if($usercircle == 3 || $usercircle == 4){
							$approve_amount = ($row['fld_fix_charges'] + $row['fld_green_cess_or_admin_ch'] + $row['fld_energy_charges'] + $row['fld_fuel_charges'] + $total_eb_charges + $row['fld_other_charges'] + $row['fld_arrears'] + $row['fld_current_month_dps'] + $row['fld_previous_month_dps'] + $row['fld_excess_demand_penalty'] ) - abs($row['fld_interestsecurity_deposit_amount']) - abs($row['fld_rebate']);
							$fld_sd_amt_required = $row['fld_sd_amt_required'];
							$approve_amount =  electricity_bill_round(__LINE__,__FILE__,$approve_amount,0);
							//$difference_approve_amount  = $row['fld_payment_after_due_date'] - $approve_amount;
							//$difference_approve_amount  = 0;
							$remark_bill_base = 'False';
							if(electricity_bill_round(__LINE__,__FILE__,$row['fld_payment_after_due_date'],0) == $approve_amount){
								$remark_bill_base = 'True';
							}
							$govt_tax_other_charges = 0;
							if($row['fld_past_reading_date'] != "" && $row['fld_past_reading_date'] != "0000-00-00"){
								$row['fld_past_reading_date'] = date('Y-m-d', strtotime($row['fld_past_reading_date'] . ' +1 day'));
							}
							//if($row['fld_present_reading_date'] != "" && $row['fld_present_reading_date'] != "0000-00-00"){
							//	$row['fld_present_reading_date'] = date('Y-m-d', strtotime($row['fld_present_reading_date'] . ' +1 day'));
							//}
							if($row['fld_present_reading_date'] != "0000-00-00" && $row['fld_past_reading_date'] != "0000-00-00"){
								//$fld_day_of_bill = $row['fld_day_of_bill'] + 1;
								$fld_day_of_bill = electricity_bill_date_diff(__LINE__,__FILE__,$row['fld_past_reading_date'],$row['fld_present_reading_date']) + 1;
							}
							$fld_agency = $row['fld_agency'];
							$current_lpsc = $row['fld_current_month_dps'] + $row['fld_previous_month_dps'];
							if($row['fld_discom_id'] == 105){
								$current_lpsc = $current_lpsc + $row['fld_late_payment_charge'];
							}
							$excess_demand_penalty = $row['fld_excess_demand_penalty'];
							$fld_infavour_of = $row['fld_infavour_of'];
							$row['fld_interestsecurity_deposit_amount'] = abs($row['fld_interestsecurity_deposit_amount']);
							$row['fld_rebate'] = abs($row['fld_rebate']);
							#Round the value for UP circle
							$row['fld_fix_charges'] = electricity_bill_round(__LINE__,__FILE__,$row['fld_fix_charges'],0);
							$row['fld_green_cess_or_admin_ch'] = electricity_bill_round(__LINE__,__FILE__,$row['fld_green_cess_or_admin_ch'],0);
							$row['fld_energy_charges'] = electricity_bill_round(__LINE__,__FILE__,$row['fld_energy_charges'],0);
							$row['fld_fuel_charges'] = electricity_bill_round(__LINE__,__FILE__,$row['fld_fuel_charges'],0);
							$total_eb_charges = electricity_bill_round(__LINE__,__FILE__,$total_eb_charges,0);
							$govt_tax_other_charges = electricity_bill_round(__LINE__,__FILE__,$govt_tax_other_charges,0);
							$difference_approve_amount = electricity_bill_round(__LINE__,__FILE__,$difference_approve_amount,0);
							$row['fld_arrears'] = electricity_bill_round(__LINE__,__FILE__,$row['fld_arrears'],0);
							$current_lpsc = electricity_bill_round(__LINE__,__FILE__,$current_lpsc,0);
							$excess_demand_penalty = electricity_bill_round(__LINE__,__FILE__,$excess_demand_penalty,0);
							$row['fld_interestsecurity_deposit_amount'] = electricity_bill_round(__LINE__,__FILE__,$row['fld_interestsecurity_deposit_amount'],0);
							$fld_rebate = electricity_bill_round(__LINE__,__FILE__,abs($fld_rebate),0);
						}
						if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
							$past_reading_date = "";
						}else{
							$past_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_past_reading_date']);
						}
							
						if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
							$present_reading_date = "";
						}else{
							$present_reading_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_present_reading_date']);
						}
							
						if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
							$fld_generated_date = "";
						}else{
							$fld_generated_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_generated_date']);
						}
							
						if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
							$fld_due_date = "";
						}else{
							$fld_due_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_due_date']);
						}
						if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
							$fld_process_date = "";
						}else{
							$fld_process_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_process_date']);
						}
						if($row['fld_last_amount_paid_date'] == "" || $row['fld_last_amount_paid_date'] == "0000-00-00" || $row['fld_last_amount_paid_date'] == "0000-00-00 00:00:00"){
							$fld_last_amount_paid_date = "";
						}else{
							$fld_last_amount_paid_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_last_amount_paid_date']);
						}
		
						if($row['fld_is_meter_changed'] == 0){
							$fld_is_meter_changed = "No";
						}else{
							$fld_is_meter_changed = "Yes";
						}
						$bill_month = electricity_bill_ucwords(__LINE__,__FILE__,electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_month'],0,3))."-".electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_year'],2,2) ;
						$disconnection_date = date('j-M-Y', strtotime($fld_due_date. ' + 15 days'));
						$eb_rate = 0;
						if($row['unit_consumed'] != 0){
							$eb_rate = electricity_bill_string_replace(__LINE__,__FILE__,',','',number_format(($row['fld_total_current_amount']/$row['unit_consumed']),4));
						}
						if($usercircle == 3 || $usercircle == 4){
							if($row['fld_disconnaction_date'] == "" || $row['fld_disconnaction_date'] == "0000-00-00"){
								$disconnection_date = "";
							}else{
								$disconnection_date = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_disconnaction_date']);
							}
						}
						$arrear_lpc = 0;
						if($row['fld_discom_id'] == 120 ){
							$arrear_lpc = $row['fld_previous_month_dps'] + $row['fld_current_month_dps'];
							$fld_infavour_of = '';
						}
						if($row['fld_discom_id'] == 126 ){
							$fld_infavour_of = '';
						}
						$advanced_paid_amount = $arrear_amount = 0;
						if($row['fld_discom_id'] == 127){
							if($row['fld_arrears'] < 0 ){
								$advanced_paid_amount = $row['fld_arrears'];
							}else{
								$arrear_amount = $row['fld_arrears'];
							}
							$fld_infavour_of = '';
						}
						$advance_payment = $surcharge = 0;
						if($row['fld_discom_id'] == 125){
							if($row['fld_remarks_possitive_entry2'] == 'Advance Payment' ){
								$advance_payment = $row['fld_other_possitive_entry2'];
							}
							if($row['fld_remarks_possitive_entry2'] == 'Surcharge' ){
								$surcharge = $row['fld_other_possitive_entry2'];
							}
						} 
						//$fld_ca_no = $row['fld_ca_no'];
						//if($row['fld_discom_id'] == '90'){
							$fld_ca_no = $row['fld_consumer_id'];
						//}
						if($usercircle == 5){
							$calculate_days = 0;
							if($row['fld_process_date'] != "0000-00-00" && $row['fld_due_date'] != "0000-00-00"){
								$calculate_days = electricity_bill_date_diff(__LINE__,__FILE__,$row['fld_process_date'],$row['fld_due_date']);
							}
							if($calculate_days >= 2){
								$current_lpsc = 0;
							}
							$fld_process_date = date('j-M-Y', strtotime($fld_generated_date. ' + 1 days'));
						}
						if($row['fld_discom_id'] == 141 ){
							$row['fld_arrears'] = $row['fld_total_arrears'];
						}
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$bill_counter, $row['fld_entity_name'])
									->setCellValue('B'.$bill_counter, $row['fld_organizationsite_id'])
									->setCellValueExplicit('C'.$bill_counter, $fld_ca_no,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('D'.$bill_counter, $row['fld_meter_no'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('E'.$bill_counter, $row['fld_discombill_no'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('F'.$bill_counter, $fld_process_date)
									->setCellValue('G'.$bill_counter, $fld_generated_date)
									->setCellValue('H'.$bill_counter, $fld_due_date)
									->setCellValue('I'.$bill_counter, $past_reading_date)
									->setCellValue('J'.$bill_counter, $present_reading_date)
									->setCellValue('K'.$bill_counter, $row['fld_previous_reading'])
									->setCellValue('L'.$bill_counter, $row['fld_meter_current_reading'])
									->setCellValueExplicit('M'.$bill_counter, $row['fld_mf'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('N'.$bill_counter, $row['unit_consumed'])
									->setCellValueExplicit('O'.$bill_counter, $eb_rate,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('P'.$bill_counter, 0)
									->setCellValue('Q'.$bill_counter, ($usercircle == 6) ? round($row['fld_meter_charges']) : $row['fld_meter_charges'])
									->setCellValue('R'.$bill_counter, ($usercircle == 6) ? round($row['fld_fix_charges']) : $row['fld_fix_charges'])
									->setCellValue('S'.$bill_counter, $row['fld_green_cess_or_admin_ch'])
									->setCellValue('T'.$bill_counter, $row['fld_energy_cess'])
									->setCellValue('U'.$bill_counter, ($usercircle == 6) ? round($row['fld_energy_charges']) : $row['fld_energy_charges'])
									->setCellValue('V'.$bill_counter, ($usercircle == 6) ? round($row['fld_fuel_charges']) : $row['fld_fuel_charges'])
									->setCellValue('W'.$bill_counter, 0)
									->setCellValue('X'.$bill_counter, ($usercircle == 6) ? round($total_eb_charges) : $total_eb_charges)
									->setCellValue('Y'.$bill_counter, ($usercircle == 6) ? round($govt_tax_other_charges) : $govt_tax_other_charges)
									->setCellValue('Z'.$bill_counter, 0)
									->setCellValue('AA'.$bill_counter, 0)
									->setCellValue('AB'.$bill_counter, ($usercircle == 6) ? round($difference_approve_amount) : $difference_approve_amount)
									->setCellValue('AC'.$bill_counter, '')
									->setCellValue('AD'.$bill_counter, '')
									->setCellValue('AE'.$bill_counter, ($usercircle == 6) ? round($row['fld_arrears']) : $row['fld_arrears'])
									->setCellValue('AF'.$bill_counter, $disconnection_date)
									->setCellValue('AG'.$bill_counter, 0)
									->setCellValue('AH'.$bill_counter, ($usercircle == 6) ? round($current_lpsc) : $current_lpsc)
									->setCellValue('AI'.$bill_counter, ($usercircle == 6) ? round($excess_demand_penalty) : $excess_demand_penalty)
									->setCellValue('AJ'.$bill_counter, $row['fld_security_deposit_paid'])
									->setCellValue('AK'.$bill_counter, $row['fld_security_deposit_refund'])
									->setCellValue('AL'.$bill_counter, $row['fld_interestsecurity_deposit_amount'])
									->setCellValue('AM'.$bill_counter, ($usercircle == 6) ? round($fld_rebate) : abs($fld_rebate))
									->setCellValue('AN'.$bill_counter, 0)
									->setCellValue('AO'.$bill_counter, $ADVANCE_RECEIVED_AMOUNT)
									->setCellValue('AP'.$bill_counter, $payment_mode)
									->setCellValue('AQ'.$bill_counter, 0)
									->setCellValue('AR'.$bill_counter, $remark_bill_base)
									->setCellValue('AS'.$bill_counter, $special_case)
									->setCellValue('AT'.$bill_counter, $fld_agency)
									->setCellValue('AU'.$bill_counter, $fld_infavour_of)
									->setCellValue('AV'.$bill_counter, $fld_day_of_bill)
									->setCellValue('AW'.$bill_counter, $approve_amount)
									->setCellValue('AX'.$bill_counter, $bill_type)
									->setCellValue('AY'.$bill_counter, 0)
									->setCellValue('AZ'.$bill_counter, $row['fld_sdo_code']);
									//if ($usercircle == 12) {	
									if(1){
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA'.$bill_counter, $row['fld_security_deposit_amount']);
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB'.$bill_counter, $fld_security_deposit_remark);
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC'.$bill_counter, $row['fld_tax_on_sale']);
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD'.$bill_counter, $row['fld_indorcom_credit']);
									}
									if($row['fld_discom_id'] == 120){
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI'.$bill_counter, $arrear_lpc);
									}
									if($row['fld_discom_id'] == 127){
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE'.$bill_counter, $arrear_amount);
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN'.$bill_counter, $advanced_paid_amount);
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AU'.$bill_counter, '');
									}
									if($row['fld_discom_id'] == 126){ 
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN'.$bill_counter, $row['fld_adjustment_amount']);
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AU'.$bill_counter, '');
									}
									if($row['fld_discom_id'] == 125){
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH'.$bill_counter, $row['fld_current_month_dps']);
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN'.$bill_counter, $advance_payment);
										$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI'.$bill_counter, $surcharge);
									}
										
						$bill_counter++;
					}
					
					$objPHPExcel->setActiveSheetIndex(1)->setTitle("Site Master Format");
					$objPHPExcel->setActiveSheetIndex(1)
									->setCellValue('A1', 'Sr no. ')
									->setCellValue('B1', 'Entity ')
									->setCellValue('C1', 'EPR No. ')
									->setCellValue('D1', 'SAP ID ')
									->setCellValue('E1', 'Site ID ')
									->setCellValue('F1', 'Globle ID ')
									->setCellValue('G1', 'Site Name ')
									->setCellValue('H1', 'Cluster ')
									->setCellValue('I1', 'Dist. ')
									->setCellValue('J1', 'Sanction Load (KW) ')
									->setCellValue('K1', 'No of BTS ')
									->setCellValue('L1', 'EB Cycle ')
									->setCellValue('M1', 'O&M Agency ')
									->setCellValue('N1', 'Old Consumer No. ')
									->setCellValue('O1', 'New Consumer No. ')
									->setCellValue('P1', 'K no. ')
									->setCellValue('Q1', 'Meter No ')
									->setCellValue('R1', 'Type of Supply (Phase) ')
									->setCellValue('S1', 'City Classification(Rural/Urban) ')
									->setCellValue('T1', 'Period Start ')
									->setCellValue('U1', 'Period End ')
									->setCellValue('V1', 'EB Bill Date ')
									->setCellValue('W1', 'Due Date ')
									->setCellValue('X1', 'Vendor DD Request Date ')
									->setCellValue('Y1', 'Disconnection Date ')
									->setCellValue('Z1', 'Arrears Starting Date ')
									->setCellValue('AA1', 'Arrears End Date ')
									->setCellValue('AB1', 'EB Reading Start ')
									->setCellValue('AC1', 'EB Reading End ')
									->setCellValue('AD1', 'Unit Multiplying Factor (M.F.) ')
									->setCellValue('AE1', 'No. Units ')
									->setCellValue('AF1', 'Arrears No. Units ')
									->setCellValue('AG1', 'Days ')
									->setCellValue('AH1', 'EB Rate ')
									->setCellValue('AI1', 'EB Bill type (Regular/Avg) ')
									->setCellValue('AJ1', 'Energy Charges ')
									->setCellValue('AK1', 'Fixed Charge ')
									->setCellValue('AL1', 'Meter Rent ')
									->setCellValue('AM1', 'Fuel S/C ')
									->setCellValue('AN1', 'Shunt Capacitor Charges ')
									->setCellValue('AO1', 'Eletricity Duty(40 Paisa) ')
									->setCellValue('AP1', 'WCC (10 Paisa) ')
									->setCellValue('AQ1', 'UC ( 15 Paisa) ')
									->setCellValue('AR1', 'Others negam Dues ')	
									->setCellValue('AS1', 'Arrear LPC ')
									->setCellValue('AT1', 'Arrear Amt. if any ')
									->setCellValue('AU1', 'Adv. Paid ')
									->setCellValue('AV1', 'Interest Received ')
									->setCellValue('AW1', 'Rebate(-) Voltage ')
									->setCellValue('AX1', 'GST ')
									->setCellValue('AY1', 'Total Amt.( Before Due Date) ')
									->setCellValue('AZ1', 'Late Payment Penalty ')
									->setCellValue('BA1', 'Total Amt.( After Due Date) ')
									->setCellValue('BB1', 'Penalty ')
									->setCellValue('BC1', 'Request DD Amount')
									->setCellValue('BD1', 'DD in Favor of ')
									->setCellValue('BE1', 'Location ')
									->setCellValue('BF1', 'Branch Code No. ')
									->setCellValue('BG1', 'If DD, Drawn on which Bank ')
									->setCellValue('BH1', 'DISCOM ')
									->setCellValue('BI1', 'Remarks ')
									->setCellValue('BJ1', 'Month ')
									->setCellValue('BK1', 'EPR No. ')
									->setCellValue('BL1', 'IOM Date ')
									->setCellValue('BM1', 'Security Amount ')
									->setCellValue('BN1', 'DD No. ')
									->setCellValue('BO1', 'DD Amount ')
									->setCellValue('BP1', 'DD Date ')
									->setCellValue('BQ1', 'Difference of TDS deducted from Interest on security ')
									->setCellValue('BR1', 'SMALL COIN ')
									->setCellValue('BS1', 'Prompt Payment Incentive ')
									->setCellValue('BT1', 'Account No. ');
									if($usercircle == 6){
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BU1', 'Power Factor ')
														->setCellValue('BV1', 'Start Reading(KVAH) ')
														->setCellValue('BW1', 'End Reading (KVAH) ')
														->setCellValue('BX1', 'Unit Difference(KVAH) ');
									}
					$bill_counter = 2;	
					$bill_loader_counter = 2;
					foreach($bill_array as $key => $row){
						if($row['fld_past_reading_date'] == "" || $row['fld_past_reading_date'] == "0000-00-00"){
							$past_reading_date = "";
						}else{
							$past_reading_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_past_reading_date']);
						}
							
						if($row['fld_present_reading_date'] == "" || $row['fld_present_reading_date'] == "0000-00-00"){
							$present_reading_date = "";
						}else{
							$present_reading_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_present_reading_date']);
						}
							
						if($row['fld_generated_date'] == "" || $row['fld_generated_date'] == "0000-00-00"){
							$fld_generated_date = "";
						}else{
							$fld_generated_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_generated_date']);
						}
							
						if($row['fld_due_date'] == "" || $row['fld_due_date'] == "0000-00-00"){
							$fld_due_date = "";
						}else{
							$fld_due_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_due_date']);
						}
						if($row['fld_process_date'] == "" || $row['fld_process_date'] == "0000-00-00"){
							$fld_process_date = "";
						}else{
							$fld_process_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_process_date']);
						}
						if($row['fld_last_amount_paid_date'] == "" || $row['fld_last_amount_paid_date'] == "0000-00-00" || $row['fld_last_amount_paid_date'] == "0000-00-00 00:00:00"){
							$fld_last_amount_paid_date = "";
						}else{
							$fld_last_amount_paid_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_last_amount_paid_date']);
						}
		
						if($row['fld_is_meter_changed'] == 0){
							$fld_is_meter_changed = "No";
						}else{
							$fld_is_meter_changed = "Yes";
						}
						$bill_month = electricity_bill_ucwords(__LINE__,__FILE__,electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_month'],0,3))."-".electricity_bill_substring_two_params(__LINE__,__FILE__,$row['fld_bill_year'],2,2) ;
						$disconnection_date = date('j-m-Y', strtotime($fld_due_date. ' + 15 days'));
						if($usercircle == 3 || $usercircle == 4){
							if($row['fld_disconnaction_date'] == "" || $row['fld_disconnaction_date'] == "0000-00-00"){
								$disconnection_date = "";
							}else{
								$disconnection_date = electricity_bill_date_format_with_formatted_3(__LINE__,__FILE__,$row['fld_disconnaction_date']);
							}
						}
						$eb_rate = 0;
						if($row['unit_consumed'] != 0){
							$eb_rate = number_format(($row['fld_total_current_amount']/$row['unit_consumed']),4);
						}
						$fld_infavour_of = $row['fld_bill_sub_division'];
						#New added
						$fld_service_no = $row['fld_service_no'];
						$fld_consumer_id = $row['fld_consumer_id'];
						$meter_no_xl=$row['fld_meter_no'];
						if($row['fld_discom_id'] == '90'){
							$fld_service_no = $row['fld_consumer_id'];
							$fld_consumer_id = $row['fld_service_no'];
						}
						if($row['fld_circle_id'] == '5'){
							$fld_service_no = $row['fld_static_consumer_no'];
							$meter_no_xl=$row['fld_static_meter_no'];
						}
						if($row['fld_circle_id'] == '6'){
							$supply_type = $row['fld_supply_voltage'];
						}else{
							$supply_type = $row['fld_phase'];
						}
						$fld_payment_after_due_date_sm = $row['fld_payment_after_due_date'];
						if($usercircle == 3 || $usercircle == 4){
							$fld_payment_after_due_date_sm = electricity_bill_round(__LINE__,__FILE__,$row['fld_payment_after_due_date'],0);
							$fld_infavour_of = $row['fld_infavour_of'];
						}
						if($row['fld_discom_id'] == 103 && ($past_reading_date == '' || $present_reading_date == '')){
							$row['fld_day_of_bill'] = 0;
						}
						$arrear_lpc = 0;
						if($row['fld_discom_id'] == 120 ){
							$row['fld_rebate'] = 0;
							$arrear_lpc = $row['fld_previous_month_dps'] + $row['fld_current_month_dps'];
						}
						if($row['fld_discom_id'] == 125 || $row['fld_discom_id'] == 130 ){ 
							$row['fld_rebate'] = 0;
						}
						if($row['fld_circle_id'] == 6){
							$fld_infavour_of = $row['fld_bill_sub_division'];	
						}
						$adv_paid = $arrear_amt_if_any = 0;
						if($row['fld_discom_id'] == 127){
							if($row['fld_arrears'] < 0 ){
								$adv_paid = $row['fld_arrears'];
							}else{
								$arrear_amt_if_any = $row['fld_arrears'];
							}
						}
						if($row['fld_discom_id'] == 141 ){
							$row['fld_arrears'] = $row['fld_total_arrears'];
						}
						$objPHPExcel->setActiveSheetIndex(1)
									->setCellValue('A'.$bill_counter, $bill_counter - 1)
									->setCellValueExplicit('B'.$bill_counter, $row['fld_entity_name'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('C'.$bill_counter, $row['fld_erp_id'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('D'.$bill_counter, $row['fld_sap_id'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('E'.$bill_counter, $row['fld_global_id'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('F'.$bill_counter, $row['fld_organizationsite_id'])
									->setCellValueExplicit('G'.$bill_counter, $row['fld_site_name'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('H'.$bill_counter, $row['fld_cluster'])
									->setCellValueExplicit('I'.$bill_counter, $row['fld_payable_location'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('J'.$bill_counter, $row['fld_meter_load'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('K'.$bill_counter, $row['fld_no_of_bts'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('L'.$bill_counter, $row['fld_eb_cycle'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('M'.$bill_counter, $row['fld_agency'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('N'.$bill_counter, $row['fld_ca_no'],PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('O'.$bill_counter, $fld_service_no,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('P'.$bill_counter, $fld_consumer_id,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('Q'.$bill_counter, $meter_no_xl,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('R'.$bill_counter, $supply_type,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('S'.$bill_counter, $row['fld_area_type'])
									->setCellValueExplicit('T'.$bill_counter, $past_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('U'.$bill_counter, $present_reading_date,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('V'.$bill_counter, $fld_generated_date,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('W'.$bill_counter, $fld_due_date,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('X'.$bill_counter, $fld_process_date,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValueExplicit('Y'.$bill_counter, $disconnection_date,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('Z'.$bill_counter, '')
									->setCellValue('AA'.$bill_counter, '')
									->setCellValue('AB'.$bill_counter, $row['fld_previous_reading'])
									->setCellValue('AC'.$bill_counter, $row['fld_meter_current_reading'])
									->setCellValue('AD'.$bill_counter, $row['fld_mf'])
									->setCellValue('AE'.$bill_counter, $row['unit_consumed'])
									->setCellValue('AF'.$bill_counter, '')
									->setCellValue('AG'.$bill_counter, $row['fld_day_of_bill'])
									->setCellValueExplicit('AH'.$bill_counter, $eb_rate,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('AI'.$bill_counter, $row['fld_bill_base'])
										
									->setCellValue('AJ'.$bill_counter, ($usercircle == 6) ? round($row['fld_energy_charges']) : $row['fld_energy_charges'])
									->setCellValue('AK'.$bill_counter, ($usercircle == 6) ? round($row['fld_fix_charges']) : $row['fld_fix_charges'])
									->setCellValue('AL'.$bill_counter, ($usercircle == 6) ? round($row['fld_meter_charges']) : $row['fld_meter_charges'])
									->setCellValue('AM'.$bill_counter, ($usercircle == 6) ? round($row['fld_fuel_charges']) : $row['fld_fuel_charges'])
									->setCellValue('AN'.$bill_counter, ($usercircle == 6) ? round($row['fld_subsidy_load_factor']) : $row['fld_subsidy_load_factor'])
									->setCellValue('AO'.$bill_counter, ($usercircle == 6) ? round($row['fld_eb_duty']) : $row['fld_eb_duty'])
									->setCellValue('AP'.$bill_counter, ($usercircle == 6) ? round($row['fld_water_cess']) : $row['fld_water_cess'])
									->setCellValue('AQ'.$bill_counter, ($usercircle == 6) ? round($row['fld_urban_cess']) : $row['fld_urban_cess'])
									->setCellValue('AR'.$bill_counter, ($usercircle == 6) ? round($row['fld_other_nigam_dues']) : $row['fld_other_nigam_dues'])
									->setCellValue('AS'.$bill_counter, '')
									->setCellValue('AT'.$bill_counter, ($usercircle == 6) ? round($row['fld_arrears']) : $row['fld_arrears'])
									->setCellValue('AU'.$bill_counter, '')
									->setCellValue('AV'.$bill_counter, '')
									->setCellValue('AW'.$bill_counter, ($usercircle == 6) ? round($row['fld_rebate']) : $row['fld_rebate'])
									->setCellValue('AX'.$bill_counter, '')
									->setCellValue('AY'.$bill_counter, ($usercircle == 6) ? round($fld_payment_after_due_date_sm) : $fld_payment_after_due_date_sm)
									->setCellValue('AZ'.$bill_counter, ($usercircle == 6) ? round($row['fld_late_payment_charge']) : $row['fld_late_payment_charge'])
									->setCellValue('BA'.$bill_counter, ($usercircle == 6) ? round($row['fld_gross_total']) : $row['fld_gross_total'])
										
									->setCellValue('BB'.$bill_counter, 0)
									->setCellValue('BC'.$bill_counter, 0)
									->setCellValue('BD'.$bill_counter, $fld_infavour_of)
									->setCellValue('BE'.$bill_counter, '')
									->setCellValue('BF'.$bill_counter, '')
									->setCellValue('BG'.$bill_counter, '')
									->setCellValue('BH'.$bill_counter, $row['fld_short_form'])
									->setCellValue('BI'.$bill_counter, '')
									->setCellValueExplicit('BJ'.$bill_counter, $bill_month,PHPExcel_Cell_DataType::TYPE_STRING)
									->setCellValue('BK'.$bill_counter, '')
									->setCellValue('BL'.$bill_counter, '')
									->setCellValue('BM'.$bill_counter, $row['fld_security_deposit_amount'])
									->setCellValue('BN'.$bill_counter, '')
									->setCellValue('BO'.$bill_counter, '')
									->setCellValue('BP'.$bill_counter, '')
									->setCellValue('BQ'.$bill_counter, $row['fld_tds_deducted_diff'])
									->setCellValue('BR'.$bill_counter, $row['fld_small_coin'])
									->setCellValue('BS'.$bill_counter, $row['fld_prompt_payment_incentive'])
									->setCellValueExplicit('BT'.$bill_counter, $row['fld_service_no'],PHPExcel_Cell_DataType::TYPE_STRING);
									if ($usercircle == 6) {
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BU'.$bill_counter, $row['fld_power_factor']);
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BV'.$bill_counter, $row['fld_start_reading_KVAH']);
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BW'.$bill_counter, $row['fld_end_reading_KVAH']);
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('BX'.$bill_counter, $row['fld_unit_diff_KVAH']);
									}
									if($row['fld_discom_id'] == 120){
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('AS'.$bill_counter, $arrear_lpc);
									}
									if($row['fld_discom_id'] == 127){
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('AT'.$bill_counter, $arrear_amt_if_any);
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('AU'.$bill_counter, $adv_paid);
									}
									if($row['fld_discom_id'] == 126){ 
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('AU'.$bill_counter, $row['fld_adjustment_amount']);
									}
									if($row['fld_discom_id'] == 125){
										$objPHPExcel->setActiveSheetIndex(1)->setCellValue('AZ'.$bill_counter, $row['fld_current_month_dps']);
									}
										
						$bill_counter++;
						$file_name_array[] = $row['fld_file_name'];
					}
				}else{
					$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', 'No Bill  Found') ;
				}
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$objPHPExcel->setActiveSheetIndex(0);
				if($is_mail_manually != 1){
					header('Content-Type: application/vnd.ms-excel');
					header("Content-Disposition: attachment; filename=$fn.xls");
					header('Cache-Control: max-age=0');
					// If you're serving to IE 9, then the following may be needed
					header('Cache-Control: max-age=1');
					 
					// If you're serving to IE over SSL, then the following may be needed
					header ('Expires: Mon, 26 Jul 2020 05:00:00 GMT'); // Date in the past
					header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
					header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
					header ('Pragma: public'); // HTTP/1.0
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
					
					$objWriter->save('php://output');	
				}else{
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
					$objWriter->save('download/'.$fn.'.xls');	
				}
	}
	$arr_return['bill_counter'] = $bill_counter;
	$arr_return['file_name_array'] = $file_name_array;
	return $arr_return;
}

function electricity_bill_api_bills_generate_excel($line,$file,$api_list_arr,$username,$usercircle,$excel_api_bills_format = 0){
	//electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($api_list_arr,$usercircle,$excel_api_bills_format));

	if($excel_api_bills_format == 1){
		$file_name_array = array();
		$fn = "API_Bills_Excel_".date('d_m_Y');
		require_once 'Classes/PHPExcel.php';
		require_once 'Classes/PHPExcel/IOFactory.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Team Codez")
					 ->setLastModifiedBy("Team Codez")
					 ->setTitle("Office 2007 XLSX Test Document")
					 ->setSubject("Office 2007 XLSX Test Document")
					 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
					 ->setKeywords("office 2007 openxml php")
					 ->setCategory("Test result file");
		if(electricity_bill_sizeof(__LINE__,__FILE__,$api_list_arr)) {
		  	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1','Global_Site_ID')
					->setCellValue('B1','Site Name')
					->setCellValue('C1','Circle')
					->setCellValue('D1','Cluster')
					->setCellValue('E1','Board Name')
					->setCellValue('F1','Consumer_Number')
					->setCellValue('G1','Old_Consumer_Kno')
					->setCellValue('H1','EB_Bill_No')
					->setCellValue('I1','Opco Site Id')
					->setCellValue('J1','Consumer_Name_On_Bill')
					->setCellValue('K1','Consumer_Address')
					->setCellValue('L1','divisionname')
					->setCellValue('M1','subdivisionname')
					->setCellValue('N1','Town District Name')
					->setCellValue('O1','Bill_Issue_Date')
					->setCellValue('P1','Bill_Due_Date')
					->setCellValue('Q1','Bill_Received_Date')
					->setCellValue('R1','EB_Connection_Date')
					->setCellValue('S1','Tariff_Category')
					->setCellValue('T1','Type_Of_Supply_Phase')
					->setCellValue('U1','Sanctioned_EB_Load_in_KW')
					->setCellValue('V1','MDI')
                    			->setCellValue('W1','Security_Deposit')
					->setCellValue('X1','Security_Deposit_Paid')
					->setCellValue('Y1','Security_Deposit_Refund')
					->setCellValue('Z1','Security_Deposit_Remark')
                    			->setCellValue('AA1','Interest_On_Security_Deposit')
					->setCellValue('AB1','Expense_Period')
					->setCellValue('AC1','Meter_Number')
					->setCellValue('AD1','Meter_Status') 
					->setCellValue('AE1','Unit_Rate')
					->setCellValue('AF1','Power_Factor')
					->setCellValue('AG1','Multiplication_Factor_KWH')
					->setCellValue('AH1','EB_Type')
					->setCellValue('AI1','EB_Start_Date')
					->setCellValue('AJ1','Previous_Meter_ReadingDate_KWH')
					->setCellValue('AK1','Current_Reading_Date_KWH')
					->setCellValue('AL1','EB_End_Date')
					->setCellValue('AM1','Arrear_Start_Reading_KWH')
					->setCellValue('AN1','Arrear_End_Reading_KWH')
					->setCellValue('AO1','Arrear_Start_Reading_KVAH')
					->setCellValue('AP1','Arrear_End_Reading_KVAH')
					->setCellValue('AQ1','EB_Start_Reading_KVAH')
					->setCellValue('AR1','EB_End_Reading_KVAH')
					->setCellValue('AS1','No_of_Billed_Days_Current_Period')
					->setCellValue('AT1','Unit_billed') 
					->setCellValue('AU1','Total_Consumption') 
					->setCellValue('AV1','Unit_Consumed_Current') 
					->setCellValue('AW1','EB_Start_Reading_KWH')
					->setCellValue('AX1','EB_End_Reading_KWH')
					->setCellValue('AY1','Meter_Rent')
					->setCellValue('AZ1','Meter_Service_Charges')
					->setCellValue('BA1','Disconnection_Date')
					->setCellValue('BB1','Arrears_Amount')
					->setCellValue('BC1','Arrears_Start_Date')
					->setCellValue('BD1','Arrears_End_Date')
					->setCellValue('BE1','Arrear_Start_Reading')
					->setCellValue('BF1','Arrear_End_Reading')
					->setCellValue('BG1','LPSC_DPS_Arrear_Amt')
					->setCellValue('BH1','Current_Month_DPS_Charges')
					->setCellValue('BI1','Current_Energy_Charges')
					->setCellValue('BJ1','Fixed_Charges')
					->setCellValue('BK1','Energy_Duty')
					->setCellValue('BL1','Excess_Demand_Charges')
					->setCellValue('BM1','Fuel_Surcharge')
					->setCellValue('BN1','Late_Payment_Charges')
					->setCellValue('BO1','Govt_Tax_Other_Charges')
					->setCellValue('BP1','Subsidy_Amt')
					->setCellValue('BQ1','Muncipality_Charges')
					->setCellValue('BR1','Re_Dis_Connection_Charges')
					->setCellValue('BS1','MMC Charge')
					->setCellValue('BT1','Rebate_Early_Payment')
					->setCellValue('BU1','E_Rebate')
					->setCellValue('BV1','Total_Current_Bill')
					->setCellValue('BW1','Payable_Amt_Before_Due_Date')
					->setCellValue('BX1','Payable_Amt_After_Due_Date')
					->setCellValue('BY1','Current_Amt_Payable')
					->setCellValue('BZ1','Opening_Balance')
					->setCellValue('CA1','Payable_Amt_on_Due_Date')
					->setCellValue('CB1','Remarks')
					->setCellValue('CC1','Energy_Duty_ED_Charges')
					->setCellValue('CD1','Sundry_Charges')
					->setCellValue('CE1','MVCA_Charges')
					->setCellValue('CF1','Intermi_Bill_Amt')
					->setCellValue('CG1','HT_Rebate_Amt')
					->setCellValue('CH1','HT_Penalty_Amt')
					->setCellValue('CI1','DTR_Penalty_Amt')
					->setCellValue('CJ1','Voltage_Penalty_Amt')
					->setCellValue('CK1','Voltage_Rebate_Amt') 
					->setCellValue('CL1','TOD_Rebate') 
					->setCellValue('CM1','TOD_Surchage')
					->setCellValue('CN1','VCR_Vigilenc_Penalty')
					->setCellValue('CO1','Power_Factor_Penalty_Amt')
					->setCellValue('CP1','Power_Factor_Rebate_Amt')
					->setCellValue('CQ1','VCA_FSA_Fuel_Surcharge')
					->setCellValue('CR1','Adjustment_Amt')
					->setCellValue('CS1','CCB_Adjustment')
					->setCellValue('CT1','CGST')
					->setCellValue('CU1','SGST')
					->setCellValue('CV1','Cess') 
					->setCellValue('CW1','PPAC_On_Energy_Charges')
					->setCellValue('CX1','PPAC_On_Fixed_Charges')
					->setCellValue('CY1','Electricity_Duty_Adjustment_Amt')
					->setCellValue('CZ1','Electricity_Duty_Arrear')
					->setCellValue('DA1','Energy_Charges_ReEstimated')
					->setCellValue('DB1','Fixed_Charges_Arrear')
					->setCellValue('DC1','FPA_PPA')
					->setCellValue('DD1','FSA_Arrear')
					->setCellValue('DE1','Interest')
					->setCellValue('DF1','Interest Arrear')
					->setCellValue('DG1','Interest_Electricity_Duty')
					->setCellValue('DH1','Pension_Surcharge_DTR')
					->setCellValue('DI1','Pension_Surcharge_On_Energy_Charges')
					->setCellValue('DJ1','Miscellaneous_Charges')
					->setCellValue('DK1','Munciple_Charges_Arrear')
					->setCellValue('DL1','Provision_Bill_Amt')
					->setCellValue('DM1','Provision_Bill_Adjust_Amt')
					->setCellValue('DN1','Round_Off')
					->setCellValue('DO1','Sundry_Allowance')
					->setCellValue('DP1','Tariff_Adjustment')
					->setCellValue('DQ1','Tax_on_Sale')
					->setCellValue('DR1','TCS Surcharge Amount')
					->setCellValue('DS1','TDS_Amt')
					->setCellValue('DT1','EB_Theft_Arrear')
					->setCellValue('DU1','Other_Nigam_Dues')
					->setCellValue('DV1','Overdraw_Penalty')
					->setCellValue('DW1','Transformer_MD_Charge')
					->setCellValue('DX1','True_Up_Charges')
					->setCellValue('DY1','Water_Cess')
					->setCellValue('DZ1','Wheeling_Charges')
					->setCellValue('EA1','Lock_Credit_Units')
					->setCellValue('EB1','Basic_EB_Chargess')
					->setCellValue('EC1','Admin_Charges')
					->setCellValue('ED1','Customer_CC_Charges')
					->setCellValue('EE1','ICST')
					->setCellValue('EF1','IndorCom_Credit')
					->setCellValue('EG1','TCS_Amt')
					->setCellValue('EH1','In_Favour_Of')
					->setCellValue('EI1','Additional_Charges_By_EB_Board')
					->setCellValue('EJ1','Consumer_Category')
					->setCellValue('EK1','Eb Connection Type')
					->setCellValue('EL1','EB_Reconnection_Date')
					->setCellValue('EM1','Region')
					->setCellValue('EN1','Entity')
					->setCellValue('EO1','Payment_Mode')
					->setCellValue('EP1','Is_Exception')
					->setCellValue('EQ1','Remarks_Bill_Type')
					->setCellValue('ER1','Estimated Actual Arrear Units')
					->setCellValue('ES1','Arrears_Bill_Remarks')
					->setCellValue('ET1','Meter_Replacement_Charge')
					->setCellValue('EU1','Load_Upgrade_Degrade_Cost')
					->setCellValue('EV1','DD_Charges')
					->setCellValue('EW1','Advance_Paid_Amt')
					->setCellValue('EX1','Advance_Received_Amt')
					->setCellValue('EY1','Total_Unit_Consumption_Last_Bill')
					->setCellValue('EZ1','Total Billable To Customer Prev Month')
					->setCellValue('FA1','Payment Reference')
					->setCellValue('FB1','Arrear Billable To Customer')
					->setCellValue('FC1','Current Billable To Customer')
					->setCellValue('FD1','Total Billable To Customer')
					->setCellValue('FE1','Billing_Cycle')
					->setCellValue('FF1','Other_Remarks')
					->setCellValue('FG1','BillPro_Ref_No')
					->setCellValue('FH1','BillPro_Revised')
					->setCellValue('FI1','BillPro_Enriched_Field')
					->setCellValue('FJ1','BillPro_Remarks')
					->setCellValue('FK1','BillPro_Review_Request');
					if($username == 'navin' ){
						$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('FL1','Unique_Id')
						->setCellValue('FM1','File_Name');
					}
			$bill_counter = 2;
			foreach($api_list_arr as $key => $row){
			
			if($row['fld_billissuedate'] == "" || $row['fld_billissuedate'] == "0000-00-00"){
				$fld_billissuedate = "";
			}else{
				$fld_billissuedate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_billissuedate']);
			}
			if($row['fld_billduedate'] == "" || $row['fld_billduedate'] == "0000-00-00"){
				$fld_billduedate = "";
			}else{
				$fld_billduedate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_billduedate']);
			}
			if($row['fld_billreceiveddate'] == "" || $row['fld_billreceiveddate'] == "0000-00-00"){
				$fld_billreceiveddate = "";
			}else{
				$fld_billreceiveddate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_billreceiveddate']);
			}
			if($row['fld_connectiondate'] == "" || $row['fld_connectiondate'] == "0000-00-00"){
				$fld_connectiondate = "";
			}else{
				$fld_connectiondate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_connectiondate']);
			}
			if($row['fld_expenseperiod'] == "" || $row['fld_expenseperiod'] == "0000-00-00"){
				$fld_expenseperiod = "";
			}else{
				$time=strtotime($row['fld_expenseperiod']);
				$month=date("F",$time);
				$year=date("Y",$time);
				$fld_expenseperiod = $month." ".$year;//electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_expenseperiod']);
			}
			if($row['fld_ebstartdate'] == "" || $row['fld_ebstartdate'] == "0000-00-00"){
				$fld_ebstartdate = "";
			}else{
				$fld_ebstartdate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_ebstartdate']);
			}
			if($row['fld_previousreadingdate'] == "" || $row['fld_previousreadingdate'] == "0000-00-00"){
				$fld_previousreadingdate = "";
			}else{
				$fld_previousreadingdate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_previousreadingdate']);
			}
			if($row['fld_currentreadingdate'] == "" || $row['fld_currentreadingdate'] == "0000-00-00"){
				$fld_currentreadingdate = "";
			}else{
				$fld_currentreadingdate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_currentreadingdate']);
			}
			if($row['fld_ebenddate'] == "" || $row['fld_ebenddate'] == "0000-00-00"){
				$fld_ebenddate = "";
			}else{
				$fld_ebenddate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_ebenddate']);
			}
			if($row['fld_disconnectiondate'] == "" || $row['fld_disconnectiondate'] == "0000-00-00"){
				$fld_disconnectiondate = "";
			}else{
				$fld_disconnectiondate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_disconnectiondate']);
			}
			if($row['fld_arrearsstartdate'] == "" || $row['fld_arrearsstartdate'] == "0000-00-00"){
				$fld_arrearsstartdate = "";
			}else{
				$fld_arrearsstartdate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_arrearsstartdate']);
			}
			if($row['fld_arrearsenddate'] == "" || $row['fld_arrearsenddate'] == "0000-00-00"){
				$fld_arrearsenddate = "";
			}else{
				$fld_arrearsenddate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_arrearsenddate']);
			}
			if($row['fld_ebReconnectionDate'] == "" || $row['fld_ebReconnectionDate'] == "0000-00-00"){
				$fld_ebReconnectionDate = "";
			}else{
				$fld_ebReconnectionDate = electricity_bill_date_format_with_formatted_2(__LINE__,__FILE__,$row['fld_ebReconnectionDate']);
			}
			if($row['fld_discom_id'] == '46'){
				$bill_type = $row['fld_tariff'];
				$fld_amountbeforeduedate = round($row['fld_amountbeforeduedate']);
				$fld_amountafterduedate = round($row['fld_amountafterduedate']);
				$fld_currentpayableamount = round($row['fld_currentpayableamount']);
				$fld_totalCurrentBill = round($row['fld_totalCurrentBill']);
			}else{
				$bill_type = $row['fld_bill_type'];
				$fld_amountbeforeduedate = $row['fld_amountbeforeduedate'];
				$fld_amountafterduedate = $row['fld_amountafterduedate'];
				$fld_currentpayableamount = $row['fld_currentpayableamount'];
				$fld_totalCurrentBill = $row['fld_totalCurrentBill'];
			}
			if($row['fld_circle_id'] == 9){
				if($row['fld_arrearsamount'] < 0 ){
					$fld_arrearsamount = $row['fld_arrearsamount'] ." ". "(CR)" ;
				}else{
					$fld_arrearsamount = $row['fld_arrearsamount']." ". "(DR)" ;
				}
				$fld_previousEBStartReading_KWH = 0;
				$fld_previousEBEndReading_KWH = 0;
				$fld_previousEBStartReading_KVAH = 0;
				$fld_previousEBEndReading_KVAH = 0;
				$fld_ebStartReading_KVAH = $row['fld_ebStartReading_KVAH'];
				$fld_ebEndReading_KVAH = $row['fld_ebEndReading_KVAH'];
				$fld_meterstatus = $row['fld_meterstatus'];
			}elseif($row['fld_circle_id'] == 6){
				$fld_previousEBStartReading_KWH = 0;
				$fld_previousEBEndReading_KWH = 0;
				$fld_previousEBStartReading_KVAH = 0;
				$fld_previousEBEndReading_KVAH = 0;
				$fld_ebStartReading_KVAH = $row['fld_ebStartReading_KVAH'];
				$fld_ebEndReading_KVAH = $row['fld_ebEndReading_KVAH'];
				$fld_meterstatus = $row['fld_meterstatus'];
			}elseif($row['fld_discom_id'] == 120 || $row['fld_discom_id'] == 127 || $row['fld_discom_id'] == 121 || $row['fld_discom_id'] == 124 || $row['fld_discom_id'] == 126 || $row['fld_discom_id'] == 123 || $row['fld_discom_id'] == 147 || $row['fld_discom_id'] == 125   ){
				$fld_previousEBStartReading_KWH = 0;
				$fld_previousEBEndReading_KWH = 0;
				$fld_previousEBStartReading_KVAH = 0;
				$fld_previousEBEndReading_KVAH = 0;
				$fld_ebStartReading_KVAH = $row['fld_ebStartReading_KVAH'];
				$fld_ebEndReading_KVAH = $row['fld_ebEndReading_KVAH'];
				$fld_meterstatus = $row['fld_meterstatus'];
			}elseif($row['fld_discom_id'] == 80 || $row['fld_discom_id'] == 106 || $row['fld_discom_id'] == 142 || $row['fld_discom_id'] == 59 || $row['fld_discom_id'] == 27 || $row['fld_discom_id'] == 28|| $row['fld_discom_id'] == 29 || $row['fld_discom_id'] == 107 || $row['fld_discom_id'] == 150){
				$fld_previousEBStartReading_KWH = 0;
				$fld_previousEBEndReading_KWH = 0;
				$fld_previousEBStartReading_KVAH = 0;
				$fld_previousEBEndReading_KVAH = 0;
				$fld_ebStartReading_KVAH = $row['fld_ebStartReading_KVAH'];
				$fld_ebEndReading_KVAH = $row['fld_ebEndReading_KVAH'];
				$fld_meterstatus = $row['fld_meterstatus'];
			}elseif($row['fld_discom_id'] == 48 || $row['fld_discom_id'] == 50 || $row['fld_discom_id'] == 51 || $row['fld_discom_id'] == 52 || $row['fld_discom_id'] == 53 ){
				$fld_previousEBStartReading_KWH = 0;
				$fld_previousEBEndReading_KWH = 0;
				$fld_previousEBStartReading_KVAH = 0;
				$fld_previousEBEndReading_KVAH = 0;
				$fld_meterstatus = '';
			}elseif($row['fld_discom_id'] == 71 || $row['fld_discom_id'] == 79 || $row['fld_discom_id'] == 54 || $row['fld_discom_id'] == 78 || $row['fld_discom_id'] == 118){
				$fld_previousEBStartReading_KWH = 0;
				$fld_previousEBEndReading_KWH = 0;
				$fld_previousEBStartReading_KVAH = 0;
				$fld_previousEBEndReading_KVAH = 0;
				$fld_meterstatus = $row['fld_meterstatus'];
			}else{
				$fld_arrearsamount = $row['fld_arrearsamount'];
				$fld_previousEBStartReading_KWH = $row['fld_previousEBStartReading_KWH'];
				$fld_previousEBEndReading_KWH = $row['fld_previousEBEndReading_KWH'];
				$fld_previousEBStartReading_KVAH = $row['fld_previousEBStartReading_KVAH'];
				$fld_previousEBEndReading_KVAH = $row['fld_previousEBEndReading_KVAH'];
				$fld_ebStartReading_KVAH = $row['fld_ebStartReading_KVAH'];
				$fld_ebEndReading_KVAH = $row['fld_ebEndReading_KVAH'];
				$fld_meterstatus = $row['fld_meterstatus'];
			}
			
			if($row['fld_discom_id'] == 27 || $row['fld_discom_id'] == 28|| $row['fld_discom_id'] == 29 || $row['fld_discom_id'] == 107 || $row['fld_discom_id'] == 42 || $row['fld_discom_id'] == 127 || $row['fld_discom_id'] == 101 || $row['fld_discom_id'] == 143 || $row['fld_discom_id'] == 141 || $row['fld_discom_id'] == 3 || $row['fld_discom_id'] == 1 || $row['fld_discom_id'] == 79 || $row['fld_discom_id'] == 71 || $row['fld_discom_id'] == 118 || $row['fld_discom_id'] == 44 || $row['fld_discom_id'] == 46 || $row['fld_discom_id'] == 115 || $row['fld_discom_id'] == 130 || $row['fld_discom_id'] == 99 || $row['fld_discom_id'] == 49 || $row['fld_discom_id'] == 103 || $row['fld_discom_id'] == 37 || $row['fld_discom_id'] == 40 || $row['fld_discom_id'] == 41 || $row['fld_discom_id'] == 42 || $row['fld_discom_id'] == 43){
				$fld_openingreading = $row['fld_openingreading'];
				$fld_closingreading = $row['fld_closingreading'];
			}else{
				$fld_openingreading = $row['fld_previousEBStartReading_KWH'];
				$fld_closingreading = $row['fld_previousEBEndReading_KWH'];
			}
			if($row['fld_discom_id'] == '45' || $row['fld_discom_id'] == '49'){
				$fld_disconnectiondate = '';
			}
			$currentmonth_dps = $row['fld_currentmonthdpscharges'];
			if($row['fld_discom_id'] == '49' || $row['fld_discom_id'] == '115'){
				$currentmonth_dps = '';
			}
			
			if($row['fld_discom_id'] == '143'){
				if($row['fld_eRebate'] < 0 ){
					$fld_eRebate = abs($row['fld_eRebate']) ." ". "(CR)" ;
				}else{
					$fld_eRebate = $row['fld_eRebate']." ". "(DR)" ;
				}
				if($row['fld_arrearsamount'] < 0 ){
					$fld_arrearsamount = abs($row['fld_arrearsamount']) ." ". "(CR)" ;
				}else{
					$fld_arrearsamount = $row['fld_arrearsamount']." ". "(DR)" ;
				}
				if($row['fld_adjustmentAmount'] < 0 ){
					$fld_adjustmentAmount = abs($row['fld_adjustmentAmount']) ." ". "(CR)" ;
				}else{
					$fld_adjustmentAmount = $row['fld_adjustmentAmount']." ". "(DR)" ;
				}
			}else{
				$fld_eRebate = $row['fld_eRebate'];
				$fld_arrearsamount = $row['fld_arrearsamount'];
				$fld_adjustmentAmount = $row['fld_adjustmentAmount'];
			}
			if($row['fld_discom_id'] == 28){
				$fld_address = '';
			}else{
				$fld_address = $row['fld_address'];
			}
			if($row['fld_circle_id'] == 18){
				$boardname = $row['fld_short_form'];
			}else{
				$boardname = $row['boardname'];
			}
			
				$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$bill_counter,$row['fld_organizationsite_id'])
			                    	->setCellValue('B'.$bill_counter,$row['fld_site_name'])
			                    	->setCellValue('C'.$bill_counter,$row['circle_name'])
			                    	->setCellValue('D'.$bill_counter,$row['fld_cluster'])
			                    	->setCellValue('E'.$bill_counter,$boardname)
			                    	->setCellValueExplicit('F'.$bill_counter,$row['fld_consumer_id'],PHPExcel_Cell_DataType::TYPE_STRING)
			                    	->setCellValueExplicit('G'.$bill_counter,$row['fld_oldaccountnumber'],PHPExcel_Cell_DataType::TYPE_STRING)
			                    	->setCellValueExplicit('H'.$bill_counter, $row['fld_billnumber'],PHPExcel_Cell_DataType::TYPE_STRING)
			                    	->setCellValue('I'.$bill_counter,$row['fld_opco_type'])
			                    	->setCellValue('J'.$bill_counter,$row['fld_nameonbill'])
			                    	->setCellValue('K'.$bill_counter,$fld_address)
			                    	->setCellValue('L'.$bill_counter,$row['fld_divisionname'])
			                    	->setCellValue('M'.$bill_counter,$row['fld_subdivisionname'])
			                    	->setCellValue('N'.$bill_counter,$row['fld_townDistrictName'])
			                   	->setCellValue('O'.$bill_counter, $fld_billissuedate)
			                    	->setCellValue('P'.$bill_counter, $fld_billduedate)
			                    	->setCellValue('Q'.$bill_counter, $fld_billreceiveddate)
			                    	->setCellValue('R'.$bill_counter, $fld_connectiondate)
			                    	->setCellValue('S'.$bill_counter,$row['fld_tariff'])
			                    	->setCellValue('T'.$bill_counter,$row['fld_phase'])
			                    	->setCellValue('U'.$bill_counter,$row['fld_sanctionload'])
			                    	->setCellValue('V'.$bill_counter,$row['fld_mdi'])
			                    	->setCellValue('W'.$bill_counter,$row['fld_securitydeposit'])
			                    	->setCellValue('X'.$bill_counter,$row['fld_securitydepositpaid'])
			                    	->setCellValue('Y'.$bill_counter,$row['fld_securitydepositrefund'])
			                    	->setCellValue('Z'.$bill_counter,$row['fld_securitydepositremark'])
			                    	->setCellValue('AA'.$bill_counter,$row['fld_interestonsecuritydeposit'])
			                    	->setCellValue('AB'.$bill_counter, $fld_expenseperiod)
			                    	->setCellValueExplicit('AC'.$bill_counter,$row['fld_meternumber'],PHPExcel_Cell_DataType::TYPE_STRING)
			                    	->setCellValue('AD'.$bill_counter, $fld_meterstatus)
			                    	->setCellValue('AE'.$bill_counter, $row['fld_unitrate'])
			                    	->setCellValue('AF'.$bill_counter, $row['fld_powerfactor'])
			                    	->setCellValue('AG'.$bill_counter, $row['fld_multiplicationfactor'])
			                    	->setCellValue('AH'.$bill_counter, $bill_type)
			                    	->setCellValue('AI'.$bill_counter, $fld_ebstartdate)
			                    	->setCellValue('AJ'.$bill_counter, $fld_previousreadingdate)
			                    	->setCellValue('AK'.$bill_counter, $fld_currentreadingdate)
			                    	->setCellValue('AL'.$bill_counter, $fld_ebenddate)
			                    	->setCellValue('AM'.$bill_counter, $fld_previousEBStartReading_KWH)
			                    	->setCellValue('AN'.$bill_counter, $fld_previousEBEndReading_KWH)
			                    	->setCellValue('AO'.$bill_counter, $fld_previousEBStartReading_KVAH)
			                    	->setCellValue('AP'.$bill_counter, $fld_previousEBEndReading_KVAH)
			                    	->setCellValue('AQ'.$bill_counter, $fld_ebStartReading_KVAH)
			                    	->setCellValue('AR'.$bill_counter, $fld_ebEndReading_KVAH)
			                    	->setCellValue('AS'.$bill_counter, $row['fld_noOfBilledDays'])
			                    	->setCellValue('AT'.$bill_counter, $row['fld_billedunits'])
			                    	->setCellValue('AU'.$bill_counter, $row['fld_totalConsumption'])
			                    	->setCellValue('AV'.$bill_counter, $row['fld_totalunitsconsumed'])
			                    	->setCellValue('AW'.$bill_counter, $fld_openingreading)
			                    	->setCellValue('AX'.$bill_counter, $fld_closingreading)
			                    	->setCellValue('AY'.$bill_counter, $row['fld_meterrent'])
			                    	->setCellValue('AZ'.$bill_counter, $row['fld_meterServiceCharge'])
			                    	->setCellValue('BA'.$bill_counter, $fld_disconnectiondate)
			                    	->setCellValue('BB'.$bill_counter, $fld_arrearsamount)
			                    	->setCellValue('BC'.$bill_counter, $fld_arrearsstartdate)
			                    	->setCellValue('BD'.$bill_counter, $fld_arrearsenddate)
			                    	->setCellValue('BE'.$bill_counter, $row['fld_arrearstartreading'])
			                    	->setCellValue('BF'.$bill_counter, $row['fld_arrearendreading'])
			                    	->setCellValue('BG'.$bill_counter,$row['fld_dpsarrear'])
			                    	->setCellValue('BH'.$bill_counter,$currentmonth_dps)
			                    	->setCellValue('BI'.$bill_counter,$row['fld_currentenergycharges'])
			                    	->setCellValue('BJ'.$bill_counter,$row['fld_fixedcharges'])
			                    	->setCellValue('BK'.$bill_counter,$row['fld_energydutyedcharges'])
			                    	->setCellValue('BL'.$bill_counter,$row['fld_excessDemandCharge'])
			                    	->setCellValue('BM'.$bill_counter,$row['fld_fuelsurcharge'])
			                    	->setCellValue('BN'.$bill_counter,$row['fld_latepaymentcharges'])
			                    	->setCellValue('BO'.$bill_counter,$row['fld_govttaxothercharges'])
			                    	->setCellValue('BP'.$bill_counter,$row['fld_subsidyAmount'])
			                    	->setCellValue('BQ'.$bill_counter,$row['fld_municipalitycharges'])
			                    	->setCellValue('BR'.$bill_counter,$row['fld_reconnectiondisconnectioncharges'])
			                    	->setCellValue('BS'.$bill_counter,$row['fld_mmccharge'])
			                    	->setCellValue('BT'.$bill_counter,$row['fld_rebateearlypayment'])
			                    	->setCellValue('BU'.$bill_counter,$fld_eRebate)
			                    	->setCellValue('BV'.$bill_counter,$fld_totalCurrentBill)
			                    	->setCellValue('BW'.$bill_counter,$fld_amountbeforeduedate)
			                    	->setCellValue('BX'.$bill_counter,$fld_amountafterduedate)
			                    	->setCellValue('BY'.$bill_counter,$fld_currentpayableamount)
			                    	->setCellValue('BZ'.$bill_counter,$row['fld_openingBalance'])
			                    	->setCellValue('CA'.$bill_counter,$row['fld_payableAmountOnDueDate'])
			                    	->setCellValue('CB'.$bill_counter,$row['fld_remarks'])
			                    	->setCellValue('CC'.$bill_counter,$row['fld_energyarrearduty'])
			                    	->setCellValue('CD'.$bill_counter,$row['fld_sundrycharges'])
			                    	->setCellValue('CE'.$bill_counter,$row['fld_MVCACharge'])
			                    	->setCellValue('CF'.$bill_counter,$row['fld_interimBill'])
			                    	->setCellValue('CG'.$bill_counter,$row['fld_htRebate'])
			                    	->setCellValue('CH'.$bill_counter,$row['fld_htPenalty'])
			                    	->setCellValue('CI'.$bill_counter,$row['fld_dtrPenalty'])
			                    	->setCellValue('CJ'.$bill_counter,$row['fld_voltagePenalty'])
			                    	->setCellValue('CK'.$bill_counter,$row['fld_voltageRebate'])
			                    	->setCellValue('CL'.$bill_counter,$row['fld_TODRebate'])
			                    	->setCellValue('CM'.$bill_counter,$row['fld_TODSurcharge'])
			                    	->setCellValue('CN'.$bill_counter,$row['fld_VCRPenalty'])
			                    	->setCellValue('CO'.$bill_counter,$row['fld_PFPenalty'])
			                    	->setCellValue('CP'.$bill_counter,$row['fld_powerFactorRebate'])
			                    	->setCellValue('CQ'.$bill_counter,$row['fld_VCAFSA'])
			                    	->setCellValue('CR'.$bill_counter,$fld_adjustmentAmount)
			                    	->setCellValue('CS'.$bill_counter,$row['fld_CCBAdjustment'])
			                    	->setCellValue('CT'.$bill_counter,$row['fld_CGST'])
			                    	->setCellValue('CU'.$bill_counter,$row['fld_sgstamount'])
			                    	->setCellValue('CV'.$bill_counter,$row['fld_cess'])
			                    	->setCellValue('CW'.$bill_counter,$row['fld_PPACOnEnergyCharges'])
			                    	->setCellValue('CX'.$bill_counter,$row['fld_PPACOnFixedCharges'])
			                    	->setCellValue('CY'.$bill_counter,$row['fld_electricityDutyAdjustmentAmount'])
			                    	->setCellValue('CZ'.$bill_counter,$row['fld_electricityDutyArrear'])
			                    	->setCellValue('DA'.$bill_counter,$row['fld_energyChargesRestimated'])
			                    	->setCellValue('DB'.$bill_counter,$row['fld_fixedChargesArrear'])
			                    	->setCellValue('DC'.$bill_counter,$row['fld_FPAPPA'])
			                    	->setCellValue('DD'.$bill_counter,$row['fld_FSAArrear'])
			                    	->setCellValue('DE'.$bill_counter,$row['fld_interest'])
			                    	->setCellValue('DF'.$bill_counter,$row['fld_interestArrear'])
			                    	->setCellValue('DG'.$bill_counter,$row['fld_interestElectricityDuty'])
			                    	->setCellValue('DH'.$bill_counter,$row['fld_pensionSurcharge_DTR'])
			                    	->setCellValue('DI'.$bill_counter,$row['fld_pensionSurchargeOnEnergyCharges'])
			                    	->setCellValue('DJ'.$bill_counter,$row['fld_miscilleneousCharge'])
			                    	->setCellValue('DK'.$bill_counter,$row['fld_municipalTaxArrear'])
			                    	->setCellValue('DL'.$bill_counter,$row['fld_provisionalBillAmount'])
			                    	->setCellValue('DM'.$bill_counter,$row['fld_provisionaladj'])
			                    	->setCellValue('DN'.$bill_counter,$row['fld_roundOff'])
			                    	->setCellValue('DO'.$bill_counter,$row['fld_sundryAllowances'])
			                    	->setCellValue('DP'.$bill_counter,$row['fld_tariffAdjustment'])
			                    	->setCellValue('DQ'.$bill_counter,$row['fld_taxOnSale'])
			                    	->setCellValue('DR'.$bill_counter,$row['fld_TCSSurchargeAmount'])
			                    	->setCellValue('DS'.$bill_counter,$row['fld_TDSAmount'])
			                    	->setCellValue('DT'.$bill_counter,$row['fld_EBTheftArrear'])
			                    	->setCellValue('DU'.$bill_counter,$row['fld_otherNigamDues'])
			                    	->setCellValue('DV'.$bill_counter,$row['fld_overdrawPenalty'])
			                    	->setCellValue('DW'.$bill_counter,$row['fld_transformerMDCharge'])
			                    	->setCellValue('DX'.$bill_counter,$row['fld_trueUPCharges'])
			                    	->setCellValue('DY'.$bill_counter,$row['fld_waterCess'])
			                    	->setCellValue('DZ'.$bill_counter,$row['fld_wheelingCharges'])
			                    	->setCellValue('EA'.$bill_counter,$row['fld_lockcreditunits'])
			                    	->setCellValue('EB'.$bill_counter,$row['fld_basicebcharges'])
			                    	->setCellValue('EC'.$bill_counter,$row['fld_admincharges'])
			                    	->setCellValue('ED'.$bill_counter,$row['fld_customercccharges'])
			                    	->setCellValue('EE'.$bill_counter,$row['fld_icstamount'])
			                    	->setCellValue('EF'.$bill_counter,$row['fld_industr_comm_credit'])
			                    	->setCellValue('EG'.$bill_counter,$row['fld_tcsamount'])
			                    	->setCellValue('EH'.$bill_counter,$row['fld_infavour_of'])
			                    	->setCellValue('EI'.$bill_counter,$row['fld_addlchargesbyebboard'])
			                    	->setCellValue('EJ'.$bill_counter,$row['fld_consumerCategory'])
			                    	->setCellValue('EK'.$bill_counter,$row['fld_connectiontype'])
			                    	->setCellValue('EL'.$bill_counter, $fld_ebReconnectionDate)
			                    	->setCellValue('EM'.$bill_counter,$row['fld_region'])
			                    	->setCellValue('EN'.$bill_counter,$row['fld_entity_name'])
			                    	->setCellValue('EO'.$bill_counter,$row['fld_payment_mode'])
			                    	->setCellValue('EP'.$bill_counter,$row['fld_exception'])
			                    	->setCellValue('EQ'.$bill_counter,$row['fld_remarksBillType'])
			                    	->setCellValue('ER'.$bill_counter,$row['fld_estimatedActualArrearUnits'])
			                    	->setCellValue('ES'.$bill_counter,$row['fld_remarksArrearBill'])
			                    	->setCellValue('ET'.$bill_counter,$row['fld_meterReplaceementCost'])
			                    	->setCellValue('EU'.$bill_counter,$row['fld_loadUpgradeDegradeCost'])
			                    	->setCellValue('EV'.$bill_counter,$row['fld_DDCharges'])
			                    	->setCellValue('EW'.$bill_counter,$row['fld_advancepaid'])
			                    	->setCellValue('EX'.$bill_counter,$row['fld_advanceReceivedAmount'])
			                    	->setCellValue('EY'.$bill_counter,$row['fld_totalUnitConsumedPrevMonth'])
			                    	->setCellValue('EZ'.$bill_counter,$row['fld_totalBillableToCustomerPrevMonth'])
			                    	->setCellValue('FA'.$bill_counter,$row['fld_paymentReference'])
			                    	->setCellValue('FB'.$bill_counter,$row['fld_arrearBillableToCustomer'])
			                    	->setCellValue('FC'.$bill_counter,$row['fld_currentBillableToCustomer'])
			                    	->setCellValue('FD'.$bill_counter,$row['fld_totalBillableToCustomer'])
			                    	->setCellValue('FE'.$bill_counter,$row['fld_billingCycle'])
			                    	->setCellValue('FF'.$bill_counter,$row['fld_remarksOther'])
			                    	->setCellValue('FG'.$bill_counter,$row['fld_billProUnqRefNo'])
			                    	->setCellValue('FH'.$bill_counter,$row['fld_billProIsRevised'])
			                    	->setCellValue('FI'.$bill_counter,$row['fld_billProEnrichedFields'])
			                    	->setCellValue('FJ'.$bill_counter,$row['fld_remarksBillPro'])
			                    	->setCellValue('FK'.$bill_counter,$row['fld_billProReviewRequest']);
						if($username == 'navin' ){
							$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('FL'.$bill_counter,$row['fld_bill_id'])
							->setCellValue('FM'.$bill_counter,$row['fld_filename']);
						}
					$bill_counter++;
			 	}
			}else{
			  	$objPHPExcel->setActiveSheetIndex(0)
			  		->setCellValue('A1', 'No Bill  Found') ;
			}
	  }
	  header('Content-Type: application/vnd.ms-excel');
	  header("Content-Disposition: attachment; filename=$fn.xls");
	  header('Cache-Control: max-age=0');
	  // If you're serving to IE 9, then the following may be needed
	  header('Cache-Control: max-age=1');
	   
	  // If you're serving to IE over SSL, then the following may be needed
	  header ('Expires: Mon, 26 Jul 2020 05:00:00 GMT'); // Date in the past
	  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	  header ('Pragma: public'); // HTTP/1.0
	  
	  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	  $objWriter->save('php://output');
}	

function comma_dot_replace($value){
	if(electricity_bill_find_position(__LINE__,__FILE__,substr($value,-3),",") !== FALSE){
		$temp_value = electricity_bill_string_replace(__LINE__,__FILE__,'.','',$value);
		$main_value = electricity_bill_string_replace(__LINE__,__FILE__,',','.',$temp_value);
	}else{
		$main_value = electricity_bill_string_replace(__LINE__,__FILE__,',','',$value);
	}
	return $main_value;
}
function electricity_bill_last_daily_mail_details($line,$file,$site_id,$daily_mail_message,$success_failuer_flag,$saved_credential_json, $last_fetch_page = ""){
		electricity_bill_debug($line,$file,__FUNCTION__,electricity_bill_json_converter($site_id,$daily_mail_message,$success_failuer_flag,$saved_credential_json));
		$error_flag = 0;
		$latest_bill_data_query = electricity_bill_query(__LINE__,__FILE__,"SELECT fld_due_date,fld_generated_date,fld_day_of_bill FROM tbl_bills WHERE fld_internalsite_id = '".$site_id."' ORDER BY fld_ai_id DESC LIMIT 0,1");
		$latest_bill_data = electricity_bill_fetch_assoc(__LINE__,__FILE__,$latest_bill_data_query);
		$expected_bill_generated_date = date('Y-m-d',strtotime('+30 days',strtotime($latest_bill_data['fld_generated_date'])));
		$bill_date = $latest_bill_data['fld_generated_date'];
		$due_date = $latest_bill_data['fld_due_date'];
	echo	$last_daily_mail_deatils_query = "SELECT fld_circle_id,fld_discom_id,fld_zone_id,tbl_fetch_bills.fld_message_count,fld_last_success_time,fld_last_bill_fetch_date,fld_bill_date,fld_expected_bill_generated_date,tbl_fetch_bills.fld_due_date,tbl_fetch_bills.fld_amount,tbl_fetch_bills.fld_message FROM tbl_fetch_bills WHERE fld_internal_site_id = '".$site_id."' ORDER BY fld_ai_id DESC LIMIT 0,1;";
		$last_daily_mail_deatils_query_result =  electricity_bill_query(__LINE__,__FILE__,$last_daily_mail_deatils_query);
		if(electricity_bill_num_rows(__LINE__,__FILE__,$last_daily_mail_deatils_query_result) > 0){
				$MESSAGE_ARRAY = array();
				$array_index = 0;
				$row_data_for_daily_mail = electricity_bill_fetch_assoc(__LINE__,__FILE__,$last_daily_mail_deatils_query_result);
				$zone_id = $row_data_for_daily_mail['fld_zone_id'];
				$circle_id = $row_data_for_daily_mail['fld_circle_id'];
				$last_success_time = $row_data_for_daily_mail['fld_last_success_time'];
				$last_bill_fetch_date = $row_data_for_daily_mail['fld_last_bill_fetch_date'];
				//$bill_date = $row_data_for_daily_mail['fld_bill_date'];
				//$expected_bill_generated_date = $row_data_for_daily_mail['fld_expected_bill_generated_date'];
				//$due_date = $row_data_for_daily_mail['fld_due_date'];
				$electricity_board_id = $row_data_for_daily_mail['fld_discom_id'];
				$prev_message = $row_data_for_daily_mail['fld_message'];
				$message_count = $row_data_for_daily_mail['fld_message_count'];
	
				 if($prev_message == $daily_mail_message){
						if($message_count>=6){
								$message_count=6;
						}else{
								$message_count++;
						}
				}else{
						$message_count =1;
				}
				if($due_date == ''){
						$due_date = "0000-00-00";
				}
				$total_amount = $row_data_for_daily_mail['fld_amount'];
				if($total_amount == ''){
						$total_amount = 0.00;
				}
		}
		echo $insert_into_daily_mail_query = "INSERT INTO tbl_fetch_bills(fld_internal_site_id,fld_circle_id,fld_discom_id,fld_zone_id,fld_message,fld_last_success_time,fld_last_bill_fetch_date,fld_bill_date,tbl_fetch_bills.fld_due_date,fld_expected_bill_generated_date,fld_saved_credential,fld_status,tbl_fetch_bills.fld_amount,tbl_fetch_bills.fld_message_count,fld_timestamp,fld_ip) VALUES ('".$site_id."','".$circle_id."','".$electricity_board_id."','".$zone_id."','".$daily_mail_message."','".$last_success_time."','".$last_bill_fetch_date."','".$bill_date."','".$due_date."','".$expected_bill_generated_date."','".$saved_credential_json."','".$success_failuer_flag."','".$total_amount."','".$message_count."',NOW(),'')";
		electricity_bill_query(__LINE__,__FILE__,$insert_into_daily_mail_query);
		if(electricity_bill_affected_rows(__LINE__,__FILE__) > 0){
			echo $checking_for_unique_query = "SELECT fld_internal_site_id FROM tbl_fetch_bills_unique WHERE fld_internal_site_id = '".$site_id."';";
			$checking_for_unique_query_result = electricity_bill_query(__LINE__,__FILE__,$checking_for_unique_query);
			if(electricity_bill_num_rows(__LINE__,__FILE__,$checking_for_unique_query_result) > 0){
				$update_into_fetch_bills_unique_query = "UPDATE tbl_fetch_bills_unique SET 	
					fld_internal_site_id = '".$site_id."',
					fld_circle_id = '".$circle_id."',
					fld_discom_id = '".$electricity_board_id."',
					fld_zone_id = '".$zone_id."',
					fld_message = '".$daily_mail_message."',
					fld_last_success_time = '".$last_success_time."',
					fld_last_bill_fetch_date = '".$last_bill_fetch_date."',
					fld_bill_date = '".$bill_date."',
					tbl_fetch_bills_unique.fld_due_date = '".$due_date."',
					fld_expected_bill_generated_date = '".$expected_bill_generated_date."',
					fld_saved_credential = '".$saved_credential_json."',
					fld_status = '".$success_failuer_flag."',
					tbl_fetch_bills_unique.fld_amount = '".$total_amount."',
					tbl_fetch_bills_unique.fld_message_count = '".$message_count."',
					fld_timestamp = NOW(),
					fld_ip = 'altius.billpro.online' 
					WHERE fld_internal_site_id = '".$site_id."';";
				electricity_bill_query(__LINE__,__FILE__,$update_into_fetch_bills_unique_query);	
			}else{
				$insert_into_fetch_bills_unique_query = "INSERT INTO tbl_fetch_bills_unique(fld_internal_site_id,fld_circle_id,fld_discom_id,fld_zone_id,fld_message,fld_last_success_time,fld_last_bill_fetch_date,fld_bill_date,tbl_fetch_bills_unique.fld_due_date,fld_expected_bill_generated_date,fld_saved_credential,fld_status,tbl_fetch_bills_unique.fld_amount,tbl_fetch_bills_unique.fld_message_count,fld_timestamp,fld_ip) VALUES ('".$site_id."','".$circle_id."','".$electricity_board_id."','".$zone_id."','".$daily_mail_message."','".$last_success_time."','".$last_bill_fetch_date."','".$bill_date."','".$due_date."','".$expected_bill_generated_date."','".$saved_credential_json."','".$success_failuer_flag."','".$total_amount."','".$message_count."',NOW(),'altius.billpro.online');";
				electricity_bill_query(__LINE__,__FILE__,$insert_into_fetch_bills_unique_query);
			}
			if(electricity_bill_affected_rows(__LINE__,__FILE__) == 0){
				$error_flag = 1;			
			}
		}

		// Update to tbl_sites
		$sites_update_query = "UPDATE tbl_sites SET fld_last_message = '$daily_mail_message',fld_message_count = $message_count, fld_last_status = $success_failuer_flag  WHERE fld_ai_internalsite_id = $site_id";
		electricity_bill_query(__LINE__,__FILE__,$sites_update_query);

		// Save Last Fetch Page Details 
		/*if ($last_fetch_page != "" && $last_fetch_page != null) {
			$last_fetch_page = addslashes($last_fetch_page);
			echo $last_fetch_page_query = "SELECT fld_ai_id FROM `tbl_last_fetch_page` WHERE fld_internal_site_id = $site_id";
			$last_fetch_page_query_result =  electricity_bill_query(__LINE__,__FILE__,$last_fetch_page_query);
			if(electricity_bill_num_rows(__LINE__,__FILE__,$last_fetch_page_query_result) > 0){
				// Update It
				$last_page_update = "UPDATE tbl_last_fetch_page SET fld_last_page = '$last_fetch_page' WHERE fld_internal_site_id = $site_id";
				electricity_bill_query(__LINE__,__FILE__,$last_page_update);

			} else {
				// Insert It
				$insert_last_page_query = "INSERT INTO tbl_last_fetch_page(fld_internal_site_id,fld_last_page) VALUES ($site_id, '$last_fetch_page')";
				electricity_bill_query(__LINE__,__FILE__,$insert_last_page_query);
			}
		}*/


	return $error_flag;
}
function check_format($value){
    if(strchr($value,'HP') !== false){
        return preg_replace('/([a-zA-Z])/i','',str_replace('HP','',$value))*0.746;
    }elseif(strchr($value,'KW') !== false){
        return preg_replace('/([a-zA-Z])/i','',str_replace('KW','',$value));
    }elseif (strchr($value,'W') !== false) {
        return preg_replace('/([a-zA-Z])/i','',str_replace('W','',$value))/1000;
    }else{
        //$error_message = 'Invalid Value Format';
	return 0;
    }
}
function electricity_bill_parse_decode($line,$file,$response){
	$bill_json_date = "{".electricity_bill_trim(__LINE__,__FILE__,get_string_between(__LINE__,__FILE__,$response, "{\"start\":\"\",",",\"end\":\"\"}"))."}";		
	return $bill_json_date = json_decode($bill_json_date,true);
}
function encryptParameter_rajasthan($data) {
    $secretKey = "1234567890abcdef"; // 16 bytes key for AES-128
    $iv = "1234567890123456"; // 16 bytes IV (should be shared securely)
    $encrypted = openssl_encrypt($data, "AES-128-CBC", $secretKey, OPENSSL_RAW_DATA, $iv);
    return urlencode(base64_encode($encrypted));
}
?>
