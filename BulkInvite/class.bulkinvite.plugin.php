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
$PluginInfo['BulkInvite'] = array(
	'Title' => 'Bulk Invite',
	'Description' => 'A plugin in that provides an interface to invite users in bulk.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'RequiredTheme' => FALSE, 
	'RequiredPlugins' => FALSE,
	'SettingsUrl' => '/settings/bulkinvite',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => "Zachary Doll",
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class BulkInvite extends Gdn_Plugin {

	// add a Bulk Invite page on the settings controller
	public function SettingsController_BulkInvite_Create($Sender) {
		// add the admin side menu
		$Sender->AddSideMenu('settings/bulkinvite');
		
		$Sender->Title('Bulk Invite Settings');
		$Sender->Render($this->GetView("settings.php"));
	}
	
	public function PluginController_BulkInvite_Create($Sender) {
		// Makes it act like a mini controller
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}
	
	public function Controller_Index($Sender) {
		echo 'I do not do anything yet :(';
	}
	
	public function Base_Render_Before($Sender) {
		$this->_AddResources($Sender);
		// echo '<pre>'; var_dump($Sender); echo '</pre>';
	}
	
	private function _AddResources($Sender) {
		$Sender->AddJsFile($this->GetResource('js/bulkinvite.js', FALSE, FALSE));
		$Sender->AddCssFile($this->GetResource('design/bulkinvite.css', FALSE, FALSE));
	}
	
	public function Setup() {
		// SaveToConfig('Plugins.BulkInvite.EnableAdvancedMode', TRUE);
	}

	public function OnDisable() {
		// RemoveFromConfig('Plugins.BulkInvite.EnableAdvancedMode');
	}
}
