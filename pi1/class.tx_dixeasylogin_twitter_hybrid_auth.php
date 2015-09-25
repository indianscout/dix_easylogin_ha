<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2016 Indian Scout
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath("dix_easylogin")."pi1/class.tx_dixeasylogin_hybrid_auth.php");

class tx_dixeasylogin_twitter_hybrid_auth extends tx_dixeasylogin_hybrid_auth{
	function init($provider, $piVars){
		$this->provider = $provider;
		$debug_mode = empty($provider['ha_debug_mode']) ? false : json_decode($provider['ha_debug_mode']);
		$this->config = array(
			"base_url" 		=> $provider['ha_base_url'].$provider['ha_endpoint_path'],
			"redirect_url" 	=> $provider['ha_redirect_url'],
			"providers" 	=> array (
				"Twitter" 	=> array ( 
					"enabled" => true,
					"keys"    => array (
						"key" 		=> $provider['consumerKey'], 
						"secret" 	=> $provider['consumerSecret']
					),
					"t3_provider_name"	=> $provider['t3_provider_name'],
					"t3_provider_id"	=> $provider['t3_provider_id']
				)
			),
			"debug_mode" => $debug_mode,
			"debug_file" => $debug_mode && !empty($provider['ha_debug_file']) ? $provider['ha_debug_file'] : "/tmp/ha_${$provider['t3_provider_name']}_debug.log"
		);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dix_easylogin/pi1/class.tx_dixeasylogin_twitter_hybrid_auth.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dix_easylogin/pi1/class.tx_dixeasylogin_twitter_hybrid_auth.php']);
}
?>