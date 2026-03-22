<?php
	function get_details_haryana($consumer_details,$is_cron=0,$from_flag = 0){
		$consumer_id = $consumer_details['consumer_id'];
		// $password = $consumer_details['password'];
		$pass_discom_id = $consumer_details['discom_id'];
		$discom_array = array(67=>'UHBVN',72=>'DHBVN',68=>'DHBVN_DELHI');
		$discom_url_array = array(67=>'uhbvn',72=>'dhbvn',68=>'dhbvn');
		$discom_name = $discom_array[$pass_discom_id];
		$discom_url = $discom_url_array[$pass_discom_id];
		$consumer_details['discom_name'] = $discom_name;
		$discom_name_lower = electricity_bill_lower(__LINE__,__FILE__,$discom_name);
		$consumer_details['discom_name_lower'] = $discom_name_lower;
		
		$bill_url = '';
		$bill_no = "";
		$amount_after_due_date = "";
		$amount_before_due_date = "";
		$due_date = "";
		$bill_date = "";
		// $site_id = $consumer_details['site_id'];
		$return_flag = array();
		$respone_array = array();
		

		$bill = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://'.$discom_url.'.org.in/Rapdrp/BD?UID='.$consumer_id,'',NULL,NULL,1,1,1,1,1);
		if(electricity_bill_find_position(__LINE__,__FILE__,$bill,'%PDF-') !== false){
			$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
			$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$bill);
			if (($save_file_check === false) || ($save_file_check == -1)) {
				$return_flag['message'] = "Unable to save bill";
			}else{									
				$return_flag['message'] = "Bill downloaded successfully";
				$return_flag['file_path'] = $file_name;
			}
		}else{
			$return_flag['message'] = "Unable to connect";
			if(electricity_bill_find_position(__LINE__,__FILE__,$bill,'CURRENT BILL NOT FOUND') !== false ){
				$return_flag['message'] =  'CURRENT BILL NOT FOUND. Please check in discom.';
			}
		}

		if (isset($return_flag['file_path'])) {
			return $return_flag;
		}

		if($return_flag['message'] != ''){
			if($discom_url == "dhbvn"){
				$url = 'https://epayment.'.$discom_url.'.org.in/mobile/MobileListener.aspx?method=8&AccountNo='.$consumer_id.'&RegID=350264';
			}else{
				$url = 'https://epayment.uhbvn.org.in/MobileListener.aspx?method=8&AccountNo='.$consumer_id.'&RegID=0';
			}
			
			$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,$url,'',NULL,NULL,0,0,0);
			if($first_page != false){
				if(get_string_between(__LINE__,__FILE__,$first_page, "<Status>","</Status>") != 2) {
					
					$bill_no = str_pad(electricity_bill_string_replace(__LINE__,__FILE__,',','',get_string_between(__LINE__,__FILE__,$first_page, "<BillNo>","</BillNo>")),12,"0",STR_PAD_LEFT);
					
						$uppcl_cookie_page = 'http://159.69.218.151/'.$discom_url.'_cookie.txt';
						
						$cookie_text = fetch_page(__LINE__,__FILE__,10000000,$uppcl_cookie_page,'',NULL,NULL,0,0);
						$bill_header = array($cookie_text);
						if($discom_url == "dhbvn"){
							$bill_url='https://www.dhbvn.org.in/web/portal/view-bill?p_p_id=ViewBill_WAR_Rapdrp&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=resourceUrl&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_count=1';
						}else{
							$bill_url = 'https://www.uhbvn.org.in/web/portal/view-bill?p_p_id=ViewBill_WAR_Rapdrp_INSTANCE_lYirWcJEz7U6&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=resourceUrl&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_pos=1&p_p_col_count=2';
						}
						$url = $bill_url.'&billId='.$bill_no;
						$bill  = fetch_page(__LINE__,__FILE__,$pass_discom_id,$url,'https://'.$discom_url.'.org.in/web/portal/view-bill', $bill_header, NULL,0,0);
						
						if($bill != false){
							if(electricity_bill_find_position(__LINE__,__FILE__,$bill,'%PDF-') !== false){
								$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
								$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf_bill);
								if (($save_file_check === false) || ($save_file_check == -1)) {
									$return_flag['message'] = "Unable to save bill";
								}else{									
									$return_flag['message'] = "Bill downloaded successfully";
									$return_flag['file_path'] = $file_name;
								}
							}else{
								$return_flag['message'] = "No Bill Found";
							}
						}else{
							$return_flag['message'] = "Unable to get bill";
						}
					
				}else{
					$return_flag['message'] = "Unable to get bill details";
				}
			}else{
				$return_flag['message'] = "Unable to connect";
			}
		}
				$return_flag['bill_date'] = $bill_date;
				$return_flag['due_date'] = $due_date;
				$return_flag['amount_before_due_date'] = $amount_before_due_date;
				$return_flag['amount_after_due_date'] = $amount_after_due_date;
				$return_flag['bill_no'] = $bill_no;
		
			return $return_flag;
	}
	?>