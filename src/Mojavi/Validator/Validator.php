<?php
namespace Mojavi\Validator;

use Mojavi\Controller\Controller as Controller;

/**
 * Validator allows you to apply constraints to user entered parameters.
 */
abstract class Validator extends \Mojavi\Util\ParameterHolder
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES													 |
	// +-----------------------------------------------------------------------+

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute this validator.
	 *
	 * @param mixed A file or parameter value/array.
	 * @param string An error message reference.
	 *
	 * @return bool true, if this validator executes successfully, otherwise
	 *			  false.
	 */
	abstract function execute (&$value, &$error);

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the current application context.
	 *
	 * @return Context The current Context instance.
	 */
	public function getContext ()
	{

		return Controller::getInstance()->getContext();

	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this validator.
	 *
	 * @param Context The current application context.
	 * @param array   An associative array of initialization parameters.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 */
	public function initialize ($context, $parameters = null)
	{

		if ($parameters != null)
		{

			$this->parameters = array_merge($this->parameters, $parameters);

		}

		return true;

	}

}

