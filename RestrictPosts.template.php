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

function template_rp_admin_panel()
{
	global $context, $txt, $scripturl;

	echo '
<div class="cat_bar">
	<h3 class="catbg">
		<span class="ie6_header floatleft">', $txt['RP_admin_panel'] ,'</span>
	</h3>
</div>
<p class="windowbg description">', $txt['RP_admin_panel_desc'] ,'</p>';

echo '
<div id="admincenter">
	<form action="'. $scripturl .'?action=admin;area=restrictposts;sa=savesettings" method="post" accept-charset="UTF-8">
		
		<div class="windowbg2">
			<span class="topslice"><span></span></span>';
			echo '
<form action="admin=main" method="post">';

	foreach($context['restrict_posts']['board_info'] as $board_info)
	{
		echo ' <fieldset style="width: 95%; margin: 0 auto; margin-bottom: 20px;">';
		echo '<legend class="global_perm_heading" id="'. $board_info['id_board']. '">' . $board_info['board_name'] . '</legend>';

		foreach ($context['restrict_posts']['groups'] as $group)
		{
			$group_exist = false;
			$post_count = 0;
			$timespan = 0;
			if(!empty($board_info['groups_restricted']) && in_array($group['id_group'], $board_info['groups_restricted'])) {
				$group_exist = true;
				$post_count = $board_info['max_posts_allowed'][0];
				$timespan = $board_info['timespan'][0];
				array_shift($board_info['max_posts_allowed']);
				array_shift($board_info['timespan']);
			}

			echo '
			<div style="width: 25%; float: left">';
				//echo '<input' . ($group_exist ? ' checked="checked"' : '') . ' id="' . $group['id_group'] . '" type="checkbox" name="' . $board_info['id_board'] . '_groups['.$group['id_group'].']" value="" />';
				echo '<label for="' . $group['id_group'] . '">' . $group['group_name'] . '</label>
			</div>
			<input type="text" name="' . $board_info['id_board'] . '_posts_'.$group['id_group'].'" id="" value="', $post_count ,'" class="input_text">';
			echo '<input type="text" name="' . $board_info['id_board'] . '_timespan_'.$group['id_group'].'" id="" value="', $timespan ,'" class="input_text"><br />';
		}

		echo ' </fieldset>';
	}

	echo '
	<input type="submit" name="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';

echo '
</form>';
			echo '<span class="botslice"><span></span></span>
		</div>

	</form>
</div>
<br class="clear">';
}

?>