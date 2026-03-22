<?php

function get_details_ARPDOP($consumer_details,$is_cron=0,$from_flag = 0) {

    $consumer_id = $consumer_details['consumer_id'];
    $pass_discom_id = $consumer_details['board_id'] = 136;
    $return_flag = array();
	$bill_date = '';
	$due_date = '';
	$amount_before_due_date = '';
	$amount_after_due_date = '';
	$bill_no = '';
        $first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://www.arpdop.gov.in/dopap-portal/#!/home','', NULL, NULL);
        if($first_page !== false){
            $session_id = electricity_bill_trim(__LINE__,__FILE__,get_string_between(__LINE__,__FILE__,$first_page,'JSESSIONID=',';'));
            $headers = array('Cookie: JSESSIONID='.$session_id);
            $login_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://www.arpdop.gov.in/dopap-portal/viewBill?consumerId='.$consumer_id,'https://www.arpdop.gov.in/dopap-portal/',$headers,NULL,1,0);
            $temp = json_decode($login_page,true);
            if (isset($temp[0]['consumerId'])) {
				if(electricity_bill_trim(__LINE__,__FILE__,$temp[0]['consumerId']) == $consumer_id && $login_page != false) {
					$bill_number_temp = electricity_bill_trim(__LINE__,__FILE__,$temp[0]['invoiceId']);
					if($bill_number_temp != ''){
							$check_pdf_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://www.arpdop.gov.in/dopap-portal/viewBill/getInvoice?consumerId='.$consumer_id.'&invoiceNo='.$bill_number_temp,'https://www.arpdop.gov.in/dopap-portal/',$headers,NULL,1,0);
							$temp_1 = json_decode($check_pdf_page,true);
							if(electricity_bill_trim(__LINE__,__FILE__,$temp_1['second']) == 'Download File' && $temp_1['valid'] == ''){
								$pdf_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://www.arpdop.gov.in/dopap-portal/viewBill/downloadBillInvoice?consumerId='.$consumer_id.'&invoiceNo='.$bill_number_temp,'https://www.arpdop.gov.in/dopap-portal/',$headers,NULL,1,0);
								if($pdf_page != false && electricity_bill_find_position(__LINE__,__FILE__,$pdf_page,"%PDF") !== false){
									$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
									$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf_page);
									if (($save_file_check === false) || ($save_file_check == -1)) {
										$return_flag['message'] = "Unable to save bill";
									}else{									
										$return_flag['message'] = "Bill downloaded successfully";
										$return_flag['file_path'] = $file_name;
									}
								}else{
										$return_flag['message'] = "Blank PDF";
								}
							}else{
								$return_flag['message'] = "Please check Bill Number";
								if(isset($temp_1['second'])){
									$return_flag['message'] = electricity_bill_trim(__LINE__,__FILE__,$temp_1['second']);
								}
							}
					}else{
						$return_flag['message'] = "Blank Bill number";
					}
				}else{
					$return_flag['message'] = "Invalid Consumer ID or mismatch 2";
				}
			}else{
				$return_flag['message'] = "Invalid Consumer ID or mismatch 1";
			}
        }else{
            $return_flag['message'] = "Unable to connect";
        }
		$return_flag['bill_date'] = $bill_date;
		$return_flag['due_date'] = $due_date;
		$return_flag['amount_before_due_date'] = $amount_before_due_date;
		$return_flag['amount_after_due_date'] = $amount_after_due_date;
		$return_flag['bill_no'] = $bill_no;
  
    return $return_flag;
}