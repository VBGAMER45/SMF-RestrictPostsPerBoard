<?php

/**
* @package manifest file for Restrict Boards per post
* @version 1.0.1
* @author Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* @copyright Copyright (c) 2012, Siddhartha Gupta
* @license http://www.mozilla.org/MPL/MPL-1.1.html
*/

/*
* Version: MPL 1.1
*
* The contents of this file are subject to the Mozilla Public License Version
* 1.1 (the "License"); you may not use this file except in compliance with
* the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS" basis,
* WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
* for the specific language governing rights and limitations under the
* License.
*
* The Initial Developer of the Original Code is
*  Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* Portions created by the Initial Developer are Copyright (C) 2012
* the Initial Developer. All Rights Reserved.
*
* Contributor(s):
*
*/

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $sourcedir, $db_prefix;

if (!array_key_exists('db_add_column', $smcFunc))
db_extend('packages');


$table = array(
	'table_name' => 'restrict_posts',
	'columns' => array(
		array(
			'name' => 'id_board',
			'type' => 'smallint',
			'unsigned' => true,
			'size' => 5,
			'null' => false,
		),
		array(
			'name' => 'id_group',
			'type' => 'smallint',
			'size' => 5,
			'null' => false,
		),
		array(
			'name' => 'max_posts_allowed',
			'type' => 'int',
			'unsigned' => true,
			'size' => 10,
			'default' => '0',
		),
		array(
			'name' => 'timespan',
			'type' => 'int',
			'size' => 10,
			'unsigned' => true,
			'null' => false,
			'default' => '1',
		),
	),
	'indexes' => array(),
);
$smcFunc['db_create_table']('{db_prefix}' . $table['table_name'], $table['columns'], $table['indexes']);

// For all general settings add 'rp_mod_' as prefix
$general_settings = array(
	'rp_mod_enable' => 0, // Disable by default
    'rp_mod_enable_calendar' => 0, // Disbale by default on calendar
);

foreach ($general_settings as $key => $value) {
    $smcFunc['db_insert']('ignore',
        '{db_prefix}settings', array('variable' => 'string', 'value' => 'string'),
        array($key, $value), ''
    );
}

//add_integration_function('integrate_pre_include', '$sourcedir/RestrictPosts.php');
add_integration_function('integrate_pre_include', $sourcedir . '/RestrictPosts.php', true);
add_integration_function('integrate_admin_areas', 'RestrictPostsAdmin');

if (SMF == 'SSI')
echo 'Database adaptation successful!';

?>