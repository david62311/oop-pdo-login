<?php

// Reguire the DPO database class by Vivek Wicky Aswal
require("Db.class.php");

// Function that checks if a user session is active
function SessionStatus() {
	if (isset($_SESSION['user_id'])) {
		return true;
	} else {
		return false;
	}
}

// Function that logs out the user by unsetting user_id and destroying the current session
function Logout() {

	// Unset the user ID and destroy the session
	unset( $_SESSION['user_id'] );
	session_destroy();

	// Verify that the logout command worked
	if(!isset($_SESSION['user_id'])) {
		echo "Logout successful";
	} else {
		// This should be impossible
		echo "Some strange magick is afoot, we can't log you out!";
	}
}
class User {

	// Create new user and write to database
	public function __construct($posted_username, $posted_password, $posted_formtoken) {

		// Form validation
		$validated = false;
		if(!isset( $posted_username, $posted_password, $posted_formtoken)) {
			$message = 'Please enter a valid username and password';
		} elseif( $posted_formtoken != $_SESSION['form_token']) {
			$message = 'Invalid form submission';
		} elseif (strlen( $posted_username) > 20 || strlen($posted_username) < 4) {
			$message = 'Incorrect Length for Username';
		} elseif (strlen( $posted_password) > 100 || strlen($posted_password) < 4) {
			$message = 'Incorrect Length for Password';
		} elseif (ctype_alnum($posted_username) != true) {
			$message = "Username must be alpha numeric";
		} else {
			$validated = true;
		}

		// Validation is done

		if ($validated == true) {
			// Sanitize the username and password. Remove prefix and postfix spaces remove code tags and HTML encode special characters
			$username = filter_var(trim($posted_username), FILTER_SANITIZE_STRING);
			$password = filter_var(trim($posted_password), FILTER_SANITIZE_STRING);

		    // Hash the password using BCRYPT and increase difficulty
		    // Set hashing cost (meaning difficulty/strength)
			$options = [
			'cost' => 12,
			];

			// Hash the password and set the plaintext variable to a hashed version
			$password = password_hash($password, PASSWORD_BCRYPT, $options);

	 	   // Open database connection
			$db = new Db();

			// Check if username is taken
			$db->bind("username",$username);
			$ifexist = $db->single("SELECT * FROM users WHERE username = :username");
			if ($ifexist) {
				$message = "This username already exists";
			}
			// If username is not taken, start creation query
			else {
				$db->bind("username",$username);
				$db->bind("password",$password);
				$db->query("INSERT INTO users (username,password) VALUES (:username,:password)");
				$message = "Your account was successfully created.";
			}

	  		// Close connection
			$db->CloseConnection();

			// Unset the unique form token
			unset( $_SESSION['form_token'] );

			// Return the result message. This can be an error or a confirmation
			echo $message;
		}
	}
}

// Session class used to log in
class Session {
	public function __construct($login_username,$login_password) {

		// Validate input
		$validated = false;

		// Lazy validation, checking purely length. We sanitize later to prevent abuse.
		if( !isset( $login_username, $login_password) || !(strlen($login_username) < 20) || !(strlen($login_password) < 100)) {
			$message = 'Please enter a valid username and password';
		} else {
			$validated = true;
		}

		// Validation complete
		if ($validated) {

			// Sanitize the username and password. Remove prefix and postfix spaces remove code tags and HTML encode special characters
			$login_username = filter_var(trim($login_username), FILTER_SANITIZE_STRING);
			$login_password = filter_var(trim($login_password), FILTER_SANITIZE_STRING);

    		// Open database connection
			$db = new Db();

			// Check if username exists in the database
			$db->bind("username",$login_username);
			$ifexist = $db->single("SELECT * FROM users WHERE username = :username");
			if (!$ifexist) {
				$message = "This user does not exist!";
			}

			// If the user exists, start password verification
			else {
				$db->bind("username",$login_username);

				// Query database for stored password hash
				$password_hash = $db->single("SELECT password FROM users WHERE username = :username");

				// Let PHP verify the hash and get user ID if password is correct
				if (password_verify($login_password, $password_hash)) {
					$db->bind("username",$login_username);
					$logged_in_id = $db->single("SELECT user_id FROM users WHERE username = :username");
				} else {
					$message = "Wrong password";
				}
			}

			// Return error message if the above didn't result in a login
			if(!isset($logged_in_id)) {
				$message = $message . ' Login Failed';
			}

			// Set the user ID into the $_SESSION array and set success message
			else {
				$_SESSION['user_id'] = $logged_in_id;
				$this->logged_in_id = $logged_in_id;
				$message = 'Login succeeded';
			}

    		// Close database connection
			$db->CloseConnection();

			// Echo the error or success message
			echo $message;
		}
	}

}

?>