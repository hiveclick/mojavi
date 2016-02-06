<?php
namespace Mojavi\Util;

/**
 * ParameterHolder provides a base class for managing parameters.
 */
abstract class ParameterHolder
{

	// +-----------------------------------------------------------------------+
	// | PROTECTED DATA														|
	// +-----------------------------------------------------------------------+

	protected
		$parameters = array();

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Clear all parameters associated with this request.
	 *
	 * @return void
	 */
	public function clearParameters ()
	{
		$this->parameters = null;
		$this->parameters = array();
	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve a parameter.
	 *
	 * @param string A parameter name.
	 * @param mixed  A default parameter value.
	 *
	 * @return mixed A parameter value, if the parameter exists, otherwise
	 *			   null.
	 */
	public function & getParameter ($name, $default = null)
	{
		$retval =& $default;
		if (isset($this->parameters[$name]))
		{
			$retval = $this->parameters[$name];
		}
		return $retval;
	}

	// -------------------------------------------------------------------------

	/**
	 * Retrieve an array of parameter names.
	 *
	 * @return array An indexed array of parameter names.
	 */
	public function getParameterNames ()
	{
		return array_keys($this->parameters);
	}
	
	// -------------------------------------------------------------------------

	/**
	 * Retrieve an array of parameters.
	 *
	 * @return array An associative array of parameters.
	 */
	public function & getParameters ()
	{
		return $this->parameters;
	}

	// -------------------------------------------------------------------------

	/**
	 * Indicates whether or not a parameter exists.
	 *
	 * @param string A parameter name.
	 *
	 * @return bool true, if the parameter exists, otherwise false.
	 */
	public function hasParameter ($name)
	{
		return isset($this->parameters[$name]);
	}

	// -------------------------------------------------------------------------

	/**
	 * Remove a parameter.
	 *
	 * @param string A parameter name.
	 *
	 * @return string A parameter value, if the parameter was removed,
	 *				otherwise null.
	 */
	public function & removeParameter ($name)
	{
		$retval = null;
		if (isset($this->parameters[$name]))
		{
			$retval =& $this->parameters[$name];
			unset($this->parameters[$name]);
		}
		return $retval;
	}

	// -------------------------------------------------------------------------

	/**
	 * Set a parameter.
	 *
	 * If a parameter with the name already exists the value will be overridden.
	 *
	 * @param string A parameter name.
	 * @param mixed  A parameter value.
	 *
	 * @return void
	 */
	public function setParameter ($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	// -------------------------------------------------------------------------

	/**
	 * Set a parameter by reference.
	 *
	 * If a parameter with the name already exists the value will be
	 * overridden.
	 *
	 * @param string A parameter name.
	 * @param mixed  A reference to a parameter value.
	 *
	 * @return void
	 */
	public function setParameterByRef ($name, $value)
	{
		$this->parameters[$name] =& $value;
	}

	// -------------------------------------------------------------------------

	/**
	 * Set an array of parameters.
	 *
	 * If an existing parameter name matches any of the keys in the supplied
	 * array, the associated value will be overridden.
	 *
	 * @param array An associative array of parameters and their associated
	 *			  values.
	 *
	 * @return void
	 */
	public function setParameters ($parameters)
	{
		$this->parameters = array_merge($this->parameters, $parameters);
	}

	// -------------------------------------------------------------------------

	/**
	 * Set an array of parameters by reference.
	 *
	 * If an existing parameter name matches any of the keys in the supplied
	 * array, the associated value will be overridden.
	 *
	 * @param array An associative array of parameters and references to their
	 *			  associated values.
	 *
	 * @return void
	 */
	public function setParametersByRef (&$parameters)
	{
		foreach ($parameters as $key => &$value)
		{
			$this->parameters[$key] =& $value;
		}
	}
}



