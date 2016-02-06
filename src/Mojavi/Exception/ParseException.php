<?php
namespace Mojavi\Exception;

/**
 * ParseException is thrown when a parsing procedure fails to complete
 * successfully.
 */
class ParseException extends ConfigurationException
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

		$this->setName('ParseException');

	}

}

