<?php
	use mikehaertl\wkhtmlto\Pdf;
	function get_details_gujrat_new($consumer_details,$is_cron=0,$from_flag = 0) {
		//var_dump($consumer_details);
		$consumer_id = $consumer_details['consumer_id'];
		$username = $consumer_details['username'];
		$password = $consumer_details['password'];
		$base_address = $consumer_details['url'];
		$pass_discom_id = $consumer_details['discom_id'];
		$site_id = $consumer_details['site_id'];
		if($is_cron == 0){
			$save_file_dir = '../download/';
		}else{
			$save_file_dir = 'download/';
		}
		$return_flag = array();
		$respone_array = array();
		$error_message = '';
		$bill_date = "";
		$due_date = "";
		$name = "";
		$meter_no = "";
		$current_reading = "";
		$previous_reading = "";
		$unit_consumed = "";
		$total_amount = "";
		$bill_month = "";
		$bill_year = '';
		$bill_assoc = array();
		$division_assoc = array();
		$parameters = array();
		$replacements = array();
		$_tmpArr = array();
		$rebate = 0;
		$e_rebate = 0;
		$penalty = 0;
		$curl_response = "";
		$post_get_values_of_page = '';
		$page_of_error = 'curl_functions/gujrat.php';
		$line_of_error = '';
		$url_of_error = $db_file_name = '';
		$file_name = '';
		$save_file_name = '';
		if($is_cron != 0){
			electricity_bill_last_bill_check(__LINE__,__FILE__,$consumer_id,$pass_discom_id );	
		}
		$line_of_error = __LINE__+1;
		
		if(strlen(electricity_bill_trim(__LINE__,__FILE__,$consumer_id)) == 10){
			$consumer_id = '0'.electricity_bill_trim(__LINE__,__FILE__,$consumer_id);	
		}
		$new_parsing = $consumer_details['new_parsing'];
		
		if($consumer_details['discom_code'] == 'DGVCL'){
			$cookie_text = fetch_page(__LINE__,__FILE__,10000000,'https://capriglobal.billpro.online/dgvcl_cookie.txt','',NULL,NULL,0,0);
		}else if($consumer_details['discom_code'] == 'UGVCL'){
			$cookie_text = fetch_page(__LINE__,__FILE__,10000000,'https://capriglobal.billpro.online/ugvcl_cookie.txt','',NULL,NULL,0,0);
		}else if($consumer_details['discom_code'] == 'MGVCL'){
			$cookie_text = fetch_page(__LINE__,__FILE__,10000000,'https://capriglobal.billpro.online/mgvcl_cookie.txt','',NULL,NULL,0,0);
		}else if($consumer_details['discom_code'] == 'PGVCL'){
			$cookie_text = fetch_page(__LINE__,__FILE__,10000000,'https://capriglobal.billpro.online/pgvcl_cookie.txt','',NULL,NULL,0,0);
		}
		$fetch_page_url = 'https://portal.guvnl.in/';
		$headers = array($cookie_text);
		
		$current_year = electricity_bill_current_year(__LINE__,__FILE__);
		$data_array = array();
		$data_array['idConsumerNo'] = $consumer_id;
		$data_array['fromYr'] = $current_year - 1;
		$data_array['fromMnth'] = "01";
		$data_array['toYr'] = $current_year;
		$data_array['toMnth'] = '12';
		
		$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$data_array);
		$viewBillHistory_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,$fetch_page_url.'viewBillHistory.php',$fetch_page_url.'viewBillHistory.php',$headers,$post_fields,0,1,1);
		$bill_link = get_string_between(__LINE__,__FILE__,$viewBillHistory_page,'?tid=','"');
		
		if($viewBillHistory_page != false){
			
					if($bill_link != ''){
						$bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://portal.guvnl.in/gprsBillDisplay.php?tid='.$bill_link,'',$headers,NULL,0,0,0);
					}
					
					if($bill_link != '' && $bill_page != false && electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'This functionality is temporary unavailable') === false) {
						$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".html";
						$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill_page);
						if (($save_file_check === false) || ($save_file_check == -1)) {
							$return_flag['message'] = "Unable to save bill";
						}else{									
							$return_flag['message'] = "Bill downloaded successfully";
							$return_flag['file_path'] = $file_name;
						}
					}else{
						if($consumer_details['discom_code'] == 'MGVCL'){
							$bill_link = 'https://portal.guvnl.in/consumer_bill_all.php?'.get_string_between(__LINE__,__FILE__,$viewBillHistory_page,"consumer_bill_all.php?",'"');
							 
							$bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,$bill_link,'',$headers,NULL,0,0,0);
							if(!strchr($bill_page,'Consumer Number has been Deleted') || strchr($bill_page,'Consumer number does not exist')){
								$html = str_get_html($bill_page);
								$bill_details_table = $html ->find('table', 2);
								$bill_no = $bill_details_table->find('tr', -5)->find('td', 1)->plaintext;
								$bill_date = electricity_bill_database_date_format(__LINE__, __FILE__, $bill_details_table->find('tr', -3)->find('td', 0)->plaintext);
								$due_date = electricity_bill_database_date_format(__LINE__, __FILE__, $bill_details_table->find('tr', -3)->find('td', 1)->plaintext);
								$check_already_exist_or_not_query = "SELECT fld_ai_id FROM tbl_bills LEFT JOIN tbl_sites ON tbl_sites.fld_ai_internalsite_id = tbl_bills.fld_internalsite_id WHERE tbl_bills.fld_discom_id = '".$pass_discom_id ."' AND fld_generated_date >= date('".$bill_date."') AND tbl_sites.fld_consumer_id = '".$consumer_id."';";
								$check_already_exist_or_not_query_result = electricity_bill_query(__LINE__,__FILE__,$check_already_exist_or_not_query);
								$num_row = electricity_bill_num_rows(__LINE__,__FILE__,$check_already_exist_or_not_query_result);
								if (0 == $num_row) {
									if($bill_page != false && electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'This functionality is temporary unavailable') === false) {
										if($consumer_details['discom_code'] == 'MGVCL'){
											$bill_page = electricity_bill_string_replace(__LINE__,__FILE__,'img/mgvcl-Logo.jpg','https://rjio.billpro.online/gujrat-logo/mgvcl-Logo.jpg',$bill_page);
										}else if($consumer_details['discom_code'] == 'DGVCL'){
										}else if($consumer_details['discom_code'] == 'UGVCL'){
										}else if($consumer_details['discom_code'] == 'PGVCL'){
										}
										$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".html";
										$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
										$save_file_name = $save_file_dir.'/'.$file_name;
										$save_file_name_1 = $save_file_dir.'/'.$site_id.".html";
										$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$save_file_name,$bill_page);
										if(($save_file_check === false) || ($save_file_check == -1)) {
											die( "Couldn't save the file for ".$consumer_id);
										}else{
											$parse_function = 'parse_gujrat_new_html';
											$bill_type = 'html';
											$parse_response = parse_gujrat_central($save_file_name,$consumer_details,1,1,$save_file_name_1,$db_file_name,$bill_type,$parse_function);if($parse_response['flag'] == '0'){
												$respone_array = $parse_response['response'];	
											}else{
												$error_message = $parse_response['message'];	
											}
										}
									}else{
										$error_message = 'Bill HTML not found';
										$curl_response = $bill_page;
										$url_of_error = $fetch_page_url.'Billing_View_All-HT.php?'.$bill_link;
										$post_get_values_of_page = '';
									}
								}else{
									$error_message = "Data already stored into database";
									$curl_response = $bill_page;
									$post_get_values_of_page = $post_values;
									$url_of_error = $fetch_page_url.'Billing_View_All-HT.php?'.$bill_link;
								}
							}else{
								if(strchr($bill_page,'Consumer Number has been Deleted')){
									$error_message = "Consumer Number has been Deleted";
								}else if(strchr($bill_page,'Consumer number does not exist')){
									$error_message = "Consumer number does not exist";
								}
								$curl_response = $bill_page;
								$post_get_values_of_page = $post_values;
								$url_of_error = $fetch_page_url.'Billing_View_All-HT.php?'.$bill_link;
							}
						}else{
							$error_message = "Bill not available in view bill portal";
						}
					}
				
			
		}else{
			$return_flag['message'] = 'Consumer id not register';
		}
	
		
		$error_flag_same_bill = false;
		if($error_message == 'Data already stored into database'){
			$error_flag_same_bill = true;
		}
		// var_dump($new_parsing,$error_message);exit;
		if($new_parsing == 1 && $error_message != '' && ($error_message != 'Data already stored into database' || $consumer_details['discom_code'] == 'MGVCL' || $consumer_details['discom_code'] == 'UGVCL')){
			$error_message = '';
			if($consumer_details['discom_id'] == 33 && $consumer_details['discom_code'] == 'DGVCL'){
				$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://bps.dgvcl.co.in/BillDetail/index.php','', NULL, NULL,0,1);
				if($first_page != false){
					//print_r($first_page);exit;
					$session_id = electricity_bill_trim(__LINE__,__FILE__,get_string_between(__LINE__,__FILE__,$first_page,'Set-Cookie:',';'));
					$headers = array('Cookie:'.$session_id.';');
					$data_array = array();
					$data_array['consumerno'] = $consumer_id;
					$data_array['verificationcode'] = '';
					$data_array['btnSearch'] = '';
					$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$data_array);
					//echo '<pre>';print_r($data_array);echo '</pre>';
					//$login_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://bps.dgvcl.co.in/BillDetail/billdetail.php','https://bps.dgvcl.co.in/',$headers,$post_fields,1,0);
					//if($login_page != false){
					$bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://bps.dgvcl.co.in/BillDetail/BillPreview.php?consumerno='.$consumer_id,'https://bps.dgvcl.co.in/BillDetail/billdetail.php',$headers,NULL,1,0);					
					//print_r($bill_page);exit;
					if($bill_page != false) {
						if (electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'index.php?msg=4') == false) {
							if (electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'index.php?msg=3') == false) {	
								$html = str_get_html($bill_page);
								$table_number_two = $html->find('table',2);
								$bill_date = electricity_bill_trim(__LINE__,__FILE__,$table_number_two ->find('tr',3) ->find('td',1)->plaintext); 
								if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'/') !== false){
									$bill_date = electricity_bill_date_to_database_format_4(__LINE__,__FILE__,$bill_date,'/');	
								}else if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'-') !== false){
									$bill_date = electricity_bill_date_to_database_format_4(__LINE__,__FILE__,$bill_date,'-');	
								}
								$table_number_five_via = $html->find('table',5);
								$total_td_into_tr = count($table_number_five_via -> find('tr',21) -> find('td'));
								$bill_amt = electricity_bill_trim(__LINE__,__FILE__,$table_number_five_via ->find('tr',21) ->find('td',$total_td_into_tr - 1)->plaintext); 

								$num_rows = 0;
								echo $check_already_exist_or_not_query = "SELECT fld_ai_id FROM tbl_bills LEFT JOIN tbl_sites ON tbl_sites.fld_ai_internalsite_id = tbl_bills.fld_internalsite_id WHERE tbl_sites.fld_discom_id = '".$pass_discom_id ."' AND fld_generated_date = date('".$bill_date."') AND tbl_sites.fld_consumer_id = '".$consumer_id."' AND tbl_bills.fld_payment_after_due_date = '".$bill_amt."'";
								$check_already_exist_or_not_query_result = electricity_bill_query(__LINE__,__FILE__,$check_already_exist_or_not_query);
								$num_rows = electricity_bill_num_rows(__LINE__,__FILE__,$check_already_exist_or_not_query_result);
								if($num_rows !== 0) {
									$error_message = 'Data already stored into database';
									$curl_response = $bill_page;
									$url_of_error = 'http://mobile.dgvcl.co.in/checkbilldetails/BillPreview.php'; 
									$post_get_values_of_page = '';
								} else {
									$bill_page = get_string_between(__LINE__,__FILE__,$bill_page,'<html>','</html>');
									$bill_page_extracted = '<html>'.electricity_bill_string_replace(__LINE__,__FILE__,'images/bill_logo.png','http://dgbill.dgvcl.co.in/CheckBillDetails/images/bill_logo.png',$bill_page).'</html>';
									$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".html";
									$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
									$save_file_name = $save_file_dir.'/'.$file_name;
									$save_file_name_1 = $save_file_dir.'/'.$site_id.".html";
									$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$save_file_name,$bill_page_extracted);
									
									if (($save_file_check === false) || ($save_file_check == -1)) {
										die( "Couldn't save the file for ".$consumer_id);
									}else{ 
										$parse_function = 'parse_gujrat_new';
										$bill_type = 'html';
										$parse_response = parse_gujrat_central($save_file_name,$consumer_details,1,1,$save_file_name_1,$db_file_name,$bill_type,$parse_function);
										if($parse_response['flag'] == '0'){
											$respone_array = $parse_response['response'];	
										}else{
											$error_message = $parse_response['message'];	
										}
									}
								}
							}else {
								$return_flag['message'] = 'Invalid Consumer Number.';
							}
						}else {
							$return_flag['message'] = 'Your Energy bill is not yet generated for the current month';
						}
					} else {
						$return_flag['message'] = 'Unable to fetch bill';
					}
					//} else {
					//	$error_message = 'Unable to Login';
					//	$curl_response = $login_page;
					//	$url_of_error = 'https://bps.dgvcl.co.in/BillDetail/billdetail.php';
					//	$post_get_values_of_page = '';
					//}
				}else {
					$error_message = 'Unable to connect';
					$curl_response = $first_page;
					$url_of_error = 'https://bps.dgvcl.co.in/BillDetail/index.php';
					$post_get_values_of_page = '';
				}
			}else if($consumer_details['discom_id'] == 51 && $consumer_details['discom_code'] == 'PGVCL'){
				//$bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://www.pgvcl.com/consumer/billview/BillHTML.php?cno='.$consumer_id,'',NULL,NULL,1,0);
				//if($bill_page != false){
				if(0){
					if (electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'Invalid or Consumer Number is not found.') == false && electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'Consumer not found. Please check consumer number') == false) { 
						$html = str_get_html($bill_page);
						$table_number_two = $html->find('table',1);
			                        $bill_date = electricity_bill_trim(__LINE__,__FILE__,$table_number_two ->find('tr',3) ->find('td',1)->plaintext); 
			                        if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'/') !== false){
			                            $bill_date = electricity_bill_date_to_database_format_4(__LINE__,__FILE__,$bill_date,'/');	
			                        }else if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'-') !== false){
							$bill_date = electricity_bill_database_date_format(__LINE__,__FILE__,$bill_date);	
						}
						$table_number_five_via = $html->find('table',4);
						$total_td_into_tr = count($table_number_five_via -> find('tr',21) -> find('td'));            
						$bill_amt = electricity_bill_trim(__LINE__,__FILE__,electricity_bill_string_replace(__LINE__,__FILE__,',','',$table_number_five_via ->find('tr',21) ->find('td',$total_td_into_tr - 1)->plaintext));
                           
						$num_rows = 0;
						$check_already_exist_or_not_query = "SELECT fld_ai_id FROM tbl_bills LEFT JOIN tbl_sites ON tbl_sites.fld_ai_internalsite_id = tbl_bills.fld_internalsite_id WHERE tbl_sites.fld_discom_id = '".$pass_discom_id ."' AND fld_generated_date = date('".$bill_date."') AND tbl_sites.fld_consumer_id = '".$consumer_id."' AND tbl_bills.fld_payment_after_due_date = '".$bill_amt."'";
						$check_already_exist_or_not_query_result = electricity_bill_query(__LINE__,__FILE__,$check_already_exist_or_not_query);
						$num_rows = electricity_bill_num_rows(__LINE__,__FILE__,$check_already_exist_or_not_query_result);
						if($num_rows !== 0) {
							$error_message = 'Data already stored into database';
							$curl_response = $bill_page;
							$url_of_error = 'http://www.pgvcl.com/consumer/billview/BillHTML.php?cno='.$consumer_id; 
							$post_get_values_of_page = '';
						} else {
							//$bill_page_extracted = get_string_between(__LINE__,__FILE__,$bill_page,'<html>','</html>');
							$bill_page = electricity_bill_string_replace(__LINE__,__FILE__,'window.print();','',$bill_page);
							$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'image/logo.jpg','http://www.pgvcl.com/consumer/billview/image/logo.jpg',$bill_page);
							$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".html";
							$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
							$save_file_name = $save_file_dir.'/'.$file_name;
							$save_file_name_1 = $save_file_dir.'/'.$site_id.".html";
							$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$save_file_name,$bill_page_extracted);
							if (($save_file_check === false) || ($save_file_check == -1)) {
								die( "Couldn't save the file for ".$consumer_id);
							}else{
								$parse_function = 'parse_gujrat_new';
								$bill_type = 'html';
								$parse_response = parse_gujrat_central($save_file_name,$consumer_details,1,1,$save_file_name_1,$db_file_name,$bill_type,$parse_function);
								if($parse_response['flag'] == '0'){
									$respone_array = $parse_response['response'];	
								}else{
									$error_message = $parse_response['message'];	
								}
							}
						}
					}else {
						$error_message = 'Invalid or Consumer Number is not found.';
						$curl_response = $bill_page;
						$url_of_error = 'http://www.pgvcl.com/consumer/billview/BillHTML.php?cno='.$consumer_id;
						$post_get_values_of_page = "";
					}
				} else {
					#========================================================== NEW CODE ADDED ON 22-06-2023 ==========================================================
					$url = 'https://www.pgvcl.com/';
					$first_page = fetch_page(__LINE__, __FILE__,$pass_discom_id,$url,'', NULL, NULL,0,1);
					if($first_page != false) {
						//if(1){	
						$session_id = get_string_between(__LINE__, __FILE__, $first_page, 'PHPSESSID=', ';');
						$second_page = fetch_page(__LINE__, __FILE__,$pass_discom_id,'https://www.pgvcl.com/consumer/billview/index.php','', NULL, NULL,0,1);
						//exit;
						if($second_page != false && $session_id != '') {
							#2Capture start here
							$g_recaptcha = get_string_between(__LINE__,__FILE__,$second_page,'<div class="g-recaptcha brochure__form__captcha" data-sitekey="','"');
							echo "g_recaptcha : ".$g_recaptcha."<br>";
							$in_reponse = fetch_page(__LINE__,__FILE__,$pass_discom_id,'http://2captcha.com/in.php?key=e72acf92e1a5b092e9e7a5e319beb0af&method=userrecaptcha&googlekey='.$g_recaptcha.'&pageurl=https://www.pgvcl.com/consumer/billview/index.php','',NULL,NULL,0,1,0);
							$temp_array = electricity_bill_explode(__LINE__,__FILE__,"|",electricity_bill_trim(__LINE__,__FILE__,$in_reponse));
							$request_id = $temp_array[count($temp_array)-1];
							echo "request_id : ".$request_id."<br>";
							$res_reponse = "CAPCHA_NOT_READY";
							$while_counter = 0;
							while ("CAPCHA_NOT_READY" == $res_reponse) {
								$res_reponse = fetch_page(__LINE__,__FILE__,$pass_discom_id,'http://2captcha.com/res.php?key=e72acf92e1a5b092e9e7a5e319beb0af&action=get&id='.$request_id,'',NULL,NULL,0,0,0);
								echo "====Captcha Response: ".$res_reponse."====\n";
								sleep(5);
								$while_counter++;
								if ($while_counter == 15) {
									$res_reponse ="";
								}							
							}

							$temp_array = electricity_bill_explode(__LINE__,__FILE__,"|",electricity_bill_trim(__LINE__,__FILE__,$res_reponse));
							$g_recaptcha_response = $temp_array[count($temp_array)-1];
							echo "g_recaptcha_response : ".$g_recaptcha_response."<br>"; 
							#2Capture end here
							if($g_recaptcha_response != '' && $g_recaptcha_response != "CAPCHA_NOT_READY"){
								$headers = array('PHPSESSID=' . $session_id);
								$params = array();
								$params['txtcno'] = $consumer_id;
								$params['g-recaptcha-response'] = $g_recaptcha_response;
								$params['btnsearch'] = 'Search';
								$post_fields = electricity_bill_http_build_query(__LINE__, __FILE__, $params);
								$third_page = fetch_page(__LINE__, __FILE__, $pass_discom_id, 'https://www.pgvcl.com/consumer/billview/index.php', $url, $headers, $post_fields, 1, 1);
								if($third_page != false) {
									$tid = electricity_bill_trim(__LINE__,__FILE__,get_string_between(__LINE__,__FILE__,$third_page,'gprsBillDisplay.php?tid=','"'));
									if($tid != '') {
										$bill_page = fetch_page(__LINE__, __FILE__, $pass_discom_id, 'https://www.pgvcl.com/consumer/billview/gprsBillDisplay.php?tid='.$tid, $url, $headers, NULL, 1, 1);
										if($bill_page != false) {
											$temp_html = get_string_between(__LINE__,__FILE__,$bill_page,'<html lang="en">','</html>');
											$bill_page_extracted = '<html lang="en">'.$temp_html.'</html>';
											$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'window.print();','',$bill_page_extracted);
											//$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'http://172.22.2.45:8120/images/Logo_UGVCL.png','https://ugvcl.info/UGBILL/image/logo.jpg',$bill_page_extracted);
											$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".html";
											$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
											$save_file_name = $save_file_dir.'/'.$file_name;
											$save_file_name_1 = $save_file_dir.'/'.$site_id.".html";
											$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$save_file_name,$bill_page_extracted);
											if (($save_file_check === false) || ($save_file_check == -1)) {
												die( "Couldn't save the file for ".$consumer_id);
											}else{
												//echo 'File Save Successfully';
												$parse_function = 'parse_gujrat_new_2';
												$bill_type = 'html';
												$parse_response = parse_gujrat_central($save_file_name,$consumer_details,1,1,$save_file_name_1,$db_file_name,$bill_type,$parse_function);
												if($parse_response['flag'] == '0'){
													$respone_array = $parse_response['response'];	
												}else{
													$error_message = $parse_response['message'];	
												}
											}
										}else {
											$return_flag['message'] = 'Unable to get bill html';
										}
									}else {
										$return_flag['message'] = 'Unable to get tid';
									}
								}else {
									$return_flag['message'] = 'Unable to get consumer details';
								}
							}else {
								$return_flag['message'] = 'Invalid Capture';
							}
						}else {
							$return_flag['message'] = 'Unable to get session';
						}
					}else {
						if(0){
							$cookie_text = fetch_page(__LINE__,__FILE__,10000000,'https://capriglobal.billpro.online/pgvcl_cookie.txt','',NULL,NULL,0,0);
							
							$headers = array($cookie_text);
							var_dump($headers);
							#$headers = array('Cookie: PHPSESSID=q5e1eiv6reh57s4k922m5fjs04; member_login=guifp');
							$params = array();
							$params['idConsumerNo'] = $consumer_id;
							$params['fromYr'] = date('Y', strtotime('last year'));
							$params['fromMnth'] = 01;
							$params['toYr'] = date("Y");
							$params['toMnth'] = date("m")+1;
							$post_fields = electricity_bill_http_build_query(__LINE__, __FILE__, $params);
							$third_page = fetch_page(__LINE__, __FILE__, $pass_discom_id, 'https://portal.guvnl.in/viewBillHistory.php','', $headers, $post_fields, 1, 1);
							$tid = get_string_between(__LINE__,__FILE__,$third_page,'<a href="gprsBillDisplay.php?tid=','&v=');
							if($tid != '') {
								$html = str_get_html($third_page);
								$table_number_one = $html->find('table', 30);
						
								if(strchr($table_number_one->plaintext,'There is no account associated with') || substr_count($table_number_one->plaintext ,'View Bill') > 1){
									$table_number_one = $html->find('table', 31);
								}
								if( electricity_bill_find_position(__LINE__,__FILE__,trim($table_number_one->find('tr',2)->plaintext),'There is no account associated with your profile. You can add your HT/LT accounts using Add HT/LT Account button in Manage Accounts.') !== false){
									$table_number_one = $html->find('table', 32);
								}
								if(isset($table_number_one->find('tr',2)->plaintext)){
									$table_tr_1 =  $table_number_one->find('tr',2);
									if(isset($table_number_one->find('tr',2)->plaintext)){
										$bill_date = electricity_bill_trim(__LINE__, __FILE__,$table_tr_1->find('td',2)->plaintext);
										if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'/') !== false){
											$bill_date = electricity_bill_date_to_database_format_3(__LINE__,__FILE__,$bill_date,'/');	
										}else if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'-') !== false){
											$bill_date = electricity_bill_date_to_database_format_3(__LINE__,__FILE__,$bill_date,'-');	
										}
										$bill_amount = electricity_bill_trim(__LINE__, __FILE__,$table_tr_1->find('td',6)->plaintext);
										if($bill_date != '' && $bill_amount != '' && $third_page != false ){
											echo $check_already_exist_or_not_query = "SELECT fld_ai_id FROM tbl_bills LEFT JOIN tbl_sites ON tbl_sites.fld_ai_internalsite_id = tbl_bills.fld_internalsite_id WHERE tbl_sites.fld_discom_id = '".$pass_discom_id ."' AND fld_generated_date = date('".$bill_date."') AND tbl_sites.fld_consumer_id = '".$consumer_id."' AND tbl_bills.fld_payment_after_due_date = '".$bill_amount."'";
											$check_already_exist_or_not_query_result = electricity_bill_query(__LINE__,__FILE__,$check_already_exist_or_not_query);
											$num_rows = electricity_bill_num_rows(__LINE__,__FILE__,$check_already_exist_or_not_query_result);
											if($num_rows !== 0) {
												$error_message = 'Data already stored into database';
												$curl_response = $third_page;
												$url_of_error = 'https://portal.guvnl.in/viewBillHistory.php';
												$post_get_values_of_page = '';
											} else {
												//echo 'https://www.pgvcl.com/consumer/billview/gprsBillDisplay.php?tid='.$tid;
												$tid = base64_decode($tid);
												$bill_page = fetch_page(__LINE__, __FILE__, $pass_discom_id, 'https://www.pgvcl.com/consumer/billview/gprsBillDisplay.php?tid='.$tid, $url, $headers, NULL, 1, 1);
												if($bill_page != false) {
													$temp_html = get_string_between(__LINE__,__FILE__,$bill_page,'<html lang="en">','</html>');
													$bill_page_extracted = '<html lang="en">'.$temp_html.'</html>';
													$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'window.print();','',$bill_page_extracted);
													//$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'http://172.22.2.45:8120/images/Logo_UGVCL.png','http://www.pgvcl.com/consumer/billview/image/logo.jpg',$bill_page_extracted);
													$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".html";
													$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
													$save_file_name = $save_file_dir.'/'.$file_name;
													$save_file_name_1 = $save_file_dir.'/'.$site_id.".html";
													$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$save_file_name,$bill_page_extracted);
													if (($save_file_check === false) || ($save_file_check == -1)) {
														die( "Couldn't save the file for ".$consumer_id);
													}else{
														//echo 'File Save Successfully';
														$parse_function = 'parse_gujrat_new_2';
														$bill_type = 'html';
														$parse_response = parse_gujrat_central($save_file_name,$consumer_details,1,1,$save_file_name_1,$db_file_name,$bill_type,$parse_function);
														if($parse_response['flag'] == '0'){
															$respone_array = $parse_response['response'];	
														}else{
															$error_message = $parse_response['message'];	
														}
													}
												}else {
													$error_message = 'Unable to get bill html';
													$curl_response = $bill_page;
													$url_of_error = 'https://www.pgvcl.com/consumer/billview/gprsBillDisplay.php';
													$post_get_values_of_page = "";
												}
											}
										}else {
											$error_message = 'Invalid Consumer Number Or Latest Bill Information not available.';
											$curl_response = $third_page;
											$url_of_error = 'https://www.pgvcl.com/consumer/billview/index.php';
											$post_get_values_of_page = "";
										}
									}else{
										$error_message = 'Unable to get tr from bill history';
										$curl_response = $third_page;
										$url_of_error = 'https://www.pgvcl.com/consumer/billview/index.php';
										$post_get_values_of_page = "";
									}
								}else{
									$error_message = 'Unable to get bill history';
									$curl_response = $third_page;
									$url_of_error = 'https://www.pgvcl.com/consumer/billview/index.php';
									$post_get_values_of_page = "";
								}
							}else{
								$error_message = 'Unable to login or Please check consumer number';
								$curl_response = $third_page;
								$url_of_error = 'https://www.pgvcl.com/consumer/billview/index.php';
								$post_get_values_of_page = "";	
							}
							// end of if(0)-2
						}
						$first_page = fetch_page(__LINE__, __FILE__, $pass_discom_id, 'https://portal.guvnl.in/services/validateConsumer.php?discom=PGVCL&consumer_no='.$consumer_id,'', NULL, NULL, 0, 0);
						if($first_page != FALSE){
							$post_fields = '{
								"consumer_no":"'.$consumer_id.'",
								"discom": "PGVCL"
							}';
							$bill_status = json_decode($first_page, true);
							if($bill_status['cons_status'] == 'ACTIVE'){
								$fetch_login_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://portal.guvnl.in/services/encryptCust.php', 'https://portal.guvnl.in/login.php', NULL, $post_fields,0,0);
								if($fetch_login_page != FALSE){
									$bill_data = json_decode($fetch_login_page, true);
									$bill_page = fetch_page(__LINE__, __FILE__, $pass_discom_id, 'https://www.pgvcl.com/consumer/billview/gprsBillDisplay.php?tid='.$bill_data['xid'],'', NULL, NULL, 0, 1);
									if($bill_page != FALSE){
										$temp_html = get_string_between(__LINE__,__FILE__,$bill_page,'<html lang="en">','</html>');
										$bill_page_extracted = '<html lang="en">'.$temp_html.'</html>';
										$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'window.print();','',$bill_page_extracted);
										//$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'http://172.22.2.45:8120/images/Logo_UGVCL.png','http://www.pgvcl.com/consumer/billview/image/logo.jpg',$bill_page_extracted);
										$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,array('href="css/style.css"' ,'href="css/form_style.css"','href="css/images/favicon.ico"','src="SpryAssets/SpryMenuBar.js"','href="SpryAssets/SpryMenuBarHorizontal.css"'),'',$bill_page_extracted);	
										$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".html";
										$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
										$save_file_name = $save_file_dir.'/'.$file_name;
										$save_file_name_1 = $save_file_dir.'/'.$site_id.".html";
										$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$save_file_name,$bill_page_extracted);
										if (($save_file_check === false) || ($save_file_check == -1)) {
											die( "Couldn't save the file for ".$consumer_id);
										}else{
											//echo 'File Save Successfully';
											$parse_function = 'parse_gujrat_new_2';
											$bill_type = 'html';
											$parse_response = parse_gujrat_central($save_file_name,$consumer_details,1,1,$save_file_name_1,$db_file_name,$bill_type,$parse_function);
											if($parse_response['flag'] == '0'){
												$respone_array = $parse_response['response'];	
											}else{
												$error_message = $parse_response['message'];	
											}
										}
									}else{
										$error_message = 'Unable to Get Bill';
										$curl_response = $bill_page;
										$url_of_error = 'https://www.pgvcl.com/consumer/billview/gprsBillDisplay.php?tid='.$bill_data['xid'];
										$post_get_values_of_page = "";
									}
								}else{
									$error_message = 'Unable to Bill Details';
									$curl_response = $fetch_login_page;
									$url_of_error = 'https://portal.guvnl.in/services/encryptCust.php';
									$post_get_values_of_page = "";
								}
							}else{
								$error_message = $bill_status['cons_status'] .' Or Invalid consumer no ';
								$curl_response = $first_page;
								$url_of_error = 'https://portal.guvnl.in/services/validateConsumer.php?discom=PGVCL&consumer_no='.$consumer_id;
								$post_get_values_of_page = "";
							}
						}else{
							$error_message = 'Unable to get Consumer Details';
							$curl_response = $first_page;
							$url_of_error = 'https://portal.guvnl.in/services/validateConsumer.php?discom=PGVCL&consumer_no='.$consumer_id;
							$post_get_values_of_page = "";
						}
					}
					#========================================================== NEW CODE ADDED ON 22-06-2023 ==========================================================
				}
			}else if($consumer_details['discom_id'] == 52 && $consumer_details['discom_code'] == 'UGVCL'){
				//echo 'http://ugvcl.info/UGBILL/BillHTML.php?cno='.$consumer_id;
				$bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://ugvcl.info/UGBILL/BillHTML.php?cno='.$consumer_id,'',NULL,NULL,0,0);
				if($bill_page != false){
					if (electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'Invalid or Consumer Number is not found.') == false) {
						$html = str_get_html($bill_page);
						$table_number_two = $html->find('table',1);
						$bill_date = electricity_bill_trim(__LINE__,__FILE__,$table_number_two ->find('tr',3) ->find('td',1)->plaintext); 
						if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'/') !== false){
							$bill_date = electricity_bill_date_to_database_format_4(__LINE__,__FILE__,$bill_date,'/');	
						}else if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'-') !== false){
							$bill_date = electricity_bill_database_date_format(__LINE__,__FILE__,$bill_date);	
						}
						$table_number_five_via = $html->find('table',4);
						$total_td_into_tr = count($table_number_five_via -> find('tr',21) -> find('td'));            
						$bill_amt = electricity_bill_trim(__LINE__,__FILE__,$table_number_five_via ->find('tr',21) ->find('td',$total_td_into_tr - 1)->plaintext);
						$num_rows = 0;
						$check_already_exist_or_not_query = "SELECT fld_ai_id FROM tbl_bills LEFT JOIN tbl_sites ON tbl_sites.fld_ai_internalsite_id = tbl_bills.fld_internalsite_id WHERE tbl_sites.fld_discom_id = '".$pass_discom_id ."' AND fld_generated_date = date('".$bill_date."') AND tbl_sites.fld_consumer_id = '".$consumer_id."' AND tbl_bills.fld_payment_after_due_date = '".$bill_amt."'";
						$check_already_exist_or_not_query_result = electricity_bill_query(__LINE__,__FILE__,$check_already_exist_or_not_query);
						$num_rows = electricity_bill_num_rows(__LINE__,__FILE__,$check_already_exist_or_not_query_result);
						if($num_rows !== 0) {
							$error_message = 'Data already stored into database';
							$curl_response = $bill_page;
							$url_of_error = 'https://ugvcl.info/UGBILL/BillHTML.php?cno='.$consumer_id; 
							$post_get_values_of_page = '';
						} else {
							//$bill_page_extracted = get_string_between(__LINE__,__FILE__,$bill_page,'<html>','</html>');
							$bill_page = electricity_bill_string_replace(__LINE__,__FILE__,'window.print();','',$bill_page);
							$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'image/logo.jpg','https://ugvcl.info/UGBILL/image/logo.jpg',$bill_page);
							$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".html";
							$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
							$save_file_name = $save_file_dir.'/'.$file_name;
							$save_file_name_1 = $save_file_dir.'/'.$site_id.".html";
							$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$save_file_name,$bill_page_extracted);
							if (($save_file_check === false) || ($save_file_check == -1)) {
								die( "Couldn't save the file for ".$consumer_id);
							}else{
								$parse_function = 'parse_gujrat_new';
								$bill_type = 'html';
								$parse_response = parse_gujrat_central($save_file_name,$consumer_details,1,1,$save_file_name_1,$db_file_name,$bill_type,$parse_function);
								if($parse_response['flag'] == '0'){
									$respone_array = $parse_response['response'];	
								}else{
									$error_message = $parse_response['message'];	
								}
							}
						}
					}else {
						$error_message = 'Invalid or Consumer Number is not found.';
						$curl_response = $bill_page;
						$url_of_error = 'http://ugvcl.info/UGBILL/BillHTML.php?cno='.$consumer_id;
						$post_get_values_of_page = "";
					}
				} else {
					$error_message = 'Unable to get Bill Page';
					$curl_response = $bill_page;
					$url_of_error = 'http://ugvcl.info/UGBILL/BillHTML.php?cno='.$consumer_id;
					$post_get_values_of_page = "";
				}
			}else if($consumer_details['discom_id'] == 50 && $consumer_details['discom_code'] == 'MGVCL'){
				$new_call = 0;
				$bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://mgvcl.co.in:8085/ebill/BillHTML.php?cno='.$consumer_id,'https://mgvcl.co.in:8085/dash_billingInfo',NULL,NULL,0,0);
			
				if($bill_page != false){
					if (electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'Invalid or Consumer Number is not found.') == false) {
						if (electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'Data uploading is in process') == false) {
							$html = str_get_html($bill_page);
							if($html->plaintext != ''){
								$table_number_two = $html->find('table',1);
								$bill_date = electricity_bill_trim(__LINE__,__FILE__,$table_number_two ->find('tr',3) ->find('td',1)->plaintext); 
								if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'/') !== false){
									$bill_date = electricity_bill_date_to_database_format_4(__LINE__,__FILE__,$bill_date,'/');	
								}else if(electricity_bill_find_position(__LINE__,__FILE__,$bill_date,'-') !== false){
									$bill_date = electricity_bill_database_date_format(__LINE__,__FILE__,$bill_date);	
								}
								$table_number_five_via = $html->find('table',4);
								$total_td_into_tr = count($table_number_five_via -> find('tr',21) -> find('td'));            
								$bill_amt = electricity_bill_trim(__LINE__,__FILE__,electricity_bill_string_replace(__LINE__,__FILE__,',','',$table_number_five_via ->find('tr',21) ->find('td',$total_td_into_tr - 1)->plaintext));
                        
								$num_rows = 0;
								$check_already_exist_or_not_query = "SELECT fld_ai_id FROM tbl_bills LEFT JOIN tbl_sites ON tbl_sites.fld_ai_internalsite_id = tbl_bills.fld_internalsite_id WHERE tbl_sites.fld_discom_id = '".$pass_discom_id ."' AND fld_generated_date >= date('".$bill_date."') AND tbl_sites.fld_consumer_id = '".$consumer_id."' AND tbl_bills.fld_payment_after_due_date = '".$bill_amt."'";
								$check_already_exist_or_not_query_result = electricity_bill_query(__LINE__,__FILE__,$check_already_exist_or_not_query);
								$num_rows = electricity_bill_num_rows(__LINE__,__FILE__,$check_already_exist_or_not_query_result);
								if($num_rows !== 0) {
									$error_message = 'Data already stored into database';
									$curl_response = $bill_page;
									$url_of_error = 'https://mgvcl.co.in:8085/ebill/BillHTML.php?cno='.$consumer_id; 
									$post_get_values_of_page = '';
									//$new_call = 1;
								} else {
									$bill_page = electricity_bill_string_replace(__LINE__,__FILE__,'window.print();','',$bill_page);
									$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'image/logo.jpg','https://mgvcl.co.in:8085/ebill/image/logo.jpg',$bill_page);
							
									$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".html";
									$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill_page_extracted);
									if (($save_file_check === false) || ($save_file_check == -1)) {
										$return_flag['message'] = "Unable to save bill";
									}else{									
										$return_flag['message'] = "Bill downloaded successfully";
										$return_flag['file_path'] = $file_name;
									}
								}
							}else {
								$return_flag['message'] = 'No bill Found';
							}
						}else {
							$return_flag['message'] = 'Data uploading is in process..Please try again after 24 hrs';
							if($error_flag_same_bill == true){
								$return_flag['message'] = 'Data already stored into database';
							}
						}
					}else {
						$return_flag['message'] = 'Invalid or Consumer Number is not found.';
					}
				} else {
					$return_flag['message'] = 'Unable to get Bill Page';
				}
				if($new_call){
					echo '<br>New Calling';
					$error_message = '';
					$first_page = fetch_page(__LINE__, __FILE__, $pass_discom_id, 'https://portal.guvnl.in/services/validateConsumer.php?discom=MGVCL&consumer_no='.$consumer_id,'', NULL, NULL, 0, 0);
					if($first_page != FALSE){
						$post_fields = '{
							"consumer_no":"'.$consumer_id.'",
							"discom": "MGVCL"
						}';
						$bill_status = json_decode($first_page, true);
						if($bill_status['cons_status'] == 'ACTIVE'){
							echo $fetch_login_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://portal.guvnl.in/services/encryptCust.php', 'https://portal.guvnl.in/login.php', NULL, $post_fields,0,0);
							//var_dump($fetch_login_page);exit;
							if($fetch_login_page != FALSE){
								$bill_data = json_decode($fetch_login_page, true);
								$bill_page = fetch_page(__LINE__, __FILE__, $pass_discom_id, 'https://portal.guvnl.in/chatbot14/chatbot13/public/gprsBillDisplay.php?discom=MGVCL&xid='.$bill_data['xid'],'', NULL, NULL, 0, 1);
								if($bill_page != FALSE){
									$temp_html = get_string_between(__LINE__,__FILE__,$bill_page,'<html lang="en">','</html>');
									$bill_page_extracted = '<html lang="en">'.$temp_html.'</html>';
									$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'window.print();','',$bill_page_extracted);
									//$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,'http://172.22.2.45:8120/images/Logo_UGVCL.png','http://www.pgvcl.com/consumer/billview/image/logo.jpg',$bill_page_extracted);
									$bill_page_extracted = electricity_bill_string_replace(__LINE__,__FILE__,array('href="css/style.css"' ,'href="css/form_style.css"','href="css/images/favicon.ico"','src="SpryAssets/SpryMenuBar.js"','href="SpryAssets/SpryMenuBarHorizontal.css"'),'',$bill_page_extracted);	
									$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".html";
									$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill_page_extracted);
									if (($save_file_check === false) || ($save_file_check == -1)) {
										$return_flag['message'] = "Unable to save bill";
									}else{									
										$return_flag['message'] = "Bill downloaded successfully";
										$return_flag['file_path'] = $file_name;
									}
								}else{
									$return_flag['message'] = 'Unable to Get Bill';
								}
							}else{
								$return_flag['message'] = 'Unable to Bill Details';
							}
						}else{
							$return_flag['message'] = $bill_status['cons_status'] .' Or Invalid consumer no ';
						}
					}else{
						$return_flag['message'] = 'Unable to get Consumer Details';
					}
				}
			}
		}
	
			return $return_flag;
	}
	
?>
