<?php
namespace Mojavi\Model;

use Mojavi\Form\Form as Form;
/**
 * BasicModel is the base class for all Models.  It is only an abstract class but will
 * reinforce the need of basic functions (add, delete, update, count, query).  Some
 * subclasses may want to add additional functions like performSearch(Form arg0) and
 * performQueryAllByAccount(Form arg0).
 *
 * All the functions should take a MysqlForm (or subclass of MysqlForm) as an argument for
 * conformity.
 */
abstract class BasicModel extends Model {
	private $errors;

	function __construct($arg0) {
		// Nothing to do here
	}

	/**
	 * Abstract method used to perform a query for an individual record
	 * @param MysqlForm $arg0
	 * @return MysqlForm
	 */
	abstract function performQuery(MysqlForm $arg0);

	/**
	 * Abstract method to perform an insert into of a record
	 * @param MysqlForm $arg0
	 * @return integer
	 */
	abstract function performInsert(MysqlForm $arg0);

	/**
	 * Abstract method to perform an update of a record
	 * @param MysqlForm $arg0
	 * @return integer
	 */
	abstract function performUpdate(MysqlForm $arg0);

	/**
	 * Abstract method to perform a deletion of a record
	 * @param MysqlForm $arg0
	 * @return integer
	 */
	abstract function performDelete(MysqlForm $arg0);

	/**
	 * Abstract method to perform a query of all the records.  A PageListForm should
	 * normally be passed into so that a limit can be performed
	 * @param MysqlForm $arg0
	 * @return array
	 */
	abstract function performQueryAll(MysqlForm $arg0);

	/**
	 * Abstract method to perform a count of all the records.  A PageListForm should
	 * normally be passed into so that a limit can be performed.
	 * @param MysqlForm $arg0
	 * @return integer
	 */
	abstract function performCountAll(MysqlForm $arg0);

	/**
	 * Returns the errors object.  Normally you want to setup an error object beforehand and pass
	 * it to all the forms and models that you use so that you can collect all the errors
	 * @return Errors
	 */
	function getErrors() {
		return $this->getContext()->getErrors();
	}

	/**
	* Retrieves the currently logged in user details
	*/
	public function getUserDetails() {
		if (defined("MO_USER_NAMESPACE")) {
			return $this->getContext()->getUser()->getAttribute(MO_USER_NAMESPACE);
		} else {
			return $this->getContext()->getUser()->getAttribute("userForm");
		}
	}
}
