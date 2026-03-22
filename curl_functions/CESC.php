<?php
	function get_details_CESC($consumer_details,$is_cron=0,$from_flag = 0) {
		$consumer_id = $consumer_details['consumer_id'];
		// $site_id = $consumer_details['site_id'];
		$return_flag = array();
		$captcha = '';
		$pass_discom_id = 56;
		$consumer_details['discom_id'] = $pass_discom_id;
		$bill_no = "";
		$amount_after_due_date = "";
		$amount_before_due_date = "";
		$due_date = "";
		$bill_date = "";
		$file_name = '';

		$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,"https://www.cesc.co.in/viewPrintBill", "http://www.cesc.co.in", NULL, NULL);
		if(electricity_bill_find_position(__LINE__,__FILE__,$first_page,'cesc_session=') !== false ){
			$session_id_1 = get_string_between(__LINE__,__FILE__,$first_page,': BNES_cesc_session=',';');
			$session_id_2 = get_string_between(__LINE__,__FILE__,$first_page,': BNES_BNES_cesc_session=',';');
			$crf_token_1 = get_string_between(__LINE__,__FILE__,$first_page,': BNES_XSRF-TOKEN=',';');
			$crf_token_2 = get_string_between(__LINE__,__FILE__,$first_page,': BNES_BNES_XSRF-TOKEN=',';');
			$BNIS_vid = get_string_between(__LINE__,__FILE__,$first_page,'BNIS_vid=',';');	
			$BNIS___utm_is1 = get_string_between(__LINE__,__FILE__,$first_page,'BNIS___utm_is1=',';');
			$BNIS___utm_is2 = get_string_between(__LINE__,__FILE__,$first_page,'BNIS___utm_is2=',';');	
			$BNIS___utm_is3 = get_string_between(__LINE__,__FILE__,$first_page,'BNIS___utm_is3=',';');	
			if (electricity_bill_string_length(__LINE__,__FILE__,$session_id_1) > 2){
				$request_header1[] = "Cookie: BNES_BNES_cesc_session=$session_id_2;BNES_cesc_session=$session_id_1;BNES_BNES_XSRF-TOKEN=$crf_token_2;BNES_XSRF-TOKEN=$crf_token_1;BNIS_vid=$BNIS_vid;BNIS___utm_is1=$BNIS___utm_is1;BNIS___utm_is2==$BNIS___utm_is2;BNIS___utm_is3=$BNIS___utm_is3;_ga_DERCNHY6T7=GS1.1.1662458307.1.1.1662458549.0.0.0;_ga=GA1.1.1558606109.1662458307;";
				$post_arr = array();
				$post_arr['_token'] = get_string_between(__LINE__,__FILE__,$first_page,'name="_token" value="','">');
				$post_arr['__ncforminfo'] = get_string_between(__LINE__,__FILE__,$first_page,'name="__ncforminfo" value="','"');
				$post_arr['customer_id'] = $consumer_id;
				$post_arr['route_param'] = 1;
				$fetch_second_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://www.cesc.co.in/viewPrintBill','https://www.cesc.co.in',$request_header1,$post_arr, 0, 1);
				if(strchr($fetch_second_page,'<input type="hidden" name="get_url" id="get_url" value="')){
					$link =get_string_between(__LINE__,__FILE__,$fetch_second_page,'<input type="hidden" name="get_url" id="get_url" value="','"');
					$link = electricity_bill_string_replace(__LINE__,__FILE__,'amp;','',$link);
					$pdf=fetch_page(__LINE__,__FILE__,$pass_discom_id,$link, "",NULL, NULL,0,0);
					if($pdf != false && electricity_bill_find_position(__LINE__,__FILE__,$pdf,'%PDF') !== false){
						$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
						$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf);
						if (($save_file_check === false) || ($save_file_check == -1)) {
							$return_flag['message'] = "Unable to save bill";
						}else{									
							$return_flag['message'] = "Bill downloaded successfully";
							$return_flag['file_path'] = $file_name;
						}
					}else{
						$return_flag['message'] = "Invalid pdf";	
						if(electricity_bill_find_position(__LINE__,__FILE__,$pdf,'PROVISIONAL BILL') !== false){
							$return_flag['message'] = "PROVISIONAL BILL Found";	
						}
					}
				}else{
					$return_flag['message'] = "Bill data not found";	
				}
			}else{
				$return_flag['message'] = "Invalid session id";
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
	
	
?>