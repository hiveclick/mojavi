<?php
/**
 * MojaviObject provides useful methods that all Mojavi classes inherit.
 *
 * @package	Mojavi
 * @subpackage Core
 */
namespace Mojavi\Core;

abstract class MojaviObject
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Retrieve a string representation of this object.
	 *
	 * @return string A string containing all public variables available in
	 *				this object.
	 */
	public function toString ()
	{

		$output = '';
		$vars   = get_object_vars($this);

		foreach ($vars as $key => &$value)
		{

			if (strlen($output) > 0)
			{

				$output .= ', ';

			}

			$output .= $key . ': ' . $value;

		}

		return $output;

	}
	
	
}

