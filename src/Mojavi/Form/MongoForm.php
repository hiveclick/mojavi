<?php
namespace Mojavi\Form;

use Mojavi\Controller\Controller;
use MongoId;

class MongoForm extends CommonForm {

	const ID_TYPE_MONGO = 1;
	const ID_TYPE_STRING = 2;

	private $_collection = '';
	private $_db_name = '';
	private $_class_name = '';
	private $_id_type = self::ID_TYPE_MONGO;

	/**
	 * Sets the id
	 * @param int|Object $arg0
	 */
	function setId($arg0) {
	    if ($this->getIdType() == self::ID_TYPE_MONGO) {
    		if (is_string($arg0) && \MongoId::isValid($arg0)) {
    			parent::setId(new \MongoId($arg0));
    		} else if ($arg0 instanceof \MongoId) {
    			parent::setId($arg0);
    		} else if (is_null($arg0)) {
    			parent::setId($arg0);
    		} else if (is_string($arg0) && trim($arg0) == '') {
    			parent::setId(null);
    		} else {
    			try {
    				throw new \Exception('Invalid ID set: ' . var_export($arg0, true));
    			} catch (\Exception $e) {
    				\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . $e->getTraceAsString());
    				throw $e;
    			}
    		}
	    } else if ($this->getIdType() == self::ID_TYPE_STRING) {
	        if (is_string($arg0)) {
	            parent::setId($arg0);
	        } else if ($arg0 instanceof \MongoId) {
	            parent::setId((string)$arg0);
	        } else if (is_null($arg0)) {
	            parent::setId($arg0);
	        } else if (is_string($arg0) && trim($arg0) == '') {
	            parent::setId(null);
	        } else {
	            try {
    				throw new \Exception('Invalid ID set: ' . var_export($arg0, true));
	            } catch (\Exception $e) {
    				\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . $e->getTraceAsString());
    				throw $e;
	            }
	        }
	    }
	}

	/**
	 * Returns a connection to the mongo database
	 * @param string $db_host
	 * @param string $db_name
	 */
	public function getConnection($db_name = null) {
		if (is_null($db_name)) {
			$db_name = $this->getDbName();
		}

		return Controller::getInstance()->getContext()->getDatabaseConnection($db_name);
	}

	/**
	 * Returns the collection based on this class
	 * @param string $db_host
	 * @param string $db_name
	 * @param string $collection
	 * @return MongoCollection
	 */
	public function getCollection($db_name = null, $collection = null) {
		if (is_null($collection)) {
			if ($this->getCollectionName() == '') {
				throw new \Exception('You have not set the collection name for ' . get_class($this));
			}
			$collection = $this->getCollectionName();
		}
		if (is_null($db_name)) {
			$db_name = $this->getDbName();
		}
		if (trim($collection) == '') {
			// Use the default database name according to the connection
			$collection = Controller::getInstance()->getContext()->getDatabaseManager()->getDatabase($db_name)->getParameter('database');
		}
		return $this->getConnection($db_name)->$collection;
	}

	/**
	 * Queries a record based on the _id
	 * @param array $criteria
	 * @return MongoForm
	 */
	public function query(array $criteria = array(), $merge_id = true) {
		if ($merge_id) {
			$criteria = array_merge($criteria, array('_id' => $this->getId()));
		}

		$record_array = $this->getCollection()->findOne($criteria);
		if (is_array($record_array)) {
			$this->populate($record_array);
			return $this;
		}
		return false;
	}

	/**
	 * Queries multiple records based on the critera
	 * @param array $criteria
	 * @return MongoCursor
	 */
	public function queryAll(array $criteria = array(), array $fields = array(), $hydrate = true, $timeout = 30000) {
		// Find our records
		$records = $this->getCollection()->find($criteria, $fields);
		$records->timeout($timeout);
		$records->maxTimeMS($timeout);
		
		// Handle sorting
		if ($this->getSort() != '-1') {
			$records = $records->sort(array($this->getSort() => (strtoupper($this->getSord()) == 'ASC' ? \MongoCollection::ASCENDING : \MongoCollection::DESCENDING)));
		}

		// Handle pagination
		if (!$this->getIgnorePagination()) {
			if ($this->getStart() !== false) {
				$records = $records->limit($this->getItemsPerPage())->skip($this->getStart());
			} else {
				$records = $records->limit($this->getItemsPerPage())->skip($this->getStartIndex());
			}
		}
		
		// Check if we need to hydrate before we return
		if ($hydrate === true) {
			// Figure out how many records we found
			$count = $this->getCollection()->count($criteria);
			$this->setTotal($count);
			
			$ret_val = array();
			if ($records->count() > 0) {
				$record_array = iterator_to_array($records);
				foreach ($record_array as $record_item) {
					$hydrate_class_name = get_class($this);
					$hydrate_class = new $hydrate_class_name();
					$hydrate_class->populate($record_item);
					$ret_val[] = $hydrate_class;
				}
			}
			// Return the array
			return $ret_val;
		} else {
			// Return the cursor
			return $records;
		}
	}

	/**
	 * Counts the records based on the critera
	 * @param array $criteria
	 * @return integer
	 */
	public function count(array $criteria = array()) {
		$record_count = $this->getCollection()->count($criteria);
		return $record_count;
	}

	/**
	 * Finds and modifies existing records
	 * @return integer
	 */
	public function insert() {
		$insert_array = $this->toDbArray(true, true);
		if (array_key_exists('_id', $insert_array)) { unset($insert_array['_id']); }
		$ret_val = $this->getCollection()->save($insert_array);
		if (isset($insert_array['_id'])) {
			$this->setId($insert_array['_id']);
		}
		return $this->getId();
	}

	/**
	 * Finds and modifies existing records
	 * @return integer
	 */
	public function updateMultiple($criteria_array = array(), $update_array = array(), $options_array = array('upsert' => true, 'multiple' => true), $use_set_notation = false) {
		// Generate an update array of only the fields that have changed
		if (empty($update_array)) {
			$update_array = $this->createUpdateArray($use_set_notation);
		}
		#\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . var_export($criteria_array, true));
		#\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . var_export($update_array, true));
		// If still empty, then nothing to update
		// @TODO: should we really return true in this circumstance? Maybe something else, like wrap the response so it resembles a mongo response?
		if (empty($update_array)) { return true; }
		$ret_val = $this->getCollection()->update($criteria_array, $update_array, $options_array);
		return $ret_val;
	}
	
	/**
	 * Finds and deletes existing records
	 * @return integer
	 */
	public function deleteMultiple($criteria_array = array(), $options_array = array('multiple' => true), $use_set_notation = false) {
		#\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . var_export($criteria_array, true));
		// If still empty, then nothing to update
		// @TODO: should we really return true in this circumstance? Maybe something else, like wrap the response so it resembles a mongo response?
		$ret_val = $this->getCollection()->remove($criteria_array, $options_array);
		return $ret_val;
	}

	/**
	 * Finds and modifies existing records
	 * @return integer
	 */
	public function update($criteria_array = array(), $update_array = array(), $options_array = array('upsert' => false), $use_set_notation = false) {
		// This function will only update a single document
		if ($this->getIdType() === self::ID_TYPE_MONGO) {
			if ($this->getId() instanceof \MongoId) {
				$criteria_array = array_merge($criteria_array, array('_id' => $this->getId()));
			} else {
				$criteria_array = array_merge($criteria_array, array('_id' => new \MongoId($this->getId())));
			}
		} else {
			$criteria_array = array_merge($criteria_array, array('_id' => $this->getId()));
		}
		// Generate an update array of only the fields that have changed
		if (empty($update_array)) {
			$update_array = $this->createUpdateArray($use_set_notation);
		}
		#\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . var_export($criteria_array, true));
		#\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . var_export($update_array, true));
		// If still empty, then nothing to update
		// @TODO: should we really return true in this circumstance? Maybe something else, like wrap the response so it resembles a mongo response?
		if (empty($update_array)) { return true; }
		$ret_val = $this->getCollection()->update($criteria_array, $update_array, $options_array);
		return $ret_val;
	}

	/**
	 * Finds a single record and modifies it atomically
	 * Generally used for selection of a record for processing and locking it to prevent outside operation
	 * @param array $criteria_array
	 * @param array $update_array
	 * @param array $sort_array
	 * @param boolean $hydrate
	 * @return multitype:multitype:unknown
	 */
	public function findAndModify(
		$criteria_array,
		$update_array,
		$fields_array = null,
		$options_array = null,
		$hydrate = true
	) {
		$record_array = $this->getCollection()->findAndModify(
			$criteria_array,
			$update_array,
			$fields_array,
			$options_array
		);
		if (is_array($record_array)) {
			if ($hydrate === true) {
				$this->populate($record_array, true);
				return $this;
			}
			return $record_array;
		}
		return null;
	}

	/**
	 * Used to iterate through an array and generate a set notation
	 * @param unknown $notation_array
	 * @return multitype:mixed
	 */
	public function createSetNotation($notation_array) {
		$ritit = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($notation_array));
		$result = array();
		foreach ($ritit as $leafValue) {
			$keys = array();
			foreach (range(0, $ritit->getDepth()) as $depth) {
				$keys[] = $ritit->getSubIterator($depth)->key();
			}
			$result[ join('.', $keys) ] = $leafValue;
		}
		return $result;
	}

	/**
	 * Generates an array of just the fields that have updated
	 * @param boolean $use_set_notation
	 * @return multitype:multitype:unknown
	 */
	protected function createUpdateArray($use_set_notation = true) {
		$return_array = array();
		$set_data_array = array();
		$unset_data_array = array();
		foreach ($this->toArray(true, true) AS $name => $value) {
			if (in_array($name, $this->getModifiedColumns())) {
				/* @todo at some point in the future we may want to reference things by more than _id, so this will have to be abstracted */
				if(strpos($name, '_') === 0) {
					continue;
				} else if(strpos($name, 'modified_columns') === 0) {
					continue;
				} else if (!is_null($value)) {
					if (is_array($value) && !empty($value) && ($use_set_notation === true)) {
						$set_notation_array = $this->createSetNotation($value);
						foreach($set_notation_array AS $set_node_id => $set_node_value) {
							$set_data_array[$name . '.' . $set_node_id] = $set_node_value; // Mongo prefers null to blank strings
						}
					} else {
						$set_data_array[$name] = $value; // Mongo prefers null to blank strings
					}
				} else if (is_null($value)) {
				    $unset_data_array[$name] = 1;
				}
			}
		}
		if(count($set_data_array) > 0) {
			$return_array['$set'] = $set_data_array;
		}
		if(count($unset_data_array) > 0) {
		    $return_array['$unset'] = $unset_data_array;
		}
		return $return_array;
	}

	/**
	 * Finds and modifies existing records
	 * @return integer
	 */
	public function delete() {
		$delete_array = array();
		if ($this->getIdType() === self::ID_TYPE_MONGO) {
			$delete_array['_id'] = new \MongoId($this->getId());
		} else {
			$delete_array['_id'] = $this->getId();
		}
		$rows_affected = $this->getCollection()->remove($delete_array);
		return $rows_affected;
	}

	/**
	 * Drops the collection completely
	 * @return integer
	 */
	public function drop() {
		$rows_affected = $this->getCollection()->drop();
		return $rows_affected;
	}

	/**
	 * Returns the _class_name
	 * @return string
	 */
	function getClassName() {
		if (is_null($this->_class_name)) {
			$this->_class_name = get_class($this);
		}
		return $this->_class_name;
	}

	/**
	 * Sets the _class_name
	 * @var string
	 */
	function setClassName($arg0) {
		$this->_class_name = $arg0;
		return $this;
	}

	/**
	 * Returns the _collection
	 * @return string
	 */
	function getCollectionName() {
		if (is_null($this->_collection)) {
			$this->_collection = get_class($this);
		}
		return $this->_collection;
	}

	/**
	 * Sets the _collection
	 * @var string
	 */
	function setCollectionName($arg0) {
		$this->_collection = $arg0;
		return $this;
	}

	/**
	 * Returns the _db_name
	 * @return string
	 */
	function getDbName() {
		if (is_null($this->_db_name)) {
			$this->_db_name = "default";
		}
		return $this->_db_name;
	}

	/**
	 * Sets the _db_name
	 * @var string
	 */
	function setDbName($arg0) {
		$this->_db_name = $arg0;
		return $this;
	}

	/**
	 * Returns the _id_type
	 * @return integer
	 */
	function getIdType() {
		if (is_null($this->_id_type)) {
			$this->_id_type = self::ID_TYPE_MONGO;
		}
		return $this->_id_type;
	}

	/**
	 * Sets the _id_type
	 * @var integer
	 */
	function setIdType($arg0) {
		$this->_id_type = $arg0;
		return $this;
	}
	
	/**
	 * Returns the created_time field.
	 * @return string
	 */
	function getCreatedTime() {
		if (is_null($this->created_time)) {
			$this->created_time = new \MongoDate();
		}
		return $this->created_time;
	}
		
	/**
	 * Sets the created_time field.
	 * @param string $arg0
	 */
	function setCreatedTime($arg0) {
		$this->created_time = $arg0;
		$this->addModifiedColumn('created_time');
		return $this;
	}

	/**
	 * Helper method to return a value using the getter or property
	 * @param string $name
	 * @param string $default
	 * @param string $encode_type
	 * @return string
	 */
	public function retrieveValue($name, $default = null, $encode_type = null) {
		$return_value = $default;

		$method_name = 'get' . \Mojavi\Util\StringTools::camelCase($name);
		if(method_exists($this, $method_name)) {
			$return_value = $this->$method_name();
		} else if(property_exists($this, $name)) {
			if(! is_null($this->$name)) {
				$return_value = $this->$name;
			}
		}
		switch($encode_type) {
			case 'htmlspecialchars':
				if (is_string($return_value)) {
					$return_value = htmlspecialchars($return_value);
				}
				break;
			case 'rawurlencode':
				if (is_string($return_value)) {
					$return_value = rawurlencode($return_value);
				}
				break;
		}
		return $return_value;
	}

	/**
	 * Helper method to return an html safe value
	 * @param string $name
	 * @param string $default
	 * @return Ambigous <string, string>
	 */
	public function retrieveValueHtml($name, $default = null) {
		return $this->retrieveValue($name, $default, 'htmlspecialchars');
	}

	/**
	 * Helper method to return a url safe value
	 * @param string $name
	 * @param string $default
	 * @return Ambigous <string, string>
	 */
	public function retrieveValueUrl($name, $default = null) {
		return $this->retrieveValue($name, $default, 'rawurlencode');
	}
	
	/**
	 * Returns an instance of this object by it's id
	 * @param integer $id
	 * @return \Mojavi\Form\MongoForm
	 */
	public static function retrieveById($id) {
		if (apc_exists(__CLASS__ . '_' . $id)) {
			$ret_val = apc_fetch(__CLASS__ . '_' . $id);
			return $ret_val;
		}
		$obj = new self();
		$obj->setId($id);
		$obj->query();
		apc_add(__CLASS__ . '_' . $id, $obj);
		return $obj;
	}
}
