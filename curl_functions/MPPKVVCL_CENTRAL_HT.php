<?php
	use mikehaertl\wkhtmlto\Pdf;
	function get_details_MPPKVVCL_CENTRAL_HT($consumer_details,$is_cron=0,$from_flag = 0) {
		$pass_discom_id = 177;
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

		$bill_month = "";
		$bill_year = '';
		$bill_assoc = array();
		$division_assoc = array();
		$parameters = array();
		$replacements = array();
		$_tmpArr = array();
		$consumer_details['discom_id'] = $pass_discom_id;
		$file_name = '';

		$params = array();
		$params['accountId'] = $consumer_id;
		$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$params);
		$dashboard_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://services.mpcz.in/serviceportal/api/ht/getHtBillDetails?idType=0&accountId='.$consumer_id,"https://services.mpcz.in/Consumer/",NULL,NULL,0,0);
		$response_array = json_decode($dashboard_page,true);
		if($response_array['message'] == 'Success'){
			$due_date = $response_array['list'][0]['dueDate'];
			$due_date = electricity_bill_database_date_format(__LINE__,__FILE__,$due_date);
			$temp_consumer_id = $response_array['list'][0]['accountId'];
			if(($temp_consumer_id == $consumer_id || $temp_consumer_id == 'H'.$consumer_id) && $due_date != 'null'){
					$bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://services.mpcz.in/serviceportal/api/ht/downloadPdf?accountId='.$consumer_id.'&idType=0','',NULL,NULL,0,0);
					if($bill_page != '' && electricity_bill_find_position(__LINE__,__FILE__,$bill_page,'%PDF') !== false){
						$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
						$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill_page);
						if (($save_file_check === false) || ($save_file_check == -1)) {
							$return_flag['message'] = "Unable to save bill";
						}else{									
							$return_flag['message'] = "Bill downloaded successfully";
							$return_flag['file_path'] = $file_name;
						}
					}else{
						$bill_page = get_string_between(__LINE__,__FILE__,$bill_page,'</head>','</div><center>');	
						$bill_page = '<html>'.$bill_page.'</html>';
						if($bill_page != ''){
							$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".html";
							$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill_page);
							if (($save_file_check === false) || ($save_file_check == -1)) {
								$return_flag['message'] = "Unable to save bill";
							}else{									
								$return_flag['message'] = "Bill downloaded successfully";
								$return_flag['file_path'] = $file_name;
							}
						}else{
							$return_flag['message'] = "Unable to download bill html";
						}
					}
				
			}else{
				$return_flag['message'] = "Invalid Consumer ID";
			}
		
		}else{
			$return_flag['message'] = "Unable to get consumer detail";
		}
			$return_flag['bill_date'] = $bill_date;
			$return_flag['due_date'] = $due_date;
			$return_flag['amount_before_due_date'] = $amount_before_due_date;
			$return_flag['amount_after_due_date'] = $amount_after_due_date;
			$return_flag['bill_no'] = $bill_no;
		return $return_flag;
	}
	
	
	
?>