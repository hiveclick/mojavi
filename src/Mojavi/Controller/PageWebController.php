<?php
/**
 * PageWebController allows you to dispatch a request by specifying a module
 * and action name in the dispatch() method.
 *
 * @package	Mojavi
 * @subpackage Controller
 */
namespace Mojavi\Controller;

use Mojavi\Exception\MojaviException;
use Exception;

class PageWebController extends WebController
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Dispatch a request.
	 *
	 * @param string A module name.
	 * @param string An action name.
	 * @param array  An associative array of parameters to be set.
	 *
	 * @return void
	 */
	public function dispatch ($moduleName, $actionName, $parameters = null)
	{

		try
		{
			// initialize the controller
			$this->initialize();

			// set parameters
			if ($parameters != null)
			{

				$this->getContext()
					 ->getRequest()
					 ->setParametersByRef($parameters);

			}

			// make the first request
			$this->forward($moduleName, $actionName);

		} catch (MojaviException $e)
		{

			$e->printStackTrace();

		} catch (Exception $e)
		{

			// most likely an exception from a third-party library
			$e = new MojaviException($e->getMessage());

			$e->printStackTrace();

		}

	}

}

