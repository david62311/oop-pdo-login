<?php
require("Db.class.php");
function SessionStatus() {
	if (isset($_SESSION['user_id'])) {
		return true;
	} else {
		return false;
	}
}
function Logout() {
	unset( $_SESSION['user_id'] );
	session_destroy();
	if(!isset($_SESSION['user_id'])) {
		echo "Logout successful";
	} else {
		echo "Some strange magick is afoot, we can't log you out!";
	}
}
class User {

	// Note that you need to post a username, password, formtoken and create $_SESSION['form_token']
	// Start session
	public function __construct($posted_username, $posted_password, $posted_formtoken) {

		// Make username public
		$this->name = $posted_username;

		// Form validation
		$validated = false;
		if(!isset( $posted_username, $posted_password, $posted_formtoken)) {
			$message = 'Please enter a valid username and password';
		} elseif( $posted_formtoken != $_SESSION['form_token']) {
			$message = 'Invalid form submission';
		} elseif (strlen( $posted_username) > 20 || strlen($posted_username) < 4) {
			$message = 'Incorrect Length for Username';
		} elseif (strlen( $posted_password) > 20 || strlen($posted_password) < 4) {
			$message = 'Incorrect Length for Password';
		} elseif (ctype_alnum($posted_username) != true) {
			$message = "Username must be alpha numeric";
		} else {
			$validated = true;
		}

		if ($validated == true) {
			// Sanitize the username and password
			$username = filter_var(trim($posted_username), FILTER_SANITIZE_STRING);
			$password = filter_var(trim($posted_password), FILTER_SANITIZE_STRING);

		    // Encrypt the password
			$options = [
			'cost' => 12,
			];
			$password = password_hash($password, PASSWORD_BCRYPT, $options);

	 	   // Open database connection
			$db = new Db();

			$db->bind("username",$username);
			$ifexist = $db->single("SELECT * FROM users WHERE username = :username");
			if ($ifexist) {
				$message = "This username already exists";
			} else {
				$db->bind("username",$username);
				$db->bind("password",$password);
				$db->query("INSERT INTO users (username,password) VALUES (:username,:password)");
				$message = "Your account was successfully created.";
			}

	  	  // Close connection
			$db->CloseConnection();

			// Unset the unique form token
			unset( $_SESSION['form_token'] );

			// return the appropriate error, or true
			echo $message;
		}
	}
}

class Session {
	public function __construct($login_username,$login_password) {
		$validated = false;
		if( !isset( $login_username, $login_password) || !(strlen($login_username) < 20) || !(strlen($login_password) < 40)) {
			$message = 'Please enter a valid username and password';
		} else {
			$validated = true;
		}

		if ($validated) {
	// Sanitize the username and password
			$login_username = filter_var(trim($login_username), FILTER_SANITIZE_STRING);
			$login_password = filter_var(trim($login_password), FILTER_SANITIZE_STRING);

    // Open database connection
			$db = new Db();

			$db->bind("username",$login_username);
			$ifexist = $db->single("SELECT * FROM users WHERE username = :username");
			if (!$ifexist) {
				$message = "This user does not exist!";
			} else {
				$db->bind("username",$login_username);
				$password_hash = $db->single("SELECT password FROM users WHERE username = :username");
				if (password_verify($login_password, $password_hash)) {
					$db->bind("username",$login_username);
					$logged_in_id = $db->single("SELECT user_id FROM users WHERE username = :username");
				} else {
					$message = "Wrong password";
				}
			}

			if(!isset($logged_in_id)) {
				$message = $message . ' Login Failed';
			} else {
				$_SESSION['user_id'] = $logged_in_id;
				$this->logged_in_id = $logged_in_id;
				$message = 'Login succeeded';
			}

    // Close database connection
			$db->CloseConnection();

			echo $message;
		}
	}

}

?>