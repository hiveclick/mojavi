<?php
namespace Mojavi\Exception;

/**
 * ForwardException is thrown when an error occurs while attempting to forward
 * the request.
 */
class ForwardException extends MojaviException
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

		$this->setName('ForwardException');

	}

}

