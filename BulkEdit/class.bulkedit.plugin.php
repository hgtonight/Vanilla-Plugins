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
$PluginInfo['BulkEdit'] = array(
	'Title' => 'Bulk Edit',
	'Description' => 'Allows for the removal of multiple users at once through the Users dashboard.', // Will Add/remove roles, remove users, set up multiple roles, all from the Users dashboard in the future
	'Version' => '1.0',
	'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
	'RequiredTheme' => FALSE, 
	'RequiredPlugins' => FALSE,
	'SettingsUrl' => '/plugin/bulkedit/settings',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => "Zachary Doll",
	'AuthorEmail' => 'hgtonight@daklutz.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class BulkEdit extends Gdn_Plugin {


	public function PluginController_BulkEdit_Create($Sender) {
		// Makes it look like a dashboard page
		$Sender->AddSideMenu('plugin/bulkedit');
		
		// Makes it act like a mini controller
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}
	
	public function Controller_Index($Sender) {
		$this->Controller_Settings($Sender);
	}
	
	public function Controller_Settings($Sender) {
		$Sender->Title('Bulk Edit Users');
		$Sender->PluginDescription = 'Allows for the removal of multiple users at once through the Users dashboard.';
		
		// Future settings page
		$Sender->Permission('Garden.Settings.Manage');
		/*
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField("Plugins.BulkEdit.AdvancedMode");
		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
        	$Data = $Sender->Form->FormValues();
			$ConfigurationModel->Validation->ApplyRule("Plugins.BulkEdit.AdvancedMode", "Boolean");
        	if ($Sender->Form->Save() !== FALSE)
        		$Sender->StatusMessage = T("Your settings have been saved.");
		}*/

		$Sender->Title('BulkEdit Settings');
		$Sender->Render($this->GetView("settings.php"));
	}
	
	public function UserController_UserCell_Handler($Sender) {
		$Sender->Form->InputPrefix = 'Form';
		if(property_exists($Sender, 'EventArgs')) {
			$User = $Sender->EventArgs['User'];
			echo '<td>'.$Sender->Form->Checkbox('Plugins.BulkEdit.UserIDs[]', NULL, array('value' => $User->UserID, 'class' => 'BulkSelect')).'</td>';
		}
		else {
			echo '<th id="BulkEditAction" title="Toggle">'.T('Action').'</th>';
		}
	}

	public function UserController_Render_Before($Sender) {
		$Sender->AddJsFile($this->GetResource('js/bulkedit.js', FALSE, FALSE));
		$Sender->AddCssFile($this->GetResource('design/bulkedit.css', FALSE, FALSE));
	}
	
	public function Controller_Remove($Sender) {
		$Sender->Title('Bulk Delete Users');
		$Sender->Permission(
			array(
				'Garden.Users.Delete'
			),
			'',
			FALSE
		);
		
		$Sender->Form = new Gdn_Form();
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		
		$ConfigurationModel->SetField('Plugins.BulkEdit.RemoveType');
		$ConfigurationModel->SetField('Plugins.BulkEdit.Confirm');
		$ConfigurationModel->SetField('Plugins.BulkEdit.UserIDs');
		
		$Sender->Form->SetModel($ConfigurationModel);
		
		$UserModel = new UserModel();
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// First time viewing
			$Sender->Form->SetData($ConfigurationModel->Data);
			
			// gnab the data from the hacked post request
			$Request = $Sender->Request->GetRequestArguments();
			
			if(empty($Request['post']['Form/Plugins-dot-BulkEdit-dot-UserIDs'])) {
				echo '<pre>'; var_dump($Request); echo '</pre>';
				Redirect('/dashboard/user');
			}
			$UserIDs = $Request['post']['Form/Plugins-dot-BulkEdit-dot-UserIDs'];
			
			// Get the full usermodel from the user ids and store it for the view
			$Sender->BulkEditUsers = $UserModel->GetIDs($UserIDs);
			
			// Add the UserIDs to the current form in a hidden field so we can operate on them
			$Sender->Form->AddHidden('Plugins.BulkEdit.UserIDs', json_encode($UserIDs), TRUE);
		} else {
			// Form submission handling
			$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.RemoveType', 'Required');
			$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.Confirm', 'Required');
        	$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.UserIDs', 'Required');
        	
			$Data = $Sender->Form->FormValues();
			$UserIDs = json_decode($Data['Plugins.BulkEdit.UserIDs']);
			
			// Need to have these in case the there is a validation error
			$Sender->BulkEditUsers = $UserModel->GetIDs($UserIDs);
			$Sender->Form->AddHidden('Plugins.BulkEdit.UserIDs', $Data['Plugins.BulkEdit.UserIDs'], TRUE);
			
			if ($Sender->Form->Save() !== FALSE) {
				// Store so we can display them
        		$Sender->BulkEditUsers = $UserModel->GetIDs($UserIDs);
				foreach ($UserIDs as $UserID) {
					$UserModel->Delete($UserID, array('DeleteMethod' => $Data['Plugins.BulkEdit.RemoveType']));
				}
				$Sender->StatusMessage = T('Users have been deleted!');
				$Sender->BulkEditActionComplete = TRUE;
			}
		}
		
		$Sender->Render($this->GetView('remove.php'));
	}
	
	public function Setup() {
		// SaveToConfig('Plugins.BulkEdit.EnableAdvancedMode', TRUE);
	}

	public function OnDisable() {
		// RemoveFromConfig('Plugins.BulkEdit.EnableAdvancedMode');
	}
}
