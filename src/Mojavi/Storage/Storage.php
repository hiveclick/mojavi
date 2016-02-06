<?php
/**
 * Storage allows you to customize the way Mojavi stores its persistent data.
 *
 * @package	Mojavi
 * @subpackage Storage
 */
namespace Mojavi\Storage;

use Mojavi\Controller\Controller as Controller;
use Mojavi\Util\ParameterHolder as ParameterHolder;
use Mojavi\Exception\FactoryException as FactoryException;

abstract class Storage extends ParameterHolder
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE DATA														  |
	// +-----------------------------------------------------------------------+


	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Retrieve the current application context.
	 *
	 * @return Context A Context instance.
	 */
	public function getContext ()
	{

		return Controller::getInstance()->getContext();

	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this Storage.
	 *
	 * @param Context A Context instance.
	 * @param array   An associative array of initialization parameters.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 *
	 * @throws <b>InitializationException</b> If an error occurs while
	 *										initializing this Storage.
	 */
	public function initialize ($context, $parameters = null)
	{

		if ($parameters != null)
		{

			$this->parameters = array_merge($this->parameters, $parameters);

		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve a new Storage implementation instance.
	 *
	 * @param string A Storage implementation name
	 *
	 * @return Storage A Storage implementation instance.
	 *
	 * @throws <b>FactoryException</b> If a storage implementation instance
	 *								 cannot be created.
	 */
	public static function newInstance ($class)
	{

		// the class exists
		$object = new $class();

		if (!($object instanceof Storage))
		{

			// the class name is of the wrong type
			$error = 'Class "%s" is not of the type Storage';
			$error = sprintf($error, $class);

			throw new FactoryException($error);

		}

		return $object;

	}

	// -------------------------------------------------------------------------

	/**
	 * Read data from this storage.
	 *
	 * The preferred format for a key is directory style so naming conflicts can
	 * be avoided.
	 *
	 * @param string A unique key identifying your data.
	 *
	 * @return mixed Data associated with the key.
	 *
	 * @throws <b>StorageException</b> If an error occurs while reading data from
	 *								 this storage.
	 */
	abstract function & read ($key);

	// -------------------------------------------------------------------------

	/**
	 * Remove data from this storage.
	 *
	 * The preferred format for a key is directory style so naming conflicts can
	 * be avoided.
	 *
	 * @param string A unique key identifying your data.
	 *
	 * @return mixed Data associated with the key.
	 *
	 * @throws <b>StorageException</b> If an error occurs while removing data from
	 *								 this storage.
	 */
	abstract function & remove ($key);

	// -------------------------------------------------------------------------

	/**
	 * Execute the shutdown procedure.
	 *
	 * @return void
	 *
	 * @throws <b>StorageException</b> If an error occurs while shutting down
	 *								 this storage.
	 */
	abstract function shutdown ();

	// -------------------------------------------------------------------------

	/**
	 * Write data to this storage.
	 *
	 * The preferred format for a key is directory style so naming conflicts can
	 * be avoided.
	 *
	 * @param string A unique key identifying your data.
	 * @param mixed  Data associated with your key.
	 *
	 * @return void
	 *
	 * @throws <b>StorageException</b> If an error occurs while writing to this
	 *								 storage.
	 */
	abstract function write ($key, &$data);

}

