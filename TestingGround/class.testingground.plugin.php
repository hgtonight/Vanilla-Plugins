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
$PluginInfo['TestingGround'] = array(
	'Title' => 'Testing Ground',
	'Description' => 'A skeleton plugin that adds its resources to every page, creates a settings page, and creates a stub minicontroller.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'RequiredTheme' => FALSE, 
	'RequiredPlugins' => FALSE,
	'SettingsUrl' => '/settings/testingground',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => "Zachary Doll",
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
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
