<?php 
require('includes/application_top.php');

if (isset($_GET['action'])&&$_GET['action']=='lightBox')
{
	
	require(DIR_WS_INCLUDES . 'modules/inxpress/lightBox.php');
}
else
{
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" media="print" href="includes/stylesheet_print.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<link rel="stylesheet" type="text/css" media="print" href="includes/inxpress/css/jquery.fancybox.css?v=2.1.5">
<link rel="stylesheet" type="text/css" media="print" href="includes/inxpress/css/jquery.fancybox-buttons.css?v=1.0.5">
<link rel="stylesheet" type="text/css" media="print" href="includes/inxpress/css/styles.css">
<script language="javascript" src="includes/inxpress/js/jquery-1.10.1.min.js"></script>
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
<script language="javascript" type="text/javascript"><!--
function couponpopupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
<script type="text/javascript">
		
		function selectvariant(id)
		{
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
			inXpress('.tdvariants input').change(function(){updateValues();});
			inXpress('.tdvariants input').blur(function(){
				
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
	</script>
</head>
<body onLoad="init()">
<!-- header //-->
<div class="header-area">
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
</div>
<!-- header_eof //-->

<!-- body //-->
<?php 
if (isset($_GET['set'])) $set = (string)$_GET['set'];
switch($set)
{
	case 'varients':
					
					require(DIR_WS_INCLUDES . 'modules/inxpress/varients.php');
					break;
	case 'dhl':												
					require(DIR_WS_INCLUDES . 'modules/inxpress/dhl.php');

				break;
}
?>
<!-- body_eof //-->

<!-- footer //-->
<div class="footer-area">
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
<?php } ?>