<?php
/**
 * IniConfigHandler is a base class for .ini configuration handlers. This class
 * provides a central location for parsing ini files and detecting required
 * categories.
 *
 * @package	Mojavi
 * @subpackage Config
 */
namespace Mojavi\Config;

use Mojavi\Exception\ConfigurationException as ConfigurationException;
use Mojavi\Exception\ParseException as ParseException;

abstract class IniConfigHandler extends ConfigHandler
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Parse an .ini configuration file.
	 *
	 * @param string An absolute filesystem path to a configuration file.
	 *
	 * @return string A parsed .ini configuration.
	 *
	 * @throws <b>ConfigurationException</b> If a requested configuration file
	 *									   does not exist or is not readable.
	 * @throws <b>ParseException</b> If a requested configuration file is
	 *							   improperly formatted.
	 */
	protected function & parseIni ($config)
	{

		if (!is_readable($config))
		{

			// can't read the configuration
			$error = 'Configuration file "%s" does not exist or is not ' .
					 'readable';
			$error = sprintf($error, $config);

			throw new ConfigurationException($error);

		}

		// parse our config
		$ini = @parse_ini_file($config, true);

		if ($ini === false)
		{

			// configuration couldn't be parsed
			$error = 'Configuration file "%s" could not be parsed';
			$error = sprintf($error, $config);

			throw new ParseException($error);

		}

		// get a list of the required categories
		if ($this->hasParameter('required_categories'))
		{

			$categories = $this->getParameter('required_categories');

			foreach ($categories as $category)
			{

				if (!isset($ini[$category]))
				{

					$error = 'Configuration file "%s" is missing "%s" category';
					$error = sprintf($error, $config, $category);

					throw new ParseException($error);

				}

			}

		}

		return $ini;

	}

}

