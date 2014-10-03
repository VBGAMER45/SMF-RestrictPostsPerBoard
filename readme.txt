[url=http://custom.simplemachines.org/mods/index.php?mod=3586]Link to Mod[/url]

This modification helps administrator to restrict each member group on per board basis by allowing them specific number of new topics or replies they can make in a time alloted by admin.

By default mod is disable and doesn't interfere with the current forum settings.

All mod settings are available under
Admin center -> Configuration -> Restrict Posts Per Board


To enable the mod go to following mentioned link and enable the mod
?action=admin;area=restrictposts;sa=generalsettings

A specific setting is available for calendar section also which restricts the user if user has crossed the limit of new topics he/she can made. Also the calendar setting works only if posting is restricted by topics.


To make settings for each membergroup go to
?action=admin;area=restrictposts;sa=postsettings

Here two coloumns are show respective to each membergroup
- Max Posts -> Enter the number of maximum new topics/replies you want a membergroup can make
- Time Limit -> Enter the time span in which you want a group can make specified posts.

For e.g. if you want the a group can make 50 new topics/replies in 2 days enter as following
- Max posts -> 50
- Time span -> 2

Note - If any of the 2 coloumns is left blank, the mod neglects the value of other column, i.e if you have entered value for 'Max posts' only but 'Time span' hasn't been filled. In that case mod neglects both values.


[i][b]Change Log[/b][/i]
[i]Version 1.2[/i]
- Fixed bug due to which mod was unable to save the data if there were too many boards, over [url=http://www.simplemachines.org/community/index.php?topic=494061.msg3739690#msg3739690]here[/url]
- Hooks ported to separate file


[i]Version 1.1[/i]
- Added an option to select restriction between new topic or replies
- Made several fixes
- Enhanced DB queries


[i]Version 1.0.1[/i]
- Fixed an issue as reported [url=http://www.simplemachines.org/community/index.php?topic=494061.msg3467029#msg3467029]here[/url]


GitHub Link - https://github.com/Joker-SMF/SMF-RestrictPostsPerBoard

[b]License[/b]
 * This SMF Modification is subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this SMF modification except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/