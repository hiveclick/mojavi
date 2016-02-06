<?php
/**
 * A view represents the presentation layer of an action. Output can be
 * customized by supplying attributes, which a template can manipulate and
 * display.
 *
 * @package	Mojavi
 * @subpackage View
 */
namespace Mojavi\View;

class BasicView extends PHPView
{
	private $title;
	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute any presentation logic and set template attributes.
	 *
	 * @return void
	 */
	public function execute()
	{
		$this->setDecoratorTemplate(MO_TEMPLATE_DIR . "/index.shell.php");
	}

	/**
	* Returns the errors
	* @return \Mojavi\Error\Errors
	*/
	public function getErrors() {
		return $this->getContext()->getErrors();
	}
	
	/**
	* Returns the messages
	* @return \Mojavi\Error\Errors
	*/
	public function getMessages() {
		return $this->getContext()->getMessages();
	}

	/**
	 * Retrieves the currently logged in user details
	 * @return CommonForm
	 */
	public function getUserDetails() {
		return $this->getContext()->getUser()->getUserDetails();
	}

	/**
	 * Returns the title
	 * @return string
	 */
	function getTitle() {
		if (is_null($this->title)) {
			$this->title = $this->getAttribute("title");
		}
		return $this->title;
	}

	/**
	 * Sets the title
	 * @param string
	 */
	function setTitle($arg0) {
		$this->title = $arg0;
	}
}

