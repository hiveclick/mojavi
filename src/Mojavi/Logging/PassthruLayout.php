<?php
namespace Mojavi\Logging;
// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.								   |
// | Copyright (c) 2003-2005 Agavi Foundation								  |
// |																		   |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the	|
// | LICENSE file online at http://www.agavi.org/LICENSE.txt				   |
// |   vi: set noexpandtab:													|
// |   Local Variables:														|
// |   indent-tabs-mode: t													 |
// |   End:																	|
// +---------------------------------------------------------------------------+
 
/**
 * PassthruLayout is a Layout that will return the Message text unaltered.
 */
class PassthruLayout extends Layout
{
 
	/**
	 * Format a message.
	 *
	 * @param Message A Message instance.
	 *
	 * @return string A formatted message.
	 */
	public function &format ($message)
	{
		$msgString = sprintf('%s', $message->__toString());
		return $msgString;
	}
 
}
  
