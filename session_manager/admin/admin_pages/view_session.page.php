<?php

if (class_exists('session_handler') === false){
	die();
}

$data = unserialize(session_handler::read_serialized($_GET['view']));

?>
<table class="list">
	<tr class="r1">
		<th>Key</th>
		<th class="left">Value</th>
	</tr>
	<?php
	
	$i = 0;
	
	foreach ($data as $key => $value){
		?>
		<tr class="<?php echo (($i % 2 !== 0) ? 'r1' : 'r2'); ?>">
			<td><?php echo $key; ?></td>
			<td class="left"><pre><?php echo var_dump($value); ?></pre></td>
		</tr>
		<?php
		
		++$i;
	}
	
	?>
</table>
