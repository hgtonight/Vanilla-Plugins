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
	'Description' => 'Remove users, add/remove roles, set multiple roles, ban, and unban multiple users all from the Users dashboard.',
	'Version' => '1.1',
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
		
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// First time viewing
			$Sender->Form->SetData($ConfigurationModel->Data);
			
			$this->_AddInjectedUserIDsProperly($Sender);
		} else {
			// Form submission handling
			$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.RemoveType', 'Required');
			$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.Confirm', 'Required');
        	$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.UserIDs', 'Required');
        	
			$Data = $Sender->Form->FormValues();
			$UserIDs = json_decode($Data['Plugins.BulkEdit.UserIDs']);
			
			// Need to have these in case the there is a validation error
			$UserModel = new UserModel();
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
	
	public function Controller_Ban($Sender) {
		$Sender->Permission(
			array(
				'Garden.Users.Edit'
			),
			'',
			FALSE
		);
		
		// Figure out if we are setting, removing, or adding roles
		$Method = strtolower($Sender->RequestArgs[1]);
		
		if($Method == 'unban') {
			$Sender->Title('Bulk Unban Users');
			$BanAction = FALSE;
		}
		else {
			$Sender->Title('Bulk Ban Users');
			$Method = 'ban';
			$BanAction = TRUE;
		}

		$Sender->BulkEditAction = $Method;
		
		$Sender->Form = new Gdn_Form();
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		
		$ConfigurationModel->SetField('Plugins.BulkEdit.UserIDs');
		
		$Sender->Form->SetModel($ConfigurationModel);
		
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// First time viewing
			$Sender->Form->SetData($ConfigurationModel->Data);
			
			$this->_AddInjectedUserIDsProperly($Sender);
		} else {
			// Form submission handling
			$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.UserIDs', 'Required');
        	
			$Data = $Sender->Form->FormValues();
			$UserIDs = json_decode($Data['Plugins.BulkEdit.UserIDs']);
			
			// Need to have these in case the there is a validation error
			$UserModel = new UserModel();
			$Sender->BulkEditUsers = $UserModel->GetIDs($UserIDs);
			$Sender->Form->AddHidden('Plugins.BulkEdit.UserIDs', $Data['Plugins.BulkEdit.UserIDs'], TRUE);
			
			if ($Sender->Form->Save() !== FALSE) {
				$Users = $UserModel->GetIDs($UserIDs);
				// Store so we can display them
        		$Sender->BulkEditUsers = $Users;
				
				$BanModel = new BanModel();
				foreach ($Users as $User) {
					$BanModel->SaveUser($User, $BanAction);
				}
				$Sender->StatusMessage = ($BanAction) ? T('Users have been banned!') : T('Users have been unbanned!');
				$Sender->BulkEditActionComplete = TRUE;
			}
		}
		
		$Sender->Render($this->GetView('ban.php'));
	}
	
	public function Controller_Role($Sender) {
		$Sender->Permission(
			array(
				'Garden.Users.Edit'
			),
			'',
			FALSE
		);
		
		// Figure out if we are setting, removing, or adding roles
		$Method = strtolower($Sender->RequestArgs[1]);
		
		switch($Method) {
		default:
		case 'set':
			$Sender->Title('Bulk Set User Roles');
			break;
		case 'remove':
			$Sender->Title('Bulk Remove User Roles');
			break;
		case 'add':
			$Sender->Title('Bulk Add User Roles');
			break;
		}
		
		if(in_array($Method, array('set', 'remove', 'add'))) {
			$Sender->BulkEditAction = $Method;
		}
		else {
			$Sender->BulkEditAction = 'set';
		}
		
		
		$Sender->Form = new Gdn_Form();
		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		
		$ConfigurationModel->SetField('Plugins.BulkEdit.UserIDs');
		$ConfigurationModel->SetField('Plugins.BulkEdit.RoleIDs');
		
		// Set the role data for the view
		$RoleModel = new RoleModel();
		$Sender->RoleData = $RoleModel->GetArray();
		
		$Sender->Form->SetModel($ConfigurationModel);
		
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// First time viewing
			$Sender->Form->SetData($ConfigurationModel->Data);
			
			$this->_AddInjectedUserIDsProperly($Sender);
		} else {
			// Form submission handling
			$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.RoleIDs', 'Required');
			$ConfigurationModel->Validation->ApplyRule('Plugins.BulkEdit.UserIDs', 'Required');
        	
			$Data = $Sender->Form->FormValues();
			$UserIDs = json_decode($Data['Plugins.BulkEdit.UserIDs']);
			
			// Need to have these in case the there is a validation error
			$UserModel = new UserModel();
			$Sender->BulkEditUsers = $UserModel->GetIDs($UserIDs);
			$Sender->Form->AddHidden('Plugins.BulkEdit.UserIDs', $Data['Plugins.BulkEdit.UserIDs'], TRUE);
			
			if ($Sender->Form->Save() !== FALSE) {
				// Store so we can display them
        		$Sender->BulkEditUsers = $UserModel->GetIDs($UserIDs);
				$Sender->BulkEditRoles = array_intersect_key($Sender->RoleData, array_flip($Data['Plugins.BulkEdit.RoleIDs']));
				
				foreach ($UserIDs as $UserID) {
					// Get the old roles
					$CurrentRoleData = $UserModel->GetRoles($UserID);
					$CurrentRoleIDs = ConsolidateArrayValuesByKey($CurrentRoleData->Result(), 'RoleID');
					
					switch($Method) {
					default:
					case 'set':
						// Completely disregard current roles
						$NewRoleIDs = $Data['Plugins.BulkEdit.RoleIDs'];
						break;
					case 'remove':
						// Remove the selected roles
						$NewRoleIDs = array_diff($CurrentRoleIDs, $Data['Plugins.BulkEdit.RoleIDs']);
						break;
					case 'add':
						// Add our selected roles
						$NewRoleIDs = array_unique(array_merge($CurrentRoleIDs, $Data['Plugins.BulkEdit.RoleIDs']));
						break;
					}
					// Set the combined roles
					if($NewRoleIDs != $CurrentRoleIDs) {
						$UserModel->SaveRoles($UserID, $NewRoleIDs);
					}
					
				}
				$Sender->StatusMessage = T('Roles have been saved!');
				$Sender->BulkEditActionComplete = TRUE;
				
			}
		}
		
		$Sender->Render($this->GetView('role.php'));
	}
	
	private function _AddInjectedUserIDsProperly($Sender) {
		// gnab the data from the hacked post request
		$Request = $Sender->Request->GetRequestArguments();
		
		if(empty($Request['post']['Form/Plugins-dot-BulkEdit-dot-UserIDs'])) {
			//echo '<pre>'; var_dump($Request); echo '</pre>';
			Redirect('/dashboard/user');
		}
		$UserIDs = $Request['post']['Form/Plugins-dot-BulkEdit-dot-UserIDs'];
		
		// Get the full user object from the usermodel and store it for the view
		$UserModel = new UserModel();
		$Sender->BulkEditUsers = $UserModel->GetIDs($UserIDs);
		
		// Add the UserIDs to the current form in a hidden field so we can operate on them
		$Sender->Form->AddHidden('Plugins.BulkEdit.UserIDs', json_encode($UserIDs), TRUE);
	}
	
	public function Setup() {
		// SaveToConfig('Plugins.BulkEdit.EnableAdvancedMode', TRUE);
	}

	public function OnDisable() {
		// RemoveFromConfig('Plugins.BulkEdit.EnableAdvancedMode');
	}
}
