<table width="100%" cellspacing="2" cellpadding="0" border="0">
		<tr>
			 <td >
				<table width="100%" cellspacing="2" cellpadding="2" border="0">
				<!-- body_text //-->

				<!-- search -->
					<tbody>
						<tr>
							<td  class="pageHeading">Edit Variant <a href="<?php echo zen_href_link('inxpress.php','set=varients') ?>" style="text-align:right;float:right;cursor:pointer;">Back</a></td>
						
						</tr>
						<tr>
							<td valign="top">
								
								<?php $varientsBoxes=$db->Execute('select * from '.INXPRESS_VARIENTS_TABLE." where id='{$edit_id}'"); ?>
								<form method="post" action="<?php echo zen_href_link('inxpress.php','set=varients&action=lightBox&id='.$varientsBoxes->fields['id'].'&action=save') ?>">
								<input type="hidden" value="<?php echo $_SESSION ['securityToken'] ?>" name="securityToken">
								<table width="100%" cellspacing="0" cellpadding="2" border="0">
									<tbody>
										<tr class="dataTableHeadingRow">
											<td width="50" align="left" class="dataTableHeadingContent">Variant</td>
											<td align="center" class="dataTableHeadingContent">Length</td>
											<td align="center" class="dataTableHeadingContent">Width</td>
											<td align="center" class="dataTableHeadingContent">Height</td>
											<td align="center" class="dataTableHeadingContent">Dim weight</td>
											<td align="center" class="dataTableHeadingContent">Variable</td>
											<td align="right" class="dataTableHeadingContent">Action</td>
										
										  </tr>
										  <?php
											
											
											/* $edit_id=$varientsBoxes->fields['id']; */
											?>
												<tr class="dataTableRow"  onmouseout="rowOutEffect(this)" onmouseover="rowOverEffect(this)">
													
													<td width="50" align="left" class="dataTableContent"><?php echo $varientsBoxes->fields['variant'] ?>				
													</td>
													<td class="tdvariants length">
													<a><?php echo $varientsBoxes->fields['length'] ?></a><input id="length" class="required-entry input-text required-entry" type="hidden" value="<?php echo $varientsBoxes->fields['length'] ?>" name="length">
													</td>
													<td align="center" class="dataTableContent tdvariants width">
													<a><?php echo $varientsBoxes->fields['width'] ?></a>
													<input type="hidden" value="<?php echo $varientsBoxes->fields['width'] ?>" name="width" id="width"/>
													</td>
													<td align="center" class="dataTableContent tdvariants height">
													<a><?php echo $varientsBoxes->fields['height'] ?></a>
													<input type="hidden" value="<?php echo $varientsBoxes->fields['height'] ?>" name="height" id="height"/>
													</td>
													<td align="center" class="dataTableContent tdvariants dim_weight">
													<a><?php echo $varientsBoxes->fields['dim_weight'] ?></a>
													<input type="hidden" value="<?php echo $varientsBoxes->fields['dim_weight'] ?>" name="dim_weight" id="dim_weight"/>
													</td>
													<td align="center" class="dataTableContent"><input id="variable" class=" input-text " type="text" value="<?php echo $varientsBoxes->fields['variable'] ?>" name="variable" /></td>
													<td align="right"><input type="image" border="0" name="saveButton" title=" Save " alt="Save" src="includes/languages/english/images/buttons/button_save.gif"></td>
													
												  </tr>
											<?php
													
												
										  ?>
										   
									</tbody>
								</table>
								</form>
								<table width="100%" cellspacing="0" cellpadding="2" border="0">
									<tbody>
										<tr class="dataTableHeadingRow">
											<td width="50" align="left" class="dataTableHeadingContent">Variant</td>
											<td align="center" class="dataTableHeadingContent">Length</td>
											<td align="center" class="dataTableHeadingContent">Width</td>
											<td align="center" class="dataTableHeadingContent">Height</td>
											<td align="right" class="dataTableHeadingContent">Action</td>
										
										  </tr>
										  <?php
											$selectQuery='select * from '.INXPRESS_DHL_TABLE.' order by id DESC';
											$dhlBoxes= $db->Execute($selectQuery);
											while (!$dhlBoxes->EOF) { 
											/* $edit_id=$varientsBoxes->fields['id']; */
											?>
												<tr class="dataTableRow option-row"  onmouseout="rowOutEffect(this)" onmouseover="rowOverEffect(this)">
													
													<td width="50" align="left" class="dataTableContent" id="variant<?php echo $dhlBoxes->fields['id'] ?>"><?php echo $dhlBoxes->fields['supplies'] ?>				
													</td>
													<td align="center" class="tdvariants length" id="length<?php echo $dhlBoxes->fields['id'] ?>"><?php echo $dhlBoxes->fields['length'] ?>
													</td>
													<td align="center" class="dataTableContent tdvariants width" id="width<?php echo $dhlBoxes->fields['id'] ?>"><?php echo $dhlBoxes->fields['width'] ?>
													</td>
													<td align="center" class="dataTableContent tdvariants height" id="height<?php echo $dhlBoxes->fields['id'] ?>"><?php echo $dhlBoxes->fields['height'] ?></td>

													<td align="right"><input type="button" class="scalable"  value="Select" onclick="selectvariant('<?php echo $dhlBoxes->fields['id'] ?>');"></td>
													
												  </tr>
											<?php
												 $dhlBoxes->MoveNext();
											}	
												
										  ?>
										   
									</tbody>
								</table>
							</td>
							
						</tr>
					</tbody>
				</table>
				
			</td>
			
		</tr>
	 
		
	</table>