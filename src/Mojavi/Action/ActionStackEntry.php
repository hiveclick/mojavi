<?php
/**
 * ActionStackEntry represents information relating to a single Action request
 * during a single HTTP request.
 *
 * @package	Mojavi
 * @subpackage Action
 */
namespace Mojavi\Action;

use Mojavi\Core\MojaviObject;

class ActionStackEntry extends MojaviObject
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES													 |
	// +-----------------------------------------------------------------------+
	
	private
		$actionInstance = null,
		$actionName	 = null,
		$microtime	  = null,
		$moduleName	 = null,
		$presentation   = null;
	
	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+
	
	/**
	 * Class constructor.
	 *
	 * @param string A module name.
	 * @param string An action name.
	 * @param Action An action implementation instance.
	 *
	 * @return void
	 */
	public function __construct ($moduleName, $actionName, $actionInstance)
	{
		$this->actionName	 = $actionName;
		$this->actionInstance = $actionInstance;
		$this->microtime	  = microtime();
		$this->moduleName	 = $moduleName;
	}
	
	/**
	 * Retrieve this entry's action name.
	 *
	 * @return string An action name.
	 */
	public function getActionName ()
	{
		return $this->actionName;
	}

	/**
	 * Retrieve this entry's action instance.
	 *
	 * @return Action An action implementation instance.
	 */
	public function getActionInstance ()
	{
		return $this->actionInstance;
	}
	
	/**
	 * Retrieve this entry's microtime.
	 *
	 * @return string A string representing the microtime this entry was
	 *				created.
	 */
	public function getMicrotime ()
	{
		return $this->microtime;
	}
	
	/**
	 * Retrieve this entry's module name.
	 *
	 * @return string A module name.
	 */
	public function getModuleName ()
	{
		return $this->moduleName;
	}
	
	/**
	 * Retrieve this entry's rendered view presentation.
	 *
	 * This will only exist if the view has processed and the render mode
	 * is set to View::RENDER_VAR.
	 *
	 * @return string An action name.
	 */
	public function & getPresentation ()
	{
		return $this->presentation;
	}
	
	/**
	 * Set the rendered presentation for this action.
	 *
	 * @param string A rendered presentation.
	 *
	 * @return void
	 */
	public function setPresentation (&$presentation)
	{
		$this->presentation =& $presentation;
	}
}
