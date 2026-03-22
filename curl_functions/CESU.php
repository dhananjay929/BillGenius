<?php
	function get_details_CESU($consumer_details,$is_cron=0,$from_flag = 0) {
		// $site_id = $consumer_details['site_id'];
		$consumer_id = $consumer_details['consumer_id'];
		$pass_discom_id = $consumer_details['discom_id'] = 61;
		$ca_number = $consumer_details['ca_number'];
		$return_flag = array();
		$respone_array = array();
		$curl_response = "";
		$bill_no = '';
		$mobile_no = '8537883243';
		
			if(strlen(electricity_bill_trim(__LINE__,__FILE__,$consumer_details['ca_number'])) == 11){
				$ca_no = electricity_bill_trim(__LINE__,__FILE__,$consumer_details['ca_number']);
			}elseif(strlen($consumer_details['consumer_id']) == 11){
				$ca_no = electricity_bill_trim(__LINE__,__FILE__,$consumer_details['consumer_id']);
			}
			$loop_count = 0;
			do{
				
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://collectionapi.tpcentralodisha.com/api/Info/getconsumerdetails',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 10,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{"ConsumerNumber":"'.$ca_no.'","ref_no":"","bill_type":"11"}',
					CURLOPT_HTTPHEADER => array(
						'Content-Type: application/json; charset=UTF-8',
						'Host: collectionapi.tpcentralodisha.com',
						'Connection: Keep-Alive',
						'Accept-Encoding: gzip',
						'User-Agent: okhttp/3.14.9'
					
					),
					CURLOPT_SSL_VERIFYPEER =>  false,
					CURLOPT_SSL_VERIFYHOST =>  false
					// CURLOPT_PROXY =>  '159.65.149.250:8086'
				));

				$response = curl_exec($curl);
				$bill_data = json_decode($response,true);
				$data = curl_getinfo( $curl );
				curl_close($curl);


				$bill_no = $bill_data[0]['BLL_NO'];
				
				$loop_count++;
				if($loop_count == 3){
					break;
				}
			}while(strlen($bill_no) < 3);

			if($bill_no != ''){
				
					$bill_link = 'https://portal.tpcentralodisha.com:8071/ConsumerBillInfo_2021/PdfBillGeneratorFrontController?documentno='.$bill_no;
					$pdf_bill = fetch_page(__LINE__,__FILE__,$pass_discom_id,$bill_link,'',NULL,NULL,0,0);
					if(electricity_bill_find_position(__LINE__,__FILE__,$pdf_bill,"%PDF") !== false){
						$file_name = './download/'.electricity_bill_get_time_in_seconds(__LINE__,__FILE__).'_'.$consumer_id.".pdf";
						$save_file_check = electricity_bill_file_put_contents(__LINE__,__FILE__,$file_name,$pdf_bill);
						if (($save_file_check === false) || ($save_file_check == -1)) {
							$return_flag['message'] = "Unable to save bill";
						}else{									
							$return_flag['message'] = "Bill downloaded successfully";
							$return_flag['file_path'] = $file_name;
						}
					}else {
						$return_flag['message'] = 'Blank Bill';
					}
			}else {
				$return_flag['message'] = 'Unable to get Bill No.';
			}
		

			return $return_flag;
	}
	
?>