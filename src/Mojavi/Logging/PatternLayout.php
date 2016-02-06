<?php
namespace Mojavi\Logging;

/**
 * PatternLayout
 */
class PatternLayout extends Layout
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Format a message.
	 *
	 * @param Message A Message instance.
	 *
	 * @return string A formatted message.
	 */
	public function & format ($message)
	{
		$msgString = sprintf('%s', $message->__toString());
		return $msgString;
	}

}

