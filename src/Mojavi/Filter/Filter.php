<?php
namespace Mojavi\Filter;

use Mojavi\Util\ParameterHolder as ParameterHolder;
use Mojavi\Controller\Controller as Controller;

/**
 * Filter provides a way for you to intercept incoming requests or outgoing
 * responses.
 */
abstract class Filter extends ParameterHolder
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES													 |
	// +-----------------------------------------------------------------------+


	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute this filter.
	 *
	 * @param FilterChain A FilterChain instance.
	 *
	 * @return void
	 */
	abstract function execute ($filterChain);

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
	 * Initialize this Filter.
	 *
	 * @param Context The current application context.
	 * @param array   An associative array of initialization parameters.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 *
	 * @throws <b>InitializationException</b> If an error occurs while
	 *										initializing this Filter.
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

