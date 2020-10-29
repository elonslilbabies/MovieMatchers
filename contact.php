<?php
session_start();
if (!isset($_SESSION["UID"]))
{
		header("Location: login.php");
}
?>
<html>
	<head>
		<title>Contact Us</title>
		<link rel="stylesheet" href="css/stylesheet.css">
		<link rel="icon" href="assets/favicon/favicon.ico">
	</head>

	<body>
		<?php include('components/header.php'); ?>
		<div class="container">
			<div class="overlay">
				<br><br>
				<!-- Connect to custom Google form -->
				<form id="msform" action="https://docs.google.com/forms/u/0/d/e/1FAIpQLSeS9K68MbO_DOoxofQkmIBfq2eCp8UuAk8-X10oMGbNdS0pbQ/formResponse" method="post" style="width: 700px;">
					<fieldset>
						<h2 class="fs-title">Contact Us</h2>
						<input type="text" name="entry.590676321" placeholder="Full Name" name="fullname" required>
						<input type="email" name="entry.730639748" placeholder="Email" name="email" required>
						<textarea type="textarea" name="entry.78704734" placeholder="Write something..." style="height: 200px" required></textarea> 
						<input type="submit" value="Submit" class="action-button"><br>
					</fieldset>
				</form>
			</div>
		</div>
		<?php include('components/footer.php'); ?>
	</body>
</html>
