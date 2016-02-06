<?php
namespace Mojavi\Filter;

/**
 * LessFilter compiles css .less files into css files
 *
 * <b>Optional parameters:</b>
 *
 * # <b>comment</b> - [Yes] - Should we add an HTML comment to the end of each
 *							output with the execution time?
 * # <b>replace</b> - [No] - If this exists, every occurance of the value in the
 *						   client response will be replaced by the execution
 *						   time.
 */
class LessFilter extends Filter
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute this filter.
	 *
	 * @param FilterChain The filter chain.
	 *
	 * @return void
	 *
	 * @throws <b>FilterException</b> If an erro occurs during execution.
	 */
	public function execute ($filterChain)
	{

		static $loaded;

		if (!isset($loaded))
		{

			// load the filter
			$start_time = microtime(true);
			$need_to_rebuild = true;
			$loaded = true;

			$css_file = $this->getParameter('css_file', null);
			$less_file = $this->getParameter('less_file', null);
			
			if (!is_null($css_file) && !is_null($less_file)) {
				if (file_exists($less_file)) {
					if (file_exists($css_file)) {
						if (filemtime($css_file) >= filemtime($less_file)) {
							// css file is newer, so skip to the next filter							
							$filterChain->execute();
							$need_to_rebuild = false;
						}
					}
					
					if ($need_to_rebuild) {
						if (file_exists(MO_WEBAPP_DIR . "/vendor/oyejorge/less.php/lib/Less/Autoloader.php")) {
							\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . sprintf("Building new CSS file because date is %s and less file date is %s", filemtime($css_file), filemtime($less_file)));
							
							try {
								require_once MO_WEBAPP_DIR . "/vendor/oyejorge/less.php/lib/Less/Autoloader.php";
								\Less_Autoloader::register();
	
								$parser = new \Less_Parser( array( 'compress'=>true ) );
								$parser->parseFile($less_file, '/');
								$css = $parser->getCss();
								file_put_contents($css_file, $css);
								\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . sprintf("Generated less file in %ss", number_format(microtime(true) - $start_time, 4)));
							} catch (\Exception $e) {
								\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . $e->getMessage());
							}
						
							// completed the caching, move on to the next filter
							$filterChain->execute();
						} else {
							// we already loaded this filter, skip to the next filter
							\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . "Missing Less vendor library, use composer require oyejorge/less.php");
							$filterChain->execute();
						}
					}
				} else {
					// less file doesn't exist so skip to the next filter
					\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . "Cannot find less file to compile: " . $less_file);
					$filterChain->execute();
				}
			} else {
				// less file or css file is not defined, so skip to the next filter
				\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . "less_file or css_file parameter is not defined");
				$filterChain->execute();
			}
		} else
		{

			// we already loaded this filter, skip to the next filter
			$filterChain->execute();

		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Initialize this filter.
	 *
	 * @param Context The current application context.
	 * @param array   An associative array of initialization parameters.
	 *
	 * @return bool true, if initialization completes successfully, otherwise
	 *			  false.
	 *
	 * @throws <b>FilterException</b> If an error occurs during initialization.
	 */
	public function initialize ($context, $parameters = null)
	{

		// set defaults
		$this->setParameter('comment', true);
		$this->setParameter('less_file', null);
		$this->setParameter('css_file', null);

		// initialize parent
		parent::initialize($context, $parameters);

		return true;

	}

}

