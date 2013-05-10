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
	'Description' => 'Allows for editing of multiple users at once. Add/remove roles, remove users, set up multiple roles, all from the Users dashboard.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.0.18'),
	'RequiredTheme' => FALSE, 
	'RequiredPlugins' => FALSE,
	'SettingsUrl' => '/dashboard/settings/bulkedit',
	'SettingsPermission' => 'Garden.AdminUser.Only',
	'Author' => "Zachary Doll",
	'AuthorEmail' => 'hgtonight@gmail.com',
	'AuthorUrl' => 'http://www.daklutz.com',
	'License' => 'GPLv3'
);

class BulkEdit extends Gdn_Plugin {

	public function SettingsController_BulkEdit_Create($Sender) {
		$Sender->Permission('Garden.Settings.Manage');

		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array("Plugins.BulkEdit.CategoryIDs"));
		$ConfigurationModel->SetField("Plugins.BulkEdit.PostsPerPage");
		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
        	$Data = $Sender->Form->FormValues();
			$ConfigurationModel->Validation->ApplyRule("Plugins.BulkEdit.PostsPerPage", "Integer");
        	if ($Sender->Form->Save() !== FALSE)
        		$Sender->StatusMessage = T("Your settings have been saved.");
		}

		$Sender->Title('BulkEdit Settings');
		$Sender->AddSideMenu('/dashboard/settings/nillablog');

		$CategoryModel = new CategoryModel();
		$Sender->SetData("CategoryData", $CategoryModel->GetAll(), TRUE);
		array_shift($Sender->CategoryData->Result());

		$Sender->Render($this->GetView("settings.php"));
	}
	
	public function PluginController_BulkEdit_Create($Sender) {
		$Sender->Title('Bulk Edit Users');
		$Sender->AddSideMenu('plugin/bulkedit');

		// get sub-pages forms ready
		$Sender->Form = new Gdn_Form();
		$this->Dispatch($Sender, $Sender->RequestArgs);
	}
	
	public function Controller_Index($Sender) {
		$Sender->Permission(
			array(
				'Garden.Users.Edit',
				'Garden.Users.Delete'
			),
			'',
			FALSE
		);
		
		// find out what we want to do and setup the right form
		$Request = $Sender->Request->GetRequestArguments();
		$Request = $Request['post'];
		$UserIDs = $Request['UserIDs'];
		
		switch($Request['WhatWeDo']) {
		case 'remove':
			$this->_Remove_Users($Sender);
			break;
		case 'role-add':
			$this->_Add_Roles($Sender);
			break;
		case 'role-remove':
			$this->_Remove_Roles($Sender);
			break;
		case 'role-set':
			$this->Set_Roles($Sender);
			break;
		default:
			echo 'No action specified!';
		}
		
		$UserModel = new UserModel();
		$UserIDs = $UserModel->GetIDs($UserIDs);
		$Sender->BulkEditUsers = $UserIDs;
		$Sender->Render($this->GetView('remove-select-type.php'));
		unset($Sender->BulkEditUsers);
	}
	
	public function UserController_UserCell_Handler($Sender) {
		$User = $Sender->EventArgs['User'];
		if($User->UserID) {
			echo '<td><input type="checkbox" name="UserIDs[]" value="'.$User->UserID.'" class="md" /></td>';//var_dump($Sender);
		}
		else {
			echo '<th id="BulkEditAction" title="Toggle">'.T('Action').'</th>';
		}
	}

	public function UserController_Render_Before($Sender) {
		$Sender->AddJsFile($this->GetResource('js/bulkedit.js', FALSE, FALSE));
		$Sender->AddCssFile($this->GetResource('design/bulkedit.css', FALSE, FALSE));
		$Sender->View = $this->GetView('user/index.php');
	}
	
	public function UserController_FilterMenu_Handler($Sender) {
		//$Asset = $Sender->EventArguments['AssetName'];
		//$SubView = $Sender->RequestArgs;
		// SubView has stuff in it if it is a subpage of the user controller
		//if($Asset == 'Content' && empty($SubView)) {
			// Pop up Modal
			?>
				<select name="WhatWeDo" id="BulkEditDropDown">
					<option value="0">With Checked Users...</option>
					<option value="remove">Remove Users...</option>
					<option value="role-add">Add Role to Users...</option>
					<option value="role-remove">Remove Role from Users</option>
					<option value="role-set">Set roles for Users</option>
				</select>
			<?
		//}
	}

	// Validate we have the right inputs, show a form asking how you want to remove them otherwise
	private function _Remove_Users($Sender) {
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField('Plugins.BulkEdit.RemoveMode');
		$Sender->Form->SetModel($ConfigurationModel);

		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// First time viewing
			//$Sender->Form->AddHidden('Users', 
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
			// Form submission handling
        	$Data = $Sender->Form->FormValues();
			$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.RemoveMode', 'Required');
        	if ($Sender->Form->Save() !== FALSE)
        		$Sender->StatusMessage = T("Users have been deleted!");
		}

		$Sender->Title('Bulk Delete Type');
	}
	
	public function Setup() {
		// SaveToConfig('Plugins.BulkEdit.Frequency', 120);
	}

	public function OnDisable() {
		// RemoveFromConfig('Plugins.BulkEdit.Frequency');
	}
}
