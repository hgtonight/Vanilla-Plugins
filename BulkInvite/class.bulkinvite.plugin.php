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
    'Description' => 'A plugin in that provides an interface to invite users in bulk.',
    'Version' => '1.0',
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
   * This function renders a form to send out emails to multiple addresses
   * with a custom subject and message
   * @param VanillaController $Sender PluginController
   */
  public function PluginController_BulkInvite_Create($Sender) {
    $this->_AddResources($Sender);
    $Sender->Form = new Gdn_Form();
    $Sender->Validation = new Gdn_Validation();
    $Sender->Permission('Garden.Settings.Manage');

    $Sender->SetData('Title', T('Bulk Invite Users'));
    $Sender->AddSideMenu('plugin/bulkinvite');

    $Sender->AddDefinition('BI_Placeholder', T('Plugins.BulkInvite.EmailPlaceholder'));

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

      // Split up the user input on commas and validate the email addresses
      $Recipients = explode(',', $Recipients);
      $CountRecipients = 0;
      foreach($Recipients as &$Recipient) {
        $Recipient = trim($Recipient);
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
        $Email->Subject($Subject);
        
        foreach($Recipients as $Recipient) {
          if($Recipient != '') {
            // Append an invite code or the forums url
            if($SendInvite) {
              $InviteCode = $this->CreateInvite($Sender, $Recipient);
              $Email->Message($Message . "\n\n" . ExternalUrl("entry/register/{$InviteCode}"));
            }
            else {
              $Email->Message($Message . "\n\n" . Gdn::Request()->Url('/', TRUE));
            }
            
            if($InviteCode != FALSE || !$SendInvite) {
              $Email->To($Recipient);
              try {
                $Email->Send();
              } catch(Exception $ex) {
                $Sender->Form->AddError($ex);
              }
            }
            else {
              $Sender->Form->AddError('A user or invite is already associated with ' . $Recipient . '.');
            }
          }
        }
      }
      if($Sender->Form->ErrorCount() == 0) {
        $Sender->InformMessage(T('Your invitations were sent successfully.'));
      }
    }
    else {
      // grab defaults
      $Sender->SetData('Plugins.BulkInvite.Message', T('Plugins.BulkInvite.Message'));
      $Sender->SetData('Plugins.BulkInvite.Subject', T('Plugins.BulkInvite.Subject'));
    }

    $Sender->Render($this->GetView('bulkinvite.php'));
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
   * Adds the plugin resources to the passed controller
   * @param mixed $Sender
   */
  private function _AddResources($Sender) {
    $Sender->AddJsFile($this->GetResource('js/bulkinvite.js', FALSE, FALSE));
    $Sender->AddCssFile($this->GetResource('design/bulkinvite.css', FALSE, FALSE));
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
      $Sender->Validation->AddValidationResult('Email', $EmailAddress . ' is already related to an existing account.');
      return FALSE;
    }

    // Make sure that the email does not already belong to an invitation in the application.
    $ExistingInvite = static::$InviteModel->GetWhere(array('Email' => $EmailAddress));
    if($ExistingInvite->NumRows() > 0) {
      $Sender->Validation->AddValidationResult('Email', 'An invitation has already been sent to ' . $EmailAddress . '.');
      return FALSE;
    }
    
    // Generate a unique invitation code
    $GeneratedCode = $this->_GetInvitationCode(); //'TODOlolo'
    // insert the invite
    $this->SQL
            ->Insert('Invitation', array(
                      'Email' => $EmailAddress,
                      'Code' => $GeneratedCode,
                      'InsertUserID' => 0,
                      'DateInserted' => date('Y-m-d H:i:s'))
          );
    //("insert GDN_ (`Email`, `Code`, `InsertUserID`, `DateInserted`) values ('hgtonight+moar@gmail.com', '5AHJVXG8', '241', '2013-08-26 15:56:12');")->Put();

    return $GeneratedCode;
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
