<?php
namespace Mojavi\Exception;

/**
 * FactoryException is thrown when an error occurs while attempting to create
 * a new factory implementation instance.
 */
class FactoryException extends MojaviException
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

		$this->setName('FactoryException');

	}

}
