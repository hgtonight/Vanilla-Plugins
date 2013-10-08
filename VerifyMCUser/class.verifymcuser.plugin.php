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
$PluginInfo['VerifyMCUser'] = array(
	'Name' => 'Verify Minecraft Username',
	'Description' => 'Verifies usernames are premium minecraft usernames during registration. A special thanks to gabessdsp at vanillaforums.org for sponsoring this plugin.',
	'Version' => '1.1',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'MobileFriendly' => TRUE,
	'HasLocale' => FALSE,
	'RegisterPermissions' => FALSE,
    'SettingsUrl' => '/settings/verifymcuser',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => 'Zachary Doll',
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class VerifyMCUser extends Gdn_Plugin {

	public function SettingsController_VerifyMCUser_Create($Sender) {
		$Sender->AddSideMenu('settings/verifymcusername');
		$this->_AddResources($Sender);
		$Sender->Title($this->GetPluginName() . ' ' . T('Settings'));
		$Sender->Render($this->GetView('settings.php'));
	}
	
	public function PluginController_VerifyMCUser_Create($Sender) {
      $Username = filter_var($Sender->RequestArgs[0], FILTER_SANITIZE_ENCODED);
      echo file_get_contents('http://minecraft.net/haspaid.jsp?user=' . $Username);
	}
	
    public function EntryController_Register_Handler($Sender) {
      $this->_AddResources($Sender);
    }
    
    public function EntryController_RegisterValidation_Handler($Sender) {
      $FormPostValues = $Sender->Request->Post();
      $Username = $FormPostValues['Name'];
      if(file_get_contents('http://minecraft.net/haspaid.jsp?user=' . $Username) == 'false') {
        $Sender->Form->AddError('Please enter a valid Minecraft username.');
        $Sender->Render();
        exit();
      }
    }
    
	private function _AddResources($Sender) {
		$Sender->AddJsFile($this->GetResource('js/verifymcuser.js', FALSE, FALSE));
		$Sender->AddCssFile($this->GetResource('design/verifymcuser.css', FALSE, FALSE));
	}
	
	public function Setup() {
		return TRUE;
	}
}
