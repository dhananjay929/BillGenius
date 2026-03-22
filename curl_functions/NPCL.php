<?php
	function get_details_NPCL($consumer_details,$is_cron=0,$from_flag = 0) {
		
		$discom_name = 'NPCL_UP_WEST';
		$consumer_id = $consumer_details['consumer_id'];
		if(!isset($consumer_details['board_id'])) {
			$consumer_details['board_id'] = 174;
			$discom_name = 'NPCL';
		}
		$pass_discom_id = $consumer_details['board_id'];
		$site_id = $consumer_details['site_id'];
		$consumer_details['discom_name'] = $discom_name;
		$return_flag = array();
		$respone_array = array();
		$error_message = '';
		$bill_date = "";
		$due_date = "";
		$name = "";
		$meter_no = "";
		$current_reading = "";
		$previous_reading = "";
		$unit_consumed = "";
		$total_amount = "";
		$bill_month = "";
		$bill_year = '';
		$bill_assoc = array();
		$division_assoc = array();
		$parameters = array();
		$replacements = array();
		$_tmpArr = array();
		$rebate = 0;
		$e_rebate = 0;
		$penalty = 0;
		$curl_response = "";
		$post_get_values_of_page = '';
		$page_of_error = 'curl_functions/'.$discom_name.'.php';
		$line_of_error = '';
		$url_of_error = '';
		$file_name = '';
		$save_file_name = '';
		if($is_cron != 0){
			electricity_bill_last_bill_check(__LINE__,__FILE__,$consumer_id,$pass_discom_id );	
		}
		$line_of_error = __LINE__+1;
		//var_dump('START');
		$file_name_temp = "";
		if(isset($consumer_details['db_addition']) && $consumer_details['db_addition'] == 1){
			$file_query = "SELECT tbl_npcl_bhilwara_bill_addition.fld_filename FROM tbl_npcl_bhilwara_bill_addition WHERE tbl_npcl_bhilwara_bill_addition.fld_error_message = '' AND tbl_npcl_bhilwara_bill_addition.fld_filename != ''  AND tbl_npcl_bhilwara_bill_addition.fld_site_id = '".$site_id."'  LIMIT 0,1";
		}else{
			$file_query = "SELECT tbl_npcl_bill_list.fld_filename FROM tbl_npcl_bill_list LEFT JOIN tbl_sites ON tbl_sites.fld_ai_internalsite_id = tbl_npcl_bill_list.fld_site_id WHERE tbl_npcl_bill_list.fld_error_message = '' AND tbl_sites.fld_organizationsite_id = '".$site_id."' ORDER BY tbl_npcl_bill_list.fld_datetime DESC LIMIT 0,1";
		}
		if($result = electricity_bill_query(__LINE__,__FILE__,$file_query)) {
			if(electricity_bill_num_rows(__LINE__,__FILE__,$result)) {
				while($row = electricity_bill_fetch_assoc(__LINE__,__FILE__,$result)) {
					$file_name_temp = $row['fld_filename'];
				}
			}
		}
		echo $file_name_temp;
		if($file_name_temp == "") {
			if(isset($consumer_details['db_addition']) && $consumer_details['db_addition'] == 1){
				$file_query = "SELECT tbl_npcl_bhilwara_bill_addition.fld_error_message FROM tbl_npcl_bhilwara_bill_addition WHERE tbl_npcl_bhilwara_bill_addition.fld_filename = '' AND tbl_npcl_bhilwara_bill_addition.fld_site_id = '".$site_id."' ORDER BY tbl_npcl_bhilwara_bill_addition.fld_datetime DESC LIMIT 0,1";
			}else{
				$file_query = "SELECT fld_error_message FROM tbl_npcl_bill_list WHERE fld_filename = '' AND fld_site_id = '".$site_id."' ORDER BY fld_datetime DESC LIMIT 0,1";
			}
			if($result = electricity_bill_query(__LINE__,__FILE__,$file_query)) {
				if(electricity_bill_num_rows(__LINE__,__FILE__,$result)) {
					while($row = electricity_bill_fetch_assoc(__LINE__,__FILE__,$result)) {
						$error_message = $row['fld_error_message'];
					}
				}
			}
			if($error_message == "") { $error_message = "UNKNOWN ERROR"; }
		} else {
			if($from_flag != "0") { $file_name_temp = "../".$file_name_temp; }
			if(file_exists($file_name_temp)){
				//if($from_flag != "0") { $file_name_temp = "../".$file_name_temp; }
				$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".pdf";
				$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
				$save_file_name = $save_file_dir.'/'.$file_name;
				$save_file_name_1 = $save_file_dir.'/'.$site_id.".pdf";
				copy($file_name_temp, $save_file_name);
			}else{
				
				$sub_folder  = date('Y-m-d');
				$site_id = $consumer_details['site_id'];
				$db_file_name='http://159.69.218.151/'.$file_name_temp;
				$file_name = electricity_bill_get_time_in_seconds(__LINE__,__FILE__).$site_id.".pdf";
				$save_file_dir = save_file_dir(__LINE__,__FILE__,'download/',$from_flag);
				$save_file_name = $save_file_dir.'/'.$file_name;
				$save_file_name_1 = $save_file_dir.'/'.$site_id.".pdf";
				$line_of_error = __LINE__+1;
				
				$db_data = fetch_page(__LINE__,__FILE__,$pass_discom_id,$db_file_name,'',NULL,NULL);
				$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$save_file_name,$db_data);
				if (($save_file_check === false) || ($save_file_check == -1)) {
					$error_message = "Unable to save bill";
				}
			}
			
			$parse_response = parse_NPCL($save_file_name, $consumer_details = $consumer_details,1,1,$save_file_name_1);
			if($parse_response['flag'] == '0'){
				$respone_array = $parse_response['response'];	
			}else{
				$error_message = $parse_response['message'];	
			}
		}
		
		return $return_flag;
	}

?>
