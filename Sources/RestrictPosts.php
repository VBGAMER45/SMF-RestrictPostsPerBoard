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

function RestrictPostsAdmin(&$admin_areas) {
	global $txt;

	loadLanguage('RestrictPosts');
	loadtemplate('RestrictPosts');

	$admin_areas['config']['areas']['restrictposts'] = array(
		'label' => $txt['rp_menu'],
		'file' => 'RestrictPosts.php',
		'function' => 'ModifyRestrictPostsSettings',
		'icon' => 'administration.gif',
		'subsections' => array(),
	);
}

function ModifyRestrictPostsSettings($return_config = false) {
	global $txt, $context, $sourcedir;

	isAllowedTo('admin_forum');

	require_once($sourcedir . '/Subs-RestrictPosts.php');
	loadLanguage('RestrictPosts');
	loadtemplate('RestrictPosts');

	$context['page_title'] = $txt['rp_admin_panel'];
	$default_action_func = 'generalRestrictPostsSettings';

	// Load up the guns
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['rp_admin_panel'],
		'tabs' => array(
			'generalsettings' => array(
				'label' => $txt['rp_general_settings'],
				'url' => 'generalsettings',
			),
			'postsettings' => array(
				'label' => $txt['rp_post_settings'],
				'url' => 'postsettings',
			),
		),
	);
	$context[$context['admin_menu_name']]['tab_data']['active_button'] = isset($_REQUEST['sa']) ? $_REQUEST['sa'] : 'generalsettings';

	$subActions = array(
		'generalsettings' => 'generalRestrictPostsSettings',
		'savegeneralsettings' => 'saveRestrictGeneralSettings',
		'postsettings' => 'basicRestrictPostsSettings',
		'savepostsettings' => 'saveRestrictPostsSettings',
	);

	//wakey wakey, call the func you lazy
	foreach ($subActions as $key => $action) {
		if (isset($_REQUEST['sa']) && $_REQUEST['sa'] === $key) {
			if (function_exists($subActions[$key])) {
				return $subActions[$key]();
			}
		}
	}

	// At this point we can just do our default.
	$default_action_func();
}

function generalRestrictPostsSettings() {
	global $context, $txt, $sourcedir;

	require_once($sourcedir . '/ManageServer.php');
	$general_settings = array(
		array('check', 'rp_mod_enable', 'subtext' => $txt['rp_enable_disable_mod']),
		array('check', 'rp_mod_enable_calendar', 'subtext' => $txt['rp_enable_disable_calendar']),
		array('select', 'rp_restrict_method', array(
			'topics' => $txt['rp_restrict_by_topics'],
			'replies' => $txt['rp_restrict_by_replies'],
		)),
	);

	$context['page_title'] = $txt['rp_admin_panel'];
	$context['sub_template'] = 'rp_admin_general_setting_panel';
	$context['restrict_posts']['tab_name'] = $txt['rp_general_settings'];
	$context['restrict_posts']['tab_desc'] = $txt['rp_general_settings_desc'];
	prepareDBSettingContext($general_settings);
}

function saveRestrictGeneralSettings() {
	global $sourcedir, $txt;

	if (isset($_POST['submit'])) {
		checkSession();

		$general_settings = array(
			array('check', 'rp_mod_enable'),
			array('check', 'rp_mod_enable_calendar'),
			array('select', 'rp_restrict_method', array(
				'topics' => $txt['rp_restrict_by_topics'],
				'replies' => $txt['rp_restrict_by_replies'],
			)),
		);

		require_once($sourcedir . '/ManageServer.php');
		saveDBSettings($general_settings);
		redirectexit('action=admin;area=restrictposts;sa=generalsettings');
	}
}

