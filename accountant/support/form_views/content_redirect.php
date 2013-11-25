<?php

$detail->DB_TABLE   = 'system_redirect';
$detail->KEY        = 'form_content_redirect';
$detail->RETURN_URL = 'index.php?sc=redirect';

$detail->executeForm();

if ($detail->RETURN)
{
	return $detail->RETURN_VAL;
}

?>


<div class="inner-spacer">
    <div class="g_1" id="content_form_tabs">
    	<form id="form-content_redirect" method="post" action="<?php echo $detail->getUrl('save'); ?>">
        <ul class="etabs">
            <li class="etabs-active"><a href="#etab31">Edit Redirect</a></li>
            <li><a href="#etab32">Help</a></li>
        </ul>
         
        <div id="etab31" class="etabs-content">
        	<div class="g_1_9">
                <label for="subject">[(subject_LABEL)]</label> 
            </div>
            <div class="g_2_6">
                <input type="text" id="subject" name="subject" value="[(subject_VALUE)]" class="tip-n" title="[(subject_INSTRUCT)]" data-validation-type="present"/>
            </div>
            <div class="g_1_9">
                <label for="site">[(site_LABEL)]</label> 
            </div>
            <div class="g_1_6">
                <select name="site" data-validation-type="present" class="tip-n" title="[(site_INSTRUCT)]" data-validation-type="present" >
                    <option value="">-- select option --</option>
                    <?php echo $detail->getSelectListHTML('site'); ?>
                </select>
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            
            <div class="g_1_9">
                <label for="action">[(action_LABEL)]</label> 
            </div>
            <div class="g_2_6">
                <input type="text" id="action" name="action" value="[(action_VALUE)]" class="tip-n" title="[(action_INSTRUCT)]" />
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            
            <div class="g_1_9">
                <label for="code">[(code_LABEL)]</label> 
            </div>
            <div class="g_2_6">
                <input type="text" id="code" name="code" value="[(code_VALUE)]" class="tip-n" title="[(code_INSTRUCT)]" data-validation-type="present" />
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            
            <div class="g_1_9">
                <label for="keywords">[(keywords_LABEL)]</label> 
            </div>
            <div class="g_7_9">
                <textarea name="keywords" id="keywords" class="tip-n autogrow-textarea" title="[(keywords_INSTRUCT)]">[(keywords_VALUE)]</textarea>
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>

            <hr>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
        
		    <div class="g_1">
		        <a href="index.php?sc=redirect" class="button-icon-text" id="cancel">Return to List<span class="rows4-10 plix-10"></span></a>
		        <a href="#" class="button-icon-text delete-button" id="delete-button" data-id="<?php echo $detail->ID; ?>" >Delete Record<span class="trashcan-10 plix-10"></span></a>
		        <input onClick="CKupdate();" type="submit" value="Save Changes" class="button-text"/>
		    </div>
        
        </div>
        <div id="etab32" class="etabs-content">
        	Documentation will appear here.
        </div>

        <div id="dialog-delete" title="Warning!" style="display:none" data-delete-url="<?php echo $detail->getUrl('delete',false); ?>" data-return-url="<?php echo $detail->RETURN_URL; ?>" >
            <p>Are you sure you want to delete this record?</p>
            <p>Subject: <strong>[(subject_VALUE)]</strong></p>
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