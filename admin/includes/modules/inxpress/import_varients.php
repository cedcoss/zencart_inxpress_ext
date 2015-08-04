<?php
$dhl=$db->Execute('select * from '.INXPRESS_DHL_TABLE.' where id='.$edit_id);

$dhlInfo = new objectInfo($dhl->fields);

 ?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
	<tbody>
		<tr class="infoBoxHeading">
			<td class="infoBoxHeading">

					<strong>Import Csv</strong>

			</td>
		</tr>
	</tbody>
</table>

<form method="post" enctype="multipart/form-data" action="<?php echo zen_href_link('inxpress.php','set=varients&action=upload&page='.$page) ?>" name="modules">

<input type="hidden" value="<?php echo $_SESSION ['securityToken'] ?>" name="securityToken">
<table width="100%" cellspacing="0" cellpadding="2" border="0">
	<tbody>
		<tr>
			<td class="infoBoxContent">

				<table width="100%" cellspacing="0" cellpadding="2" border="0">
					<tbody>
						<tr>
							<td class="infoBoxContent"><input type="file" name="file"/></td>
							<td class="infoBoxContent">
								<input type="submit" value="Upload" name="upload"/>
							</td>
						</tr>
				
					</tbody>
				</table>
				
			</td>
		</tr>
		
	</tbody>
</table>
</form>