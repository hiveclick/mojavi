<?php
namespace Mojavi\Exception;

/**
 * ConfigurationException is thrown when the framework finds an error in a
 * configuration setting.
 */
class ConfigurationException extends MojaviException
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

		$this->setName('ConfigurationException');

	}

}

