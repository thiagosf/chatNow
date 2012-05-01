<?php

require('app/inc/autoload.php');

Template::getHeader();

?>
	<div id="content">
		<h2>Rooms</h2>
<?php

$action = (isset($_GET['action'])) ? $_GET['action'] : '';

switch ($action)
{
	// Add room
	case 'add' :
		
		if (!empty($_POST)) {
			$data = $_POST;
			$fields = array('room', 'capacity', 'capacity_exclusive');
			
			$rooms_model = new RoomsModel;
			$rooms_model->setData($data);
			$rooms_model->setFields($fields);
			$rooms_model->insert();
			
			header('location: rooms.php');
			exit;
			
		}
		else {
?>

<form method="post" name="add_room" action="?action=add">
	<div class="block_field">
		<label for="room">Room<label>
		<input type="text" name="room" id="room" size="30" />
	</div>
	<div class="block_field">
		<label for="capacity">Capacity<label>
		<input type="text" name="capacity" id="capacity" size="5" />
	</div>
	<div class="block_field">
		<label for="capacity_exclusive">Capacity exclusive<label>
		<input type="text" name="capacity_exclusive" id="capacity_exclusive" size="5" />
	</div>
	<div class="block_field">
		<button type="submit" value="Add">Add</button>
	</div>
</form>

<?php
		}
		
		break;
		
	// Edit room
	case 'edit' : 
	
		if (isset($_GET['room'])) {
			$id_room = (int) $_GET['room'];
			
			if (!empty($_POST)) {
				$data = $_POST;
				$data['capacity'] = (int) $data['capacity'];
				$data['capacity_exclusive'] = (int) $data['capacity_exclusive'];
				
				$fields = array('room', 'capacity', 'capacity_exclusive');
				
				$rooms_model = new RoomsModel;
				$rooms_model->setData($data);
				$rooms_model->setFields($fields);
				$rooms_model->update($id_room);
				
				header('location: rooms.php');
				exit;
				
			}
			else {
			
				$rooms_model = new RoomsModel;
				$room = $rooms_model->load($id_room);
?>

<form method="post" name="add_room" action="?action=edit&room=<?=$id_room;?>">
	<div class="block_field">
		<label for="room">Room<label>
		<input type="text" name="room" id="room" size="30" value="<?=$room['room'];?>" />
	</div>
	<div class="block_field">
		<label for="capacity">Capacity<label>
		<input type="text" name="capacity" id="capacity" size="5" value="<?=$room['capacity'];?>" />
	</div>
	<div class="block_field">
		<label for="capacity_exclusive">Capacity exclusive<label>
		<input type="text" name="capacity_exclusive" id="capacity_exclusive" size="5" value="<?=$room['capacity_exclusive'];?>" />
	</div>
	<div class="block_field">
		<button type="submit" value="Edit">Edit</button>
	</div>
</form>

<?php
			}
		}
		else {
			header('location: rooms.php');
			exit;
		}
		
		break;
		
	// Delete room
	case 'delete' : 
		
		if (isset($_GET['room'])) {
			$id_room = (int) $_GET['room'];
			
			$users_model = new UsersModel;
			$users_model->setCond('id_room = '.$id_room);
			$users = $users_model->load_all();
			
			if (empty($users)) {
				$rooms = new RoomsModel;
				$rooms->delete($id_room);
				
				header('location: rooms.php');
			}
			else {
				header('location: rooms.php?message=room_not_delete');
			}
			
			exit;
		}
		
		break;
	
	// List rooms
	case '' : default : 

?>
		<p><a href="?action=add">New room</a></p>
		
		<?php
		
		if (isset($message)) {
			echo sprintf('<p>%s</p>', $message);
		}
		
		?>
		<table>
			<thead>
				<tr>
					<th>Actions</th>
					<th>Room</th>
					<th>Capacity</th>
					<th>Capacity exclusive</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				$rooms_model = new RoomsModel;
				$rooms_model->setOrderBy('room ASC');
				$rooms = $rooms_model->load_all();
				
				foreach ($rooms as $room) {
					echo '<tr>';
					echo '<td><a href="?action=delete&room='.$room['id'].'">Delete</a> | <a href="?action=edit&room='.$room['id'].'">Edit</a></td>';
					echo '<td>'.$room['room'].'</td>';
					echo '<td>'.$room['capacity'].'</td>';
					echo '<td>'.$room['capacity_exclusive'].'</td>';
					echo '</tr>';
				}
				
				?>
			</tbody>
		</table>
	
<?php
		break;
}
?>
	</div>
<?php
Template::getFooter();
?>