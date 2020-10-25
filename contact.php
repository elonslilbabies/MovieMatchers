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
	</head>

	<body>
		<?php include('components/header.php'); ?>
		<div class= container>
			<div class=overlay>
				<br><br>
				<form id = msform method="POST" style="width: 700px;">
					<fieldset>
						<h2 class="fs-title">Contact Us</h2>
						<input type="text" placeholder="Full Name" name="fullname">
						<input type="email" placeholder="Email" name="email">
						<textarea id="subject" name="subject" placeholder="Write something.." style="height:200px"></textarea>
						<input type="submit" value="Submit" class = action-button><br>
					</fieldset>
				</form>
			</div>
		</div>

		<div id="footer">
		<?php include('components/footer.php'); ?>
		</div>
</body>

</html>
