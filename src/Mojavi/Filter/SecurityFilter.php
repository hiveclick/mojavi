<?php
namespace Mojavi\Filter;

use Mojavi\Exception\FactoryException as FactoryException;

/**
 * SecurityFilter provides a base class that classifies a filter as one that
 * handles security.
 */
abstract class SecurityFilter extends Filter
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Retrieve a new Controller implementation instance.
	 *
	 * @param string A Controller implementation name.
	 *
	 * @return Controller A Controller implementation instance.
	 *
	 * @throws <b>FactoryException</b> If a security filter implementation
	 *								 instance cannot be created.
	 */
	public static function newInstance ($class)
	{

		// the class exists
		$object = new $class();

		if (!($object instanceof SecurityFilter))
		{

			// the class name is of the wrong type
			$error = 'Class "%s" is not of the type SecurityFilter';
			$error = sprintf($error, $class);

			throw new FactoryException($error);

		}

		return $object;

	}

}

