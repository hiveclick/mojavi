<?php
namespace Mojavi\Form;

use Mojavi\Core\MojaviObject as MojaviObject;
use Mojavi\Util\StringTools as StringTools;
use Mojavi\Controller\Controller as Controller;
use Mojavi\Database\DatabaseResultResource as DatabaseResultResource;
use ReflectionClass;
use ReflectionProperty;

/**
 * MojaviForm is the base class for ALL forms used.  It has a single function set (Id).  Every
 * table should have an auto_increment field called id for conformity.  MojaviForm has support
 * for the Errors object and populate(Array).  Subclasses will be auto-populated as long as they
 * contain getters and setters that match the criteria of the populate() method.
 *
 * MojaviForm can be instantiated with an errors object for ease of use.
 */
class MojaviForm extends MojaviObject {

	protected $_id;
	
	/*   VARS FOR REPOPULATION   */
	private $populated;
	private $repopulated;
	private $module_name;
	private $model_name;
	private $modified_columns;
	
	/**
	 * returns the id.  If your dataset is setup so that the primary key (id) is named, such as
	 * customer_id, then this function should be overridden to alias that function.  I.e.:
	 * function getId() {
	 *		 return $this->getCustomerId();
	 * }
	 * @return string
	 */
	function getId() {
	    if (is_null($this->_id)) {
	        $this->_id = null;
	    }
	    return $this->_id;
	}
	
	/**
	 * sets the id.  If your dataset is setup so that the primary key (id) is named, such as
	 * customer_id, then this function should be overridden to alias that function.  I.e.:
	 * function setId($arg0) {
	 *		 $this->setCustomerId($arg0);
	 * }
	 * @param string $arg0
	 */
	function setId($arg0) {
	    $this->_id = $arg0;
	    return $this;
	}
	
	/**
	 * Returns the modified_columns.  Modified columns can be used to build an update string and
	 * only update data that has been changed
	 * @return array
	 */
	function getModifiedColumns() {
	    if (is_null($this->modified_columns)) {
	        $this->modified_columns = array();
	    }
	    return $this->modified_columns;
	}
	
	/**
	 * Sets the modified_columns.  Modified columns can be used to build an update string and
	 * only update data that has been changed.  Set to null to clear out any previous saved
	 * modifications
	 * @param array
	 */
	function setModifiedColumns($arg0) {
	    $this->modified_columns = $arg0;
	}
	
	/**
	 * Adds an entry to the modified_columns
	 * @param string
	 */
	function addModifiedColumn($arg0) {
	    $tmp_array = $this->getModifiedColumns();
	    $tmp_array[] = $arg0;
	    $this->setModifiedColumns($tmp_array);
	}
	
	/**
	 * Returns if this form has any modified columns
	 * @return boolean
	 */
	function isModified() {
	    return (count($this->getModifiedColumns()) > 0);
	}

