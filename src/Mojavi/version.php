<?php

/**
 * Version initialization script.
 */

define('MO_APP_NAME',		  'Mojavi');

define('MO_APP_MAJOR_VERSION', '3');

define('MO_APP_MINOR_VERSION', '0');

define('MO_APP_MICRO_VERSION', '0');

define('MO_APP_BRANCH',		'3.0.0');

define('MO_APP_STATUS',		'PROD');

define('MO_APP_VERSION',	   MO_APP_MAJOR_VERSION . '.' .
							   MO_APP_MINOR_VERSION . '.' .
							   MO_APP_MICRO_VERSION . '-' . MO_APP_STATUS);

define('MO_APP_URL',		   'http://www.mojavi.org');

define('MO_APP_INFO',		  MO_APP_NAME . ' ' . MO_APP_VERSION .
							   ' (' . MO_APP_URL . ')');

