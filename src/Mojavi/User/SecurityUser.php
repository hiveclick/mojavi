<?php
namespace Mojavi\User;

/**
 * SecurityUser provides advanced security manipulation methods.
 */
abstract class SecurityUser extends User
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Add a credential to this user.
	 *
	 * @param mixed Credential data.
	 *
	 * @return void
	 */
	abstract function addCredential ($credential);

	// -------------------------------------------------------------------------

	/**
	 * Clear all credentials associated with this user.
	 *
	 * @return void
	 */
	abstract function clearCredentials ();

	// -------------------------------------------------------------------------

	/**
	 * Indicates whether or not this user has a credential.
	 *
	 * @param mixed Credential data.
	 *
	 * @return bool true, if this user has the credential, otherwise false.
	 */
	abstract function hasCredential ($credential);

	// -------------------------------------------------------------------------

	/**
	 * Indicates whether or not this user is authenticated.
	 *
	 * @return bool true, if this user is authenticated, otherwise false.
	 */
	abstract function isAuthenticated ();

	// -------------------------------------------------------------------------

	/**
	 * Remove a credential from this user.
	 *
	 * @param mixed Credential data.
	 *
	 * @return void
	 */
	abstract function removeCredential ($credential);

	// -------------------------------------------------------------------------

	/**
	 * Set the authenticated status of this user.
	 *
	 * @param bool A flag indicating the authenticated status of this user.
	 *
	 * @return void
	 */
	abstract function setAuthenticated ($authenticated);

}

