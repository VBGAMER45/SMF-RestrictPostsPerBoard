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

function RestrictPostsAdmin(&$admin_areas)
{
	global $txt, $modSettings, $context;

	loadLanguage('RestrictPosts');
	loadtemplate('RestrictPosts');

	$admin_areas['config']['areas']['restrictposts'] = array(
		'label' => $txt['RP_menu'],
		'file' => 'RestrictPosts.php',
		'function' => 'ModifyRestrictPostsSettings',
		'icon' => 'administration.gif',
		'subsections' => array(),
	);
}

function ModifyRestrictPostsSettings($return_config = false)
{
	global $txt, $scripturl, $context, $sourcedir;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	require_once($sourcedir . '/Subs-RestrictPosts.php');
	loadLanguage('RestrictPosts');
	loadtemplate('RestrictPosts');

	

	$context['page_title'] = $txt['RP_admin_panel'];

	// Load up the guns
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['RP_admin_panel'],
		'description' => $txt['RP_admin_panel_desc'],
		'tabs' => array(
			'basic' => array(),
		),
	);

	$subActions = array(
		'basic' => 'basicRestrictPostsSettings',
		'savesettings' => 'saveRestrictPostsSettings'
	);

	$default_action_func = 'basicRestrictPostsSettings';
	//wakey wakey, call the func you lazy
	foreach ($subActions as $key => $action)
	{
		if (isset($_REQUEST['sa']) && $_REQUEST['sa'] === $key)
		{
			if (function_exists($subActions[$key]))
			{
				return $subActions[$key]();
			}
		}
	}

	// At this point we can just do our default.
	$default_action_func();
}

/*
 *default/basic function
 */
function basicRestrictPostsSettings($return_config = false)
{
	global $txt, $scripturl, $context, $sourcedir, $user_info;

	/* I can has Adminz? */
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

	
	
	foreach($context['restrict_posts']['board_info'] as $board_key => $boards) {
		$context['restrict_posts']['board_info'][$board_key]['groups_restricted'] = '';
		foreach($context['restrict_posts']['status'] as $status_key => $status) {
			if($status['id_board'] === $boards['id_board']) {
				$context['restrict_posts']['board_info'][$board_key]['groups_restricted'][] = $status['id_group'];
				$context['restrict_posts']['board_info'][$board_key]['max_posts_allowed'][] = $status['max_posts_allowed'];
				$context['restrict_posts']['board_info'][$board_key]['timespan'] = $status['timespan'];
				$context['restrict_posts']['board_info'][$board_key]['is_collapsed'] = $status['is_collapsed'];
			} else {
				$context['restrict_posts']['board_info'][$board_key]['groups_restricted'] = '';
				$context['restrict_posts']['board_info'][$board_key]['max_posts_allowed'] = $status['max_posts_allowed'];
				$context['restrict_posts']['board_info'][$board_key]['timespan'] = $status['timespan'];
				$context['restrict_posts']['board_info'][$board_key]['is_collapsed'] = $status['is_collapsed'];
			}
		}
	}


	//print_r($context['restrict_posts']['status']);
	//echo '<br />';
	//print_r($context['restrict_posts']['board_info']);
	/*foreach($context['restrict_posts']['board_info'] as $board_key => $boards) {
		print_r($context['restrict_posts']['board_info'][$board_key]);
			echo '<br />';
	}
	die();*/

	$context['page_title'] = $txt['RP_admin_panel'];
	$context['sub_template'] = 'rp_admin_panel';
	$context['restrict_posts']['tab_name'] = $txt['rp_basic_tab'];
	$context['restrict_posts']['tab_desc'] = $txt['rp_basic_tab_desc'];
}

function saveRestrictPostsSettings() {
	global $context;

	/* I can has Adminz? */
	isAllowedTo('admin_forum');

	//echo 'we are in';
	print_r($_POST);
	die();
}

?>