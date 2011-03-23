<?php

class UsersModel extends Model
{
	/**
	 * Table
	 */
	protected $table 			= 'chat_users';
	protected $id_tabela 		= 'chat_users.id';
	protected $_filters_get 	= array('all' => 'convert');
	
	// Delete user idle
	public static function userDeleteIdle () {
	
		$users_model = new UsersModel;
		$users_model->setCond('timestamp < "'.(date('Y-m-d H:i:s', strtotime('-'.TIME_USER_IDLE.' seconds'))).'"');
		$users_model->setFields(array('active'));
		$users_model->setData(array('active' => 0));
		$users = $users_model->load_all();
		
		if (count($users)) {
			foreach ($users as $load) {
				if (isset($_SESSION['chat_login']['timestamp']) && $load['timestamp'] >= $_SESSION['chat_login']['timestamp']) {
					AlertModel::addExit($load['user'], $load['id'], $load['id_room']);
				}
			}
		}
		
		return $users_model->delete();
	}

}

?>