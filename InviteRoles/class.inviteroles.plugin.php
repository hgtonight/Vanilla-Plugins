<?php

if(!defined('APPLICATION'))
  exit();
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
$PluginInfo['InviteRoles'] = array(// You put whatever you want to call your plugin folder as the key
    'Name' => 'Invite Roles', // User friendly name, this is what will show up on the garden plugins page
    'Description' => 'A plugin that automatically assigns roles to invited users.', // This is also shown on the garden plugins page. Will be used as the first line of the description if uploaded to the official addons repository at vanillaforums.org/addons
    'Version' => '0.1', // Anything can go here, but it is suggested that you use some type of naming convention; will appear on the garden vanilla plugins page
    'RequiredApplications' => array('Vanilla' => '2.0.18.8'), // Can require multiple applications (e.g. Vanilla and Conversations)
    'RequiredTheme' => FALSE, // Any prerequisite themes
    'RequiredPlugins' => FALSE, // Any prerequisite plugins
    'MobileFriendly' => FALSE, // Should this plugin be run on mobile devices?
    'HasLocale' => TRUE, // Does this plugin have its own local file?
    'RegisterPermissions' => FALSE, // E.g. array('Plugins.TestingGround.Manage') will register this permissions automatically on enable
    'SettingsUrl' => '/settings/inviteroles', // A settings button linked to this URL will show up on the garden plugins page when enabled
    'SettingsPermission' => 'Garden.Settings.Manage', // The permissions required to visit the settings page. Garden.Settings.Manage is suggested.
    'Author' => 'Zachary Doll', // This will appear in the garden plugins page
    'AuthorEmail' => 'hgtonight@daklutz.com',
    'AuthorUrl' => 'http://www.daklutz.com',
    'License' => 'GPLv3' // Specify your license to prevent ambiguity
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
            'LabelCode' => T('Please select which roles invited users should be assigned'),
            'Items' => $Roles,
            'Options' => array('TextField' => 'Name', 'ValueField' => 'RoleID', 'listclass' => 'ColumnCheckBoxList')
    )));


    $Sender->ConfigurationModule = $ConfigModule;

    $Sender->ConfigurationModule->RenderAll();
  }

  public function EntryController_Register_Handler($Sender) {
    // The invite code is lost if there is a validation error after submission
    $InviteCode = $Sender->Request->Post('User/InvitationCode', FALSE);
    
    // Override any post values with get values if present
    if(!empty($Sender->RequestArgs)) {
      $InviteCode = $Sender->RequestArgs[0];
    }
    
    // Add the invite code to the form for later processing
    if($InviteCode) {
      $Sender->Form->AddHidden('InvitationCode', $InviteCode);
      $Sender->InvitationCode = $InviteCode;
    }
  }

  public function EntryController_RegistrationSuccessful_Handler($Sender) {
    // The user has been created at this point
    $UserID = $Sender->UserModel->EventArguments['UserID'];
    // If there is an invite code submitted with the form, validate it
    $InvitationCode = $Sender->Form->GetValue('InvitationCode', FALSE);
    if($InvitationCode) {
      $SQL = Gdn::SQL();
      $SQL->Select('i.InvitationID, i.InsertUserID, i.Email')
         ->Select('s.Name', '', 'SenderName')
         ->From('Invitation i')
         ->Join('User s', 'i.InsertUserID = s.UserID', 'left')
         ->Where('Code', $InvitationCode)
         ->Where('AcceptedUserID is null'); // Do not let them use the same invitation code twice!
      
      $InviteExpiration = Gdn::Config('Garden.Registration.InviteExpiration');
      
      if ($InviteExpiration != 'FALSE' && $InviteExpiration !== FALSE) {
         $SQL->Where('i.DateInserted >=', Gdn_Format::ToDateTime(strtotime($InviteExpiration)));
      }

      $Invitation = $SQL->Get()->FirstRow();
      
      if ($Invitation !== FALSE) {
         // Associate the new user id with the invitation (so it cannot be used again)
         $SQL
            ->Update('Invitation')
            ->Set('AcceptedUserID', $UserID)
            ->Where('InvitationID', $Invitation->InvitationID)
            ->Put();
         
         // Add the configured role to the user
         $Roles = C('Plugins.InviteRoles.Roles', FALSE);
         if($Roles) {
           $UserModel = new UserModel();
           $UserModel->SaveRoles($UserID, $Roles);
         }
      }
    }
    
    
    // It checks out, so give the user the roles determined by config
    
    // Invalidate the invite code so it only can be used once.
  }
  
  public function Setup() {
    // SaveToConfig('Plugins.TestingGround.EnableAdvancedMode', TRUE);
  }

  public function OnDisable() {
    // RemoveFromConfig('Plugins.TestingGround.EnableAdvancedMode');
  }

}
