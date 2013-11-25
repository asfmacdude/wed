<?php

$detail->DB_TABLE   = 'content_control';
$detail->KEY        = 'form_content_control';
$detail->RETURN_URL = 'index.php?sc=page_struct';

$detail->executeForm();

if ($detail->RETURN)
{
	return $detail->RETURN_VAL;
}

?>


<div class="inner-spacer">
    <div class="g_1" id="content_form_tabs">
    	<form id="form-content_control" method="post" action="<?php echo $detail->getUrl('save'); ?>">
        <ul class="etabs">
            <li class="etabs-active"><a href="#etab31">Edit Page</a></li>
            <li><a href="#etab32">Help</a></li>
        </ul>
         
        <div id="etab31" class="etabs-content">
        	<div class="g_1_9">
                <label for="title">[(title_LABEL)]</label> 
            </div>
            <div class="g_2_6">
                <input type="text" id="title" name="title" value="[(title_VALUE)]" class="tip-n" title="[(title_INSTRUCT)]" data-validation-type="present"/>
            </div>
            <div class="g_1_9">
                <label for="status">[(status_LABEL)]</label> 
            </div>
            <div class="g_1_6">
                <select name="status" data-validation-type="present" class="tip-n" title="[(status_INSTRUCT)]" data-validation-type="present" >
                    <option value="">-- select option --</option>
                    <?php echo $detail->getSelectListHTML('status'); ?>
                </select>
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            
            <div class="g_1_9">
                <label for="site">[(site_LABEL)]</label> 
            </div>
            <div class="g_1_6">
            	<select name="site" data-validation-type="present" class="tip-n" title="[(site_INSTRUCT)]" data-validation-type="present" >
                    <option value="">-- select option --</option>
                    <?php echo $detail->getSelectListHTML('site'); ?>
                </select>    
            </div>
            
            <div class="g_1_9">
                <label for="theme">[(theme_LABEL)]</label> 
            </div>
            <div class="g_1_6">
            	<select name="theme" data-validation-type="present" class="tip-n" title="[(theme_INSTRUCT)]" data-validation-type="present" >
                    <option value="">-- select option --</option>
                    <?php echo $detail->getSelectListHTML('theme'); ?>
                </select>    
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            
            <div class="g_1_9">
                <label for="code">[(code_LABEL)]</label> 
            </div>
            <div class="g_1_6">
                <input type="text" id="code" name="code" value="[(code_VALUE)]" class="tip-n" title="[(code_INSTRUCT)]" data-validation-type="present" />
            </div>
            
            <div class="g_1_9">
                <label for="themepage">[(themepage_LABEL)]</label> 
            </div>
            <div class="g_1_6">
            	<select name="themepage" data-validation-type="present" class="tip-n" title="[(themepage_INSTRUCT)]" data-validation-type="present" >
                    <option value="">-- select option --</option>
                    <?php echo $detail->getSelectListHTML('themepage'); ?>
                </select>    
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            <hr>
            
            <div class="g_1">                                      
                <label class="top_label" for="structure">[(structure_LABEL)]</label>
                <span class="field-helper">[(structure_INSTRUCT)]</span>
                <textarea name="structure" id="structure" class="tip-n autogrow-textarea" title="[(structure_INSTRUCT)]">[(structure_VALUE)]</textarea>
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
        
		    <div class="g_1">
		        <a href="index.php?sc=page_struct" class="button-icon-text" id="cancel">Return to List<span class="rows4-10 plix-10"></span></a>
		        <a href="#" class="button-icon-text delete-button" id="delete-button" data-id="<?php echo $detail->ID; ?>" >Delete Record<span class="trashcan-10 plix-10"></span></a>
		        <input onClick="CKupdate();" type="submit" value="Save Changes" class="button-text"/>
		    </div>
        
        </div> 
        <div id="etab32" class="etabs-content">
        	Documentation will appear here.
        </div>
        
        <div id="dialog-delete" title="Warning!" style="display:none" data-delete-url="<?php echo $detail->getUrl('delete',false); ?>" data-return-url="<?php echo $detail->RETURN_URL; ?>" >
            <p>Are you sure you want to delete this record?</p>
            <p>Title: <strong>[(title_VALUE)]</strong></p>
        </div>
        
        </form>                                                                                        
    </div>
</div>                                            

<script type="text/javascript">

$("#content_form_tabs").eTabs({
	storeTab: false,
	responsive: false,
	callback: function(){ }	
});

$(".delete-button").click(function() {
	var deleteobj = $(this);
	id = deleteobj.data("id");
	formDeleteRecord(id);
	return false;
});

</script>