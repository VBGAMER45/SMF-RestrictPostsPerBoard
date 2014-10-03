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

(function() {
	var restrictPosts = function() {};

	restrictPosts.prototype = function() {
		var parentElemRef = null,
			userSessionId = null,
			userSessionVar = null,
			tempData = {},

			init = function() {
				inputRef = restrictPosts.jQRef('#restrictPostsBoardSettings input');
			},

			saveBoardSettings = function(event, params) {
				event.preventDefault();

				resetVars();
				userSessionId = params.sessId;
				userSessionVar = params.sessVar;

				inputRef.each(function(index, elem) {
					var inputName = restrictPosts.jQRef(this).attr('name'),
						inputVal = restrictPosts.jQRef(this).val();

					inputName = inputName.split('_');

					if (inputName.length === 3) {
						if (!isNaN(inputName[0]) && !isNaN(inputName[2])) {
							// console.log(inputName + inputVal);
							console.log(inputVal);
						}
					}
				});
			},

			resetVars = function() {
				userSessionId = null;
				userSessionVar = null;
				tempData = {};
			},

			isNullUndefined = function(val) {
				var isNull = false,
					type = getType(val);

				switch (type) {
					case '[object array]':
						if (val.length === 0) {
							isNull = true;
						}
						break;

					case '[object object]':
						if (Object.keys(val).length === 0) {
							isNull = true;
						}
						break;

					default:
						if (typeof(val) === "undefined" || val === null || val === "" || val === "null" || val === "undefined") {
							isNull = true;
						}
				}
				return isNull;
			};

		return {
			'init': init,
			'saveBoardSettings': saveBoardSettings
		};
	}();

	this.restrictPosts = restrictPosts;
	if (typeof(restrictPosts.jQRef) !== 'function' && typeof(restrictPosts.jQRef) === 'undefined') {
		restrictPosts.jQRef = lp_jquery2_0_3;
	}

	this.restrictPosts.prototype.init();
})();
