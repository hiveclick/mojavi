<?php
namespace Mojavi\Logging;
// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.								   |
// | Copyright (c) 2003-2005 Agavi Foundation.								 |
// |																		   |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the	|
// | LICENSE file online at http://www.agavi.org/LICENSE.txt				   |
// |   vi: set noexpandtab:													|
// |   Local Variables:														|
// |   indent-tabs-mode: t													 |
// |   End:																	|
// +---------------------------------------------------------------------------+

/**
 * Appender allows you to specify a destination for log data and provide
 * a custom layout for it, through which all log messages will be formatted.
 */
abstract class Appender extends \Mojavi\Core\MojaviObject
{

	private $layout = null;
	private $priority = null;

	/**
	 * Initialize the object.
	 *
	 * @return void
	 */
	abstract function initialize($params);

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the layout.
	 *
	 * @return Layout A Layout instance, if one has been set, otherwise null.
	 */
	public function getLayout ()
	{
		return $this->layout;
	}

	// -------------------------------------------------------------------------

	/**
	 * Set the layout.
	 *
	 * @param Layout A Layout instance.
	 *
	 * @return Appender
	 */
	public function setLayout ($layout)
	{
		$this->layout = $layout;
		return $this;
	}

	// -------------------------------------------------------------------------

	/**
	 * Execute the shutdown procedure.
	 *
	 * @return void
	 */
	abstract function shutdown ();

	// -------------------------------------------------------------------------

	/**
	 * returns the priority
	 * @return string
	 */
	function getPriority() {
		if (is_null($this->priority)) {
			$this->priority = Logger::INFO;
		}
		return $this->priority;
	}
	
	/**
	 * sets the priority
	 * @param string $arg0
	 */
	function setPriority($arg0) {
		$this->priority = $arg0;
	}
	
	/**
	 * Write log data to this appender.
	 *
	 * @param string Log data to be written.
	 *
	 * @return void
	 */
	abstract function write ($message);

}

