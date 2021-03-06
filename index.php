<?php
// Start the session
session_start();

// Require the User management class
require("inc/Usermanagement.class.php");

// Set form token and set it to session
$form_token = md5( uniqid('auth', true) );
$_SESSION['form_token'] = $form_token;
?>

<!-- Include header -->
<?php include_once 'inc/header.php'; ?>

	<div class="row">
		<?php

		// Show the login form if user is not logged in
		if (!SessionStatus()) {
			echo '<h1>Log in / Sign up</h1>
			<form action="inc/user_actions.php" method="post" role="form">
				<fieldset>
					<p>
						<input type="radio" id="login" name="action" value="login"/> Login
						<input type="radio" id="register" name="action" value="register"/> Register
					</p>
					<p>
						<input type="text" id="username" name="username" value="" maxlength="20" placeholder="Username (max 20 char)" />
					</p>
					<p>
						<input type="password" id="password" name="password" value="" maxlength="100" placeholder="Password (max 100 char)" />
					</p>
					<p>
						<input type="hidden" name="form_token" value="' . $form_token . '" />
						<input type="submit" value="&rarr; Login / register" />
					</p>
				</fieldset>
			</form>';
		}

		// Show a welcome message and logout button if a user is logged in

		else {
			echo '<h1>You are logged in</h1>
			<form action="inc/user_actions.php" method="post" role="form">
				<fieldset>
					<input type="hidden" id="logout" name="action" value="logout"/>
					<p>
						<input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
						<input type="submit" value="&rarr; Logout" />
					</p>
				</fieldset>
			</form>';
		}
		?>
	</div>

	<!-- Echo the page source, this is for demo purposes only -->
	<div class="row">
		<h1>Page Source</h1>
		<p>
			<?php show_source(__FILE__); ?>
		</p>
	</div>


<!-- Include footer -->
<?php include_once 'inc/footer.php'; ?>