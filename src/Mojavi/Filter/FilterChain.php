<?php
namespace Mojavi\Filter;

use Mojavi\Core\MojaviObject as MojaviObject;

/**
 * FilterChain manages registered filters for a specific context.
 */
class FilterChain extends MojaviObject
{

	// +-----------------------------------------------------------------------+
	// | PRIVATE VARIABLES													 |
	// +-----------------------------------------------------------------------+

	private
		$chain = array(),
		$index = -1;

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute the next filter in this chain.
	 *
	 * @return void
	 */
	public function execute ()
	{

		// skip to the next filter
		$this->index++;

		if ($this->index < count($this->chain))
		{

			// execute the next filter
			$this->chain[$this->index]->execute($this);

		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Register a filter with this chain.
	 *
	 * @param Filter A Filter implementation instance.
	 *
	 * @return void
	 */
	public function register ($filter)
	{

		$this->chain[] = $filter;

	}

}

