<?php if (!defined('APPLICATION')) exit();
/*	Copyright 2013 Zachary Doll
*	This program is free software: you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
$PluginInfo['StatsBox'] = array(
	'Name' => 'Stats Box',
	'Description' => 'Adds a stats box to the discussions list that shows the total comments, views, and follows. Inspired on Voting by Mark O\'Sullivan.',
	'Version' => '1.0',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'License' => 'GPLv3'
);

class StatsBoxPlugin extends Gdn_Plugin {

	public function DiscussionsController_Render_Before($Sender) {
		$Sender->AddCSSFile($this->GetResource('design/statsbox.css', FALSE, FALSE));
	}

	public function DiscussionsController_BeforeDiscussionContent_Handler($Sender) {
		$Session = Gdn::Session();
		$Discussion = GetValue('Discussion', $Sender->EventArguments);
		
		if (!is_numeric($Discussion->CountBookmarks)) {
			$Discussion->CountBookmarks = 0;
		}
		
		echo Wrap(
			Wrap(T('Comments')) . Gdn_Format::BigNumber($Discussion->CountComments - 1),
			'div',
			array('class' => 'StatsBox AnswersBox'));
		echo Wrap(
			Wrap(T('Views')) . Gdn_Format::BigNumber($Discussion->CountViews),
			'div',
			array('class' => 'StatsBox ViewsBox'));

		$BookmarkAction = T($Discussion->Bookmarked == '1' ? 'Unbookmark' : 'Bookmark');
		if ($Session->IsValid()) {
			echo Wrap(
				Anchor(
					Wrap(T('Follows')) . Gdn_Format::BigNumber($Discussion->CountBookmarks),
					'/vanilla/discussion/bookmark/'.$Discussion->DiscussionID.'/'.$Session->TransientKey().'?Target='.urlencode($Sender->SelfUrl),
					'',
					array('title' => $BookmarkAction)
				),
			'div',
			array('class' => 'StatsBox FollowsBox'));
		}
		else {
			echo Wrap(
				Wrap(T('Follows')) . $Discussion->CountBookmarks,
				'div',
				array('class' => 'StatBox FollowsBox'));
		}
	}

	public function OnDisable() {}

	public function Setup() {}
}