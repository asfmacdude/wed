<?php

$detail->DB_TABLE   = 'content_control';
$detail->TABLE_ID   = 'dt_content_control';
$detail->ORDER      = array('code');
$detail->RETURN_URL = 'index.php?sc=page_struct';
$detail->ADD_LINK   = '/admin/index.php?sc=form_cntcontrol&key=form_content_control&mode=new&id=0';

$edit_link   = '<a href="/admin/index.php?sc=form_cntcontrol&key=form_content_control&id=[(id)]" class="button-icon tip-s" title="Edit page"><span class="pencil-10 plix-10"></span></a>';
$delete_link = '<a href="javascript:deleteRowFunction([(id)],\'[(title)]\')" class="button-icon tip-s" title="Delete page"><span class="trashcan-10 plix-10"></span></a>';

$columns['title'] = array(
	'TITLE'    =>  'Title',
	'WIDTH'    =>  '20%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3><p>Site: [(site)]</p></div>',
	'REPLACE'  =>  array( 'site' => '[(site)]'),
	'JSCRIPT'  =>  'null'
);

$columns['code'] = array(
	'TITLE'    =>  'Code',
	'WIDTH'    =>  '20%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3></div>',
	'NO_SHOW'  =>  array('iPHONE','MOBILE'),
	'JSCRIPT'  =>  'null'
);

$columns['theme'] = array(
	'TITLE'    =>  'Theme',
	'WIDTH'    =>  '20%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3></div>',
	'NO_SHOW'  =>  array('iPHONE','MOBILE'),
	'JSCRIPT'  =>  'null'
);

$columns['themepage'] = array(
	'TITLE'    =>  'Theme Page',
	'WIDTH'    =>  '20%',
	'FORMAT'   =>  '<div class="table_td_div"><h3>%s</h3></div>',
	'NO_SHOW'  =>  array('iPHONE','MOBILE'),
	'JSCRIPT'  =>  'null'
);

$columns['ACTION'] = array(
	'TITLE'       =>  'Actions',
	'FORMAT'      =>  '<div class="table_td_div_right">'.$edit_link.'%s'.$delete_link.'</div>',
	'REPLACE'     =>  array( 'id' => '[(id)]', 'title' => '[(title)]'),
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
		"sAjaxSource": '/admin/ajax.php?img_code=acco&key=table_content_control&json=1',
		"bDeferRender": true,
		"aoColumns": [<?php echo $detail->getJScript(); ?>]
	});
</script>
<?php } ?>