<?php
namespace Mojavi\Util;
/**
 * Pulled from https://github.com/mtrbean/MongoJson
 * For a reference of MongoDB Extended JSON, see
 * http://docs.mongodb.org/manual/reference/mongodb-extended-json/
 */
class MongoJson extends \ArrayObject implements \JsonSerializable {

	public function __construct(array $doc) {
		parent::__construct($doc);
	}
	
	/**
	 * Outputs an array as strict json
	 * @param array $doc
	 * @param number $options
	 * @return string
	 */
	public static function strict(array $doc, $options=0) {
		if (empty($doc))
			$options |= JSON_FORCE_OBJECT;
		return json_encode(new self($doc), $options);
	}

	/**
	 * Outputs an array as extended Mongo json
	 * @param array $doc
	 * @param number $options
	 * @return string
	 */
	public static function extended(array $doc, $options=0) {
		$encoded = self::strict($doc, $options);
		$encoded = preg_replace('/\{\s*"\$date": ?(\d+)\s*\}/', 'new Date($1)', $encoded);
		$encoded = preg_replace('/\{\s*"\$regex": ?"(.+)",\s*"\$options": ?"(.*)"\s*\}/', '/$1/$2', $encoded);
		return $encoded;
	}	

	/**
	 * Expose the jsonSerialize function to fulfill the interface
	 * @return array
	 */
	public function jsonSerialize() {
		$doc = $this->getArrayCopy();
		array_walk_recursive($doc, function (&$var) {
			if ($var instanceof \MongoId) {
				$var = array('$oid' => (string)$var);
			}
			elseif ($var instanceof \MongoDate) {
				$ts = $var->sec * 1000 + $var->usec / 1000;
				$var = array('$date' => $ts);
			}
			elseif ($var instanceof \MongoRegex) {
				$var = array('$regex'   => $var->regex,
							 '$options' => $var->flags);
			}
			elseif ($var instanceof \MongoBinData) {
				$var = array('$binary' => $var->bin,
							 '$type'   => (string)$var->type);
			}
			elseif ($var instanceof \MongoTimestamp) {
				$var = array('$timestamp' => 
							array('t'=> $var->sec,
								  'i'=> $var->inc));
			}
			elseif ($var instanceof \MongoMinKey) {
				$var = array('$minKey' => 1);
			}
			elseif ($var instanceof \MongoMaxKey) {
				$var = array('$maxKey' => 1);
			}
			return $var;
		});
		return $doc;
	}
}