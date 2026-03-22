<?php
	use mikehaertl\wkhtmlto\Pdf;
	function get_details_MPPKVVCL_CENTRAL($consumer_details,$is_cron=0,$from_flag = 0) {
		$consumer_id = $consumer_details['consumer_id'];
		// $site_id = $consumer_details['site_id'];
		$bill_no = "";
		$amount_after_due_date = "";
		$amount_before_due_date = "";
		$due_date = "";
		$bill_date = "";
		$bill_month = "";
		$return_flag = array();
		$pass_discom_id = 20;
		$consumer_details['board_id'] = $consumer_details['discom_id'] = $pass_discom_id;
		$file_name = '';
		$bill_purpose = "";
		$bill_number_temp = "";
		$temp = array();
		$type = "ccnb";
		$url = 'https://services.mpcz.in/serviceportal/api/payment/verification?idType='.$type.'&idNumber='.$consumer_id;
		$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,$url, 'https://services.mpcz.in/Consumer/', NULL, NULL,0,0);
		if($first_page != false){ 
			$temp = json_decode($first_page,true);
			if($temp['code'] != 200) {
				$type = "rms";
				$url = 'https://services.mpcz.in/serviceportal/api/payment/verification?idType='.$type.'&idNumber='.$consumer_id;
				$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,$url, 'https://services.mpcz.in/Consumer/', NULL, NULL,0,0);
				if($first_page != false){
					$temp = json_decode($first_page,true);
					if($temp['code'] == 200) {
						$bill_number_temp = $temp['list'][0]['billId'];
						$bill_purpose = $temp['list'][0]['billPurpose'];
					}
				}
			} else {
				$bill_number_temp = $temp['list'][0]['billId'];
				$bill_purpose = $temp['list'][0]['billPurpose'];
			}
		}
		
		if($bill_number_temp !== "") {
					#New Code start here
					$tarrifCode = $billMonth = '';
					if($temp['code'] == 200){
						$tarrifCode = $temp['list'][0]['tarrifCode'];
						$billMonth = $temp['list'][0]['billMonth'];
					}
					if($tarrifCode == ''){
						$tarrifCode = 'null';
					}
					$bill_url = 'https://services.mpcz.in/serviceportal/api/payment/generateBillPdfNew?consumerId='.$consumer_id.'&billMonth='.$billMonth.'&tarrifCode='.$tarrifCode;
					if($tarrifCode != '' && $billMonth != ''){
						$bill_fetch = fetch_page(__LINE__,__FILE__,$pass_discom_id,$bill_url,'',NULL,NULL,0,0);
						if($bill_fetch != false && electricity_bill_find_position(__LINE__,__FILE__,$bill_fetch,"%PDF") !== false){
							$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
							$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill_fetch);
							if (($save_file_check === false) || ($save_file_check == -1)) {
								$return_flag['message'] = "Unable to save bill";
							}else{									
								$return_flag['message'] = "Bill downloaded successfully";
								$return_flag['file_path'] = $file_name;
							}
						}else{
							$return_flag['message'] = 'Unable to get Bill';
						}
					}else{
						$return_flag['message'] = 'Bill not generated';
					}
					
			
		} else {
			$return_flag['message'] = "Parameter not found";
		}
		
		$return_flag['bill_date'] = $bill_date;
		$return_flag['due_date'] = $due_date;
		$return_flag['amount_before_due_date'] = $amount_before_due_date;
		$return_flag['amount_after_due_date'] = $amount_after_due_date;
		$return_flag['bill_no'] = $bill_no;

		return $return_flag;
	}
	

?>