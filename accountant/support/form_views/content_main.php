<?php

$detail->DB_TABLE   = 'content_main';
$detail->KEY        = 'form_content_main';
$detail->RETURN_URL = 'index.php?sc=content';

$detail->executeForm();

if ($detail->RETURN)
{
	return $detail->RETURN_VAL;
}

?>


<div class="inner-spacer">
    <div class="g_1" id="content_form_tabs">
    	<form id="form-content_main" method="post" action="<?php echo $detail->getUrl('save'); ?>">
        <ul class="etabs">
            <li class="etabs-active"><a href="#etab31">Edit Article</a></li>
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
                <label for="code">[(code_LABEL)]</label> 
            </div>
            <div class="g_1_6">
                <input type="text" id="code" name="code" value="[(code_VALUE)]" class="tip-n" title="[(code_INSTRUCT)]" data-validation-type="present" />
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            <hr>
            
            <div class="g_3_6">                                      
                <label class="top_label" for="details">[(details_LABEL)]</label>
                <span class="field-helper">[(details_INSTRUCT)]</span>
                <textarea name="details" id="details" class="half_height tip-n" title="[(details_INSTRUCT)]">[(details_VALUE)]</textarea>
            </div>
            <div class="g_3_6_last">                                      
                <label class="top_label" for="keywords">[(keywords_LABEL)]</label>
                <span class="field-helper">[(keywords_INSTRUCT)]</span>
                <textarea name="keywords" id="keywords" class="half_height tip-n" title="[(keywords_INSTRUCT)]">[(keywords_VALUE)]</textarea>
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            
        	<div class="g_1">
                <label class="top_label" for="excerpt">[(excerpt_LABEL)]</label>
                <span class="field-helper">[(excerpt_INSTRUCT)]</span>
            </div>
        	<div class="g_1"> 
                <textarea name="excerpt" id="excerpt" class="mceSimple" style="width:100%;">[(excerpt_VALUE)]</textarea>
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
            
        	<div class="g_1">
                <label class="top_label" for="fullarticle">[(fullarticle_LABEL)]</label>
                <span class="field-helper">[(fullarticle_INSTRUCT)]</span>
            </div>
        	<div class="g_1"> 
                <textarea name="fullarticle" id="fullarticle" class="mceAdvanced" style="width:100%;">[(fullarticle_VALUE)]</textarea>
            </div>
            
            <div class="spacer-10"><!-- spacer 10px --></div>
        
		    <div class="g_1">
		        <a href="index.php?sc=content" class="button-icon-text" id="cancel">Return to List<span class="rows4-10 plix-10"></span></a>
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

CKEDITOR.replace( 'fullarticle' , {
	toolbar: 'FullArticle',
	height: 400
});

CKEDITOR.replace( 'excerpt', {
    toolbar: 'Excerpt',
    height: 100
});

</script>