<?php
/**
 * Base controller used for console actions
 * @package	Mojavi
 * @subpackage Controller
 */
namespace Mojavi\Controller;

abstract class ConsoleController extends Controller
{
	/**
	 * Initializes this controller for use outside a normal context
	 *
	 * @return void
	 */
	public function loadContext()
	{
		// initialize the controller
		$this->initialize();
		
		// get the application context
		$context = $this->getContext();
		
		return true;
	}
}

