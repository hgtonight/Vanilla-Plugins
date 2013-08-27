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
$PluginInfo['BulkInvite'] = array(
    'Title' => 'Bulk Invite',
    'Description' => 'A plugin in that provides an interface to invite users in bulk. It optionally sends an invitation registration code.',
    'Version' => '1.1',
    'RequiredApplications' => array('Vanilla' => '2.0.18.8'),
    'HasLocale' => TRUE,
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'Author' => "Zachary Doll",
    'AuthorEmail' => 'hgtonight@daklutz.com',
    'AuthorUrl' => 'http://www.daklutz.com',
    'License' => 'GPLv3'
);

class BulkInvite extends Gdn_Plugin {

  protected static $UserModel;
  protected static $InviteModel;
  public $SQL;
  
  public function __construct() {
    if(!self::$UserModel) {
      self::$UserModel = new UserModel();
    }
    if(!self::$InviteModel) {
      self::$InviteModel = new InvitationModel();
    }
    $this->SQL = Gdn::Database()->SQL();
    parent::__construct();
  }

  /**
   * Create a minicontroller to handle bypassing the invite model
   * @param PluginController $Sender
   */
  public function PluginController_BulkInvite_Create($Sender) {
    $this->Dispatch($Sender, $Sender->RequestArgs);
  }

  /**
   * This function renders a form to send out emails to multiple addresses
   * with a custom subject and message
   * @param PluginController $Sender
   */
  public function Controller_Index($Sender) {
    $Sender->Form = new Gdn_Form();
    $Sender->Validation = new Gdn_Validation();
    $Sender->Permission('Garden.Settings.Manage');

    if($Sender->Form->AuthenticatedPostBack()) {
      // TODO: Do I need to sanitize the message field?
      $Message = $Sender->Form->GetFormValue('Plugins.BulkInvite.Message');
      $Message = trim($Message);
      
      $SendInvite = $Sender->Form->GetFormValue('Plugins.BulkInvite.SendInviteCode');
      
      $Subject = $Sender->Form->GetFormValue('Plugins.BulkInvite.Subject', T('Plugins.BulkInvite.Subject'));
      $Recipients = $Sender->Form->GetFormValue('Plugins.BulkInvite.Recipients');
      
      if($Recipients == T('Plugins.BulkInvite.EmailPlaceholder')) {
        $Recipients = '';
      }

      // Split up the user input on commas and trim up the whitespace
      $Recipients = array_map(trim, explode(',', $Recipients));
      $CountRecipients = 0;
      
      // Validate the email addresses and get a total count
      foreach($Recipients as $Recipient) {
        if($Recipient != '') {
          $CountRecipients++;
          if(!ValidateEmail($Recipient)) {
            $Sender->Form->AddError(sprintf(T('%s is not a valid email address'), $Recipient));
          }
        }
      }
      
      if($CountRecipients == 0) {
        $Sender->Form->AddError(T('You must provide at least one recipient'));
      }
      
      // Send out invite emails only if all addresses entered are valid
      if($Sender->Form->ErrorCount() == 0) {
        $Email = new Gdn_Email();

        foreach($Recipients as $Recipient) {
          if($Recipient != '') {
            // reset email object
            $Email->Clear();
            $Email->Subject($Subject);
            $InviteCode = FALSE;
            // Append an invite code or the forums url
            if($SendInvite) {
              $InviteCode = $this->CreateInvite($Sender, $Recipient);
              if($InviteCode == FALSE) {
                $Email->Clear();
                break;
              }
              $Email->Message($Message . "\n\n" . ExternalUrl("entry/register/{$InviteCode}"));
            }
            else {
              $Email->Message($Message . "\n\n" . Gdn::Request()->Url('/', TRUE));
            }

            $Email->To($Recipient);
            try {
              $Email->Send();
            } catch(Exception $ex) {
              $Sender->Form->AddError($ex);
            }
          }
        }
      }
      if($Sender->Form->ErrorCount() == 0) {
        $Sender->InformMessage(T('Your invitations were sent successfully.'));
      }
    }

    $this->_PseudoRender($Sender);
  }

  /**
   * This function bypasses the normal permissions required to remove invites
   * through the invite model, but only from the configured user
   * @param PluginController $Sender
   */
  public function Controller_UnInvite($Sender) {
    $Sender->Permission('Garden.Settings.Manage');
    $InviteID = $Sender->RequestArgs[1];
    $TransientKey = $Sender->RequestArgs[2];
    if(Gdn::Session()->ValidateTransientKey($TransientKey)) {
      $this->SQL->Delete('Invitation', array(
          'InvitationID' => $InviteID,
          'InsertUserID' => C('Plugins.BulkInvite.InsertUserID', 1)
              )
      );
      $Sender->InformMessage(T('Invitation removed successfully.'));
    }
    $this->_PseudoRender($Sender);
  }
  