	/**
	 * Populate will parse the elements of an array (ResultSet) or XML_ELEMENT_NODE and attempt
	 * to populate the form.  It will convert _a to A (i.e. first_name => firstName) and will
	 * search for an appropriate setter (such as first_name => setFirstName).  Be aware the it
	 * will search for case sensitive functions, so first_name => firstName => setFirstName() is
	 * not the same as firstname => setFirstname().
	 * @param array $arg0
	 */
	function populate($arg0, $modify_columns = true) {
		$this->setModifiedColumns(null);
		if (is_array($arg0)) {
			// Attempt to populate the form
			foreach ($arg0 as $key => $value) {
				$callableName = null;
				$entry = str_replace("_", "", $key);
				$entry = str_replace("-", "", $entry);
				if (is_array($value)) {
					/*
					* If this is an array, then we need to add all the elements, so first check for an
					* add***($arg0) function.  If it does not exist, then fallback to a set***($arg0)
					*/
					# The regex will change '_a' to 'A' or '_1' to '1'
					//$entry = str_replace("_", "", $key);
					
					//$entry = preg_replace_callback("/_([a-zA-Z0-9])/", function($matches) { return strtoupper($matches[1]); }, strtolower($key));
					
					if (is_callable(array($this, 'add' . $entry),false, $callableName)) {
						foreach ($value as $key2 => $value1) {
							$this->{'add' . $entry}($value1, $key2);
						}
					} else {
						# The regex will change '_a' to 'A' or '_1' to '1'
						//$entry = preg_replace_callback("/_([a-zA-Z0-9])/", function($matches) { return strtoupper($matches[1]); }, strtolower($key));
						if (is_callable(array($this, 'set' . $entry),false, $callableName)) {
							$this->{'set' . $entry}($value);
						}
					}
				} else {
					# The regex will change '_a' to 'A' or '_1' to '1'
					//$entry = preg_replace_callback("/_([a-zA-Z0-9])/", function($matches) { return strtoupper($matches[1]); }, strtolower($key));
					if (is_callable(array($this, 'set' . $entry),false, $callableName)) {
						$this->{'set' . $entry}($value);
					}
				}
			}
		} else if (is_object($arg0)) {
			# Treat the argument as an object and copy any getters to the appropriate setters
			$reflection = new ReflectionClass($arg0);
			$properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);
			foreach ($properties as $property) {
				$method_name = str_replace("_", "", $property->getName());
				if (method_exists($arg0, 'get' . $method_name)) {
					$value = $arg0->{'get' . $method_name}();
					# if this form has a setter that matches this getter (i.e. setId() would match getId()), then set it
					if (is_callable(array($this, 'set' . $method_name), false, $callableName)) {
						$this->{'set' . $method_name}($value);
					}
				}
			}
		}// End is_array($arg0)
		if ($modify_columns === false) {
			$this->setModifiedColumn(null);
		}
		return $this;
	}
	
	
	
	/**
	 * Checks if the value is null
	 * @return boolean
	 */
	function value_is_null($arg0) {
		return (is_null($arg0));
	}

	/**
	 * Returns the errors object.  Normally you want to setup an error object beforehand and pass
	 * it to all the forms and models that you use so that you can collect all the errors
	 * @return Errors
	 */
	function getErrors() {
		return Controller::getInstance()->getContext()->getErrors();
	}

	/**
	 * Attempts to validate this form.  If any errors occur, they are
	 * populated in the internal errors object.
	 * @return boolean - true if validation succeeds
	 */
	function validate() {
		return true;
	}

	/**
	 * Resets a form.  This is mostly used with certain form elements (like a checkbox).
	 * If a checkbox is checked, it is passed in the request, if it is not checked, then
	 * nothing is passed.  By resetting a checkbox property to false here, then every
	 * request it is set to false, UNLESS a value is passed in - which is the way it's
	 * supposed to work.
	 * @return boolean - true if validation succeeds
	 */
	function reset() {
		return true;
	}

	/**
	 * Initialize this form.
	 *
	 * @param Context The current application context.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 *
	 * @since  3.0.0
	 */
	public function initialize ($context) {
		return true;
	}

	/**
	 * Retrieve the current application context.
	 *
	 * @return Context The current Context instance.
	 *
	 * @since  3.0.0
	 */
	public function getContext() {
		return Controller::getInstance()->getContext();
	}
	
	/**
	 * Converts this object to an array for use in the database
	 * @return array
	 */
	function toDbArray() {
	    $ret_val = array();
	    $reflection = new \ReflectionClass($this);
	    $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
	    foreach ($properties as $property) {
	        $property_name = $property->getName();
	        $method_name = 'get' . str_replace("_", "", $property_name);
	        if ($reflection->hasMethod($method_name)) {
	            $value = $this->$method_name();
	            if (is_string($value) && trim($value) == '') { continue; }
	            if (is_array($value) && empty($value)) { continue; }
	            if (is_null($value)) { continue; }
	             
	            if (strtolower($method_name) == 'getid') {
	                $ret_val['_id'] = $value;
	                $ret_val[$property_name] = $value;
	            } else if ($value instanceof \MongoId) {
	                $ret_val[$property_name] = $value;
	            } else if ($value instanceof \Mojavi\Form\MojaviForm) {
	                $val = $value->toDbArray();
	                if (!empty($val)) {
	                    $ret_val[$property_name] = $val;
	                }
	            } else if ($value instanceof \Mojavi\Database\DatabaseResultResource) {
	                foreach ($value as $item) {
	                    $val = $item->toDbArray();
	                    if (!empty($val)) {
	                        $ret_val[$property_name][] = $val;
	                    }
	                }
	            } else if (is_array($value)) {
	                if (count($value) > 0) {
	                    foreach ($value as $key => $item) {
	                        if ($item instanceof \Mojavi\Form\MojaviForm) {
	                            $val = $item->toDbArray();
	                            if (!empty($val)) {
	                                $ret_val[$property_name][$key] = $val;
	                            }
	                        } else {
	                            if (is_string($item) && trim($item) == '') { continue; }
	                            if (is_null($item)) { continue; }
	                            if (is_array($item) && empty($item)) { continue; }
	                            $ret_val[$property_name][$key] = $item;
	                        }
	                    }
	                } else {
	                    $ret_val[$property_name] = $value;
	                }
	            } else {
	                $ret_val[$property_name] = $value;
	            }
	        } else {
	            $value = $this->$property_name;
	            if (is_string($value) && trim($value) == '') { continue; }
	            if (is_array($value) && empty($value)) { continue; }
	            if (is_null($value)) { continue; }
	            $ret_val[$property_name] = $value;
	        }
	    }
	    return $ret_val;
	}
	
	/**
	 * Converts this object to an array
	 * @return array
	 */
	function toArray($deep = false, $use_null_for_blank = true, $preserve_object_ids = false) {
		$ret_val = array();
		$reflection = new \ReflectionClass($this);
		$properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
		foreach ($properties as $property) {
			$property_name = $property->getName();
			$method_name = 'get' . str_replace("_", "", $property_name);
			if ($reflection->hasMethod($method_name)) {
				$value = $this->$method_name();
				if (is_string($value) && $use_null_for_blank && trim($value) == '') { $value = null; }
				if (strtolower($method_name) == 'getid') {
					if ($preserve_object_ids) {
						$ret_val['_id'] = $value;
						$ret_val[$property_name] = $value;
					} else {
						$ret_val['_id'] = (string)$value;
						$ret_val[$property_name] = (string)$value;						
					}
				} else if (is_null($value)) {
					$ret_val[$property_name] = $value;
				} else if ($value instanceof \Mojavi\Form\MojaviForm) {
					$ret_val[$property_name] = $value->toArray($deep, $use_null_for_blank, $preserve_object_ids);
				} else if ($value instanceof \MongoId) {
					if ($preserve_object_ids) {
						$ret_val[$property_name] = $value;
					} else {
						$ret_val[$property_name] = (string)$value;
					}
				} else if ($value instanceof \Mojavi\Database\DatabaseResultResource) {
					foreach ($value as $item) {
						$ret_val[$property_name][] = $item->toArray($deep, $use_null_for_blank, $preserve_object_ids);
					}
				} else if (is_array($value)) {
					if (count($value) > 0) {
						foreach ($value as $key => $item) {
							if ($item instanceof \Mojavi\Form\MojaviForm) {
								$ret_val[$property_name][$key] = $item->toArray($deep, $use_null_for_blank, $preserve_object_ids);
							} else {
								if ($use_null_for_blank && is_string($item) && trim($item) == '') { $item = null; }
								$ret_val[$property_name][$key] = $item;
							}
						}
					} else {
						 $ret_val[$property_name] = $value;
					}
				} else {
					$ret_val[$property_name] = $value;
				}
			} else {
				$value = $this->$property_name;
				if ($use_null_for_blank && is_string($value) && trim($value) == '') { $value = null; }
				$ret_val[$property_name] = $value;
			}
		}
		return $ret_val;
	}
}