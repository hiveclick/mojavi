<?php
/**
 * FrontWebController allows you to centralize your entry point in your web
 * application, but at the same time allow for any module and action combination
 * to be requested.
 *
 * @package	Mojavi
 * @subpackage Controller
 */
namespace Mojavi\Controller;

use Mojavi\Exception\MojaviException;
use Exception;

class FrontWebController extends WebController
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Dispatch a request.
	 *
	 * This will determine which module and action to use by request parameters
	 * specified by the user.
	 *
	 * @return void
	 */
	public function dispatch ()
	{
		try
		{
			// initialize the controller
			$this->initialize();

			// get the application context
			$context = $this->getContext();

			// determine our module and action
			$moduleName = ucfirst($context->getRequest()
								  ->getParameter(MO_MODULE_ACCESSOR));

			$actionName = ucfirst($context->getRequest()
								  ->getParameter(MO_ACTION_ACCESSOR));

			$moduleName = preg_replace_callback("/_([a-zA-Z0-9])/", function($matches) { return strtoupper($matches[1]); }, $moduleName);
			$moduleName = preg_replace_callback("/-([a-zA-Z0-9])/", function($matches) { return strtoupper($matches[1]); }, $moduleName);
			$actionName = preg_replace_callback("/_([a-zA-Z0-9])/", function($matches) { return strtoupper($matches[1]); }, $actionName);
			$actionName = preg_replace_callback("/-([a-zA-Z0-9])/", function($matches) { return strtoupper($matches[1]); }, $actionName);
			
			if ($moduleName == null)
			{
				// no module has been specified
				$moduleName = MO_DEFAULT_MODULE;
			}
			if ($actionName == null)
			{
				// no action has been specified
				if ($this->actionExists($moduleName, 'Index'))
				{
					// an Index action exists
					$actionName = 'Index';
				} else
				{
					// use the default action
					$actionName = MO_DEFAULT_ACTION;
				}
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

