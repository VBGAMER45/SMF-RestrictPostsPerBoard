<?php

/**
* @package manifest file for Restrict Boards per post
* @version 1.0
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

if (!defined('SMF'))
	die('Hacking attempt...');

function RP_load_all_boards()
{
	global $smcFunc;
	
	$request = $smcFunc['db_query']('', '
		SELECT id_board, name, member_groups
		FROM {db_prefix}boards',
		array()
	);
	if ($smcFunc['db_num_rows']($request) == 0)
		return;

	$boards_info = array();
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$boards_info[$row['id_board']] = array(
			'id_board' => $row['id_board'],
			'board_name' => $row['name'],
			'member_groups' => !empty($row['member_groups']) ? explode(',', $row['member_groups']) : '',
		);
	}
	$smcFunc['db_free_result']($request);

	return $boards_info;
}

function RP_load_all_member_groups()
{
	global $smcFunc;

	$exclude_groups = array('1', '3');
	$request = $smcFunc['db_query']('', '
		SELECT id_group, group_name
		FROM {db_prefix}membergroups
		WHERE id_group NOT IN ({array_int:exclude_groups})',
		array(
			'exclude_groups' => $exclude_groups
		)
	);

	$groups_info = array();
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$groups_info[$row['id_group']] = array(
			'id_group' => $row['id_group'],
			'group_name' => $row['group_name']
		);
	}
	$smcFunc['db_free_result']($request);

	return $groups_info;
}

function RP_load_post_restrict_status()
{
	global $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT id_board, id_group, max_posts_allowed, timespan, is_collapsed
		FROM {db_prefix}restrict_posts',
		array()
	);

	$post_restrict_status = array();
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$post_restrict_status[] = array(
			'id_board' => $row['id_board'],
			'id_group' => $row['id_group'],
			'max_posts_allowed' => $row['max_posts_allowed'],
			'timespan' => $row['timespan'],
			'is_collapsed' => $row['is_collapsed']
		);
	}
	$smcFunc['db_free_result']($request);

	return $post_restrict_status;
}

?>