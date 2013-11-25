<?php

$detail->DB_TABLE   = 'system_redirect';
$detail->TABLE_ID   = 'dt_system_redirect';
$detail->ORDER      = array('site','subject');
$detail->RETURN_URL = 'index.php?sc=redirect';
$detail->ADD_LINK   = '/admin/index.php?sc=form_redirect&key=form_content_redirect&mode=new&id=0';

$edit_link   = '<a href="/admin/index.php?sc=form_redirect&key=form_content_redirect&id=[(id)]" class="button-icon tip-s" title="Edit redirect"><span class="pencil-10 plix-10"></span></a>';
$delete_link = '<a href="javascript:deleteRowFunction([(id)],\'[(subject)]\')" class="button-icon tip-s" title="Delete redirect"><span class="trashcan-10 plix-10"></span></a>';


if ((isset($device['MOBILE'])) && $device['MOBILE'])
{
	$columns['subject'] = array(
	'TITLE'    =>  'Subject',
	'WIDTH'    =>  '20%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3><p>Site: [(site)]</p></div>',
	'REPLACE'  =>  array( 'site' => '[(site)]'),
	'JSCRIPT'  =>  'null'
	);
}
else
{
	$columns['subject'] = array(
	'TITLE'    =>  'Subject',
	'WIDTH'    =>  '20%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3></div>',
	'JSCRIPT'  =>  'null'
	);
}


$columns['code'] = array(
	'TITLE'    =>  'Code',
	'WIDTH'    =>  '10%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3></div>',
	'SCOPE'    =>  'row',
	'JSCRIPT'  =>  'null'
);

$columns['site'] = array(
	'TITLE'    =>  'Site',
	'WIDTH'    =>  '15%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3></div>',
	'JSCRIPT'  =>  'null',
	'NO_SHOW'  =>  array('iPHONE','MOBILE'),
);

$columns['keywords'] = array(
	'TITLE'    =>  'Keywords',
	'WIDTH'    =>  '35%',
	'FORMAT'   =>  '<div class="table_td_div"><p>%s</p></div>',
	'LIMIT'    =>  50,
	'FUNCTION' => 'formatCommaList',
	'NO_SHOW'  =>  array('iPHONE','MOBILE'),
	'JSCRIPT'  =>  '{ "bSortable": false }'
);

$columns['ACTION'] = array(
	'TITLE'       =>  'Actions',
	'FORMAT'      =>  '<div class="table_td_div_right">'.$edit_link.'%s'.$delete_link.'</div>',
	'REPLACE'     =>  array( 'id' => '[(id)]','subject' => '[(subject)]'),
	'JSCRIPT'     =>  '{ "bSortable": false }'
);

$detail->COLUMNS = $columns;

echo $detail->getTableHTML();


if (!$detail->JSON) 
{
?>

<div id="dialog-delete" title="Warning!" style="display:none" data-delete-url="<?php echo $detail->getUrl('delete',false); ?>" data-return-url="<?php echo $detail->RETURN_URL; ?>">
    <p>Are you sure you want to delete this record?</p>
    <p id="dialog_subject"></p>
</div>
		
<script type="text/javascript">
	$("#<?php echo $detail->TABLE_ID; ?> tbody").click(function(event) {
		$(oTable.fnSettings().aoData).each(function (){
			$(this.nTr).removeClass('row_selected');
		});
		$(event.target.parentNode).addClass('row_selected');
	});
	
	oTable = $("#<?php echo $detail->TABLE_ID; ?>").dataTable( {
		"sScrollY": "400px",
		"bScrollCollapse": true,
		"bPaginate": false,
		"bJQueryUI": true,
		"bProcessing": true,
		"sAjaxSource": '/admin/ajax.php?img_code=acco&key=table_system_redirect&json=1',
		"aoColumns": [<?php echo $detail->getJScript(); ?>]
	});
</script>
<?php } ?>