<?php

class AlertModel extends Model
{
	/**
	 * Table
	 */
	protected $table 			= 'chat_alert';
	protected $id_table 		= 'chat_alert.id';
	protected $_filters_get 	= array('all' => 'convert');
	
	// Adiciona exit do user
	public static function addExit ($user, $id_user, $id_room) {

		$data = new StdClass;
		$data->user 		= $user;
		$data->id_user 		= $id_user;
		$data->message 		= 'left the room';
		$data->id_room 		= $id_room;
		$data->reserved 	= 0;
		$data->type 		= 'exit';
		$data->to_user 		= 0;
		$data->timestamp 	= date('Y-m-d H:i:s');
		
		$fields = array_keys((array) $data);
		
		$alert_model = new MessagesModel;
		$alert_model->setFields($fields);
		$alert_model->setData($data);
		
		return $alert_model->insert();
	}
	
	// Adiciona exit do user
	public static function addEntry ($user, $id_user, $id_room) {
		$data = new StdClass;
		$data->user 		= $user;
		$data->id_user 		= $id_user;
		$data->message 		= 'entered the room';
		$data->id_room 		= $id_room;
		$data->reserved 	= 0;
		$data->type 		= 'entry';
		$data->to_user 		= 0;
		$data->timestamp 	= date('Y-m-d H:i:s');
		
		$fields = array_keys((array) $data);
		
		$alert_model = new MessagesModel;
		$alert_model->setFields($fields);
		$alert_model->setData($data);
		
		return $alert_model->insert();
	}
	

}

?>