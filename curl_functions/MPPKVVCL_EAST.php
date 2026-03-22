<?php
	function get_details_MPPKVVCL_EAST($consumer_details,$is_cron=0,$from_flag = 0) {
		define('CAPTCHA_API_KEY',"e72acf92e1a5b092e9e7a5e319beb0af");
		$consumer_id = $consumer_details['consumer_id'];
		// $site_id = $consumer_details['site_id'];
		//$pass_discom_id = $consumer_details['discom_id'];
		$return_flag = array();
		$respone_array = array();
		$bill_no = "";
		$amount_after_due_date = "";
		$amount_before_due_date = "";
		$due_date = "";
		$bill_date = "";
		$bill_month = "";
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
		$pass_discom_id = 19;
		$consumer_details['discom_id'] = $pass_discom_id;
		$file_name = '';


		#Check New bill available or not START here
		
		
		$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://mpeb.mponline.gov.in/portal/Services/MPEZ/RMS/DashboardMPPKVV.aspx', '', NULL, NULL);
		if(electricity_bill_find_position(__LINE__,__FILE__,$first_page,'ASP.NET_SessionId=') !== false) {
			$session_id = get_string_between(__LINE__,__FILE__,$first_page,'ASP.NET_SessionId=',';');
			$header = array('Cookie:ASP.NET_SessionId='.$session_id);
			$__EVENTTARGET = get_string_between(__LINE__,__FILE__,$first_page,'<input type="hidden" name="__EVENTTARGET" id="__EVENTTARGET" value="','"');
			$__EVENTARGUMENT = get_string_between(__LINE__,__FILE__,$first_page,'<input type="hidden" name="__EVENTARGUMENT" id="__EVENTARGUMENT" value="','"');
			$__VIEWSTATE = get_string_between(__LINE__,__FILE__,$first_page,'<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="','"');
			$__VIEWSTATEGENERATOR = get_string_between(__LINE__,__FILE__,$first_page,'<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="','"');
			$__VIEWSTATEENCRYPTED = get_string_between(__LINE__,__FILE__,$first_page,'<input type="hidden" name="__VIEWSTATEENCRYPTED" id="__VIEWSTATEENCRYPTED" value="','"');
			$data_array['__EVENTTARGET'] = 'btnSubmit';
			$data_array['__EVENTARGUMENT'] = $__EVENTARGUMENT;
			$data_array['__VIEWSTATE'] = $__VIEWSTATE;
			$data_array['__VIEWSTATEGENERATOR'] = $__VIEWSTATEGENERATOR;
			$data_array['__VIEWSTATEENCRYPTED'] = $__VIEWSTATEENCRYPTED;
			$data_array['txtIvrsNo'] = electricity_bill_trim(__LINE__,__FILE__,electricity_bill_string_replace(__LINE__,__FILE__,'N','',$consumer_id));
			$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$data_array);
			$login_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://mpeb.mponline.gov.in/portal/Services/MPEZ/RMS/DashboardMPPKVV.aspx', 'https://mpeb.mponline.gov.in/portal/Services/MPEZ/RMS/DashboardMPPKVV.aspx',$header,$post_fields,1,0,1,1,1);
			$check_alert =  electricity_bill_trim(__LINE__,__FILE__,electricity_bill_string_replace(__LINE__,__FILE__,'"','',get_string_between(__LINE__,__FILE__,$login_page,"alert(",")")));
			if( $check_alert != 'Please Enter valid IVRS ID.') {
				if($check_alert != 'Data Not Found on Server' && electricity_bill_find_position(__LINE__,__FILE__,$login_page,'Enter IVRS ID and Get your Bill Details') == false && $login_page != false){
				$html = str_get_html($login_page);
				$bill_consumer_id = electricity_bill_trim(__LINE__,__FILE__,$html->find('span[@id="lblAccountID"]',0)->plaintext);
				$due_date = electricity_bill_trim(__LINE__,__FILE__,$html->find('span[@id="lblCashDate"]',0)->plaintext);
				if(electricity_bill_find_position(__LINE__,__FILE__,$due_date,'/') !== false){
					$due_date = electricity_bill_date_to_database_format_4(__LINE__,__FILE__,$due_date,'/');	
				}else if(electricity_bill_find_position(__LINE__,__FILE__,$due_date,'-') !== false){
					$due_date = electricity_bill_date_to_database_format_4(__LINE__,__FILE__,$due_date,'-');	
				}
				$lblMonth = electricity_bill_trim(__LINE__,__FILE__,$html->find('span[@id="lblMonth"]',0)->plaintext);
				$temp_arr = electricity_bill_explode(__LINE__,__FILE__,"-",electricity_bill_trim(__LINE__,__FILE__,$lblMonth));
				$bill_month = electricity_bill_trim(__LINE__,__FILE__,$temp_arr[0]);
				$bill_year = electricity_bill_trim(__LINE__,__FILE__,$temp_arr[1]);
				if($bill_consumer_id == $consumer_id || "N".$bill_consumer_id == $consumer_id){
					$fetch_login_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://billing.mpez.co.in/', '', NULL, NULL);
					if(electricity_bill_find_position(__LINE__,__FILE__,$fetch_login_page,'JSESSIONID=') !== false) {
						$session_id = get_string_between(__LINE__,__FILE__,$fetch_login_page,'JSESSIONID=',';');
						$header = array('Cookie:JSESSIONID='.$session_id.' path=/; HttpOnly');
						$g_recaptcha = get_string_between(__LINE__,__FILE__,$fetch_login_page,'data-sitekey="','"');
						if($g_recaptcha != ''){
							$in_reponse = fetch_page(__LINE__,__FILE__,$pass_discom_id,'http://2captcha.com/in.php?key='.CAPTCHA_API_KEY.'&method=userrecaptcha&googlekey='.$g_recaptcha.'&pageurl=https://billing.mpez.co.in/','',NULL,NULL,0,1,0);
							$temp_array = electricity_bill_explode(__LINE__,__FILE__,"|",electricity_bill_trim(__LINE__,__FILE__,$in_reponse));
							$request_id = $temp_array[count($temp_array)-1];
							sleep(60);
							$res_reponse = fetch_page(__LINE__,__FILE__,$pass_discom_id,'http://2captcha.com/res.php?key='.CAPTCHA_API_KEY.'&action=get&id='.$request_id,'',NULL,NULL,0,0,0);
							$temp_array = electricity_bill_explode(__LINE__,__FILE__,"|",electricity_bill_trim(__LINE__,__FILE__,$res_reponse));
							$g_recaptcha_response = $temp_array[count($temp_array)-1];
							if($g_recaptcha_response == 'CAPCHA_NOT_READY'){
								sleep(60);
								$res_reponse = fetch_page(__LINE__,__FILE__,$pass_discom_id,'http://2captcha.com/res.php?key='.CAPTCHA_API_KEY.'&action=get&id='.$request_id,'',NULL,NULL,0,0,0);
								$temp_array = electricity_bill_explode(__LINE__,__FILE__,"|",electricity_bill_trim(__LINE__,__FILE__,$res_reponse));
								$g_recaptcha_response = $temp_array[count($temp_array)-1];
							}
						}
						$data_array = array();
						$data_array['ivrs'] = electricity_bill_trim(__LINE__,__FILE__,$consumer_id);
						$data_array['g-recaptcha-response'] = $g_recaptcha_response;
						$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$data_array);
						$login_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://billing.mpez.co.in/GetInsBill', 'https://billing.mpez.co.in/',$header,$post_fields,1,1,1);
						$session_id = get_string_between(__LINE__,__FILE__,$login_page,'JSESSIONID=',';');
						$header = array('Cookie:JSESSIONID='.$session_id);
						$location = electricity_bill_trim(__LINE__,__FILE__,get_string_between(__LINE__,__FILE__,$login_page,'Location:','Content'));
						if($location != ''){
							$dashboard_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://billing.mpez.co.in/'.$location, 'https://billing.mpez.co.in/',$header,NULL);
							if($dashboard_page != false) {
								if(strchr($dashboard_page,'Consumer Information')){
										$ivrs_dashboard = get_string_between(__LINE__,__FILE__,$dashboard_page,"name='ivrs' value=","/>");
										$txtDate_dashboard = get_string_between(__LINE__,__FILE__,$dashboard_page,"name='txtDate' value=","/>");
										if($ivrs_dashboard != '' && $txtDate_dashboard != ''){
											$data_array = array();
											$data_array['ivrs'] = $ivrs_dashboard;
											$data_array['txtDate'] = $txtDate_dashboard;
											$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$data_array);
											$bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://billing.mpez.co.in/NGBReport','https://billing.mpez.co.in/'.$location,$header,$post_fields,0,0);
											if($bill_page != FALSE) {
												if(electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'%PDF') !== false){
													$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
													$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill_page);
													if (($save_file_check === false) || ($save_file_check == -1)) {
														$return_flag['message'] = "Unable to save bill";
													}else{									
														$return_flag['message'] = "Bill downloaded successfully";
														$return_flag['file_path'] = $file_name;
													}
												}else{
													$return_flag['message'] = "Please contact to DevTeam";
												}
											}  else {
												$return_flag['message'] = "Error in loading bill pdf";
											}
										}  else {
											$return_flag['message'] = "Internal ivrs and txtDate not found";
										}
								} else {
									$return_flag['message'] = "Unable to find table";
								}
							} else {
								$return_flag['message'] = "Unable to get dashboard page";
							}
						} else {
							$return_flag['message'] = "Unable to get Location";
						}
					}else {
						$return_flag['message'] = 'Unable to connect3';
					}
			
					if($return_flag['message'] != ''){
						$username	=	'mpez@atc.myelectricity.co.in';
						$password	=	'Atc@12345';	
						$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://consolidatedbill.mpez.co.in/index.jsp', '', NULL, NULL);
						if($first_page != false){
							$session_id = get_string_between(__LINE__,__FILE__,$first_page,'JSESSIONID=',';');
							$header = array('Cookie: JSESSIONID='.$session_id.';');
							$params = array();
							$params['username'] = $username;
							$params['password'] = $password;
							$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$params);
							$login_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://consolidatedbill.mpez.co.in/LoginChk', 'https://consolidatedbill.mpez.co.in/',$header,$post_fields,1,1,1);
							if($login_page != false){
								$dashboard_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://consolidatedbill.mpez.co.in/home.jsp', 'https://consolidatedbill.mpez.co.in/',$header,NULL,1,1,1);
								$salt = get_string_between(__LINE__,__FILE__,$dashboard_page,'id="salt" value=','>');
								$params = array();
								$params['ivrs'] = electricity_bill_trim(__LINE__,__FILE__,electricity_bill_string_replace(__LINE__,__FILE__,'N','',$consumer_id)).'/';
								$bill_month_number = get_month_number(__LINE__,__FILE__,$bill_month);
								$params['txtDate'] = $bill_year.$bill_month_number.'01/';
								$params['csrfPreventionSalt'] = $salt;
								$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$params);
								$fetch_bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://consolidatedbill.mpez.co.in/NGBReport', 'https://consolidatedbill.mpez.co.in/',$header,$post_fields,0,0,0);
								if(electricity_bill_find_position(__LINE__,__FILE__,$fetch_bill_page,'%PDF') !== false) {
									$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
										$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$fetch_bill_page);
										if (($save_file_check === false) || ($save_file_check == -1)) {
											$return_flag['message'] = "Unable to save bill";
										}else{									
											$return_flag['message'] = "Bill downloaded successfully";
											$return_flag['file_path'] = $file_name;
										}
								}else{
									$return_flag['message'] = 'Invalid PDF';
								}		
							}else {
								$return_flag['message'] = 'Unable to login';
							}
						}else{
							$return_flag['message'] = 'Unable to connect';
						}
					}else{
						if (isset($return_flag['file_path'])) {
							$return_flag['bill_date'] = $bill_date;
							$return_flag['due_date'] = $due_date;
							$return_flag['amount_before_due_date'] = $amount_before_due_date;
							$return_flag['amount_after_due_date'] = $amount_after_due_date;
							$return_flag['bill_no'] = $bill_no;
							return $return_flag;
						}
						$return_flag['message'] = "Need code updation, please contact dev team.";
					}
				}else{
					$return_flag['message'] = "Consumer ID updated to : N".$bill_consumer_id;
				}
			}
			}else{
				$return_flag['message'] = "Please Enter valid IVRS ID.";
			}
	}else{
		$return_flag['message'] = 'Unable to connect';
	}

		$return_flag['bill_date'] = $bill_date;
		$return_flag['due_date'] = $due_date;
		$return_flag['amount_before_due_date'] = $amount_before_due_date;
		$return_flag['amount_after_due_date'] = $amount_after_due_date;
		$return_flag['bill_no'] = $bill_no;

		return $return_flag;
	}

	
?>