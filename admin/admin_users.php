<?php

require('app/inc/autoload.php');

Template::getHeader();

?>
	<div id="content">
		<h2>Admin Users</h2>
<?php

$action = (isset($_GET['action'])) ? $_GET['action'] : '';

switch ($action)
{
	// Add admin_user
	case 'add' :
		
		if (!empty($_POST)) {
			$data = $_POST;

			$admin_users_model = new AdminUsersModel;
			$admin_users_model->setCond('user = "' . $data['user'] . '"');
			$check = $admin_users_model->load();

			if (empty($check)) {
				$data['password'] = md5($data['password']);
				$fields = array('user', 'password');
				
				$admin_users_model = new AdminUsersModel;
				$admin_users_model->setData($data);
				$admin_users_model->setFields($fields);
				$admin_users_model->insert();

				$message = 'success_add';
			}
			else {
				$message = 'user_exists';
			}
			
			header('location: admin_users.php?message='.$message);
			exit;
			
		}
		else {
?>

<form method="post" name="add_admin_user" action="?action=add">
	<div class="block_field">
		<label for="user">User<label>
		<input type="text" name="user" id="user" size="30" />
	</div>
	<div class="block_field">
		<label for="password">Password<label>
		<input type="password" name="password" id="password" size="20" />
	</div>
	<div class="block_field">
		<button type="submit" value="Add">Add</button>
	</div>
</form>

<?php
		}
		
		break;
		
	// Edit admin_user
	case 'edit' : 
	
		if (isset($_GET['admin_user'])) {
			$data = $_POST;
			$id_admin_user = (int) $_GET['admin_user'];

			if (!empty($_POST)) {
				$admin_users_model = new AdminUsersModel;
				$admin_users_model->setCond('user = "' . $data['user'] . '"');
				$check = $admin_users_model->load();
				
				if (empty($check) || $check[0]['id'] == $id_admin_user) {
					$data['password'] = md5($data['password']);
					
					$fields = array('user', 'password');
					
					$admin_users_model = new AdminUsersModel;
					$admin_users_model->setData($data);
					$admin_users_model->setFields($fields);
					$admin_users_model->update($id_admin_user);
					$message = 'success_update';
				}
				else {
					$message = 'user_exists';
				}
				
				header('location: admin_users.php?message='.$message);
				exit;
				
			}
			else {
			
				$admin_users_model = new AdminUsersModel;
				$admin_user = $admin_users_model->load($id_admin_user);
?>

<form method="post" name="add_admin_user" action="?action=edit&admin_user=<?=$id_admin_user;?>">
	<div class="block_field">
		<label for="user">User<label>
		<input type="text" name="user" id="user" size="30" value="<?=$admin_user['user'];?>" />
	</div>
	<div class="block_field">
		<label for="password">Password<label>
		<input type="password" name="password" id="password" size="20" />
	</div>
	<div class="block_field">
		<button type="submit" value="Edit">Edit</button>
	</div>
</form>

<?php
			}
		}
		else {
			header('location: admin_users.php');
			exit;
		}
		
		break;
		
	// Delete admin_user
	case 'delete' : 
		
		if (isset($_GET['admin_user'])) {
			$id_user = (int) $_GET['admin_user'];
			
			if ($id_user != $_SESSION['id_admin'] && $id_admin != 1) {
				if (empty($users)) {
					$admin_users = new AdminUsersModel;
					$admin_users->delete($id_user);
					
					header('location: admin_users.php');
				}
				else {
					header('location: admin_users.php?message=admin_user_not_delete');
				}
			}
			else {
				header('location: admin_users.php');
			}

			exit;
		}
		
		break;
	
	// List admin_users
	case '' : default : 

?>
		<p><a href="?action=add">New user</a></p>
		
		<?php
		
		if (isset($message)) {
			echo sprintf('<p>%s</p>', $message);
		}
		
		?>
		<table>
			<thead>
				<tr>
					<th width="100">Actions</th>
					<th>User</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				$admin_users_model = new AdminUsersModel;
				$admin_users_model->setOrderBy('user ASC');
				$admin_users = $admin_users_model->load_all();
				
				foreach ($admin_users as $admin_user) {
					echo '<tr>';
					echo '<td><a href="?action=delete&admin_user='.$admin_user['id'].'">Delete</a> | <a href="?action=edit&admin_user='.$admin_user['id'].'">Edit</a></td>';
					echo '<td>'.$admin_user['user'].'</td>';
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