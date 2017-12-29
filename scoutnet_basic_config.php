<?php

	function get_scoutnet_api_url()	{
		$scoutnet_api_url = "www.scoutnet.se";
		return $scoutnet_api_url;
	}

	/*
	Function to get the value of the kår-id from the option page
	*/
	function scoutnet_get_option_kar_id()	{
		return 765;	
	}
	/*
	Function to get the value of the api-nyckel Kår deltajerad from the option page
	*/
	function scoutnet_get_option_api_nyckel_kar_full()	{
		return 'fyh5644680756j870e56e73745he5d38352f';
	}
		
	/*
	 * Check if options to be able to use functions based on the detailed memberlist
	 */
	function scoutnet_get_member_list($args = "") {
		// detaljerad medlemslista /api/group/memberlist

		$karid = scoutnet_get_option_kar_id();
		$apinyckel = scoutnet_get_option_api_nyckel_kar_full();
		$apiurl = get_scoutnet_api_url();

		$result = @file_get_contents("https://$karid:$apinyckel@$apiurl/api/group/memberlist$args");

		if($result !== FALSE)	{
			return json_decode($result, true);
		}
	}



?>