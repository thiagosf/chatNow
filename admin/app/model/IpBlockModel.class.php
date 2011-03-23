<?php

class IpBlockModel extends Model
{
	/**
	 * Table
	 */
	protected $table 			= 'chat_ip_block';
	protected $id_table 		= 'chat_ip_block.id';

	// Check blocked
	public static function isBlocked () {
		$ip_block_model = new self;
		$ip_block_model->setCond('ip = "'.$_SERVER['REMOTE_ADDR'].'"');
		$ip_block = $ip_block_model->load();
		return !empty($ip_block);
	}
	
}

?>