<?php
	function get_details_MPPKVVCL_WEST($consumer_details,$is_cron=0,$from_flag = 0) {
		$consumer_id = $consumer_details['consumer_id'];
		// $site_id = $consumer_details['site_id'];
		$return_flag = array();
		$respone_array = array();
		$error_message = '';
		$bill_no = "";
		$amount_after_due_date = "";
		$amount_before_due_date = "";
		$due_date = "";
		$bill_date = "";
		$_tmpArr = array();
		$pass_discom_id = 18;
		$consumer_details['discom_id'] = $pass_discom_id;
		$file_name = '';
		$headers = array('Content-Type: multipart/form-data');
		$data_array = array();
		$data_array['username'] = "AKa6fTkqrhlhhpGzZPhWwQ==";
		$data_array['password'] = "Gr/VzjyXDVS9qUfLHC6c0Q==";
		$url = "https://mpwzservices.mpwin.co.in/billpayments/token/generate-token";
		$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$data_array);
		$token_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,$url, "https://mpwzservices.mpwin.co.in/westdiscom/home", null,$post_fields,0,0); 
		$token_json_array = json_decode($token_page,true);
		if($token_json_array['token'] != ""){
			$epoch_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,"https://mpwzservices.mpwin.co.in/billpayments/BPLXY/SH_BLJ_SHRM", "https://mpwzservices.mpwin.co.in/westdiscom/home",null, "",0,0); 	
			$epoch_json_array = json_decode($epoch_page,true);
			//This variable is to be decrypted and then encrypted for calling
			if($epoch_json_array['txPcXvn'] != ""){
				$decrypted =  CryptoJSAesDecrypt($epoch_json_array['txPcXvn'], "WINPAY@#10@MPEBV", "JAIBALAJIVIBHORM");
				$encrypted = CryptoJSAesEncrypt($decrypted."@SEM","XYBLJSHRMJM_M7GN", "THISISDSCMVLDURJ");
				$headers = array('Authorization: Discom-Payment '.$token_json_array['token']);
				$data_array = array();
				$data_array['ivrs'] = CryptoJSAesEncrypt($consumer_id, "WINPAY@#10@MPEBV", "JAIBALAJIVIBHORM");
				$data_array['BPLX'] = $encrypted;
				$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$data_array);
				$bill_details_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,"https://mpwzservices.mpwin.co.in/billpayments/fetch-consumer-details/get-for-payment", "https://mpwzservices.mpwin.co.in/westdiscom/home", $headers,$post_fields,0,0); 
				$bill_json_array = json_decode($bill_details_page, true);
				if($bill_json_array['msg'] != 'Not a valid Consumer Id'){
				
					$query_string = urlencode(CryptoJSAesEncrypt($bill_json_array['ivrs']."$".$bill_json_array['billmonth'],"WINPAY@#10@MPEBV", "JAIBALAJIVIBHORM")."|".$encrypted);
					//echo "\nQuery String: ".$query_string."\n";
					$bill_fetch = fetch_page(__LINE__,__FILE__,$pass_discom_id,"https://mpwzservices.mpwin.co.in/billpayments/get-bill-view/get-pdf?data=".$query_string, "https://mpwzservices.mpwin.co.in/westdiscom/home", $headers, null,0,0); 

					if($bill_fetch != false && electricity_bill_find_position(__LINE__,__FILE__,$bill_fetch,"Content-Length: 0") === false){
						$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
						$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill_fetch);
						if (($save_file_check === false) || ($save_file_check == -1)) {
							$return_flag['message'] = "Unable to save bill";
						}else{									
							$return_flag['message'] = "Bill downloaded successfully";
							$return_flag['file_path'] = $file_name;
						}
					} else {
						$return_flag['message'] = 'Unable to fetch bill page';
					}
				}else{
					$return_flag['message'] = 'Not a valid Consumer Id';
				}
			}else{
				$return_flag['message'] = 'Unable to generate BPLXY';
			}
			
		}else{
			$return_flag['message'] = 'Unable to generate token';
		}
	
		$return_flag['bill_date'] = $bill_date;
		$return_flag['due_date'] = $due_date;
		$return_flag['amount_before_due_date'] = $amount_before_due_date;
		$return_flag['amount_after_due_date'] = $amount_after_due_date;
		$return_flag['bill_no'] = $bill_no;
		
		return $return_flag;
	}
function CryptoJSAesEncrypt($plain_text, $passphrase, $iv){
    $encrypted_data = openssl_encrypt($plain_text, 'aes-128-cbc', $passphrase, OPENSSL_RAW_DATA, $iv);
    return base64_encode($encrypted_data);
}


function CryptoJSAesDecrypt($ciphertext, $passphrase, $iv){
    $decrypted= openssl_decrypt(base64_decode($ciphertext) , 'aes-128-cbc', $passphrase, OPENSSL_RAW_DATA, $iv);
    return $decrypted;

}
?>
