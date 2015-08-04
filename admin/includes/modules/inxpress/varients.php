<?php 
/**********Save,Delete,Update**********/
$edit_id=0;
$edit=false;
if(isset($_GET['id'])&&!empty($_GET['id']))
{
	$edit_id=(int)$_GET['id'];
	if(isset($_GET['action'])&&$_GET['action']=='save')
	{
		
		$table=INXPRESS_VARIENTS_TABLE;
		
	
		$length=(float)$_POST['length'];
		$width=(float)$_POST['width'];
		$height=(float)$_POST['height'];
		$dim_weight=(float)$_POST['dim_weight'];
		$variable=(float)$_POST['variable'];
		/* $variant=isset($_POST['variant'])?$_POST['variant']:''; */
		$db->Execute("update {$table} set length='{$length}',width='{$width}',height='{$height}',variable='{$variable}',dim_weight='{$dim_weight}' where id='{$edit_id}'");
		
		$edit=true;
	} 
	elseif(isset($_GET['action'])&&$_GET['action']=='delete')
	{
		$edit=false;
		$table=INXPRESS_VARIENTS_TABLE;
		$db->Execute("delete from {$table} where id='{$edit_id}'");
	} 
	elseif(isset($_GET['action'])&&$_GET['action']=='edit')
	{
		$edit=true;
		
	} 
}
elseif(isset($_GET['product_id'])&&!empty($_GET['product_id'])&&isset($_GET['action'])&&$_GET['action']=='save')
{
	$table=INXPRESS_VARIENTS_TABLE;
	$length=(float)$_POST['length'];
	$width=(float)$_POST['width'];
	$height=(float)$_POST['height'];
	$dim_weight=(float)$_POST['dim_weight'];
	$variable=(float)$_POST['variable'];
	$product_id=(int)$_GET['product_id'];
	$variant=isset($_POST['variant'])?$_POST['variant']:'';
	$modifieddate=date('Y-m-d H:i:s');
	$db->Execute("INSERT INTO {$table} (length,width,product_id,height,variable,dim_weight,variant,modifieddate) VALUES('{$length}','{$width}','{$product_id}','{$height}','{$variable}','{$dim_weight}','{$variant}','{$modifieddate}') ");
	
	$edit=true;
}
elseif(isset($_GET['action'])&&$_GET['action']=='upload')
{
	if(isset($_FILES["file"]["name"]))
		{	
			$table=INXPRESS_VARIENTS_TABLE;
			$handle = fopen($_FILES["file"]["tmp_name"], "r");
			$data = fgetcsv($handle, 4000, ",");
			$indexes=array();
			foreach($data as $key=>$val)
			{
				$indexes[$val]=$key;
			}
			
			if(!isset($indexes['length'])||!isset($indexes['width'])||!isset($indexes['height'])||!isset($indexes['id']))
			{
				echo 'Some required attributes are missing.';
				
				
			}
			$count=0;			$lbh_check=0;			$success=0;
			while(($data = fgetcsv($handle, 4000, ",")) !== FALSE)
			{	

				$data1=array();
				$product = $db->Execute("select pd.products_name
                              from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
                              where p.products_id = '" . (int)$data[$indexes['id']] . "'
                              and p.products_id = pd.products_id
                              and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
				$check=$db->Execute("SELECT product_id FROM {$table} where product_id='{$data[$indexes['id']]}'");
				
				if(isset($product->fields['products_name']))
				{
					foreach($indexes as $key=>$index )
					{
						$data1[$key]=$data[$index];
					}		
					$product_id=$data[$indexes['id']];		
					$height=(float)$data1['height'];
					$width=(float)$data1['width'];
					$length=(float)$data1['length'];
					$modifieddate=date('Y-m-d H:i:s');
					$variant=$product->fields['products_name'];
					
					$dim_weight=ceil((((float)$data1['height'])*((float)$data1['width'])*((float)$data1['length']))/139);	
					if(!isset($check->fields['product_id']))
					{
						
					
						$db->Execute("INSERT INTO {$table} (length,width,product_id,height,variable,dim_weight,variant,modifieddate) VALUES('{$length}','{$width}','{$product_id}','{$height}','','{$dim_weight}','{$variant}','{$modifieddate}') ");
						$success++;
					}
					else
					{
						$db->Execute("UPDATE {$table} SET length='{$length}',width='{$width}',height='{$height}',variable='',dim_weight='{$dim_weight}',variant='{$variant}',modifieddate='{$modifieddate}' WHERE product_id='{$product_id}' ");
						$success++;
						
					}
					
				}
				else
				{
					$count++;
				}
			}			
			if($count!=0)			
			{	
				echo $count.' Id\'s of csv are not matching to magento products..<br/>';
			}			
			if($lbh_check!=0){
				echo $lbh_check.' Row\'s of csv have not valid LBH value..<br/>';
			}
			echo 'Csv imported successfully';
			
		}
}


/**********Save,Delete,Update**********/
if($edit)
{
	require(DIR_WS_INCLUDES . 'modules/inxpress/varients_edit.php');
}
else
{
	$page_size=20;
	$page=(int)$_GET['page'];
	$selectQuery='select * from '.INXPRESS_VARIENTS_TABLE.' order by id DESC';

	$currentPageNumber=$page;
	$collection_split = new splitPageResults($currentPageNumber,$page_size, $selectQuery,$query_num_rows);
	$varientsBoxes= $db->Execute($selectQuery);


	 ?>
	 
	 <table width="100%" cellspacing="2" cellpadding="0" border="0">
		<tr>
			 <td >
				<table width="100%" cellspacing="2" cellpadding="2" border="0">
				<!-- body_text //-->

				<!-- search -->
					<tbody>
						<tr>
							<td  class="pageHeading">Inxpress Manage Variants</td>
						</tr>
						<tr>
							<td valign="top">
								<table width="100%" cellspacing="0" cellpadding="2" border="0">
									<tbody>
										<tr class="dataTableHeadingRow">
											<td align="center" class="dataTableHeadingContent">Product ID</td>
											<td width="50" align="left" class="dataTableHeadingContent">Supplies</td>
											<td class="dataTableHeadingContent">Length</td>
											<td align="right" class="dataTableHeadingContent">Width</td>
											<td align="center" class="dataTableHeadingContent">Height</td>
											<td align="right" class="dataTableHeadingContent">Dim weight</td>
											<td align="right" class="dataTableHeadingContent">Variable</td>
											<td align="right" class="dataTableHeadingContent">Created On</td>
											<td align="center" class="dataTableHeadingContent">Action</td>
										
										  </tr>
										  <?php
											
											while (!$varientsBoxes->EOF) { 
											/* $edit_id=$varientsBoxes->fields['id']; */
											?>
												<tr class="dataTableRow" onclick="document.location.href='<?php echo zen_href_link('inxpress.php','set=varients&action=edit&id='.$varientsBoxes->fields['id'].'&page='.$page) ?>'" onmouseout="rowOutEffect(this)" onmouseover="rowOverEffect(this)">
													<td align="center" class="dataTableContent"><?php echo $varientsBoxes->fields['product_id'] ?></td>
													<td width="50" align="left" class="dataTableContent"><?php echo $varientsBoxes->fields['variant'] ?></td>
													<td class=""><?php echo $varientsBoxes->fields['length'] ?></td>
													<td align="right" class="dataTableContent"><?php echo $varientsBoxes->fields['width'] ?></td>
													<td align="center" class="dataTableContent"><?php echo $varientsBoxes->fields['height'] ?></td>
													<td align="right" class="dataTableContent"><?php echo $varientsBoxes->fields['dim_weight'] ?></td>
													<td align="right" class="dataTableContent"><?php echo $varientsBoxes->fields['variable'] ?></td>
													<td align="right" class="dataTableContent"><?php echo $varientsBoxes->fields['modifieddate'] ?></td>
													<td align="right"><a href="<?php echo zen_href_link('inxpress.php','set=varients&action=delete&id='.$varientsBoxes->fields['id'].'&page='.$page) ?>"><img border="0" name="cancelButton" title=" Delete " alt="Delete" src="includes/languages/english/images/buttons/button_delete.gif"></a></td>
													
												  </tr>
											<?php
													 $varientsBoxes->MoveNext();
												}
										  ?>
										   
									</tbody>
								</table>
								<table>
									<tr>
													<td class="smallText" valign="top"><?php echo $collection_split->display_count($query_num_rows,$page_size,$currentPageNumber, 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> All rows)'); ?></td>
													<td class="smallText" align="right"><?php echo $collection_split->display_links($query_num_rows, $page_size, 5, $currentPageNumber,zen_get_all_get_params(array('page', 'id', 'action'))); ?></td>
									 </tr>
								</table>
							</td>
							<td width="25%" valign="top">
								<?php						
									require(DIR_WS_INCLUDES . 'modules/inxpress/import_varients.php');
								?>
								
							
							</td>
							
						</tr>
					</tbody>
				</table>
			</td>
			
		</tr>
	 
		
	</table>
<?php } ?>