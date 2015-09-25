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

require_once(t3lib_extMgm::extPath("dix_easylogin")."pi1/class.tx_dixeasylogin_user_data.php");
require_once(t3lib_extMgm::extPath("dix_easylogin")."res/hybridauth/hybridauth/Hybrid/Auth.php");
require_once(t3lib_extMgm::extPath("dix_easylogin")."res/hybridauth/hybridauth/Hybrid/Logger.php"); 

class tx_dixeasylogin_hybrid_auth{
	protected $config;
	protected $adapter;
	protected $provider;
	protected $hybridauth;
	protected $providerName;

	function main(){
		$tmp = array_keys($this->config['providers']);
		$this->providerName = $tmp[0];
		$this->hybridauth = new Hybrid_Auth($this->config);

		$this->adapter = $this->hybridauth->authenticate($this->providerName);
		if($this->adapter->isUserConnected()){
			return($this->verifyLogin());
		}else{
			return($this->adapter->logout());
		}
	}

	function verifyLogin(){
		try{
			$userinfo = $this->getUserInfo($this->adapter->getUserProfile());
		}catch(Exception $e){
			error_log('class.tx_dixeasylogin_hybrid_auth.php: '.$e->getMessage().' Error code: '.$e->getCode());
			$this->adapter->logout();
			return($GLOBALS['piObj']->pi_getLL('error_getting_userinfo'));
		}
		
		if(isset($userinfo)){
			$user = tx_dixeasylogin_div_hybrid_auth::loginFromIdentifier($userinfo);
			
			//Process all registered hooks
			if(isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_dixeasylogin']['login_post_process'])){
				foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_dixeasylogin']['login_post_process'] AS $userFunc){
					t3lib_div::callUserFunction($userFunc, new tx_dixeasylogin_user_data($this->provider, $this->hybridauth, $userinfo, $user), $this);
				}
			}
			
			tx_dixeasylogin_div_hybrid_auth::redirectTo($this->config['redirect_url']);
		}else{
			error_log('class.tx_dixeasylogin_hybrid_auth.php: '.$e->getMessage().' Error code: '.$e->getCode());
			$this->adapter->logout();
			return($GLOBALS['piObj']->pi_getLL('error_getting_userinfo'));
		}
	}

	function getUserInfo($userinfo){
		$userinfo = get_object_vars($userinfo);
		
		if(isset($userinfo['identifier'])){
			foreach($this->provider['profileMap.'] as $dbField => $detailsField){
				if(isset($userinfo["{$detailsField}"])){
					$userinfo[$dbField] = $userinfo[$detailsField];
				}else{
					eval('$evaledProperty = '.$detailsField.';');
					$userinfo[$dbField] = $evaledProperty;
				}
			}

			return($userinfo);
		}else{
			return(null);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dix_easylogin/pi1/class.tx_dixeasylogin_hybrid_auth.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dix_easylogin/pi1/class.tx_dixeasylogin_hybrid_auth.php']);
}
?>