<?php if(!defined('APPLICATION')) exit();
/* 	Copyright 2013 Zachary Doll
 * 	This program is free software: you can redistribute it and/or modify
 * 	it under the terms of the GNU General Public License as published by
 * 	the Free Software Foundation, either version 3 of the License, or
 * 	(at your option) any later version.
 *
 * 	This program is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	GNU General Public License for more details.
 *
 * 	You should have received a copy of the GNU General Public License
 * 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
$PluginInfo['InviteRoles'] = array(
    'Name' => 'Invite Roles',
    'Description' => 'A plugin that automatically assigns roles to invited users.',
    'Version' => '1.0',
    'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'MobileFriendly' => TRUE,
    'HasLocale' => TRUE,
    'RegisterPermissions' => FALSE,
    'SettingsUrl' => '/settings/inviteroles',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'Author' => 'Zachary Doll',
    'AuthorEmail' => 'hgtonight@daklutz.com',
    'AuthorUrl' => 'http://www.daklutz.com',
    'License' => 'GPLv3'
);

class InviteRoles extends Gdn_Plugin {

  public function SettingsController_InviteRoles_Create($Sender) {
    $Sender->Permission($this->GetPluginKey('SettingsPermission'));
    $Sender->SetData('Title', $this->GetPluginName() . ' ' . T('Settings'));

    $Sender->AddSideMenu('dashboard/settings/inviteroles');

    $ConfigModule = new ConfigurationModule($Sender);

    $RoleModel = new RoleModel();
    $Roles = $RoleModel->Get()->Result(DATASET_TYPE_ARRAY);

    $ConfigModule->Initialize(array(
        'Plugins.InviteRoles.Roles' => array(
            'Control' => 'checkboxlist',
            'LabelCode' => T('Plugin.InviteRoles.RoleSelectionLabel'),
            'Items' => $Roles,
            'Options' => array('TextField' => 'Name', 'ValueField' => 'RoleID', 'listclass' => 'ColumnCheckBoxList')
    )));

    $Sender->ConfigurationModule = $ConfigModule;
    $Sender->ConfigurationModule->RenderAll();
  }

  public function EntryController_Render_Before($Sender) {
    // Only function on the register method
    if($Sender->RequestMethod === 'register') {
      // The invite code is lost if there is a validation error after submission
      $InviteCode = $Sender->Form->GetValue('InvitationCode', FALSE);
      // This is where the invite code is normally, but don't override it if it
      // is already in the form
      if(!empty($Sender->RequestArgs) && $InviteCode == FALSE) {
        $InviteCode = $Sender->RequestArgs[0];
      }
      // Add the invite code to the form for later processing
      if($InviteCode) {
        $Sender->Form->AddHidden('InvitationCode', $InviteCode);
      }
    }
  }

  public function EntryController_RegistrationSuccessful_Handler($Sender) {
    // The user has been created at this point
    $UserID = $Sender->UserModel->EventArguments['UserID'];
    // If there is an invite code submitted with the form, validate it
    $InvitationCode = $Sender->Form->GetValue('InvitationCode', FALSE);
    if($InvitationCode) {
      $SQL = Gdn::SQL();
      // Only allow unused invite codes
      $SQL->Select('i.InvitationID, i.InsertUserID, i.Email')
              ->Select('s.Name', '', 'SenderName')
              ->From('Invitation i')
              ->Join('User s', 'i.InsertUserID = s.UserID', 'left')
              ->Where('Code', $InvitationCode)
              ->Where('AcceptedUserID is null');
      

      $InviteExpiration = Gdn::Config('Garden.Registration.InviteExpiration');

      if($InviteExpiration != 'FALSE' && $InviteExpiration !== FALSE) {
        $SQL->Where('i.DateInserted >=', Gdn_Format::ToDateTime(strtotime($InviteExpiration)));
      }

      $Invitation = $SQL->Get()->FirstRow();

      if($Invitation !== FALSE) {
        // Associate the new user id with the invitation to invalidate the code
        $SQL
                ->Update('Invitation')
                ->Set('AcceptedUserID', $UserID)
                ->Where('InvitationID', $Invitation->InvitationID)
                ->Put();

        // Add the configured role to the user
        $Roles = C('Plugins.InviteRoles.Roles', FALSE);
        if($Roles) {
          // Only bother if the plugin has been configured
          $UserModel = new UserModel();
          $CurrentRoleData = $UserModel->GetRoles($UserID);
          $CurrentRoleIDs = ConsolidateArrayValuesByKey($CurrentRoleData->Result(), 'RoleID');
          // merge in the configured roles
          $NewRoleIDs = array_unique(array_merge($CurrentRoleIDs, $Roles));

          // Set the combined roles if there are any changes
          if($NewRoleIDs != $CurrentRoleIDs) {
            $UserModel->SaveRoles($UserID, $NewRoleIDs);
          }
        }
      }
    }
  }
}
