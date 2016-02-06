<?php
/**
 * BasicAction is a non-abstract action that you can either extend in your
 * project or use as the base action
 *
 * @package	Mojavi
 * @subpackage Action
 */
namespace Mojavi\Action;

use Mojavi\View\View;

class BasicAction extends Action {

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute any application/business logic for this action.
	 *
	 * In a typical database-driven application, execute() handles application
	 * logic itself and then proceeds to create a model instance. Once the model
	 * instance is initialized it handles all business logic for the action.
	 *
	 * A model should represent an entity in your application. This could be a
	 * user account, a shopping cart, or even a something as simple as a
	 * single product.
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
		return View::SUCCESS;
	}

	/**
	 * Retrieve the credential required to access this action.
	 *
	 * @return mixed Data that indicates the level of security for this action.
	 */
	public function getCredential ()
	{
		$retVal = null;
		if ($this->isSecure()) {
			$userForm = $this->getUserDetails();
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
		return View::SUCCESS;
	}

	/**
	* Retrieves the currently logged in user details
	* @return object
	*/
	public function getUserDetails() {
		return $this->getContext()->getUser()->getAttribute(MO_USER_NAMESPACE);
	}

	/**
	* Retrieves the currently logged in user details
	* @param object
	*/
	public function setUserDetails($arg0) {
		$this->getContext()->getUser()->setAttribute(MO_USER_NAMESPACE, $arg0);
	}

	/**
	 * Returns true if script should be restricted to console, false if can be used via console or web
	 * @return bool
	 */
	public function isConsole() {
		return false;
	}

	/**
	 * returns the errors
	 * @return \Mojavi\Error\Errors
	 */
	function getErrors() {
		return $this->getContext()->getErrors();
	}

	/**
	 * returns the messages
	 * @return string
	 */
	function getMessages() {
		return $this->getContext()->getMessages();
	}
	
	/**
	 * Indicates that this action requires security.
	 *
	 * @return bool true, if this action requires security, otherwise false.
	 */
	public function isSecure ()
	{
	
		return true;
	
	}
}