function basicRestrictPostsSettings($return_config = false) {
	global $txt, $context, $sourcedir;

	isAllowedTo('admin_forum');

	loadLanguage('RestrictPosts');
	loadtemplate('RestrictPosts');
	
	require_once($sourcedir . '/Subs-Membergroups.php');
	$context['restrict_posts']['groups'][-1] = array(
		'id_group' => -1,
		'group_name' => $txt['membergroups_guests'],
	);
	$context['restrict_posts']['groups'][0] = array(
		'id_group' => 0,
		'group_name' => $txt['membergroups_members'],
	);
	$context['restrict_posts']['groups'] += RP_load_all_member_groups();
	$context['restrict_posts']['board_info'] = RP_load_all_boards();
	$context['restrict_posts']['status'] = RP_load_post_restrict_status();


	foreach ($context['restrict_posts']['board_info'] as $board_key => $boards) {
		foreach ($context['restrict_posts']['groups'] as $groups_key => $groups) {
			if (in_array($groups['id_group'], $boards['member_groups'])) {
				$context['restrict_posts']['board_info'][$board_key]['groups_data'][$boards['id_board'] . '_' .$groups['id_group']] = array(
					'id_group' => $groups['id_group'],
					'group_name' => $groups['group_name'],
					'max_posts_allowed' => '',
					'timespan' => ''
				);
			}
		}

		foreach ($context['restrict_posts']['status'] as $status_key => $status) {
			if ($status['id_board'] === $boards['id_board'] && isset($context['restrict_posts']['board_info'][$board_key]['groups_data']) && isset($context['restrict_posts']['board_info'][$board_key]['groups_data'][$boards['id_board'] . '_' . $status['id_group']])) {
					$context['restrict_posts']['board_info'][$board_key]['groups_data'][$boards['id_board'] . '_' .$status['id_group']]['max_posts_allowed'] = $status['max_posts_allowed'];
					$context['restrict_posts']['board_info'][$board_key]['groups_data'][$boards['id_board'] . '_' .$status['id_group']]['timespan'] = $status['timespan'];
			}
		}
		unset($context['restrict_posts']['board_info'][$board_key]['member_groups']);
	}

	$context['page_title'] = $txt['rp_admin_panel'];
	$context['sub_template'] = 'rp_admin_post_setting_panel';
	$context['restrict_posts']['tab_name'] = $txt['rp_post_settings'];
	$context['restrict_posts']['tab_desc'] = $txt['rp_basic_post_settings_desc'];
}

function saveRestrictPostsSettings() {
	global $context;

	isAllowedTo('admin_forum');

	$data = array();
	unset($_POST['submit']);
	foreach ($_POST as $key => $value) {
		
		//just boom the data, let them get happy
		$temp_data = explode('_', $key);

		//if i found something fishy, you are going back
		if (!is_numeric($temp_data[0]) || !is_numeric($temp_data[2])) {
			return false;
		}

		$id_board = (int) $temp_data[0];
		$id_group = (int) $temp_data[2];

		if ($temp_data[1] === 'posts') {
			if (isset($data[$id_board . '_' . $id_group])) {
				$data[$id_board . '_' . $id_group]['max_posts_allowed'] = (int) $value;
			} else {
				$data[$id_board . '_' . $id_group] = array(
					'id_board' => $id_board,
					'id_group' => $id_group,
					'max_posts_allowed' => (int) $value,
				);
			}
		} else if ($temp_data[1] === 'timespan') {
			if (isset($data[$id_board . '_' . $id_group])) {
				$data[$id_board . '_' . $id_group]['timespan'] = (int) $value;
			} else {
				$data[$id_board . '_' . $id_group] = array(
					'id_board' => $id_board,
					'id_group' => $id_group,
					'timespan' => (int) $value,
				);
			}
		}
	}

	//Lets clear the junk values
	$context['restrict_posts_db_data'] = RP_sanitizeRestrictDBData($data);

	if (empty($context['restrict_posts_db_data'])) {
		// They might want to clear some data
		RP_clear_restrict_data();
		redirectexit('action=admin;area=restrictposts;sa=postsettings');
	} else {
		RP_add_restrict_data($context['restrict_posts_db_data']);
		redirectexit('action=admin;area=restrictposts;sa=postsettings');
	}
}

function RP_sanitizeRestrictDBData ($data = array()) {	
	if (!is_array($data)) {
		$data = array($data);
	}

	foreach ($data as $key => $val) {
		if (empty($val['max_posts_allowed']) || empty($val['timespan'])) {
			unset($data[$key]);
		}
	}
	return $data;
}

function RP_isAllowedToPost() {
	global $context, $user_info, $sourcedir, $modSettings;

	require_once($sourcedir . '/Subs-RestrictPosts.php');
	if (!isset($context['current_board'])) {
		return false;
	}

	if ($user_info['is_admin']) {
		return true;
	}

	if($modSettings['rp_restrict_method'] === 'topics') {
		$rp_is_allowed = RP_DB_isAllowedToPostTopics();
	} else {
		$rp_is_allowed = RP_DB_isAllowedToPostReplies();
	}
	return $rp_is_allowed;
}

function RP_isAllowedToPostEvents() {
	global $user_info, $sourcedir;

	require_once($sourcedir . '/Subs-RestrictPosts.php');

	if ($user_info['is_admin']) {
		return true;
	}

	$rp_is_allowed = RP_DB_isAllowedToPostEvents();
	return $rp_is_allowed;
}

?>
