<?php
	function get_details_DHBVN($consumer_details,$is_cron=0,$from_flag = 0){
		$consumer_details['discom_id'] = 72;
		return get_details_haryana($consumer_details,$is_cron,$from_flag);	
	}
?>