  /**
   * Resends an invite message with the default invitation message
   * @param type $Sender
   */
  public function Controller_SendInvite($Sender) {
    $Sender->Permission('Garden.Settings.Manage');
    $InviteID = $Sender->RequestArgs[1];
    $TransientKey = $Sender->RequestArgs[2];
    if(Gdn::Session()->ValidateTransientKey($TransientKey)) {
      $Invitation = static::$InviteModel->GetByInvitationID($InviteID);
      $RegistrationUrl = ExternalUrl("entry/register/{$Invitation->Code}");
      $AppTitle = Gdn::Config('Garden.Title');
      $Email = new Gdn_Email();
      $Email->Subject(sprintf(T('[%s] Invitation'), $AppTitle));
      $Email->To($Invitation->Email);
      $Email->Message(
         sprintf(
            T('EmailInvitation'),
            $Invitation->SenderName,
            $AppTitle,
            $RegistrationUrl
         )
      );
      
      try {
        $Email->Send();
      } catch(Exception $ex) {
        $Sender->Form->AddError($ex);
      }
      if($Sender->Form->ErrorCount() == 0) {
        $Sender->InformMessage(T('An invitation message was resent to '. $Invitation->Email. ' successfully.'));
      }
    }
    $this->_PseudoRender($Sender);
  }
  
  /**
   * Inserts a menu link in the dashboard
   * @param mixed $Sender
   */
  public function Base_GetAppSettingsMenuItems_Handler($Sender) {
    $Menu = &$Sender->EventArguments['SideMenu'];
    $Menu->AddLink('Users', 'Bulk Invite', 'plugin/bulkinvite', 'Garden.Settings.Manage');
  }
   
  /**
   * Completely bypass the invitation model since it uses a built in definition
   * @param type $Sender
   * @param type $EmailAddress
   * @return boolean
   */
  public function CreateInvite($Sender, $EmailAddress) {
    // Make sure that the email does not already belong to an account in the application.
    $ExistingAccount = static::$UserModel->GetWhere(array('Email' => $EmailAddress));
    if($ExistingAccount->NumRows() > 0) {
      $Sender->Form->AddError($EmailAddress . ' is already related to an existing account.');
      return FALSE;
    }

    // Make sure that the email does not already belong to an invitation in the application.
    $ExistingInvite = static::$InviteModel->GetWhere(array('Email' => $EmailAddress));
    if($ExistingInvite->NumRows() > 0) {
      $Sender->Form->AddError('An invitation has already been sent to ' . $EmailAddress . '.');
      return FALSE;
    }
    
    // Generate a unique invitation code
    $GeneratedCode = $this->_GetInvitationCode(); //'TODOlolo'
    // insert the invite
    $this->SQL
            ->Insert('Invitation', array(
                      'Email' => $EmailAddress,
                      'Code' => $GeneratedCode,
                      'InsertUserID' => C('Plugins.BulkInvite.InsertUserID', 1),
                      'DateInserted' => date('Y-m-d H:i:s'))
          );

    return $GeneratedCode;
  }

  /**
   * Renders the mini controller consistently
   * @param PluginController $Sender
   */
  public function _PseudoRender($Sender) {
    $Sender->AddJsFile($this->GetResource('js/bulkinvite.js', FALSE, FALSE));
    $Sender->AddCssFile($this->GetResource('design/bulkinvite.css', FALSE, FALSE));
    $Sender->SetData('Title', T('Bulk Invite Users'));
    $Sender->InvitationData = static::$InviteModel->GetByUserID(C('Plugins.BulkInvite.InsertUserID', 1));
    if(!$Sender->Form->AuthenticatedPostBack()) {
      $Sender->SetData('Plugins.BulkInvite.Message', T('Plugins.BulkInvite.Message'));
      $Sender->SetData('Plugins.BulkInvite.Subject', T('Plugins.BulkInvite.Subject'));
    }
    $Sender->AddDefinition('BI_Placeholder', T('Plugins.BulkInvite.EmailPlaceholder'));
    $Sender->AddSideMenu('plugin/bulkinvite');
    $Sender->Render($this->GetView('bulkinvite.php'));
  }
  
  public function Setup() {
    // SaveToConfig('Plugins.BulkInvite.EnableAdvancedMode', TRUE);
  }

  public function OnDisable() {
    // RemoveFromConfig('Plugins.BulkInvite.EnableAdvancedMode');
  }

  /**
   * Returns a unique 8 character invitation code
   */
  protected function _GetInvitationCode() {
    // Generate a new invitation code.
    $Code = RandomString(8);

    // Make sure the string doesn't already exist in the invitation table
    $CodeData = static::$InviteModel->GetWhere(array('Code' => $Code));
    if($CodeData->NumRows() > 0) {
      return $this->_GetInvitationCode();
    }
    else {
      return $Code;
    }
  }
}
