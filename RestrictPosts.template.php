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

			foreach($context['restrict_posts']['board_info'] as $board_info)
			{
				echo '
				<fieldset style="width: 95%; margin: 0 auto; margin-bottom: 20px;">';

				echo '
				<legend class="global_perm_heading" id="'. $board_info['id_board']. '">' . $board_info['board_name'] . '</legend>';

				foreach ($board_info['groups_data'] as $key => $group)
				{
					//print_r($group);
					echo '
						<div style="width: 25%; float: left">';
					echo '
						<label for="' . $group['id_group'] . '">' . $group['group_name'] . '</label>';
		
					echo '
					</div>
					<input type="text" name="' . $board_info['id_board'] . '_posts_'.$group['id_group'].'" id="" value="', $group['max_posts_allowed'] ,'" class="input_text" placeholder="'. $txt['rp_max_posts'] .'" />';
					echo '
					<input type="text" name="' . $board_info['id_board'] . '_timespan_'.$group['id_group'].'" id="" value="', $group['timespan'] ,'" class="input_text" placeholder="'. $txt['rp_time_limit'] .'" /><br />';
				}
		
				echo '
				</fieldset>';
			}

				echo '
				<input type="submit" name="submit" value="', $txt['ts_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';

			echo '
			<span class="botslice"><span></span></span>
		</div>

	</form>
</div>
<br class="clear">';

}

?>