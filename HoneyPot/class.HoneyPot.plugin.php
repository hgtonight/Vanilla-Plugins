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
$PluginInfo['HoneyPot'] = array(
   'Description' => 'Adds protection from bots registering while being transparent to actual users.',
   'RequiredApplications' => array('Vanilla' => '2.0.10'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => FALSE,
   'SettingsUrl' => '/plugin/honeypot',
   'SettingsPermission' => 'Garden.AdminUser.Only',
   'Version' => '0.1',
   'Author' => "Zachary Doll",
   'AuthorEmail' => 'hgtonight@gmail.com',
   'AuthorUrl' => 'http://www.daklutz.com',
   'License' => 'GPLv3'
);


class HoneyPot extends Gdn_Plugin 
{

	public function Setup() {
		SaveToConfig('Plugins.HoneyPot.Question', 'What is three plus three?');
		SaveToConfig('Plugins.HoneyPot.Answer1', '6');
		SaveToConfig('Plugins.HoneyPot.Answer2', 'six');
	}


	public function EntryController_Render_Before($Sender,$Args) {
		if(strcasecmp($Sender->RequestMethod,'register')==0)
		{
			if(strcasecmp($Sender->View,'registerthanks')!=0 && strcasecmp($Sender->View,'registerclosed')!=0)
			{
				$RegistrationMethod = Gdn::Config('Garden.Registration.Method');
				$Sender->View = $this->GetView( 'register'.strtolower($RegistrationMethod).'.php');
			}
		}
    }

	public function UserModel_BeforeRegister_Handler($Sender) {
        $test = $Sender->EventArguments['User']['BotCheck'];
		$a1 = C('Plugins.HoneyPot.Answer1');
		$a2 = C('Plugins.HoneyPot.Answer2');

		if ($test != $a1 && $test != $a2)
		{
			$Sender->Validation->AddValidationResult('BotCheck','Your humanity is suspect... Please try again.');
			$Sender->EventArguments['Valid'] = FALSE;
		}
       // return FALSE;
	}

	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu = $Sender->EventArguments['SideMenu'];
		$Menu->AddItem('Forum', T('Forum'));
		$Menu->AddLink('Forum', T('HoneyPot'), 'settings/HoneyPot', 'Garden.Settings.Manage');
	}


	public function SettingsController_HoneyPot_Create($Sender) {
		$Sender->Permission('Garden.Settings.Manage');
		$Sender->Form = new Gdn_Form();
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array('Plugins.HoneyPot.Question','Plugins.HoneyPot.Answer1','Plugins.HoneyPot.Answer2',));
		$Sender->Form->SetModel($ConfigurationModel);
		$Sender->Title('HoneyPot Plugin Settings');
		$Sender->AddSideMenu('settings/HoneyPot');

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} 
		else {
			$Data = $Sender->Form->FormValues();
			if ($Sender->Form->Save() !== FALSE) {
				$Sender->StatusMessage = T("Your settings have been saved.");
			}
		}
		$Sender->Render($this->GetView('settings.php'));
	}
}

?>