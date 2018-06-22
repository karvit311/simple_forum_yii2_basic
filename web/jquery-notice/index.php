<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Papermashup.com | jQuery Growl Notification Plugin</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<link href="jquery.notice.css" type="text/css" media="screen" rel="stylesheet" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js" type="text/javascript"></script>
<script src="jquery.notice.js" type="text/javascript"></script>
<script type="text/javascript"> 
	
	$(document).ready(function()
	{
		
			jQuery.noticeAdd({
				text: 'This is a simple notification using the jQuery notice plugin. Click the X above to remove this notice.',
				stay: true
			});
		
		
		$('.add').click(function()
		{
			jQuery.noticeAdd({
				text: 'This is a notification that you have to remove',
				stay: true
			});
		});
		
		$('.add2').click(function()
		{
			jQuery.noticeAdd({
				text: 'This is a notification that will remove itself',
				stay: false
			});
		});
		
		
			$('.add3').click(function()
		{
			jQuery.noticeAdd({
				text: 'This is an error notification!',
				stay: false,
				type: 'error'
			});
		});
			
			
					$('.add4').click(function()
		{
			jQuery.noticeAdd({
				text: 'This is a success notification!',
				stay: false,
				type: 'success'
			});
		});
		
		$('.remove').click(function()
		{
			jQuery.noticeRemove($('.notice-item-wrapper'), 400);
		});
	});
</script>
<style>
.success {
	background-color:
 #090;
}
.error {
	background-color:#900;
}
ul li {
	padding:3px;
}
ul li:hover {
	cursor:pointer;
	background-color:#000;
	color:#FFF;
}
</style>
</head>
<body>
<?php include '../includes/header.php';
        $link = '| <a href="http://papermashup.com/jquery-growl-notification-plugin/">Back To Tutorial</a>';
?>
<ul>
  <li class="add">Click here to see a notification that you have to remove</li>
  <li class="add2">Click here to see a notification that does not stay</li>
  <li class="add3">Error Notification</li>
  <li class="add4">Success Notification</li>
  <li class="remove">Remove all active notifications</li>
</ul>
<?php include '../includes/footer.php';?>
</body>
</html>
