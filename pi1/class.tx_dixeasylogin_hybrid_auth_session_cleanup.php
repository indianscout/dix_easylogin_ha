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

require_once(t3lib_extMgm::extPath("dix_easylogin")."res/hybridauth/hybridauth/Hybrid/Auth.php");
require_once(t3lib_extMgm::extPath("dix_easylogin")."res/hybridauth/hybridauth/Hybrid/Logger.php"); 

class tx_dixeasylogin_hybrid_auth_session_cleanup {
	public function logoff(){
		/*
		 * Sniff the session for an existing HA connections...
		 */
		if(isset($_SESSION['HA::CONFIG'])){
			if(isset($_SESSION['HA::CONFIG']['config'])){
				$config = unserialize($_SESSION['HA::CONFIG']['config']);
				
				if(isset($config['providers'])){
					$provider = $config['providers'];
					$providerName = array_keys($provider);
					
					try{
						$hybridauth = new Hybrid_Auth($provider[$providerName[0]]);
						
						$adapter = $hybridauth->authenticate($providerName[0]);
						
						Hybrid_Provider_Adapter::logout();
							
						unset($_SESSION['HA::CONFIG']);
					}catch(Exception $e){
						error_log($e->getMessage());
					}
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dix_easylogin/pi1/class.tx_dixeasylogin_hybrid_auth_session_cleanup.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dix_easylogin/pi1/class.tx_dixeasylogin_hybrid_auth_session_cleanup.php']);
}
?>