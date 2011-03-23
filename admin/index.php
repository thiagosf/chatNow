<?php

require('app/inc/autoload.php');

Template::getHeader();

// Control
if (!empty($get))
{
	$keys = array_keys($get);
	switch ($keys[0])
	{
		case 'delete' : 
			$id = (int) $_GET['delete'];
			if ($id) {
				$users = new UsersModel;
				$users->delete($id);
				$message = 'User deleted';
			}
			break;
			
		case 'block_user' : 
			$id = (int) $_GET['block_user'];
			if ($id) {
				$users = new UsersModel;
				$users->setData(array('active' => 0));
				$users->setFields(array('active'));
				$users->update($id);
				$message = 'User blocked';
			}
			break;
			
		case 'block_ip' : 
			$ip = $_GET['block_ip'];
			if ($ip) {
			
				$ip_block = new IpBlockModel;
				$ip_block->setCond('ip = "'.$ip.'"');
				$data = $ip_block->load_all();
				
				if (empty($data)) {
					$data = new StdClass;
					$data->ip = $ip;
					$data->permanent = 0;
					$data->timestamp = date('Y-m-d H:i:s');
					$data->end = date('Y-m-d H:i:s', strtotime('+1 day'));
					$fields = array_keys((array) $data);
				
					$ip_block = new IpBlockModel;
					$ip_block->setData($data);
					$ip_block->setFields($fields);
					$ip_block->insert();
					$message = 'Ip blocked';
				}
				else {
					$message = 'The ip has been blocked';
				}
			}
			
			break;
	}
}

?>

	<div id="content">
		<h2>Users</h2>
		
		<div id="rooms_filter">
			<?php
			
			$rooms_model = new RoomsModel;
			$rooms_model->setOrderBy('room ASC');
			$rooms = $rooms_model->load_all();
			
			echo 'Room filter: <a href="index.php">All</a> ';
			
			foreach($rooms as $room) {
				echo sprintf('| <a href="?room=%s">%s</a> ', $room['id'], $room['room']);
			}
			
			?>
		</div>
		
		<?php
		
		if (isset($message)) {
			echo sprintf('<p>%s</p>', $message);
		}
		
		?>
		<table>
			<thead>
				<tr>
					<th>Actions</th>
					<th>ID</th>
					<th>User</th>
					<th>IP</th>
					<th>Active</th>
					<th>Room</th>
					<th>Capacity</th>
					<th>Capacity exclusive</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				$conditions = null;
				if (isset($_GET['room'])) {
					$id_room = (int) $_GET['room'];
					$conditions = 'chat_rooms.id = '.$id_room;
				}
				
				$users_model = new UsersModel;
				$users_model->setFieldsSelect(array(
					'chat_users.*', 
					'chat_rooms.room', 
					'chat_rooms.description', 
					'chat_rooms.capacity', 
					'chat_rooms.capacity_exclusive'
				));
				$users_model->setJoin('INNER JOIN chat_rooms ON (chat_users.id_room = chat_rooms.id)');
				$users_model->setCond($conditions);
				$users = $users_model->load_all();
				
				foreach ($users as $user) {
					echo '<tr>';
					echo '<td><a href="?delete='.$user['id'].'">Delete User</a> | <a href="?block_ip='.$user['ip'].'">Block IP</a> | <a href="?block_user='.$user['id'].'">Block User</a></td>';
					echo '<td>'.$user['id'].'</td>';
					echo '<td>'.$user['user'].'</td>';
					echo '<td>'.$user['ip'].'</td>';
					echo '<td>'.($user['active'] ? 'Yes' : 'No	').'</td>';
					echo '<td>'.$user['room'].'</td>';
					echo '<td>'.$user['capacity'].'</td>';
					echo '<td>'.$user['capacity_exclusive'].'</td>';
					echo '</tr>';
				}
				
				?>
			</tbody>
		</table>
	</div>
	
<?php
Template::getFooter();
?>