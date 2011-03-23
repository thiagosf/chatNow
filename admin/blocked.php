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
				$ip_block = new IpBlockModel;
				$ip_block->delete($id);
				$message = 'Ip blocked was deleted';
			}
			break;
	}
}

?>
	
	<div id="content">
		<h2>IP Blocked</h2>
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
					<th>IP</th>
					<th>Permanent</th>
					<th>Date</th>
					<th>Final date</th>
				</tr>
			</thead>
			<tbody>
				<?php
				
				$ip_model = new IpBlockModel;
				$ip = $ip_model->load_all();
				
				foreach ($ip as $line) {
					echo '<tr>';
					echo '<td><a href="?delete='.$line['id'].'">Delete IP</a></td>';
					echo '<td>'.$line['id'].'</td>';
					echo '<td>'.$line['ip'].'</td>';
					echo '<td>'.($line['permanent'] ? 'Yes' : 'No	').'</td>';
					echo '<td>'.$line['timestamp'].'</td>';
					echo '<td>'.$line['end'].'</td>';
					echo '</tr>';
				}
				
				?>
			</tbody>
		</table>
	</div>
	
<?php
Template::getFooter();
?>