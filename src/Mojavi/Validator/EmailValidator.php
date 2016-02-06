<?php
namespace Mojavi\Validator;

/**
 * EmailValidator verifies a parameter contains a value that qualifies as an
 * email address.
 */
class EmailValidator extends Validator
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute this validator.
	 *
	 * @param mixed A file or parameter value/array.
	 * @param error An error message reference.
	 *
	 * @return bool true, if this validator executes successfully, otherwise
	 *			  false.
	 */
	public function execute (&$value, &$error)
	{

	}

}

