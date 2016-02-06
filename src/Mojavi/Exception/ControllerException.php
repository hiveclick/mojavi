<?php
namespace Mojavi\Exception;

/**
 * ControllerException is thrown when a requested Controller implementation
 * doesn't exist.
 */
class ControllerException extends MojaviException
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

		$this->setName('ControllerException');

	}

}

