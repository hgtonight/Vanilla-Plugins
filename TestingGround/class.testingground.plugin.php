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
$PluginInfo['TestingGround'] = array( // You put whatever you want to call your plugin folder as the key
	'Name' => 'Testing Ground', // User friendly name, this is what will show up on the garden plugins page
	'Description' => 'A skeleton plugin that adds its resources to every page, creates a settings page, and creates a stub minicontroller.', // This is also shown on the garden plugins page. Will be used as the first line of the description if uploaded to the official addons repository at vanillaforums.org/addons
	'Version' => '0.1', // Anything can go here, but it is suggested that you use some type of naming convention; will appear on the garden vanilla plugins page
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'), // Can require multiple applications (e.g. Vanilla and Conversations)
	'RequiredTheme' => FALSE, // Any prerequisite themes
	'RequiredPlugins' => FALSE, // Any prerequisite plugins
	'SettingsUrl' => '/settings/testingground', // A settings button linked to this URL will show up on the garden plugins page when enabled
	'SettingsPermission' => 'Garden.Settings.Manage', // The permissions required to visit the settings page. Garden.Settings.Manage is suggested.
	'Author' => 'Zachary Doll', // This will appear in the garden plugins page
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3' // Specify your license to prevent ambiguity
);

class TestingGround extends Gdn_Plugin {

	// add a Testing Ground page on the settings controller
	public function SettingsController_TestingGround_Create($Sender) {
		// add the admin side menu
		$Sender->AddSideMenu('settings/testingground');
		
		$Sender->Title('Testing Ground Settings');
		$Sender->Render($this->GetView("settings.php"));
	}
	
	public function PluginController_TestingGround_Create($Sender) {
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
		$Sender->AddJsFile($this->GetResource('js/testingground.js', FALSE, FALSE));
		$Sender->AddCssFile($this->GetResource('design/testingground.css', FALSE, FALSE));
	}
	
	public function Setup() {
		// SaveToConfig('Plugins.TestingGround.EnableAdvancedMode', TRUE);
	}

	public function OnDisable() {
		// RemoveFromConfig('Plugins.TestingGround.EnableAdvancedMode');
	}
}
