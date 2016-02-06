<?php
/**
 * WebController provides web specific methods to Controller such as, url
 * redirection.
 *
 * @package	Mojavi
 * @subpackage Controller
 */
namespace Mojavi\Controller;

abstract class WebController extends Controller
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES													 |
	// +-----------------------------------------------------------------------+

	private
		$contentType = null;

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Generate a formatted Mojavi URL.
	 *
	 * @param string An existing URL for basing the parameters.
	 * @param array  An associative array of URL parameters.
	 *
	 * @return string A URL to a Mojavi resource.
	 */
	public function genURL ($url = null, $parameters = array())
	{

		if ($url == null)
		{

			$url = $_SERVER['SCRIPT_NAME'];

		}

		if (MO_URL_FORMAT == 'PATH')
		{

			// use PATH format
			$divider  = '/';
			$equals   = '/';
			$url	 .= '/';

		} else
		{

			// use GET format
			$divider  = '&';
			$equals   = '=';
			$url	 .= '?';

		}

		// loop through the parameters
		foreach ($parameters as $key => &$value)
		{

			$url .= urlencode($key) . $equals . urlencode($value) . $divider;

		}

		// strip off last divider character
		$url = rtrim($url, $divider);

		// replace &'s with &amp;
		$url = str_replace('&', '&amp;', $url);

		return $url;

	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve the requested content type.
	 *
	 * @return string A content type.
	 */
	public function getContentType ()
	{

		return $this->contentType;

	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this controller.
	 *
	 * @return void
	 */
	protected function initialize ()
	{

		// initialize parent
		parent::initialize();

		// set our content type
		$this->contentType = $this->getContext()
								  ->getRequest()
								  ->getParameter('ctype', MO_CONTENT_TYPE);

	}

	// -------------------------------------------------------------------------

	/**
	 * Redirect the request to another URL.
	 *
	 * @param string An existing URL.
	 * @param int	A delay in seconds before redirecting. This only works on
	 *			   browsers that do not support the PHP header.
	 *
	 * @return void
	 */
	public function redirect ($url, $delay = 0)
	{

		// shutdown the controller
		$this->shutdown();

		// redirect
		header('Location: ' . $url);

		$echo = '<html>' .
				'<head>' .
				'<meta http-equiv="refresh" content="%d;url=%s"/>' .
				'</head>' .
				'</html>';

		$echo = sprintf($echo, $delay, $url);

		echo $echo;

		exit;

	}

	// -------------------------------------------------------------------------

	/**
	 * Set the content type for this request.
	 *
	 * @param string A content type.
	 *
	 * @return void
	 */
	public function setContentType ($type)
	{

		$this->contentType = $type;

	}

}

