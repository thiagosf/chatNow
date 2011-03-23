<?php

class AdminModel extends Model
{
	/**
	 * Table
	 */
	protected $table 			= 'chat_admin';
	protected $id_tabela 		= 'chat_admin.user';
	protected $_filters_get 	= array('all' => 'convert');

}

?>