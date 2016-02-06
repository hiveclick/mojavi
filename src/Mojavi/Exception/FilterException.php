<?php
namespace Mojavi\Exception;

/**
 * FilterException is thrown when an error occurs while attempting to initialize
 * or execute a filter.
 */
class FilterException extends MojaviException
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

		$this->setName('FilterException');

	}

}

