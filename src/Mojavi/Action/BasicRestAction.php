<?php
/**
 * BasicRestAction is a non-abstract action that you can either extend in your
 * project or use as the base action for any RESTful actions
 *
 * @package	Mojavi
 * @subpackage Action
 */
namespace Mojavi\Action;

class BasicRestAction extends BasicAction {

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute any application/business logic for this action.
	 * @return mixed - A string containing the view name associated with this
	 *				 action, or...
	 *			   - An array with three indices:
	 *				 0. The parent module of the view that will be executed.
	 *				 1. The parent action of the view that will be executed.
	 *				 2. The view that will be executed.
	 */
	public function execute ()
	{
		$input_form = $this->getInputForm();
		$input_form->populate($this->getContext()->getRequest()->getParameters());
		if ($this->getContext()->getRequest()->getMethod() == \Mojavi\Request\Request::POST) {
			$ajax_form = $this->executePost($input_form);
		} else if ($this->getContext()->getRequest()->getMethod() == \Mojavi\Request\Request::PUT) {
			$ajax_form = $this->executePut($input_form);
		} else if ($this->getContext()->getRequest()->getMethod() == \Mojavi\Request\Request::DELETE) {
			$ajax_form = $this->executeDelete($input_form);
		} else {
			$ajax_form = $this->executeGet($input_form);
		}
		$this->getContext()->getRequest()->setAttribute('ajax_form', $ajax_form);

		// Default the view so that we don't have to create a bunch of view files
		return array('Default','IndexSuccess');
	}

	/**
	 * Executes a GET request
	 * @return \Mojavi\Form\AjaxForm
	 */
	function executeGet($input_form) {
		// Handle GET Requests
		$ajax_form = new \Mojavi\Form\AjaxForm();
		if (\MongoId::isValid($input_form->getId())) {
			$input_form->query();
			$ajax_form->setRecord($input_form);
		} else {
			$entries = $input_form->queryAll();
			$ajax_form->setEntries($entries);
			$ajax_form->setTotal($input_form->getTotal());
			$ajax_form->setPage($input_form->getPage());
			$ajax_form->setItemsPerPage($input_form->getItemsPerPage());
		}
		return $ajax_form;
	}

	/**
	 * Executes a PUT request
	 * @return \Mojavi\Form\AjaxForm
	 */
	function executePut($input_form) {
		// Handle PUT Requests
		$ajax_form = new \Mojavi\Form\AjaxForm();
		$rows_affected = $input_form->update();
		if (isset($rows_affected['n'])) {
			$ajax_form->setRowsAffected($rows_affected['n']);
		}
		$ajax_form->setRecord($input_form);
		return $ajax_form;
	}

	/**
	 * Executes a POST request
	 * @return \Mojavi\Form\AjaxForm
	 */
	function executePost($input_form) {
		// Handle POST Requests
		$ajax_form = new \Mojavi\Form\AjaxForm();
		if (isset($_REQUEST['is_bulk_request']) && ($_REQUEST['is_bulk_request'] == '1') && isset($_REQUEST['bulk_items'])) {
			$rows_affected = 0;
			$entries = array();
			foreach ($_REQUEST['bulk_items'] as $bulk_item) {
				$input_form->populate($bulk_item);
				$insert_id = $input_form->insert();
				$rows_affected++;
				   $input_form->setId($insert_id);
				   $entries[] = $input_form;
			}
			$ajax_form->setRowsAffected($rows_affected);
			$ajax_form->setEntries($entries);
		} else {
		   $insert_id = $input_form->insert();
		   $input_form->setId($insert_id);
		   $ajax_form->setInsertId($insert_id);
		   $ajax_form->setRowsAffected(1);
		   $ajax_form->setRecord($input_form);
		}
		return $ajax_form;
	}

	/**
	 * Executes a DELETE request
	 * @return \Mojavi\Form\AjaxForm
	 */
	function executeDelete($input_form) {
		// Handle DELETE Requests
		$ajax_form = new \Mojavi\Form\AjaxForm();
		$rows_affected = $input_form->delete();
		if (isset($rows_affected['n'])) {
			$ajax_form->setRowsAffected($rows_affected['n']);
		}
		$ajax_form->setRecord($input_form);
		   
		return $ajax_form;
	}

	/**
	 * Returns the input form to use for this REST request
	 * @return \Mojavi\Form\CommonForm
	 */
	public function getInputForm() {
		return new \Mojavi\Form\CommonForm();
	}

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
		return \Mojavi\Request\Request::GET | \Mojavi\Request\Request::POST | \Mojavi\Request\Request::PUT | \Mojavi\Request\Request::DELETE;
	}
	
	/**
	 * Indicates that this action requires security.
	 *
	 * @return bool true, if this action requires security, otherwise false.
	 */
	public function isSecure ()
	{
		return false;
	}
}

