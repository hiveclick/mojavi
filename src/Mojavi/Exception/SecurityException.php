<?php
namespace Mojavi\Exception;

/**
 * SecurityException is thrown when a security related error occurs.
 */
class SecurityException extends MojaviException
{

	// +-----------------------------------------------------------------------+
	// | CONSTRUCTOR														   |
	// +-----------------------------------------------------------------------+

	/**
	 * Class constructor.
	 *
	 * @param string The error message.
	 * @param int	The error code.
	 */
	public function __construct ($message = null, $code = 0)
	{

		parent::__construct($message, $code);

		$this->setName('SecurityException');

	}

}

