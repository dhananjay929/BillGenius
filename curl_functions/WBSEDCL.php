<?php
	function get_details_WBSEDCL($consumer_details,$is_cron=0,$from_flag = 0) {
		$consumer_id = $consumer_details['consumer_id'];
		$installation_id = $consumer_details['installation_no'];
		$password = $consumer_details['password'];
		if(!isset($consumer_details['pass_discom_id'])) {
			$consumer_details['pass_discom_id'] = 38;
		}
		if(!isset($consumer_details['discom_name'])) {
			$consumer_details['discom_name'] = 'WBSEDCL';
		}
		$pass_discom_id = $consumer_details['pass_discom_id'];
		// $site_id = $consumer_details['site_id'];
		$captcha = '';
		$return_flag = array();
		$bill_no = "";
		$amount_after_due_date = "";
		$amount_before_due_date = "";
		$due_date = "";
		$bill_date = "";
		$captcha = '';
		$file_name = '';
		$counter = 5;
		
		do{
			$first_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,'https://portal.wbsedcl.in/webdynpro/resources/wbsedcl/viewbillwl/WBViewBillWL', 'https://www.wbsedcl.in/irj/go/km/docs/internet/new_website/Consumer_Login.html', NULL, NULL,0,1);
			if($first_page != false){
				$session_id = get_string_between(__LINE__,__FILE__,$first_page,'JSESSIONID=',';');
				$saplb = get_string_between(__LINE__,__FILE__,$first_page,'saplb_*=',';');
				$capcha1 = electricity_bill_string_replace(__LINE__,__FILE__,"'",'',electricity_bill_string_replace(__LINE__,__FILE__,'\x2f','/',get_string_between(__LINE__,__FILE__,$first_page,'<img id="BIEI.WBViewBillWLCompView.Image2" ct="IMG" lsdata="{3:',"'}")));
				$image_page  = 'https://portal.wbsedcl.in'.$capcha1;
				$response =0;
				if($response == 0){
					preg_match_all("/([0-9]+)/",$capcha1,$matches);
					$response = $matches[0][0];
				}
				$fixed_data = array("sap-wd-cltwndid","sap-wd-norefresh","sap-wd-appwndid","sap-wd-secure-id");
				$parameter = get_parameters($first_page,$fixed_data);
				$post_fields = http_build_query($parameter).'&SAPEVENTQUEUE=InputField_Change%EE%80%82Id%EE%80%84BIEI.WBViewBillWLCompView.InputField%EE%80%85Value%EE%80%84'.$consumer_id.'%EE%80%83%EE%80%82Delay%EE%80%84full%EE%80%83%EE%80%82urEventName%EE%80%84INPUTFIELDCHANGE%EE%80%83%EE%80%81InputField_Change%EE%80%82Id%EE%80%84BIEI.WBViewBillWLCompView.InputField1%EE%80%85Value%EE%80%84'.$installation_id.'%EE%80%83%EE%80%82Delay%EE%80%84full%EE%80%83%EE%80%82urEventName%EE%80%84INPUTFIELDCHANGE%EE%80%83%EE%80%81InputField_Change%EE%80%82Id%EE%80%84BIEI.WBViewBillWLCompView.InputField2%EE%80%85Value%EE%80%84'.$response.'%EE%80%83%EE%80%82Delay%EE%80%84full%EE%80%83%EE%80%82urEventName%EE%80%84INPUTFIELDCHANGE%EE%80%83%EE%80%81Button_Press%EE%80%82Id%EE%80%84BIEI.WBViewBillWLCompView.Button%EE%80%83%EE%80%82ClientAction%EE%80%84submit%EE%80%83%EE%80%82urEventName%EE%80%84BUTTONCLICK%EE%80%83%EE%80%81Form_Request%EE%80%82Id%EE%80%84...form%EE%80%85Async%EE%80%84false%EE%80%85FocusInfo%EE%80%84%40%7B%22sFocussedId%22%3A%22BIEI.WBViewBillWLCompView.Button%22%7D%EE%80%85Hash%EE%80%84%EE%80%85DomChanged%EE%80%84false%EE%80%85IsDirty%EE%80%84false%EE%80%83%EE%80%82EnqueueCardinality%EE%80%84single%EE%80%83%EE%80%82%EE%80%83';
				 $view_bill_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,"https://portal.wbsedcl.in/webdynpro/resources/wbsedcl/viewbillwl/WBViewBillWL", 'https://portal.wbsedcl.in/webdynpro/resources/wbsedcl/viewbillwl/WBViewBillWL', array('Cookie:saplb_*='.$saplb.'; JSESSIONID='.$session_id,'Expect:'), $post_fields,0,1);
				if(false != $view_bill_page) {
					$data_html = electricity_bill_string_replace(__LINE__,__FILE__,array("<![CDATA[","]]>"),"",$view_bill_page);
					$html = str_get_html($data_html);
					if(null !== $html->find('span[@id="BIEI.WBViewBillWLCompView.TextView"]',0)){
						$error_text = electricity_bill_trim(__LINE__,__FILE__,electricity_bill_string_replace(__LINE__,__FILE__,'&#x20;',' ',$html->find('span[@id="BIEI.WBViewBillWLCompView.TextView"]',0)->plaintext));
					}else{
						$error_text = '';
					}
					if(strpos($error_text,'Please provide the correct combination of consumer ID and Installation number') == false ){
						if(strchr($error_text,'No Bills available for this consumer') == false ){
							if(strpos($error_text,'HT Consumer Cannot avail this facility') == false ){
								if(null !== $html->find('span[@id="BIEI.WBViewBillWLCompView.TextView1.0"]',0)){
									$return_flag['message'] = '';
									break;
								}else {
									$return_flag['message'] = "Please provide the correct Captcha Image Code";
								}
							}else{
								$return_flag['message'] = "HT Consumer Cannot avail this facility";
								break;
							}
						}else{
							$return_flag['message'] = "No Bills available for this consumer";
							break;
						}
					}else{
						$return_flag['message'] = "Please provide the correct combination of consumer ID and Installation number";
						break;
					}
				}else {
					$return_flag['message'] = "Unable to load view bill page";
				}
			}else {
				$return_flag['message'] = "Unable to connect";
			}
			$counter --;		
		}while($counter != 0 );

		if($return_flag['message'] == ''){
			$num_row = 0;
			// Get data from html
			$total_amount_payable = 0;
			if(null !== $html->find('span[@id="BIEI.WBViewBillWLCompView.TextView1.0"]',0)){
				$new_bill_no = $prev_bill_no = $html->find('span[@id="BIEI.WBViewBillWLCompView.TextView1.0"]',0)->plaintext;	
			}
			$count = 0;
			if($new_bill_no != ''){
				while($new_bill_no == $prev_bill_no){
					$bill_details_array[$count]['bill_no'] = electricity_bill_trim(__LINE__,__FILE__,$html->find('span[@id="BIEI.WBViewBillWLCompView.TextView1.'.$count.'"]',0)->plaintext);
					$bill_details_array[$count]['month'] = electricity_bill_string_replace(__LINE__,__FILE__,'&nbsp;','',electricity_bill_trim(__LINE__,__FILE__,$html->find('span[@id="BIEI.WBViewBillWLCompView.TextView2.'.$count.'"]',0)->plaintext));
					$bill_details_array[$count]['due_date'] = electricity_bill_string_replace(__LINE__,__FILE__,'&nbsp;','',electricity_bill_string_replace(__LINE__,__FILE__,'&#x2f;','-',electricity_bill_trim(__LINE__,__FILE__,$html->find('span[@id="BIEI.WBViewBillWLCompView.TextView3.'.$count.'"]',0)->plaintext)));
					$bill_details_array[$count]['payment_before_due_date'] = electricity_bill_trim(__LINE__,__FILE__,$html->find('span[@id="BIEI.WBViewBillWLCompView.TextView4.'.$count.'"]',0)->plaintext);
					$bill_details_array[$count]['payment_after_due_date'] = electricity_bill_trim(__LINE__,__FILE__,$html->find('span[@id="BIEI.WBViewBillWLCompView.TextView5.'.$count.'"]',0)->plaintext);
					$bill_details_array[$count]['bill_link'] = 'BIEI.WBViewBillWLCompView.Image3.'.$count;
					if(isset($bill_details_array[$count]['due_date']) && $bill_details_array[$count]['due_date'] != ''){
						$temp_data = $bill_details_array[$count]['due_date']; 
						$bill_due_date = electricity_bill_string_replace(__LINE__,__FILE__,'&nbsp;','',electricity_bill_string_replace(__LINE__,__FILE__,'&#x2f;','-',$temp_data));
						$bill_due_date = electricity_bill_database_date_format(__LINE__,__FILE__,$bill_due_date);
					}
					$final_bill_link = $bill_details_array[$count]['bill_link'];
					$count++;
					if(null !== $html->find('span[@id="BIEI.WBViewBillWLCompView.TextView1.'.$count.'"]',0)){
						$new_bill_no = $html->find('span[@id="BIEI.WBViewBillWLCompView.TextView1.'.$count.'"]',0)->plaintext;
					}else{
						$new_bill_no =  '';
					}
				}
			}
			
				$post_fields = http_build_query($parameter).'&SAPEVENTQUEUE=SapTable_RowSelect%EE%80%82Id%EE%80%84BIEI.WBViewBillWLCompView.Table%EE%80%85RowIndex%EE%80%842%EE%80%85RowUserData%EE%80%84ViewBill.1%EE%80%85CellUserData%EE%80%84%EE%80%85AccessType%EE%80%84STANDARD%EE%80%85TriggerCellId%EE%80%84%EE%80%83%EE%80%82ClientAction%EE%80%84submit%EE%80%83%EE%80%82urEventName%EE%80%84RowSelect%EE%80%83%EE%80%81Form_Request%EE%80%82Id%EE%80%84...form%EE%80%85Async%EE%80%84false%EE%80%85FocusInfo%EE%80%84%40%7B%22iRowIndex%22%3A1%2C%22iColIndex%22%3A6%2C%22sFocussedId%22%3A%22BIEI.WBViewBillWLCompView.Image3.0%22%2C%22sApplyControlId%22%3A%22BIEI.WBViewBillWLCompView.Table%22%7D%EE%80%85Hash%EE%80%84%EE%80%85DomChanged%EE%80%84false%EE%80%85IsDirty%EE%80%84false%EE%80%83%EE%80%82EnqueueCardinality%EE%80%84single%EE%80%83%EE%80%82%EE%80%83';
				$post_fields = http_build_query($parameter).'&SAPEVENTQUEUE=SapTable_RowSelect%EE%80%82Id%EE%80%84BIEI.WBViewBillWLCompView.Table%EE%80%85RowIndex%EE%80%841%EE%80%85RowUserData%EE%80%84ViewBill.0%EE%80%85CellUserData%EE%80%84%EE%80%85AccessType%EE%80%84STANDARD%EE%80%85TriggerCellId%EE%80%84%EE%80%83%EE%80%82ClientAction%EE%80%84submit%EE%80%83%EE%80%82urEventName%EE%80%84RowSelect%EE%80%83%EE%80%81Form_Request%EE%80%82Id%EE%80%84...form%EE%80%85Async%EE%80%84false%EE%80%85FocusInfo%EE%80%84%40%7B%22iRowIndex%22%3A1%2C%22iColIndex%22%3A6%2C%22sFocussedId%22%3A%22BIEI.WBViewBillWLCompView.Image3.0%22%2C%22sApplyControlId%22%3A%22BIEI.WBViewBillWLCompView.Table%22%7D%EE%80%85Hash%EE%80%84%EE%80%85DomChanged%EE%80%84false%EE%80%85IsDirty%EE%80%84false%EE%80%83%EE%80%82EnqueueCardinality%EE%80%84single%EE%80%83%EE%80%82%EE%80%83';
				$view_bill_page_next_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,"https://portal.wbsedcl.in/webdynpro/resources/wbsedcl/viewbillwl/WBViewBillWL", 'https://portal.wbsedcl.in/webdynpro/resources/wbsedcl/viewbillwl/WBViewBillWL', array('Cookie:saplb_*='.$saplb.'; JSESSIONID='.$session_id), $post_fields,0,1);
				if(false != $view_bill_page_next_page){
					$view_bill_page_next_page = str_replace(array("<![CDATA[","]]>"), '', $view_bill_page_next_page);
					$bill_link_text = get_string_between(__LINE__,__FILE__,$view_bill_page_next_page,"openExternalWindow(","')");
					$temp_arr = explode(',',$bill_link_text);
					$bill_link = htmlspecialchars(electricity_bill_string_replace(__LINE__,__FILE__,"../..","",electricity_bill_string_replace(__LINE__,__FILE__,"'","",electricity_bill_string_replace(__LINE__,__FILE__,'\x2f','/',electricity_bill_string_replace(__LINE__,__FILE__,'\x2d','-',electricity_bill_string_replace(__LINE__,__FILE__,'\x7e','~',electricity_bill_string_replace(__LINE__,__FILE__,'\x26','&',electricity_bill_string_replace(__LINE__,__FILE__,'\x3d','=',electricity_bill_string_replace(__LINE__,__FILE__,'\x3f','?',electricity_bill_string_replace(__LINE__,__FILE__,'\x25','%',$temp_arr[1]))))))))));
					$pdf_link = 'https://portal.wbsedcl.in/webdynpro/resources'.$bill_link;
					$pdf_page = fetch_page(__LINE__,__FILE__,$pass_discom_id,$pdf_link,'https://portal.wbsedcl.in/webdynpro/resources/wbsedcl/viewbillwl/WBViewBillWL',array('Cookie: saplb_*='.$saplb.'; JSESSIONID='.$session_id,'Expect:'),$post_fields,0,0);
					if(false != $pdf_page){
						$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
						$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf_page);
						if (($save_file_check === false) || ($save_file_check == -1)) {
							$return_flag['message'] = "Unable to save bill";
						}else{									
							$return_flag['message'] = "Bill downloaded successfully";
							$return_flag['file_path'] = $file_name;
						}
					} else {
						$return_flag['message'] = "Failed to load PDF document.";	
					}
				}else{
					$return_flag['message'] = "Invalid Bill page";
				}
		}else{
			$return_flag['message'] = "Unknown error occured";
		}
		
		$return_flag['bill_date'] = $bill_date;
		$return_flag['due_date'] = $due_date;
		$return_flag['amount_before_due_date'] = $amount_before_due_date;
		$return_flag['amount_after_due_date'] = $amount_after_due_date;
		$return_flag['bill_no'] = $bill_no;

		return $return_flag;
	}

	
	function get_parameters($string,$fixed_data){
		$data_assoc = array();			
		for($index =0;$index<sizeof($fixed_data);$index++){
			$data = '';
			if(strpos($string,'type="hidden" name="'.$fixed_data[$index].'" value="') !== false ){
				$data = electricity_bill_string_replace(__LINE__,__FILE__,'&#x3d;','=',get_string_between(__LINE__,__FILE__,$string,'type="hidden" name="'.$fixed_data[$index].'" value="','"'));
			}
			else {
				return false;
			}
			$data_assoc[$fixed_data[$index]] = $data;
		}
		return 	$data_assoc;		
	}
	
	
?>
