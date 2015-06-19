<!-- Include header -->
<?php include_once 'header.php'; ?>

<?php
require("Usermanagement.class.php");
session_start();

if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case "register":
		$user = new User($_POST['username'],$_POST['password'],$_POST['form_token']);
		break;
		case "login":
		$session = new Session($_POST['username'],$_POST['password']);
		break;
		case "logout":
		Logout();
		break;
		default:
		echo "Something went wrong with the form, or you are trying to abuse it.";
	}
} else {
	echo "Something went wrong with the form, or you are trying to abuse it.";
}

echo SessionStatus()?"<br>Current status logged in":"<br>Current status logged out";

?>

	<div class="row">
		<h1>Page Source</h1>
		<p>
			<?php show_source(__FILE__); ?>
		</p>
	</div>


<!-- Include footer -->
<?php include_once 'footer.php'; ?>