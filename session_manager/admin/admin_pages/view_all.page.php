<?php

if (class_exists('session_handler') === false){
	die();
}

$sessions = session_handler::fetch_all();

?>
<table class="list">
	<tr class="r1">
		<th>Session ID</th>
		<th>Last Active</th>
		<th>Data Size</th>
		<th>&nbsp;</th>
	</tr>
	<?php
	
	$i = 0;
	
	foreach ($sessions as $i => $session){
		?>
		<tr class="<?php echo (($i % 2 !== 0) ? 'r1' : 'r2'); ?>">
			<td><?php echo $session['id']; ?></td>
			<td><?php echo date(session_manager_config::$date_format, $session['last_use']); ?></td>
			<td><?php echo round($session['size'] / 1024, 2); ?> KB</td>
			<td>
				<a href="?view=<?php echo $session['id']; ?>" title="View Session Data"><img src="ext/img/view.png" alt="View" /></a>
				<a href="?delete=<?php echo $session['id']; ?>" title="Delete Session"><img src="ext/img/delete.png" alt="Delete" /></a>
			</td>
		</tr>
		<?php
		
		++$i;
	}
	
	?>
</table>
