<?php

class RoomsModel extends Model
{
	/**
	 * Table
	 */
	protected $table 			= 'chat_rooms';
	protected $id_table 		= 'chat_rooms.id';
	protected $_filters_get 	= array('all' => 'convert');
}

?>