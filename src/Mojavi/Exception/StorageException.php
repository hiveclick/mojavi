<?php
namespace Mojavi\Exception;

/**
 * StorageException is thrown when a requested Storage implementation doesn't
 * exist or data cannot be read from or written to the storage.
 */
class StorageException extends MojaviException
{

	// +-----------------------------------------------------------------------+
	// | CONSTRUCTOR														   |
	// +-----------------------------------------------------------------------+

	/**
	 * Class constructor.
	 *
	 * @param string The error message.
	 * @param int	The error code.
	 */
	public function __construct ($message = null, $code = 0)
	{

		parent::__construct($message, $code);

		$this->setName('StorageException');

	}

}

