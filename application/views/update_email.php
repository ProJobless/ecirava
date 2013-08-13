<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?PHP echo $site_title.' - '.$page_title; ?></title>
		<link rel="shortcut icon" href="favicon.ico" type="img/blank.icon">
	</head>
	<body>
	<?PHP echo $this->load->view('header'); ?>
	<div class="container">
		<div class="span6 offset3 update_email_result">
			<h1><?PHP echo $update_email_message; ?></h1>
		</div>
	</div>
	</body>
	<?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
	<style>
	.update_email_result {
		background-color: #51a351;
		color: #FFF;
		text-align: center;
		padding: 15px;
		margin-top: 10px;
	}
	.update_email_result h1 {
		color: #FFF;
	}
	

	</style>
</html>