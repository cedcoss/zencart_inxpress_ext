<?php 
/**********Save,Delete,Update**********/
$edit_id=0;
if(isset($_GET['id']))
{
	$edit_id=(int)$_GET['id'];
	if(isset($_GET['action'])&&$_GET['action']=='save')
	{
		
		$table=INXPRESS_DHL_TABLE;
		$supplies=trim($_POST['dhl']['supplies']);
		if(!empty($supplies))
		{
			$length=(float)$_POST['dhl']['length'];
			$width=(float)$_POST['dhl']['width'];
			$height=(float)$_POST['dhl']['height'];
			$db->Execute("update {$table} set supplies='{$supplies}',length='{$length}',width='{$width}',height='{$height}' where id='{$edit_id}'");
		}
	} 
	elseif(isset($_GET['action'])&&$_GET['action']=='delete')
	{
		$table=INXPRESS_DHL_TABLE;
		$db->Execute("delete from {$table} where id='{$edit_id}'");
	} 
}
elseif(isset($_GET['action'])&&$_GET['action']=='save')
{
	
	$table=INXPRESS_DHL_TABLE;
	$supplies=trim($_POST['dhl']['supplies']);
	if(!empty($supplies))
	{
		$length=(float)$_POST['dhl']['length'];
		$width=(float)$_POST['dhl']['width'];
		$height=(float)$_POST['dhl']['height'];
		$result=$db->Execute("INSERT INTO {$table} (supplies,length,width,height,modifieddate) values ('{$supplies}','{$length}','{$width}','{$height}',now());");
		
	}
	
} 
/**********Save,Delete,Update**********/
$page_size=20;
$page=$_GET['page'];
$selectQuery='select * from '.INXPRESS_DHL_TABLE.' order by id DESC';

$currentPageNumber=$page;
$collection_split = new splitPageResults($currentPageNumber,$page_size, $selectQuery,$query_num_rows);
$dhlBoxes= $db->Execute($selectQuery);


 ?>
 
 <table width="100%" cellspacing="2" cellpadding="0" border="0">
	<tr>
		 <td >
			<table width="100%" cellspacing="2" cellpadding="2" border="0">
			<!-- body_text //-->

			<!-- search -->
				<tbody>
					<tr>
						<td  class="pageHeading">Inxpress Manage Dhl</td>
					</tr>
					<tr>
						<td valign="top">
							<table width="100%" cellspacing="0" cellpadding="2" border="0">
								<tbody>
									<tr class="dataTableHeadingRow">
										<td align="center" class="dataTableHeadingContent">ID</td>
										<td width="50" align="left" class="dataTableHeadingContent">Supplies</td>
										<td class="dataTableHeadingContent">Length</td>
										<td align="right" class="dataTableHeadingContent">Width</td>
										<td align="center" class="dataTableHeadingContent">Height</td>
										<td align="right" class="dataTableHeadingContent">Created On</td>
										<td align="right" class="dataTableHeadingContent">Action</td>
									
									  </tr>
									  <?php
										
										while (!$dhlBoxes->EOF) { 
										/* $edit_id=$dhlBoxes->fields['id']; */
										?>
											<tr class="dataTableRow" onclick="document.location.href='<?php echo zen_href_link('inxpress.php','set=dhl&action=edit&id='.$dhlBoxes->fields['id'].'&page='.$page) ?>'" onmouseout="rowOutEffect(this)" onmouseover="rowOverEffect(this)">
												<td align="center" class="dataTableContent"><?php echo $dhlBoxes->fields['id'] ?></td>
												<td width="50" align="left" class="dataTableContent"><?php echo $dhlBoxes->fields['supplies'] ?></td>
												<td class=""><?php echo $dhlBoxes->fields['length'] ?></td>
												<td align="right" class="dataTableContent"><?php echo $dhlBoxes->fields['width'] ?></td>
												<td align="center" class="dataTableContent"><?php echo $dhlBoxes->fields['height'] ?></td>
												<td align="right" class="dataTableContent"><?php echo $dhlBoxes->fields['modifieddate'] ?></td>
												<td align="right"><a href="<?php echo zen_href_link('inxpress.php','set=dhl&action=delete&id='.$dhlBoxes->fields['id'].'&page='.$page) ?>"><img border="0" name="cancelButton" title=" Delete " alt="Delete" src="includes/languages/english/images/buttons/button_delete.gif"></a></td>
											  </tr>
										<?php
												 $dhlBoxes->MoveNext();
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
									require(DIR_WS_INCLUDES . 'modules/inxpress/dhl_edit.php');
								?>
								
							
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		
	</tr>
 
	
</table>