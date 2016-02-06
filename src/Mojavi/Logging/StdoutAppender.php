<?php
namespace Mojavi\Logging;

/**
 * StdoutAppender appends a Message to stdout.
 */
class StdoutAppender extends FileAppender
{

	/**
	 * Initialize the object.
	 *
	 * @param array An array of parameters.
	 *
	 * @return mixed
	 */
	public function initialize($params)
	{
		$params['file'] = 'php://stdout';
		return parent::initialize($params);
	}

}

