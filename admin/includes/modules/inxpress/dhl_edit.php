<?php
$dhl=$db->Execute('select * from '.INXPRESS_DHL_TABLE.' where id='.$edit_id);

$dhlInfo = new objectInfo($dhl->fields);

 ?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
	<tbody>
		<tr class="infoBoxHeading">
			<td class="infoBoxHeading">
				<?php if(isset($dhlInfo->id)) {
				?>
				<strong>#<?php echo "{$edit_id}"; ?></strong><strong style="float:right;"><?php echo $dhlInfo->modifieddate; ?></strong>
				<?php } else{ ?>
					<strong>Add New</strong><strong style="float:right;"><?php echo $dhlInfo->modifieddate; ?></strong>
				<?php } ?>
			</td>
		</tr>
	</tbody>
</table>
<?php if(isset($dhlInfo->id)) {
				?>
<form method="post" action="<?php echo zen_href_link('inxpress.php','set=dhl&action=save&id='.$edit_id.'&page='.$page) ?>" name="modules">
<?php } else{ ?>
<form method="post" action="<?php echo zen_href_link('inxpress.php','set=dhl&action=save&page='.$page) ?>" name="modules">
<?php } ?>
<input type="hidden" value="<?php echo $_SESSION ['securityToken'] ?>" name="securityToken">
<table width="100%" cellspacing="0" cellpadding="2" border="0">
	<tbody>
		<tr>
			<td class="infoBoxContent">
				<b>Edit DHL Details</b><br><br>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tbody>
						<tr>
							<td class="infoBoxContent"><b>Supplies</b></td>
							<td class="infoBoxContent">
								<input type="text" value="<?php echo $dhlInfo->supplies ?>" name="dhl[supplies]">
							</td>
						</tr>
						<tr>
							<td class="infoBoxContent"><b>Length</b></td>
							<td class="infoBoxContent">
								<input type="text" value="<?php echo $dhlInfo->length ?>" name="dhl[length]">
							</td>
						</tr>
						<tr>
							<td class="infoBoxContent"><b>Width</b></td>
							<td class="infoBoxContent">
								<input type="text" value="<?php echo $dhlInfo->width ?>" name="dhl[width]">
							</td>
						</tr>
						<tr>
							<td class="infoBoxContent"><b>Height</b></td>
							<td class="infoBoxContent">
								<input type="text" value="<?php echo $dhlInfo->height ?>" name="dhl[height]">
							</td>
						</tr>
				
					</tbody>
				</table>
				
			</td>
		</tr>
		<tr>
			<td align="center" class="infoBoxContent">
				<br><input type="image" border="0" name="saveButton" title=" Save " alt="Save" src="includes/languages/english/images/buttons/button_save.gif">
				<?php if(isset($dhlInfo->id)) {
				?>
				<a href="<?php echo zen_href_link('inxpress.php','set=dhl&action=delete&id='.$edit_id.'&page='.$page) ?>"><img border="0" name="cancelButton" title=" Delete " alt="Delete" src="includes/languages/english/images/buttons/button_delete.gif"></a>
				<a href="<?php echo zen_href_link('inxpress.php','set=dhl') ?>">Add New</a>
				<?php } ?>
			</td>
		</tr>
	</tbody>
</table>
</form>