<!-- Include header -->
<?php include_once 'header.php'; ?>

<?php

// Require the usermanagement class for login and registration management
require("Usermanagement.class.php");

// Start PHP session
session_start();

// Check if post action was set (login/register). Return an error if it wasn't
if (isset($_POST['action'])) {

	// A decision tree executing actions based on the $_POST action
	switch ($_POST['action']) {

		// If user indicated they want to register
		case "register":
		$user = new User($_POST['username'],$_POST['password'],$_POST['form_token']);
		break;

		// If user indicated they want to log in
		case "login":
		$session = new Session($_POST['username'],$_POST['password']);
		break;

		// if user indicated they want to log out
		case "logout":
		Logout();
		break;

		// Default message that should never be shown
		default:
		echo "Something went wrong with the form, or you are trying to abuse it.";
	}
} 

// Echo error message if no action was specified.
else {
	echo "Something went wrong with the form, or you are trying to abuse it.";
}

// Shorthand for an if starement. If session status is True or False echo appropriate error message
echo SessionStatus()?"<br>Current status logged in":"<br>Current status logged out";

?>

<!-- Echo the page source for demonstration porposes -->
	<div class="row">
		<h1>Page Source</h1>
		<p>
			<?php show_source(__FILE__); ?>
		</p>
	</div>


<!-- Include footer -->
<?php include_once 'footer.php'; ?>