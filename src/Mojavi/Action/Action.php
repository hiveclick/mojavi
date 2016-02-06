<?php
/**
 * Action allows you to separate application and business logic from your
 * presentation. By providing a core set of methods used by the framework,
 * automation in the form of security and validation can occur.
 *
 * @package	Mojavi
 * @subpackage Action
 */
namespace Mojavi\Action;

use Mojavi\Core\MojaviObject;
use Mojavi\Controller\Controller;
use Mojavi\View\View;
use Mojavi\Request\Request;

abstract class Action extends MojaviObject
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES													 |
	// +-----------------------------------------------------------------------+

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute any application/business logic for this action.
	 *
	 * @return mixed A string containing the view name associated with this
	 *			   action.
	 *
	 *			   Or an array with the following indices:
	 *
	 *			   - The parent module of the view that will be executed.
	 *			   - The view that will be executed.
	 */
	abstract function execute ();

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the current application context.
	 * @return \Mojavi\Core\Context
	 */
	public function getContext ()
	{
		return Controller::getInstance()->getContext();
	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the credential required to access this action.
	 *
	 * @return mixed Data that indicates the level of security for this action.
	 */
	public function getCredential ()
	{

		return null;

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the default view to be executed when a given request is not
	 * served by this action.
	 *
	 * @return mixed A string containing the view name associated with this
	 *			   action.
	 *
	 *			   Or an array with the following indices:
	 *
	 *			   - The parent module of the view that will be executed.
	 *			   - The view that will be executed.
	 */
	public function getDefaultView ()
	{

		return View::INPUT;

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the request methods on which this action will process
	 * validation and execution.
	 *
	 * @return int One of the following values:
	 *
	 *			 - Request::GET
	 *			 - Request::POST
	 *			 - Request::NONE
	 */
	public function getRequestMethods ()
	{

		return Request::GET | Request::POST;

	}

	// -------------------------------------------------------------------------

	/**
	 * Execute any post-validation error application logic.
	 *
	 * @return mixed A string containing the view name associated with this
	 *			   action.
	 *
	 *			   Or an array with the following indices:
	 *
	 *			   - The parent module of the view that will be executed.
	 *			   - The view that will be executed.
	 */
	public function handleError ()
	{

		return View::ERROR;

	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this action.
	 *
	 * @param Context The current application context.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 */
	public function initialize ($context)
	{
		return true;

	}

	// -------------------------------------------------------------------------

	/**
	 * Indicates that this action requires security.
	 *
	 * @return bool true, if this action requires security, otherwise false.
	 */
	public function isSecure ()
	{

		return false;

	}

	// -------------------------------------------------------------------------

	/**
	 * Manually register validators for this action.
	 * @param ValidatorManager A ValidatorManager instance.
	 * @return void
	 */
	public function registerValidators ($validatorManager)
	{

	}

	// -------------------------------------------------------------------------

	/**
	 * Manually validate files and parameters.
	 *
	 * @return bool true, if validation completes successfully, otherwise false.
	 */
	public function validate ()
	{

		return true;

	}
	
	/**
	 * returns the errors
	 * @return \Mojavi\Error\Errors
	 */
	function getErrors() {
		return $this->getContext()->getErrors();
	}
	
}

