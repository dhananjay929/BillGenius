<?php
	function get_details_MESCOM($consumer_details,$is_cron=0,$from_flag = 0){
		$consumer_details['url'] = "https://www.mescom.org.in/SCP/Myhome.aspx/";
		$consumer_details['main_url'] = 'https://mescom.org.in:8081/mescom/api/';
		$consumer_details['base_url'] = 'https://mescom.org.in';
		$consumer_details['color_code'] = "#8a49c8";
		$consumer_details['board_id'] = '29';
		$consumer_details['board_name'] = 'MESCOM';
		return get_details_karnataka($consumer_details,$is_cron,$from_flag);	
	}
?>
