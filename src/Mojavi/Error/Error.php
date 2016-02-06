<?php
/**
 * Error contains error information.  It is instantiated with an error string that will be
 * displayed to the client.  Multiple Error objects can be added to the Errors object with a
 * key.  For instance:
 *
 * $errors = new Errors();
 * $errors->addError("error", new Error("Your email address is invalid"));
 * $errors->addError("error", new Error("Your phone number is invalid"));
 *
 * Or you can also add errors with individual keys so that you can display an appropriate error
 * message next to each respective form field.  Like so:
 *
 * $errors = new Errors();
 * $errors->addError("email", new Error("Your email address is invalid"));
 * $errors->addError("phone", new Error("Your phone number is invalid"));
 *
 * Refer to the Errors documentation on how to display the errors.
 */
namespace Mojavi\Error;

class Error {

	private $message;

	/**
	 * Constructor to create a new Error object.  You can pass in a String for the argument to
	 * create a new Error Object.
	 */
	function __construct($arg0, $arg1 = null) {
		$this->setMessage($arg0);
	}

	/**
	 * Returns the error message
	 * @return string Error Message that was stored in this Error object
	 */
	function getMessage() {
		if (is_null($this->message)) {
			$this->message= "";
		}
		return($this->message);
	}

	/**
	 * Sets the error Mesage
	 * @param string $arg0 String that contains the error message
	 */
	function setMessage($arg0) {
		$this->message = $arg0;
	}
}