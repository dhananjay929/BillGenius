<?php
	function get_details_MPPKVVCL_EAST_HT($consumer_details,$is_cron=0,$from_flag = 0) {
		$pass_discom_id = 179;
		$consumer_id = $consumer_details['consumer_id'];
		// $site_id = $consumer_details['site_id'];
		$return_flag = array();
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
		$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://services.mpcz.in/serviceportal/api/ht/getHtBillDetails?idType=1&accountId='.$consumer_id,'', NULL, NULL,0,0);
		if($first_page != false){
			$data_array = json_decode($first_page,true);
			if(array_key_exists('message',$data_array) && $data_array['message'] == 'Success'){
					$due_date = $data_array['list'][0]['dueDate'];
					$due_date = electricity_bill_date_to_database_format_4(__LINE__,__FILE__,$due_date,'/');
					$accountId = $data_array['list'][0]['accountId'];
						if($accountId != ''){
							$fetch_html_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://services.mpcz.in/serviceportal/api/ht/downloadPdf?accountId='.$consumer_id.'&idType=1','', NULL, NULL,0,1);
							if(strpos($fetch_html_page,'Content-Type: application/pdf') !== false || strpos($fetch_html_page,'Content-Type: application/pdf') !== -1){
								$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
								$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$fetch_html_page);
								if (($save_file_check === false) || ($save_file_check == -1)) {
									$return_flag['message'] = "Unable to save bill";
								}else{									
									$return_flag['message'] = "Bill downloaded successfully";
									$return_flag['file_path'] = $file_name;
								}
							}else{
								$return_flag['message'] = 'Unable to get pdf page';
							}
						}else{
							$return_flag['message'] = "Bill No. not found";
						}
				}else{
				$return_flag['message'] = 'Invalid Consumer ID';
			}
		}else{
			$return_flag['message'] = 'Unable to connect2';
		}
			$return_flag['bill_date'] = $bill_date;
			$return_flag['due_date'] = $due_date;
			$return_flag['amount_before_due_date'] = $amount_before_due_date;
			$return_flag['amount_after_due_date'] = $amount_after_due_date;
			$return_flag['bill_no'] = $bill_no;
		return $return_flag;
	}
?>
