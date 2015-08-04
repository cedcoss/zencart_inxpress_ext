<tr>  <td class="label">Variant's</td>
			<td class="grid tier" colspan="10">
				<table cellspacing="10" id="group_prices_table" class="data border">
					<thead>
						<tr class="headings">
						   
						   
						   
									<th>Variant</th>
									<th>L x W x H</th>
									<th>Dim Weight</th>
									<th>Variable</th>
									<th  class="last">Action</th>
						  
						</tr>
					</thead>
					<tbody id="group_price_container">
						<tr>
									<?php 
									$pid=(int)$_GET['pID'];
									$varientsBoxes=$db->Execute('select * from '.INXPRESS_VARIENTS_TABLE.' where product_id='.$pid);  ?>
									<td>
									<link rel="stylesheet" type="text/css" href="includes/inxpress/css/jquery.fancybox.css?v=2.1.5">

									<script language="javascript" src="includes/inxpress/js/jquery-1.10.1.min.js"></script>
									<script language="javascript" src="includes/inxpress/js/jquery.fancybox.js"></script>
									<script language="javascript" src="includes/inxpress/js/jquery.fancybox.pack.js"></script>
									<script type="text/javascript">
										inXpress(document).ready(function() {
											
											//inXpress('#varient_edit').fancybox();
											inXpress('#varient_edit').fancybox({ type: "iframe",
																				 afterClose  : function() { 
																																																window.location.reload();
																									}
																				});
										
										});
										
									</script>
									<?php  echo $varientsBoxes->fields['variant']  ?></td>
									<td  align="center"><?php if($varientsBoxes->fields['length']){ echo $varientsBoxes->fields['length']  ?> x <?php } if($varientsBoxes->fields['width']) { echo $varientsBoxes->fields['width']  ?> x <?php } echo $varientsBoxes->fields['height']  ?></td>
									<td  align="center"><?php  echo $varientsBoxes->fields['dim_weight'] ?></td>
									<td  align="center"><?php  echo $varientsBoxes->fields['variable' ] ?></td>
									<td  align="center"><a id="varient_edit" href="<?php echo zen_href_link('inxpress.php','set=variants&action=lightBox&product_id='.$pid.'&id='.(int)$varientsBoxes->fields['id']) ?>">Add/Edit</a></td>
						</tr>
					</tbody>
				</table>
			</td>
	</tr>