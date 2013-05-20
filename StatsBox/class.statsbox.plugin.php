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
	'Version' => '1.1',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'SettingsUrl' => '/settings/statsbox',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class StatsBoxPlugin extends Gdn_Plugin {

	public function DiscussionsController_Render_Before($Sender) {
		if(C('Plugins.StatsBox.DisableCSS', FALSE) == FALSE) {
			$Sender->AddCSSFile($this->GetResource('design/statsbox.css', FALSE, FALSE));
		}
	}

	public function DiscussionsController_DiscussionOptions_Handler($Sender) {
		$Session = Gdn::Session();
		$Discussion = GetValue('Discussion', $Sender->EventArguments);
		
		if (!is_numeric($Discussion->CountBookmarks)) {
			$Discussion->CountBookmarks = 0;
		}
		
		if(C('Plugins.StatsBox.HideComments', FALSE) == FALSE) {
			echo Wrap(
				Wrap(T('Comments')) . Gdn_Format::BigNumber($Discussion->CountComments - 1),
				'span',
				array('class' => 'StatsBox AnswersBox'));
		}
		
		if(C('Plugins.StatsBox.HideViews', FALSE) == FALSE) {
			echo Wrap(
				Wrap(T('Views')) . Gdn_Format::BigNumber($Discussion->CountViews),
				'span',
				array('class' => 'StatsBox ViewsBox'));
		}

		
		if(C('Plugins.StatsBox.HideFollows', FALSE) == FALSE) {
			$BookmarkAction = T($Discussion->Bookmarked == '1' ? 'Unbookmark' : 'Bookmark');
			if ($Session->IsValid()) {
				echo Wrap(
					Anchor(
						Wrap(T('Follows')) . Gdn_Format::BigNumber($Discussion->CountBookmarks),
						'/vanilla/discussion/bookmark/'.$Discussion->DiscussionID.'/'.$Session->TransientKey().'?Target='.urlencode($Sender->SelfUrl),
						'',
						array('title' => $BookmarkAction)
					),
				'span',
				array('class' => 'StatsBox FollowsBox'));
			}
			else {
				echo Wrap(
					Wrap(T('Follows')) . $Discussion->CountBookmarks,
					'span',
					array('class' => 'StatsBox FollowsBox'));
			}
		}
	}

	public function SettingsController_StatsBox_Create($Sender) {
		$Sender->Permission('Garden.Settings.Manage');

		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array(
			'Plugins.StatsBox.HideComments',
			'Plugins.StatsBox.HideViews',
			'Plugins.StatsBox.HideFollows',
			'Plugins.StatsBox.DisableCSS',
			));
		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
        	$Data = $Sender->Form->FormValues();
			if ($Sender->Form->Save() !== FALSE) {
        		$Sender->InformMessage('<span class="InformSprite Sliders"></span>'.T("Your changes have been saved."),'HasSprite');
			}
		}
		
		$Sender->AddSideMenu();
		$Sender->Render($this->GetView('settings.php'));
	}
	
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu = &$Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', 'Stats Box', 'settings/statsbox', 'Garden.Settings.Manage');
	}
	
	public function OnDisable() {}

	public function Setup() {}
}