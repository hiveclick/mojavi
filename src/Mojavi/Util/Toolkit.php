<?php
namespace Mojavi\Util;

/**
 * Toolkit provides basic utility methods.
 */
class Toolkit extends \Mojavi\Core\MojaviObject
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Extract the class or interface name from filename.
	 *
	 * @param string A filename.
	 *
	 * @return string A class or interface name, if one can be extracted,
	 *				otherwise null.
	 */
	public static function extractClassName ($filename)
	{

		$retval = null;

		if (self::isPathAbsolute($filename))
		{

			$filename = basename($filename);

		}

		$pattern = '/(.*?)\.(class|interface)\.php/i';

		if (preg_match($pattern, $filename, $match))
		{

			$retval = $match[1];

		}

		return $retval;

	}

	// -------------------------------------------------------------------------

	/**
	 * Determine if a filesystem path is absolute.
	 *
	 * @param path A filesystem path.
	 *
	 * @return bool true, if the path is absolute, otherwise false.
	 */
	public static function isPathAbsolute ($path)
	{

		if ($path{0} == '/' || $path{0} == '\\' ||
			(strlen($path) > 3 && ctype_alpha($path{0}) &&
			 $path{1} == ':' &&
			 ($path{2} == '\\' || $path{2} == '/')
			)
		   )
		{

			return true;

		}

		return false;

	}
	
	/**
	 * Recursively removed a directory
	 * @param string $dir
	 */
	public static function delTree($dir) {
		if (is_dir($dir)) {
			$objects = @scandir($dir);
			if (is_array($objects)) {
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						if (@filetype($dir . DIRECTORY_SEPARATOR . $object) == "dir") {
							Toolkit::delTree($dir . DIRECTORY_SEPARATOR . $object);
						} else {
							@unlink($dir . DIRECTORY_SEPARATOR . $object);
						}
					}
				}
				reset($objects);
			}
			 @rmdir($dir);
		}
	}

}

