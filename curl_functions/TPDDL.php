<?php
	function get_details_TPDDL($consumer_details,$is_cron=0,$from_flag = 0) {
		$consumer_id = $consumer_details['consumer_id'];
		// $site_id = $consumer_details['site_id'];
		
		$pass_discom_id = $consumer_details['discom_id'] = 148;
		$return_flag = array();
		$respone_array = array();
		$error_message = '';
		$pass_discom_id = 148;
		$consumer_details['discom_id'] = $pass_discom_id;
		$file_name = '';
		$bill_no = "";
		$amount_after_due_date = "";
		$amount_before_due_date = "";
		$due_date = "";
		$bill_date = "";
		$bill_month = "";
		$domain = $consumer_details['client'];
		$session_url = get_session_url($domain);

		if ($session_url != false) {
			$session_user_id = get_string_between(__LINE__,__FILE__,$session_url,'user_id=',';');
			$session_account_no = get_string_between(__LINE__,__FILE__,$session_url,'account_no=',';');
			$session_session_id = get_string_between(__LINE__,__FILE__,$session_url,'session_id=',';');
			$session_authorization = get_string_between(__LINE__,__FILE__,$session_url,'authorization=',';');
			$proxy_var = '159.89.167.246:8086';
			
			$first_chr = substr($consumer_id, 0,1);
			if($first_chr == '0'){
				$consumer_id = substr($consumer_id,1);	
			}

			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://tatapower-prod.azure-api.net/TPDDL-API/Billing/GetBill',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'{"LanguageCode":"EN","request_number":"","UserID":"'.$session_user_id.'","IsBillPDF":"0","UtilityAccountNumber":"0'.$consumer_id.'","bill_type":"11","IsDashboard":0,"AccountType":"Postpaid","AccountNumber":"'.$session_account_no.'"}',
		  	// CURLOPT_PROXY => $proxy_var,
			CURLOPT_HTTPHEADER => array(
				    'Connection: close',
					'Authorization: '.$session_authorization,
				'Sourcetype: 1',
					'Sessionid: '.$session_session_id,
				'Utilityid: 5',
				    'platform: Android / v2.20 / 13 / WIFI / 720x480',
				'Content-Type: application/json',
				    'Accept-Encoding: gzip',
				'User-Agent: okhttp/4.9.0',
				'Cookie: ApplicationGatewayAffinity=bf31b527b1fb5a2b66ac7679228aa726; ApplicationGatewayAffinityCORS=bf31b527b1fb5a2b66ac7679228aa726'
			),
			CURLOPT_SSL_VERIFYPEER => false, 
			));
			$response = curl_exec($curl);
			curl_close($curl);
			$bill_history = json_decode($response,true);

			if(isset($bill_history['result'])){
				if(isset($bill_history['result']['Table1'])){
					if(isset($bill_history['result']['Table1']['billno'])){
						$bill_no = $bill_history['result']['Table1']['billno'];
						if ($bill_no!= '') {
							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_URL => 'https://tatapower-prod.azure-api.net/TPDDL-API/Billing/GetBillHistoryPdf',
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'POST',
								CURLOPT_POSTFIELDS =>'{"LanguageCode":"EN","BillingId":"'.$bill_no.'","IsBillPDF":"1","UserID":"'.$session_user_id.'","UtilityAccountNumber":"0'.$consumer_id.'","AccountNumber":"'.$session_account_no.'"}',
								// CURLOPT_PROXY => $proxy_var,
								CURLOPT_HTTPHEADER => array(
								'Host: tatapower-prod.azure-api.net',
								'Sourcetype: 1',
								'Sessionid: '.$session_session_id,
								'Utilityid: 5',
								'Platform: Android / v2.16 / 8 / WIFI',
								'User-Agent: okhttp/4.9.0',
								'Authorization: '.$session_authorization,
								'Content-Type: application/json',
								'Content-Length: 146',
								'Connection: close',
								'Origin: foo.example.org',
								'Cookie: ApplicationGatewayAffinity=bf31b527b1fb5a2b66ac7679228aa726; ApplicationGatewayAffinityCORS=bf31b527b1fb5a2b66ac7679228aa726'
								),
								CURLOPT_SSL_VERIFYPEER => false, 

							));
							$pdf_response = curl_exec($curl);
							curl_close($curl);
							$pdf_history_response = json_decode($pdf_response,true);
												if(isset($pdf_history_response['result'])){
													$pdf_result = base64_decode($pdf_history_response['result']);
													if($pdf_result != ''){
														$pdf_result = base64_decode($pdf_history_response['result']);
														$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
														$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf_result);
														if (($save_file_check === false) || ($save_file_check == -1)) {
															$return_flag['message'] = "Unable to save bill";
														}else{									
															$return_flag['message'] = "Bill downloaded successfully";
															$return_flag['file_path'] = $file_name;
														}
													}else{
														$return_flag['message'] = "Blank Bill PDF";
													}
												}else{
													if(isset($pdf_response['error'])){
														$return_flag['message'] = $pdf_response['error']['Message'];
													}
													if(!isset($return_flag['message'] )){
														$return_flag['message'] = "Unable to get details 1";
													}
												}
						}else{
							$return_flag['message'] = "Unable to fetch bill no";
						}
					}else{
						$return_flag['message'] = "Unable to get bill no";
					}
				}else{
					$return_flag['message'] = "Invalid table data";
				}
			}else{
				$return_flag['message'] = "Invalid bill history result";
			}
		}else{
			$return_flag['message'] = "Invalid domain format";
		}

		$return_flag['bill_date'] = $bill_date;
		$return_flag['due_date'] = $due_date;
		$return_flag['amount_before_due_date'] = $amount_before_due_date;
		$return_flag['amount_after_due_date'] = $amount_after_due_date;
		$return_flag['bill_no'] = $bill_no;

		return $return_flag;

	}
	
	function get_session_url($domain){
		$parts = explode('.', $domain);
		$client = $parts[0];

		if ($client == 'rretail') {	$session_url_path = 'https://rretail.billpro.online/upload/session_tpddl.txt';} 
			elseif ($client == 'rjio') {	$session_url_path = 'https://rjio.billpro.online/upload_rjio/session_tpddl.txt';} 
			elseif ($client == 'gtl') {	$session_url_path = 'https://rjio.billpro.online/upload_rjio/session_tpddl.txt';} 
		elseif ($client == 'industowers') {	$session_url_path = 'https://industowers.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'atc') {	$session_url_path = 'https://altius.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'towervision') {	$session_url_path = 'https://towervision.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'jiobpev') {	$session_url_path = 'https://jiobpev.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'jiobpfr') {	$session_url_path = 'https://jiobpfr.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'fis') {	$session_url_path = 'https://fis.billpro.online/upload/session_tpddl.txt';} 
			elseif ($client == 'lenskart') {	$session_url_path = 'https://lenskart.billpro.online/upload/session_tpddl.txt';} 
			elseif ($client == 'jfl') {	$session_url_path = 'https://lenskart.billpro.online/upload/session_tpddl.txt';} 
			elseif ($client == 'billproenergy') {	$session_url_path = 'https://lenskart.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'allen') {	$session_url_path = 'https://allen.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'capriglobal') {	$session_url_path = 'https://capriglobal.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'adani') {	$session_url_path = 'https://adani.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'sunmobility') {	$session_url_path = 'https://sunmobility.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'crestdigitel') {	$session_url_path = 'https://crestdigitel.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'ttsl') {	$session_url_path = 'https://ttsl.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'ags') {	$session_url_path = 'https://ags.billpro.online/upload/session_tpddl.txt';} 
		elseif ($client == 'firstCry') {	$session_url_path = 'https://firstcry.billpro.online/upload/session_tpddl.txt';} 
		// elseif ($client == 'balbillpro') {	$session_url_path = 'https://balbillpro.online/upload_rjio/session_tpddl.txt';} 
		else {	$session_url_path = "";}

		$cookie_text = fetch_page(__LINE__,__FILE__,00000000,$session_url_path,'',NULL,NULL,1,0,1,1,1);
		if ($cookie_text!=false) {
			return $cookie_text;
		}else{
			return false ;
		}
		
	}
?>