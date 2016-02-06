<?php
/**
 * Console Request class for all console requests.  A request is a container that holds the query
 * string and other attributes that you want to pass between an action and a
 * template.
 *
 * @package	Mojavi
 * @subpackage Request
 */
namespace Mojavi\Request;

class ConsoleRequest extends Request
{

	/**
	 * Initialize this Request.
	 *
	 * @param Context A Context instance.
	 * @param array   An associative array of initialization parameters.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 *
	 * @throws <b>InitializationException</b> If an error occurs while
	 *										initializing this Request.
	 */
	public function initialize ($context, $parameters = null)
	{
		return parent::initialize($context, $parameters);
	}

	/**
	 * Execute the shutdown procedure.
	 *
	 * @return void
	 */
	public function shutdown ()
	{
		// nothing to do here
	}

}

