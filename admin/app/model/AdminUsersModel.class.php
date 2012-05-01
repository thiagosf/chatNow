<?php

class AdminUsersModel extends Model
{
	/**
	 * Table
	 */
	protected $table 			= 'chat_admin';
	protected $id_tabela 		= 'chat_admin.id';
	protected $_filters_get 	= array('all' => 'convert');
	
}

?>