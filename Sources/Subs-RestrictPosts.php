<?php

/**
* @package manifest file for Restrict Boards per post
* @version 1.1
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

function RP_load_all_boards() {
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
			'member_groups' => !empty($row['member_groups']) ? explode(',', $row['member_groups']) : array(),
		);
	}
	$smcFunc['db_free_result']($request);

	return $boards_info;
}

function RP_load_all_member_groups() {
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

function RP_load_post_restrict_status() {
	global $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT id_board, id_group, max_posts_allowed, timespan
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
		);
	}
	$smcFunc['db_free_result']($request);

	return $post_restrict_status;
}

function RP_add_restrict_data($data = array()) {
	global $smcFunc;

	//not possible, if it still happens, go back
	if (!is_array($data)) {
		return false;
	}

	foreach ($data as $val) {
		$smcFunc['db_insert']('',
			'{db_prefix}restrict_posts',
			array(
				'id_board' => 'int', 'id_group' => 'int', 'max_posts_allowed' => 'int', 'timespan' => 'int',
			),
			array(
				(int) $val['id_board'], (int) $val['id_group'], (int) $val['max_posts_allowed'], (int) $val['timespan'],
			),
			array()
		);
	}
	return true;
}

function RP_DB_getRestrictParams() {
	global $smcFunc, $context, $user_info;

	$restrictParams = array(
		'max_posts_allowed' => 0,
		'timespan' => 0
	);

	$request = $smcFunc['db_query']('', '
		SELECT MIN(max_posts_allowed) as max_posts_allowed, MIN(timespan) as timespan
		FROM {db_prefix}restrict_posts
		WHERE id_board = {int:id_board}
		AND id_group IN ({array_int:id_group})',
		array(
			'id_board' => $context['current_board'],
			'id_group' => $user_info['groups']
		)
	);

	if ($smcFunc['db_num_rows']($request) == 0) {
		return $restrictParams;
	}

	//another cool method strtotime("-5 day");
	//time() - 86400 * $row['timespan'];
	list ($restrictParams['max_posts_allowed'], $restrictParams['timespan']) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	return $restrictParams;
}

function RP_DB_isAllowedToPostTopics() {
	global $smcFunc, $context, $user_info;

	$restrictParams = RP_DB_getRestrictParams();

	if(empty($restrictParams['max_posts_allowed'])) {
		return true;
	}

	$timespan = time() - 86400 * $restrictParams['timespan'];

	$request = $smcFunc['db_query']('', '
		SELECT COUNT(t.id_topic)
		FROM {db_prefix}topics as t
		INNER JOIN {db_prefix}messages as m on (m.id_msg = t.id_first_msg)
		WHERE t.id_member_started = {int:id_member}
		AND t.id_board = {int:id_board}
		AND m.poster_time > {int:poster_time}',
		array(
			'id_member' => $user_info['id'],
			'poster_time' => $timespan,
			'id_board' => $context['current_board'],
		)
	);
	list ($count) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	//echo 'before count';
	if (!empty($count) && $count >= $restrictParams['max_posts_allowed']) {
		return false;
	} else {
		return true;
	}
}

function RP_DB_isAllowedToPostReplies() {
	global $smcFunc, $context, $user_info;

	$restrictParams = RP_DB_getRestrictParams();

	if(empty($restrictParams['max_posts_allowed'])) {
		return true;
	}

	$timespan = time() - 86400 * $restrictParams['timespan'];

	$request = $smcFunc['db_query']('', '
		SELECT COUNT(m.id_msg)
		FROM {db_prefix}messages as m
		LEFT JOIN {db_prefix}topics as t on (m.id_topic = t.id_topic)
		WHERE m.id_member = {int:id_member}
		AND m.id_board = {int:id_board}
		AND m.poster_time > {int:poster_time}
		AND m.id_msg != t.id_first_msg',
		array(
			'id_member' => $user_info['id'],
			'poster_time' => $timespan,
			'id_board' => $context['current_board'],
		)
	);
	list ($count) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	//echo 'before count';
	if (!empty($count) && $count >= $restrictParams['max_posts_allowed']) {
		return false;
	} else {
		return true;
	}
}

function RP_DB_isAllowedToPostEvents() {
	global $smcFunc, $context, $user_info;

	$boards_to_exclude = array();
	$request = $smcFunc['db_query']('', '
		SELECT id_board, MAX(max_posts_allowed) as max_posts_allowed, MAX(timespan) as timespan
		FROM {db_prefix}restrict_posts
		WHERE id_group IN ({array_int:id_group})
		GROUP BY id_board',
		array(
			'id_group' => $user_info['groups']
		)
	);

	if ($smcFunc['db_num_rows']($request) == 0) {
		return $boards_to_exclude;
	}
	$temp_boards_to_exclude = array();

	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$temp_boards_to_exclude[$row['id_board']] = array(
			'max_posts_allowed' => $row['max_posts_allowed'],
			'timespan' => time() - 86400 * $row['timespan'],
		);
	}
	$smcFunc['db_free_result']($request);

	foreach ($temp_boards_to_exclude as $key => $val) {
		$request = $smcFunc['db_query']('', '
			SELECT COUNT(t.id_topic)
			FROM {db_prefix}topics as t
			INNER JOIN {db_prefix}messages as m on (m.id_msg = t.id_first_msg)
			WHERE t.id_member_started = {int:id_member}
			AND t.id_board = {int:id_board}
			AND m.poster_time > {int:poster_time}',
			array(
				'id_member' => $user_info['id'],
				'poster_time' => $val['timespan'],
				'id_board' => $key,
			)
		);
		list ($count) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		if (!empty($count) && $count >= $val['max_posts_allowed']) {
			$boards_to_exclude[] = $key;
		}
	}
	unset($temp_boards_to_exclude);
	return $boards_to_exclude;
}

?>
