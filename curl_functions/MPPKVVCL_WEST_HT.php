<?php
	function get_details_MPPKVVCL_WEST_HT($consumer_details,$is_cron=0,$from_flag = 0) {
		$pass_discom_id = 178;
		$consumer_id = $consumer_details['consumer_id'];
		// $site_id = $consumer_details['site_id'];
		$discom_type = $consumer_details['discom_type'] ;
		$return_flag = array();
		$bill_no = "";
		$amount_after_due_date = "";
		$amount_before_due_date = "";
		$due_date = "";
		$bill_date = "";
		$consumer_details['discom_id'] = $pass_discom_id;
		$file_name = '';
						
		$discom_name_array  = array(
		'Bhopal' => '0',
		'BHOPAL' => '0',
		'bhopal' => '0',
		'Jabalpur' => '1',
		'JABALPUR' => '1',
		'jabalpur' => '1',
		'Indore' => '2',
		'INDORE' => '2',
		'indore' => '2'
		);
		
		$type = $discom_name_array[$discom_type];
	
		$bill_detail_link = 'https://services.mpcz.in/serviceportal/api/ht/getHtBillDetails?idType='.$type.'&accountId='.$consumer_id;
		$bill_detail_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,$bill_detail_link,'https://services.mpcz.in/Consumer/',NULL,NULL,0,0);
		if($bill_detail_page != false){
			$bill_detail_json_array = json_decode($bill_detail_page, true);
			$message = $bill_detail_json_array['message'];
			if($message == 'Success'){
				$temp_due_date = $bill_detail_json_array['list'][0]['dueDate'];
				if(strchr($temp_due_date,'/')){
					$temp_due_date = electricity_bill_trim(__LINE__,__FILE__,electricity_bill_string_replace(__LINE__,__FILE__,"/","-",$temp_due_date));
				}
				$bill_due_date = electricity_bill_database_date_format(__LINE__,__FILE__,$temp_due_date);
				$bill_date = electricity_bill_database_date_format(__LINE__,__FILE__,$bill_detail_json_array['list'][0]['billDate']);

				$bill_fetch_pdf_link = 'https://services.mpcz.in/serviceportal/api/ht/downloadPdf?accountId='.$consumer_id.'&idType='.$type;
				$bill_fetch = fetch_page(__LINE__,__FILE__,$pass_discom_id,$bill_fetch_pdf_link,'https://services.mpcz.in/Consumer/',NULL,NULL,0,0); 
				if($bill_fetch != false){
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
			
			} else {
				$return_flag['message'] = 'Not a valid Consumer Id or Type';
			}
		}else {
			$return_flag['message'] = 'unable to Connect';
		}

		$return_flag['bill_date'] = $bill_date;
		$return_flag['due_date'] = $due_date;
		$return_flag['amount_before_due_date'] = $amount_before_due_date;
		$return_flag['amount_after_due_date'] = $amount_after_due_date;
		$return_flag['bill_no'] = $bill_no;
		return $return_flag;
	}
?>
