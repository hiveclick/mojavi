<?php
/**
 * Mongo.php can convert parameters to be safe for Mongo
 * @author    hobby
 * @created   10/18/16 11:04 AM
 */

namespace Mojavi\Util;

final class Mongo
{
	/**
	 * Returns a \MongoId from the parameter
	 * @param $value \MongoId|string
	 * @return \MongoId|null
	 */
	static function toMongoId($value) {
		if ($value instanceof \MongoId) {
			return $value;
		} else if (is_string($value) && \MongoId::isValid($value)) {
			return new \MongoId($value);
		}
		return null;
	}

	/**
	 * Returns a boolean from the parameter
	 * @param $value array|string
	 * @return int
	 */
	static function toMongoBoolean($value) {
		if (!(is_array($value) || is_object($value))) {
			return (boolean)$value;
		}
		return $value;
	}

	/**
	 * Returns an int from the parameter
	 * @param $value array|string
	 * @return int
	 */
	static function toMongoInt($value) {
		if (!(is_array($value) || is_object($value))) {
			return (int)$value;
		}
		return $value;
	}

	/**
	 * Returns an int from the parameter
	 * @param $value array|string
	 * @return int
	 */
	static function toMongoString($value) {
		if (is_array($value)) {
			return (string)json_encode($value);
		} else if (is_object($value)) {
			return (string)json_encode($value);
		} else if (is_string($value)) {
			return trim($value);
		} else {
			return trim((string)$value);
		}
	}

	/**
	 * Returns an array from the parameter
	 * @param $value array|string
	 * @return array
	 */
	static function toMongoArray($value) {
		if (is_array($value)) {
			array_walk($value, function(&$val) { if (is_array($val)) { $val = json_encode($val); }});
			$value = array_map('trim', $value); // trim all elements in array
			$value = array_values(array_filter($value, 'strlen')); // remove blank lines
			return $value;
		} else if (is_string($value)) {
			if (strpos($value, ",") !== false) {
				$value = explode(",", $value);
				$value = array_map('trim', $value); // trim all elements in array
				$value = array_values(array_filter($value, 'strlen')); // remove blank lines
				return $value;
			} else if (strpos($value, "\n") !== false) {
				$value = explode("\n", $value);
				$value = array_map('trim', $value); // trim all elements in array
				$value = array_values(array_filter($value, 'strlen')); // remove blank lines
				return $value;
			} else {
				$value = array($value);
				$value = array_map('trim', $value); // trim all elements in array
				$value = array_values(array_filter($value, 'strlen')); // remove blank lines
				return $value;
			}
		}
		return array();
	}

	/**
	 * Returns an array from the parameter
	 * @param $value array|string
	 * @param $class_name string
	 * @return array
	 */
	static function toMongoSubDocArray($value, $class_name) {
		$ret_val = array();
		if (is_array($value)) {
			foreach ($value as $item) {
				if (is_array($item)) {
					$sub_doc = new $class_name();
					$sub_doc->populate($item);
					$ret_val[] = $sub_doc;
				} else if ($item instanceof \MongoId) {
					$sub_doc = new $class_name();
					$sub_doc->setId($item);
					$ret_val[] = $sub_doc;
				} else if (is_string($item) && \MongoId::isValid($item)) {
					$sub_doc = new $class_name();
					$sub_doc->setId(new \MongoId($item));
					$ret_val[] = $sub_doc;
				}
			}
		} else if ($value instanceof \MongoId) {
			$sub_doc = new $class_name();
			$sub_doc->setId($value);
			$ret_val[] = $sub_doc;
		} else if (is_string($value) && \MongoId::isValid($value)) {
			$sub_doc = new $class_name();
			$sub_doc->setId(new \MongoId($value));
			$ret_val[] = $sub_doc;
		}
		return $ret_val;
	}

	/**
	 * Returns an array from the parameter
	 * @param $value array|string
	 * @param $class_name string
	 * @return array
	 */
	static function toMongoSubDoc($value, $class_name) {
		if (is_array($value)) {
			$ret_val = new $class_name();
			$ret_val->populate($value);
		} else if ($value instanceof \MongoId) {
			$ret_val = new $class_name();
			$ret_val->setId($value);
		} else if (is_string($value) && \MongoId::isValid($value)) {
			$ret_val = new $class_name();
			$ret_val->setId(new \MongoId($value));
		}
		return $ret_val;
	}

	/**
	 * Returns a MongoDate from the parameter
	 * @param $value \MongoDate|int|string
	 * @return \MongoDate
	 */
	static function toMongoDate($value) {
		if ($value instanceof \MongoDate) {
			return $value;
		} else if (is_int($value)) {
			return new \MongoDate($value);
		} else if (is_string($value)) {
			return new \MongoDate(strtotime($value));
		}
		return new \MongoDate();
	}

}