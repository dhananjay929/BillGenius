<?php
	function get_details_karnataka($consumer_details,$is_cron=0,$from_flag = 0){
		$consumer_id = $consumer_details['consumer_id'];
		$username = $consumer_details['username'];
		$password = $consumer_details['password'];
		
		$base_discom_path = $consumer_details['base_url'];
		$main_discom_path = $consumer_details['main_url'];
		// $site_id = $consumer_details['site_id'];
		$curl_response = "";
		$respone_array = array();
		$session_id = "";
		$new_calling = 0;
		
		
			if(strlen($consumer_id) == 9){
				$db_consumer_id = '0'.$consumer_id;
			}else{
				$db_consumer_id = $consumer_id;
			}
			$data_arr = array();
			$data_arr['accountNumber'] = $db_consumer_id;
			$data_arr['billCondition'] = null;
			$data_arr['billDate'] = null;
			$data_arr['billId'] = null;
			$data_arr['cisDivision'] = 0;
			$data_arr['selectedBillDate'] = null;
	
			$post_fields = json_encode($data_arr);
			if ($consumer_details['board_id'] == 24) {
				$encoded_arr = array();
				$encoded_arr["_cdata"] = encryptData($post_fields,"b57ea4714sd2d6ahi7896e8");
				$post_fields_encoded = json_encode($encoded_arr);
			} 
			$header_array = array('AppServicekey: $3z$23$JBC7QqHzHEzJ/TzoS5qH4.Morw8ublIgfA.0byOEKrvnMyOr1K8Aj',
									"Content-Type: application/json");
		
			$details = array();
			if($consumer_details['board_name'] == 'CESCOM' || $consumer_details['board_name'] == 'HESCOM' || $consumer_details['board_name'] == 'GESCOM' || $consumer_details['board_name'] == 'BESCOM'){
				 	$acc_details_link = $consumer_details['base_url'].":8081/ccb-escom/api/getCustomerEntity?ConsumerAccountId=".$consumer_id;
			}else{
				 	$acc_details_link = "https://".strtolower($consumer_details['board_name']).".org.in:8081/ccb-escom/api/getCustomerEntity?ConsumerAccountId=".$consumer_id;
			}
			$fetch_details_page = fetch_page(__LINE__,__FILE__,$consumer_details['board_id'],$acc_details_link,$base_discom_path,$header_array,NULL,0,0);
			if($fetch_details_page !== false){
				$acc_details_arr = electricity_bill_json_decode(__LINE__,__FILE__, $fetch_details_page,true);
				if ($consumer_details['board_id'] != 24) {
					$acc_details_arr = electricity_bill_json_decode(__LINE__,__FILE__,decryptData($acc_details_arr['_cdata'], "b57ea4714sd2d6ahi7896e8"));
				}
				if ($acc_details_arr['statusMsg'] == 'Consumer Found') {
					$acc_details_arr = $acc_details_arr['consumerEntity'];
					$details['consumer_id'] = electricity_bill_trim(__LINE__,__FILE__,$acc_details_arr['consumerAccountId']);
					$details['address'] = electricity_bill_trim(__LINE__,__FILE__,$acc_details_arr['serviceAddress']); 
					$details['tarrif'] = electricity_bill_trim(__LINE__,__FILE__,$acc_details_arr['accountType']);
					$details['name'] = electricity_bill_trim(__LINE__,__FILE__,$acc_details_arr['accountName']);
					if ($consumer_details['board_id'] != 24) {
						$fetch_login_page = fetch_page(__LINE__,__FILE__,$consumer_details['board_id'],$main_discom_path.'billdetails',$base_discom_path,$header_array, $post_fields,0,0);
					} else {
								
						$fetch_login_page = fetch_page(__LINE__,__FILE__,$consumer_details['board_id'],$main_discom_path.'billdetails',$base_discom_path,$header_array, $post_fields_encoded,0,0);
					}
					if($fetch_login_page !== false){
						$bill_data_arr = json_decode($fetch_login_page, true);
						if ($consumer_details['board_id'] != 24) {
							$bill_data_arr = electricity_bill_json_decode(__LINE__,__FILE__,decryptData($bill_data_arr['_cdata'], "b57ea4714sd2d6ahi7896e8"));
						} 
						if (electricity_bill_lower(__LINE__,__FILE__,$bill_data_arr['status'])  == 'success') {
							$temp_details = $bill_data_arr['CustomerAccountBillInfo'];
							$count = 0;
							$details['bill_no'] = electricity_bill_trim(__LINE__,__FILE__,$temp_details['billId']);
							$details['payment_after_due_date'] =  electricity_bill_trim(__LINE__,__FILE__,$temp_details['currentAmount']);
							$details['total_current_amount'] =  $details['total_amount'] = $details['gross_bill'] = electricity_bill_trim(__LINE__,__FILE__,$temp_details['dueAmount']);
							$details['arrear'] = abs($details['total_current_amount'] - $details['payment_after_due_date']);
							$bill_date_arr = electricity_bill_explode(__LINE__,__FILE__, '-',electricity_bill_trim(__LINE__,__FILE__,$temp_details['billDate']));
							$details['bill_date'] = $bill_date_arr[2].'-'.$bill_date_arr[0].'-'.$bill_date_arr[1];
							$date=date_create($details['bill_date']);
						
							$details['bill_date_display'] = date_format($date,"d-m-Y");
							$details['bill_month'] = get_full_month(__LINE__,__FILE__,electricity_bill_month_formatted_2(__LINE__,__FILE__,electricity_bill_trim(__LINE__,__FILE__,$details['bill_date'])));
							$details['bill_year'] = electricity_bill_trim(__LINE__,__FILE__,$bill_date_arr[2]);
						
							$due_date_arr = electricity_bill_explode(__LINE__,__FILE__,'-',electricity_bill_trim(__LINE__,__FILE__,$temp_details['dueDate']));
							$details['due_date'] = $consumer_details['due_date'] = $due_date_arr[2].'-'.$due_date_arr[0].'-'.$due_date_arr[1];
							$date=date_create($details['due_date']);
						
							$details['due_date_display'] = date_format($date,"d-m-Y");
							$consumer_details['name'] = electricity_bill_string_replace(__LINE__,__FILE__,"'","",$details['name']);

								if($consumer_details['board_name'] == 'CESCOM' ){
									$bill_link = $base_discom_path.":8081/cesc/view-billpdf?accountId=".$db_consumer_id."&billId=".$details['bill_no']."&discomId=3";
								}
								if($consumer_details['board_name'] == 'BESCOM' ){
									$bill_link = $base_discom_path.":8081/bescom/view-billpdf?accountId=".$db_consumer_id."&billId=".$details['bill_no']."&discomId=1";
								}
								if($consumer_details['board_name'] == 'MESCOM' ){
									$bill_link = $base_discom_path.":8081/mescom/view-billpdf?accountId=".$db_consumer_id."&billId=".$details['bill_no']."&discomId=2"; 
								}
								if($consumer_details['board_name'] == 'HESCOM' ){
									$bill_link = $base_discom_path.":8081/hescom/view-billpdf?accountId=".$db_consumer_id."&billId=".$details['bill_no']."&discomId=5"; 
								}
								if($consumer_details['board_name'] == 'GESCOM' ){
									$bill_link = $base_discom_path.":8081/gescom/view-billpdf?accountId=".$db_consumer_id."&billId=".$details['bill_no']."&discomId=4"; 
								}
								$pdf_link_page = fetch_page(__LINE__,__FILE__,$consumer_details['board_id'],$bill_link,NULL, $header_array, NULL,0,0);
								if($pdf_link_page != false){
									$pdf_link_arr = electricity_bill_json_decode(__LINE__,__FILE__, $pdf_link_page,true);
									if ($consumer_details['board_id'] != 24) {
										$pdf_link_arr = electricity_bill_json_decode(__LINE__,__FILE__,decryptData($pdf_link_arr['_cdata'], "b57ea4714sd2d6ahi7896e8"));
									} 
									
									if($pdf_link_arr['ResultCode'] == 1){
										$pdf_data = base64_decode($pdf_link_arr['Result']);
										$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
										$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf_data);
										if (($save_file_check === false) || ($save_file_check == -1)) {
											$return_flag['message'] = "Unable to save bill";
										}else{
											$return_flag['message'] = "Bill downloaded successfully";
											$return_flag['file_path'] = $file_name;
										}
									}else{
										$return_flag['message'] = "Pdf is not generated";
										$new_calling = 1;
									}
								}else{
									$return_flag['message'] = "Unable to get Pdf page";
									$new_calling = 1;
								}
							
							
						}  else {
							$return_flag['message'] = "Unable to get bill details";
							$new_calling = 1;
						}
					
					}else {
						$return_flag['message'] = "Unable to get dashboard";
						$new_calling = 1;
					}
				} else {
					$return_flag['message'] = "Unable to find consumer";
					$new_calling = 1;
				}
	
			
			} else {
				$return_flag['message'] = "Unable to find acc details";
				$new_calling = 1;
			}
		
		if($new_calling == 1 && $consumer_details['board_name'] == 'BESCOM'){
			$return_flag = get_details_BESCOM_new($consumer_details,$is_cron,$from_flag);
		}
		if($new_calling == 1 && $consumer_details['board_name'] == 'MESCOM'){
			$return_flag = get_details_MESCOM_new($consumer_details,$is_cron,$from_flag);
		}
		
		
			return $return_flag;
	}
	function decryptData($data, $encryptionKey) {
                    $keySize = 256; // Key size in bits
                    $ivSize = 128; // IV size in bits
                    $iterationCount = 1989; // iteration count
                    $keySizeBytes = $keySize / 4;
                    $ivSizeBytes = $ivSize / 4; 
                    // Extract salt, IV, and ciphertext from the input
                    $salt = substr($data, 0, $keySizeBytes);
                    $iv = substr($data, $keySizeBytes, $ivSizeBytes);
                    $ciphertext = substr($data, $keySizeBytes + $ivSizeBytes);
                    // Decrypt using the components
                    return decryptWithIvSalt($salt, $iv, $encryptionKey, $ciphertext);
    	}
        // Function to decrypt with IV and salt
        function decryptWithIvSalt($salt, $iv, $encryptionKey, $ciphertext) {
            // Generate the key using the salt and encryption key
            $key = generateKey($salt, $encryptionKey);
            // Perform AES decryption
            $ciphertext = base64_decode($ciphertext);
            $iv = hex2bin($iv);
            $plaintext = openssl_decrypt(
                $ciphertext,
                'aes-256-cbc',
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );
            return $plaintext;
        }
        // Function to generate a derived key using PBKDF2
        function generateKey($salt, $encryptionKey) {
            $keySize = 256; // Key size in bits
	    $ivSize = 128; // IV size in bits
	    $iterationCount = 1989; // iteration count
            return hash_pbkdf2(
                'sha1',
                $encryptionKey,
                hex2bin($salt),
                $iterationCount,
                $keySize / 8,
                true
            );
        }
	function encryptData($data, $encryptionKey) {
                    $keySize = 256; // Key size in bits
                    $ivSize = 128; // IV size in bits
                    $iterationCount = 1989; // iteration count
               	    $iv = bin2hex(openssl_random_pseudo_bytes($ivSize / 8)); // Equivalent to WordArray.random
                    $key = bin2hex(openssl_random_pseudo_bytes($keySize / 8)); 
                    return $key . $iv . encryptWithIvSalt($key, $iv, $encryptionKey, $data);
    	}
	function encryptWithIvSalt($salt, $iv, $encryptionKey, $ciphertext) {
            $key = generateKey($salt, $encryptionKey);
            // Perform AES decryption
            //$ciphertext = base64_decode($ciphertext);
            $iv = hex2bin($iv);
            $encrypted = openssl_encrypt($ciphertext, 'aes-256-cbc', $key ,OPENSSL_RAW_DATA, $iv);
            return base64_encode($encrypted);
        }
	
	
	function get_details_BESCOM_new($consumer_details,$is_cron=0,$from_flag = 0) {
		$consumer_id = $consumer_details['consumer_id'];
		$username = $consumer_details['username'];
		$password = $consumer_details['password'];
		$base_discom_path = $consumer_details['url'];
		// $site_id = $consumer_details['site_id'];
		$curl_response = "";
		$post_get_values_of_page = '';
		$page_of_error = 'curl_functions/karnataka.php';
		$line_of_error = '';
		$url_of_error = '';
		$respone_array = array();
		$error_message= "";
		$line_of_error = __LINE__+1;
		$session_id = "";
		$curl = curl_init();
		$proxy_var = '159.65.149.250:8086';
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://bescommitra.in/CE/API/GetAmountDetails',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>'{"accountid":"'.$consumer_id.'","phonenumber":7669300679,"googlepushtokenno":"dcSLM5_uQG-8exT7mxba6L:APA91bHkg15Eg9sLAj9J5wsckRXng7PnIFqmwdxIhbhD6LAIGM3ZIsqXI3jX2CFXyfXH8HmZPiEwRa3xukF8hB6dQ1qq9WFOq8q6pu1uBUahoxksfzzzUbtiybsOoURUA1lwgMsSbNmD"}',
		  CURLOPT_HTTPHEADER => array(
			'Host: bescommitra.in',
			'Accept: application/json',
			'Username: bescom',
			'Password: bescom@123',
			'Client-Tag: bescom_mithra/android/3.0.5',
			'Content-Type: application/json',
			'User-Agent: okhttp/4.9.2',
			'Connection: close',
			'Origin: foo.example.org'
		  ),
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_SSL_VERIFYHOST => false,
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$response_array = json_decode($response, true);
		$consumer_details['bill_data'] = $response_array;
		if($response_array['error'] == '' ){
					$bill_url = "https://bescommitra.in/CE/BillDownload/Download?accountid=".$consumer_id."&billno=".$bill_no."&phonenumber=7669300679";
					$pdf_data = fetch_page(__LINE__,__FILE__,999,$bill_url,'',NULL,NULL,1,0);					
					$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
					$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf_data);
					if (($save_file_check === false) || ($save_file_check == -1)) {
						$return_flag['message'] = "Unable to save bill";
					}else{
						$return_flag['message'] = "Bill downloaded successfully";
						$return_flag['file_path'] = $file_name;
					}
		}else{
			$return_flag['message'] =   electricity_bill_string_replace(__LINE__,__FILE__,"'","/'",$response_array['message']);
		}
		return $return_flag;
	}
	function get_details_MESCOM_new($consumer_details,$is_cron=0,$from_flag = 0) {
		$consumer_id = $consumer_details['consumer_id'];
		$username = $consumer_details['username'];
		$password = $consumer_details['password'];
		$base_discom_path = $consumer_details['url'];
		// $site_id = $consumer_details['site_id'];
		$curl_response = "";
		$respone_array = array();
		$session_id = "";

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://mescommobileapp.net/CE_M_Test/API/GetAmountDetails',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'{"accountid":"'.$consumer_id.'","phonenumber":8346020080,"googlepushtokenno":"dtzqA80rTJS6IEwARKqbEz:APA91bEjDEHbwqPkHhwL9u5y9cgv37XZEDd4Cd-3_s1c6Y8wfYICnyG5bo-I13KqwDz1RM1ir3q-eBrJ2cB8ptEe0rPmf_iZTzKfmA1pz_48gjbirG-M4tPRagzm1zrLYgUEuAUtJWPY"}',
			CURLOPT_HTTPHEADER => array(
				'Host: mescommobileapp.net',
				'Accept: application/json',
				'Username: mescom',
				'Password: mescom@123',
				'Client-Tag: nanna_mescom/android/2.1.4',
				'Content-Type: application/json',
				'User-Agent: Mozilla/5.0 (Linux; U; Android 2.2; en-us; Droid Build/FRG22D) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
				'Connection: close',
				'Origin: foo.example.org'
			),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			));

			$response = curl_exec($curl);

		curl_close($curl);
		
		$response_array = json_decode($response, true);
		$consumer_details['bill_data'] = $response_array;
		if($response_array['error'] == '' ){
			
					$bill_url = "https://mescommobileapp.net/CE_M_Test/BillDownload/Download?accountid=".$consumer_id."&billno=".$bill_no."&phonenumber=7669300679";
					$pdf_data = fetch_page(__LINE__,__FILE__,999,$bill_url,'',NULL,NULL,1,0);					
					$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
					$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf_data);
					if (($save_file_check === false) || ($save_file_check == -1)) {
						$return_flag['message'] = "Unable to save bill";
					}else{
						$return_flag['message'] = "Bill downloaded successfully";
						$return_flag['file_path'] = $file_name;
					}
		}else{
			$return_flag['message'] =   electricity_bill_string_replace(__LINE__,__FILE__,"'","/'",$response_array['message']);
		}
		return $return_flag;
	}
	


?>	
		