<?php
/**
 * Работа с деньгами в игре 
 */

 class Game_Finance
 {
 	
	//получает деньги юзера 
	static public function getMoney($uid = null, $moneyType = null)
	{
		if (empty($uid)) return false;
		
		$db = Zend_Registry::get('db');
		
		$sql = 'SELECT  fid, balance, account_type, money_type, last_changed_at, account_last_transaction_id  
				FROM user_finance_accounts_tbl ';
		
		if ((empty($moneyType)) || (($moneyType != '$') && ($moneyType != 'gold')))
			$sql = $sql . ' WHERE user_id = '.$db->quote($uid).' AND account_type = \'main\' ORDER BY money_type DESC  ';
		else
			$sql = $sql . ' WHERE user_id = '.$db->quote($uid).' AND account_type = \'main\' AND money_type = '.$db->quote($moneyType).' ';
			
		$res = $db->fetchAll($sql);
		
		if (empty($res))
			return false;
		else
		{
			$_result = Array();
			
			foreach($res as $x)
			{
				$_result[ $x['money_type'] ] = $x;
			}
		}
		
		return $_result;	
	}
	
	
 }