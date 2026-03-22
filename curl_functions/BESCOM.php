<?php
	function get_details_BESCOM($consumer_details,$is_cron=0,$from_flag = 0){
		
		$consumer_details['url'] = 'https://www.bescom.co.in/';
		$consumer_details['main_url'] = 'https://bescom.co.in:8081/bescom/api/';
		$consumer_details['base_url'] = "https://www.bescom.co.in";
		$consumer_details['color_code'] = "#0093dc";
		$consumer_details['board_id'] = '24';
		$consumer_details['board_name'] = 'BESCOM';
		
		return get_details_karnataka($consumer_details,$is_cron,$from_flag);	
	}
?>