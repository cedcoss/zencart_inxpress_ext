<?php

$edit_id=(int)$_GET['id'];
$varientsBoxes=$db->Execute('select * from '.INXPRESS_VARIENTS_TABLE.' where id='.$edit_id);
$product = $db->Execute("select pd.products_name
                              from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
                              where p.products_id = '" . (int)$_GET['product_id'] . "'
                              and p.products_id = pd.products_id
                              and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");

?>
<script language="javascript" src="includes/inxpress/js/jquery-1.10.1.min.js"></script>
<div class="entity-edit" id="matage-options-panel">
    <div class="entry-edit-head">
    <h4 class="icon-head head-edit-form fieldset-legend">Manage Options (values of your markup)</h4>
    </div>
	<form id="variant_form" action="<?php echo zen_href_link('inxpress.php','set=varients&product_id='.$_GET['product_id'].'&id='.$varientsBoxes->fields['id'].'&action=save') ?>" method="post">
    <input type="hidden" value="<?php echo $_SESSION ['securityToken'] ?>" name="securityToken">
	<div class="box">
        <div class="hor-scroll">
            <table style="border:1px solid;" class="dynamic-grid" cellspacing="0"  cellpadding="0">
                <tr style="border:1px solid;" id="markup-options-table">
                    
						<th style="border:1px solid;">Variant Name</th>
                        <!--<th>supplies</th>-->
						<th style="border:1px solid;">length</th>
						<th style="border:1px solid;">Width</th>
						<th style="border:1px solid;">Height</th>
						<th style="border:1px solid;">Dim Weight</th>
						<th style="border:1px solid;">Variable</th>
						<th style="border:1px solid;">Action</th>
						
                  
                        
                    </tr>
                   				
					<tr class="option-row">
						<td style="width:110px;"  align="center" class="dataTableContent"><?php echo $product->fields['products_name']; ?>		
							<input name="variant" type="hidden" value="<?php echo $product->fields['products_name']; ?>	">						
													</td>
													<td  style="width:110px;" class="tdvariants length" align="center" >
													<a><?php echo $varientsBoxes->fields['length'] ?></a><input size="5" id="length" class="required-entry input-text required-entry" type="<?php if($varientsBoxes->fields['length']){ ?>hidden <?php } ?>" value="<?php echo (int)$varientsBoxes->fields['length'] ?>" name="length">
													</td>
													<td style="width:110px;" align="center" class="dataTableContent tdvariants width">
													<a><?php echo $varientsBoxes->fields['width'] ?></a>
													<input size="5" type="<?php if($varientsBoxes->fields['width']){ ?>hidden <?php } ?>" value="<?php echo (int)$varientsBoxes->fields['width'] ?>" name="width" id="width"/>
													</td>
													<td style="width:110px;" align="center" class="dataTableContent tdvariants height">
													<a><?php echo $varientsBoxes->fields['height'] ?></a>
													<input size="5" type="<?php if($varientsBoxes->fields['height']){ ?>hidden <?php } ?>" value="<?php echo (int)$varientsBoxes->fields['height'] ?>" name="height" id="height"/>
													</td>
													<td style="width:110px;" align="center" class="dataTableContent tdvariants dim_weight">
													<a><?php echo $varientsBoxes->fields['dim_weight'] ?></a>
													<input size="5" type="<?php if($varientsBoxes->fields['dim_weight']){ ?>hidden <?php } ?>" value="<?php echo (float)$varientsBoxes->fields['dim_weight'] ?>" name="dim_weight" id="dim_weight"/>
													</td>
													<td style="width:110px;" align="center" class="dataTableContent"><input size="5" id="variable" class=" input-text " type="text" value="<?php echo $varientsBoxes->fields['variable'] ?>" name="variable" /></td>
													<td style="width:110px;" align="center"><input type="image" border="0" name="saveButton" title=" Save " alt="Save" src="includes/languages/english/images/buttons/button_save.gif"></td>
					</tr>
                    		
                   
                 
            </table>
        </div>
		
		<div class="hor-scroll">
            <table class="dynamic-grid" style="border:1px solid;margin-top:10px;" cellspacing="0"  cellpadding="0">
                <tr id="markup-options-table">
                    
                        <th style="width:110px;">supplies</th>
						<th style="width:110px;">length</th>
						<th style="width:110px;">width</th>
						<th style="width:110px;">height</th>
						<!--<th>dim_weight</th>
						<th>variable</th>-->
						<th style="width:110px;">action</th>
						
                  
                        
                    </tr>
                   
                    <?php
											$selectQuery='select * from '.INXPRESS_DHL_TABLE.' order by id DESC';
											$dhlBoxes= $db->Execute($selectQuery);
											while (!$dhlBoxes->EOF) { 
											/* $edit_id=$varientsBoxes->fields['id']; */
											?>
												<tr class="dataTableRow option-row"  >
													
													<td width="50" align="center" class="dataTableContent" id="variant<?php echo $dhlBoxes->fields['id'] ?>"><?php echo $dhlBoxes->fields['supplies'] ?>				
													</td>
													<td align="center" class="tdvariants length" id="length<?php echo $dhlBoxes->fields['id'] ?>"><?php echo $dhlBoxes->fields['length'] ?>
													</td>
													<td align="center" class="dataTableContent tdvariants width" id="width<?php echo $dhlBoxes->fields['id'] ?>"><?php echo $dhlBoxes->fields['width'] ?>
													</td>
													<td align="center" class="dataTableContent tdvariants height" id="height<?php echo $dhlBoxes->fields['id'] ?>"><?php echo $dhlBoxes->fields['height'] ?></td>

													<td align="center"><input type="button" class="scalable"  value="Select" onclick="selectvariant('<?php echo $dhlBoxes->fields['id'] ?>');"></td>
													
												  </tr>
											<?php
												 $dhlBoxes->MoveNext();
											}	
												
										  ?>
                 
            </table>
        </div>
      
    </div>
	</form>
	<script type="text/javascript">
		function selectvariant(id)
		{
			/* 
			length=parseFloat(document.getElementById('length'+id).innerHTML);
			width=parseFloat(document.getElementById('width'+id).innerHTML);
			height=parseFloat(document.getElementById('height'+id).innerHTML);
			document.getElementById('length').value=document.getElementById('length'+id).innerHTML;
			document.getElementById('width').value=document.getElementById('width'+id).innerHTML;
			document.getElementById('height').value=document.getElementById('height'+id).innerHTML;
			document.getElementById('dim_weight').value=Math.ceil((length*width*height)/139); */
			length=parseFloat(inXpress('#length'+id).html());
			width=parseFloat(inXpress('#width'+id).html());
			height=parseFloat(inXpress('#height'+id).html());
			inXpress('#length').val(length);
			inXpress('.length a').html(length);
			inXpress('#width').val(width);
			inXpress('.width a').html(width);
			inXpress('#height').val(height);
			inXpress('.height a').html(height);
			dim_weight=Math.ceil((length*width*height)/139);
			inXpress('#dim_weight').val(dim_weight);
			inXpress('.dim_weight a').html(dim_weight);
			
		}
		inXpress('document').ready(function(){
			inXpress('.tdvariants a').click(function(){
				if(inXpress(this).parent().find('input[type=hidden]').attr('name')=='dim_weight')
					return true;
				inXpress(this).hide();
				inXpress(this).parent().find('input[type=hidden]').attr('type','text');
			});
			inXpress('.tdvariants input').blur(function(){
				updateValues();
				inXpress(this).attr('type','hidden');
				inXpress(this).parent().find('a').show();
			});
		});
	function updateValues()
		{
			length=parseFloat(inXpress('#length').val());
			width=parseFloat(inXpress('#width').val());
			height=parseFloat(inXpress('#height').val());
			dim_weight=Math.ceil((length*width*height)/139);
			inXpress('.length a').html(length);
			inXpress('.width a').html(width);
			inXpress('.height a').html(height);
			inXpress('.dim_weight a').html(dim_weight);
			inXpress('#dim_weight').val(dim_weight);
		}
		inXpress("#variant_form").submit(function() {
				
				inXpress('#loading-mask').css('cssText','display:block !important;height:100px;z-index:9999;');
				var url = inXpress('#variant_form').attr('action');// the script where you handle the form input.

				inXpress.ajax({
					   type: "POST",
					   url: url,
					   data: inXpress("#variant_form").serialize(), // serializes the form's elements.
					   success: function(data)
					   {
						self.parent.location.reload();
						 // show response from the php script.
					   }
					 });

				return false; // avoid to execute the actual submit of the form.
			});
	</script>
</div>
