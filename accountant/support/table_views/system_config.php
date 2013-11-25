<?php

$detail->DB_TABLE   = 'system_config';
$detail->TABLE_ID   = 'dt_system_config';
$detail->ORDER      = array('site','name');
$detail->RETURN_URL = 'index.php?sc=config';
$detail->ADD_LINK   = '/admin/index.php?sc=form_config&key=form_content_config&mode=new&id=0';

$edit_link   = '<a href="/admin/index.php?sc=form_config&key=form_content_config&id=[(id)]" class="button-icon tip-s" title="Edit configuration"><span class="pencil-10 plix-10"></span></a>';
$delete_link = '<a href="javascript:deleteRowFunction([(id)],\'[(name)]\')" class="button-icon tip-s" title="Delete configuration"><span class="trashcan-10 plix-10"></span></a>';

$columns['name'] = array(
	'TITLE'    =>  'Name',
	'WIDTH'    =>  '40%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3><p>Site: [(site)]</p></div>',
	'REPLACE'  =>  array( 'site' => '[(site)]'),
	'JSCRIPT'  =>  'null'
);

$columns['site'] = array(
	'TITLE'    =>  'Site',
	'WIDTH'    =>  '10%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3></div>',
	'SCOPE'    =>  'row',
	'NO_SHOW'  =>  array('iPHONE','MOBILE'),
	'JSCRIPT'  =>  'null'
);

$columns['value'] = array(
	'TITLE'    =>  'Value',
	'WIDTH'    =>  '30%',
	'FORMAT'   =>  '<div class="table_td_div"><p>%s</p></div>',
	'LIMIT'    => 100,
	'FUNCTION' => 'formatCommaList',
	'NO_SHOW'  =>  array('iPHONE','MOBILE'),
	'JSCRIPT'  =>  '{ "bSortable": false }'
);

$columns['ACTION'] = array(
	'TITLE'       =>  'Actions',
	'FORMAT'      =>  '<div class="table_td_div_right">'.$edit_link.'%s'.$delete_link.'</div>',
	'REPLACE'     =>  array( 'id' => '[(id)]','name' => '[(name)]'),
	'JSCRIPT'  =>  '{ "bSortable": false }'
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
		"sAjaxSource": '/admin/ajax.php?img_code=acco&key=table_system_config&json=1',
		"bDeferRender": true,
		"aoColumns": [<?php echo $detail->getJScript(); ?>]
	});
</script>
<?php } ?>