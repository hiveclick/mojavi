<?php
namespace Mojavi\Exception;

/**
 * AutoloadException is thrown when a class that has been required cannot be
 * loaded.
 */

class AutoloadException extends MojaviException
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
		error_log("Autoload exception: " . $message);

		parent::__construct($message, $code);

		$this->setName('AutoloadException');
		error_log($this->printStackTrace(''));

	}

}
