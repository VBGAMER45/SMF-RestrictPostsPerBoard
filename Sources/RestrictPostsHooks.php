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

/*
 * A generic function to load JS and css related to mod
*/
function RC_includeAssets() {
	global $settings, $context;

	loadlanguage('RestrictPosts');
	$context['insert_after_template'] .= '
	<script type="text/javascript"><!-- // --><![CDATA[
		var rcLoaded = false,
		inConflict = false;

		function compareJQueryVersion(v1, v2, callback) {
			var v1parts = v1.split('.');
			var v2parts = v2.split('.');

			for (var i = 0; i < v1parts.length; ++i) {
				if (v2parts.length == i) {
					//v1 + " is larger"
					callback(1);
					return;
				}

				if (v1parts[i] == v2parts[i]) {
					continue;
				} else if (v1parts[i] > v2parts[i]) {
					//v1 + " is larger";
					callback(1);
					return;
				} else {
					//v2 + " is larger";
					callback(2);
					return;
				}
			}

			if (v1parts.length != v2parts.length) {
				//v2 + " is larger";
				callback(2);
				return;
			}
			callback(false);
			return;
		}

		function loadJquery(url, callback) {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = url;

			var head = document.getElementsByTagName("head")[0],
				done = false;

			script.onload = script.onreadystatechange = function() {
				if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
					done = true;
					callback();
					script.onload = script.onreadystatechange = null;
					head.removeChild(script);
				};
			};
			head.appendChild(script);
		}

		// Only do anything if jQuery isn"t defined
		if (typeof(jQuery) == "undefined") {
			console.log("jquery not found");
			if (typeof($) == "function") {
				console.log("jquery but in conflict");
				inConflict = true;
			}

			loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
				if (typeof(jQuery) !=="undefined") {
					console.log("directly loaded with version: " + jQuery.fn.jquery);
					lp_jquery2_0_3 = jQuery.noConflict(true);
					loadModScript();
				}
			});
		} else {
			// jQuery is already loaded
			console.log("jquery is already loaded with version: " + jQuery.fn.jquery);
			compareJQueryVersion(jQuery.fn.jquery, "2.0.3", function(result) {
				console.log("result of version check: " + result)
				switch(result) {
					case false:
					case 1:
						lp_jquery2_0_3 = jQuery.noConflict(true);
						loadModScript();
						break;

					case 2:
						loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
							if (typeof(jQuery) !=="undefined") {
								console.log("after version check loaded with version: " + jQuery.fn.jquery);
								lp_jquery2_0_3 = jQuery.noConflict(true);
								loadModScript();
							}
						});
						break;

					default:
						loadJquery("http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js", function() {
							if (typeof(jQuery) !=="undefined") {
								console.log("default version check loaded with version: " + jQuery.fn.jquery);
								lp_jquery2_0_3 = jQuery.noConflict(true);
								loadModScript();
							}
						});
						break;
				}
			})
		};

		function loadModScript() {
			var js = document.createElement("script");
			js.type = "text/javascript";
			js.src = "' . $settings['default_theme_url'] . '/scripts/RestrictPosts.js";
			document.body.appendChild(js);
		}
	// ]]></script>';

	LP_checkJsonEncode();
}

function LP_checkJsonEncode() {
	if (!function_exists('json_encode')) {
		function json_encode($a = false) {

			switch(gettype($a)) {
				case 'integer':
				case 'double':
					return floatval(str_replace(",", ".", strval($a)));
				break;

				case 'NULL':
				case 'resource':
				case 'unknown':
					return 'null';
				break;

				case 'boolean':
					return $a ? 'true' : 'false' ;
				break;

				case 'array':
				case 'object':
					$output = array();
					$isAssoc = false;

					foreach(array_keys($a) as $key) {
						if (!is_int($key)) {
							$isAssoc = true;
							break;
						}
					}

					if($isAssoc) {
						foreach($a as $k => $val) {
							$output []= json_encode($k) . ':' . json_encode($val);
						}
						$output = '{' . implode(',', $output) . '}';
					} else {
						foreach($a as $val){
							$output []= json_encode($val);
						}
						$output = '[' . implode(',', $output) . ']';
					}
					return $output;
				break;

				default:
				return '"' . addslashes($a) . '"';
			}
		}
	}
}

?>
