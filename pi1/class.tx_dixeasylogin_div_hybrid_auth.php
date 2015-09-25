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

class tx_dixeasylogin_div_hybrid_auth extends tx_dixeasylogin_div{
	static function loginFromIdentifier($userinfo){
		$user = self::fetchUserByIdentifier($userinfo['username']);
		
		if(!$user['uid'] && $GLOBALS['piObj']->conf['allowCreate']){
			$user = self::createUser($userinfo);
		}else{
			$user = self::updateUser($userinfo);
		}

		if(isset($user) && isset($user['uid'])){
			return(self::login($user));
		} else {
			return(sprintf($GLOBALS['piObj']->pi_getLL('nouser'), $userinfo['username']));
		}
	}
	
	static function login($user){
		//See: https://forge.typo3.org/issues/62194
		$tsfe = $GLOBALS['TSFE'];
		$tsfe->fe_user->createUserSession($user);
		$tsfe->fe_user->setAndSaveSessionData('dummy', TRUE);
		return($user);
	}
	
	static function fetchUserByIdentifier($identifier){
		$table = 'fe_users';
		$where = sprintf('username = %s %s', $GLOBALS['TYPO3_DB']->fullQuoteStr($identifier, $table), $GLOBALS['piObj']->cObj->enableFields($table));
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, $where);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		return($row);
	}
	
	static function createUser($userinfo){
		$table = 'fe_users';
		
		$tstamp = time();
		$values = array(
			'email'			=> isset($userinfo['email']) ? $userinfo['email'] : '',
			'username' 		=> $userinfo['username'],
			'pid' 			=> $GLOBALS['piObj']->conf['user_pid'],
			'crdate' 		=> $tstamp,
			'tstamp' 		=> $tstamp,
			'password' 		=> t3lib_div::getRandomHexString(32),
			'usergroup' 	=> $userinfo['usergroup'],
			'name' 			=> isset($userinfo['name']) ? $userinfo['name'] : '',
			'image' 		=> isset($userinfo['image']) ? $userinfo['image'] : '',
			'first_name' 	=> isset($userinfo['first_name']) ? $userinfo['first_name'] : '',
			'last_name' 	=> isset($userinfo['last_name']) ? $userinfo['last_name'] : '',
			'zip' 			=> isset($userinfo['zip']) ? $userinfo['zip'] : '',
			'city' 			=> isset($userinfo['city']) ? $userinfo['city'] : '',
			'country' 		=> isset($userinfo['country']) ? $userinfo['country'] : '',
			'address' 		=> isset($userinfo['address']) ? $userinfo['address'] : '',
			'gender' 		=> isset($userinfo['gender']) ? $userinfo['gender'] : '',
			'language' 		=> isset($userinfo['language']) ? $userinfo['language'] : 'en',
			'fax' 			=> isset($userinfo['fax']) ? $userinfo['fax'] : '',
			'date_of_birth' => isset($userinfo['date_of_birth']) ? $userinfo['date_of_birth'] : ''
		);
		
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $values);
		$uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
		self::linkIdentifier2User($userinfo['username'], $uid);
		$user = self::fetchUserByIdentifier($userinfo['username']);
		return($user);
	}
	
	static function updateUser($userinfo){
		$table = 'fe_users';
		
		$tstamp = time();
		$values = array(
			'email'			=> $userinfo['email'],
			'username' 		=> $userinfo['username'],
			'pid' 			=> $GLOBALS['piObj']->conf['user_pid'],
			'crdate' 		=> $tstamp,
			'tstamp' 		=> $tstamp,
			'password' 		=> t3lib_div::getRandomHexString(32),
			'usergroup' 	=> $userinfo['usergroup'],
			'name' 			=> $userinfo['name'],
			'image' 		=> $userinfo['image'],
			'first_name' 	=> $userinfo['first_name'],
			'last_name' 	=> $userinfo['last_name'],
			'zip' 			=> $userinfo['zip'],
			'city' 			=> $userinfo['city'],
			'country' 		=> $userinfo['country'],
			'address' 		=> $userinfo['address'],
			'gender' 		=> $userinfo['gender'],
			'language' 		=> $userinfo['language'],
			'fax' 			=> $userinfo['fax'],
			'date_of_birth' => $userinfo['date_of_birth']
		);
		
		$where = sprintf('username = %s %s', $GLOBALS['TYPO3_DB']->fullQuoteStr($userinfo['username'], $table), $GLOBALS['piObj']->cObj->enableFields($table));
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $values);
		$user = self::fetchUserByIdentifier($userinfo['username']);
		
		return($user);
	}
	
	static function redirectTo($location){
		header("Location: {$location}");
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dix_easylogin/pi1/class.tx_dixeasylogin_div_hybrid_auth.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dix_easylogin/pi1/class.tx_dixeasylogin_div_hybrid_auth.php']);
}
?>