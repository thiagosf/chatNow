<?php

class MessagesModel extends Model
{
	/**
	 * Table
	 */
	protected $table 			= 'chat_messages';
	protected $id_table 		= 'chat_messages.id';
	protected $_filters_get 	= array('all' => 'convert');

}

?>