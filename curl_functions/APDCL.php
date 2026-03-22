<?php
function get_details_APDCL($consumer_details) {
		$consumer_id = $consumer_details['consumer_id'];
		$pass_discom_id = 21;
		$return_flag = array();
        $bill_date = '';
        $due_date = '';
        $amount_before_due_date = '';
        $amount_after_due_date = '';
        $bill_no = '';
				$data_array = array();
				$data_array['consNo'] = $consumer_id;
				$data_array['billCount'] = 6;
				$post_fields = electricity_bill_http_build_query(__LINE__,__FILE__,$data_array);
				$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://apdclrms.com/cbs/RestAPI/OnlineBill/billData','https://www.apdcl.org/',NULL, $post_fields,0,0);
				if($first_page != false ){
					$session_id = get_string_between(__LINE__,__FILE__,$first_page,'JSESSIONID=',';');
					$decode_array = json_decode($first_page);
					if(!electricity_bill_array_empty(__LINE__,__FILE__,$decode_array)){
						$decode_array_0 = $decode_array[0];
						$decode_array_0  = get_object_vars($decode_array_0);
						
							$pdf_URL = $decode_array_0['pdf_URL'];
							 $download_pdf = fetch_page(__LINE__,__FILE__,$pass_discom_id,$pdf_URL,' ', NULL, NULL,0,0);
							if(electricity_bill_find_position(__LINE__,__FILE__,$download_pdf,'%PDF') !== false) {
								$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
								$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$download_pdf);
								if (($save_file_check === false) || ($save_file_check == -1)) {
									$return_flag['message'] = "Unable to save bill";
								}else{									
									$return_flag['message'] = "Bill downloaded successfully";
									$return_flag['file_path'] = $file_name;
								}
							}else{
								$return_flag['message'] = "Unable to Download Bill Pdf";
							}
						
					}else{
						if(isset($decode_array['status']) && $decode_array['status'] == 'exception'){
							$return_flag['message'] = $decode_array['message'];
						}else{
							$return_flag['message'] = "Invalid JSON";
						}
					}
				}else{
					$return_flag['message'] = "Unable to get Validation Page";
				}
			
				$return_flag['bill_date'] = $bill_date;
				$return_flag['due_date'] = $due_date;
				$return_flag['amount_before_due_date'] = $amount_before_due_date;
				$return_flag['amount_after_due_date'] = $amount_after_due_date;
				$return_flag['bill_no'] = $bill_no;
		
		return $return_flag;
	}

	
?>