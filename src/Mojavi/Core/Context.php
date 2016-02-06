<?php
/**
 * Context provides information about the current application context, such as
 * the module and action names and the module directory. References to the
 * current controller, request, and user implementation instances are also
 * provided.
 *
 * @package	Mojavi
 * @subpackage Core
 */
namespace Mojavi\Core;

class Context extends MojaviObject
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES													 |
	// +-----------------------------------------------------------------------+

	private
	$actionStack	 = null,
	$controller	  = null,
	$databaseManager = null,
	$request		 = null,
	$storage		 = null,
	$errors		  = null,
	$user			= null;

	// +-----------------------------------------------------------------------+
	// | CONSTRUCTOR														   |
	// +-----------------------------------------------------------------------+

	/**
	 * Class constructor.
	 *
	 * @param Controller	  The current Controller implementation instance.
	 * @param WebRequest		 The current Request implementation instance.
	 * @param User			The current User implementation instance.
	 * @param Storage		 The current Storage implementation instance.
	 * @param DatabaseManager The current DatabaseManager instance.
	 */
	public function __construct ($controller, $request, $user, $storage,
	$databaseManager)
	{

		$this->actionStack	 = $controller->getActionStack();
		$this->controller	  = $controller;
		/* @var $this->databaseManager DatabaseManager */
		$this->databaseManager = $databaseManager;
		$this->request		 = $request;
		$this->storage		 = $storage;
		$this->user			= $user;

	}

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Retrieve the action name for this context.
	 *
	 * @return string The currently executing action name, if one is set,
	 *				otherwise null.
	 */
	public function getActionName ()
	{

		// get the last action stack entry
		$actionEntry = $this->actionStack->getLastEntry();

		return $actionEntry->getActionName();

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the controller.
	 *
	 * @return \Mojavi\Controller\Controller
	 */
	public function getController ()
	{

		return $this->controller;

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve a database connection from the database manager.
	 *
	 * This is a shortcut to manually getting a connection from an existing
	 * database implementation instance.
	 *
	 * If the MO_USE_DATABASE setting is off, this will return null.
	 *
	 * @param name A database name.
	 *
	 * @return mixed A Database instance.
	 *
	 * @throws <b>DatabaseException</b> If the requested database name does
	 *								  not exist.
	 */
	public function getDatabaseConnection ($name = 'default')
	{

		if ($this->databaseManager != null)
		{
			return $this->databaseManager->getDatabase($name)->getConnection();

		}

		return null;

	}
	
	/**
	 * Alias for database manager's getAllDatabases, returns an array of all database names
	 *
	 * @return array of strings
	 */
	public function getAllDatabases() {
		$retval = null;
		if(!is_null($this->databaseManager)) {
			$retval = $this->databaseManager->getAllDatabases();
		}
		return $retval;
	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the database manager.
	 *
	 * @return DatabaseManager The current DatabaseManager instance.
	 */
	public function getDatabaseManager ()
	{

		return $this->databaseManager;

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the module directory for this context.
	 *
	 * @return string An absolute filesystem path to the directory of the
	 *				currently executing module, if one is set, otherwise null.
	 */
	public function getModuleDirectory ()
	{

		// get the last action stack entry
		$actionEntry = $this->actionStack->getLastEntry();

		return MO_MODULE_DIR . '/' . $actionEntry->getModuleName();

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the module name for this context.
	 *
	 * @return string The currently executing module name, if one is set,
	 *				otherwise null.
	 */
	public function getModuleName ()
	{

		// get the last action stack entry
		$actionEntry = $this->actionStack->getLastEntry();

		return $actionEntry->getModuleName();

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the request.
	 *
	 * @return \Mojavi\Request\WebRequest The current Request implementation instance.
	 */
	public function getRequest ()
	{

		return $this->request;

	}

	/**
	 * Returns the Errors object
	 * @return \Mojavi\Error\Errors
	 */
	public function getErrors() {
		return $this->getRequest()->getErrors();
	}
	
	/**
	 * Clears the errors object
	 * @return void
	 */
	public function clearErrors() {
		$this->getRequest()->clearErrors();
	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the storage.
	 *
	 * @return Storage The current Storage implementation instance.
	 */
	public function getStorage ()
	{

		return $this->storage;

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the user.
	 *
	 * @return User The current User implementation instance.
	 */
	public function getUser ()
	{

		return $this->user;
	}

}

