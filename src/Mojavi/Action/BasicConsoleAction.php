<?php
/**
 * BasicConsoleAction is a non-abstract action that you can either extend in your
 * project or use as the base action for any console actions
 *
 * @package	Mojavi
 * @subpackage Action
 */
namespace Mojavi\Action;

use Mojavi\View\View;
use Mojavi\Request\Request;

class BasicConsoleAction extends BasicAction
{
	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute any application/business logic for this action.
	 *
	 * @return mixed - A string containing the view name associated with this
	 *				 action, or...
	 *			   - An array with three indices:
	 *				 0. The parent module of the view that will be executed.
	 *				 1. The parent action of the view that will be executed.
	 *				 2. The view that will be executed.
	 */
	public function execute ()
	{
		return View::NONE;
	}

	/**
	 * Retrieve the credential required to access this action.
	 *
	 * @return mixed Data that indicates the level of security for this action.
	 */
	public function getCredential ()
	{
		$retVal = null;
		if ($this->isConsole()) {
			$retVal = MO_CONSOLE_CREDENTIAL;
		} elseif(!defined('MO_IS_CONSOLE')) {
			$retVal = parent::getCredential();
		}
		return $retVal;
	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the default view to be executed when a given request is not
	 * served by this action.
	 *
	 * @return mixed - A string containing the view name associated with this
	 *				 action, or...
	 *			   - An array with three indices:
	 *				 0. The parent module of the view that will be executed.
	 *				 1. The parent action of the view that will be executed.
	 *				 2. The view that will be executed.
	 */
	public function getDefaultView ()
	{
		return View::NONE;
	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the request methods on which this action will process
	 * validation and execution.
	 *
	 * @return int - Request::GET - Indicates that this action serves only GET
	 *			   requests, or...
	 *			 - Request::POST - Indicates that this action serves only POST
	 *			   requests, or...
	 *			 - Request::NONE - Indicates that this action serves no
	 *			   requests, or...
	 */
	public function getRequestMethods ()
	{
		return Request::GET;
	}

	/**
	 * Returns whether the user needs to be logged in to view this action
	 *
	 * @return	 boolean
	 */
	function isSecure() {
		return false;
	}
}

