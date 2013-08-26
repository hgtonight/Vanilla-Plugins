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
echo Wrap(T($this->Data['Title']), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();

$Subject = array();
$Message = array();
if($this->Data['Plugins.BulkInvite.Subject']) {
  $Subject = array('value' => $this->Data['Plugins.BulkInvite.Subject']);
}
if($this->Data['Plugins.BulkInvite.Message']) {
  $Message = array('value' => $this->Data['Plugins.BulkInvite.Message']);
}
echo Wrap(
        Wrap(
                $this->Form->Label(T('Invitation Subject Line'), 'Plugins.BulkInvite.Subject') .
                Wrap(T('This will be sent as the email subject'), 'div', array('class' => 'Info')) .
                $this->Form->TextBox('Plugins.BulkInvite.Subject', array_merge(array('id' => 'BI_Subject'), $Subject)), 'li') .
        Wrap(
                $this->Form->Label(T('Recipients E-mail Addresses'), 'Plugins.BulkInvite.Recipients') .
                Wrap(T('Separate email addresses with a comma (,)'), 'div', array('class' => 'Info')) .
                $this->Form->TextBox('Plugins.BulkInvite.Recipients', array('Multiline' => TRUE, 'id' => 'BI_Recipients')), 'li') .
        Wrap(
                $this->Form->Label(T('Invitation Message'), 'Plugins.BulkInvite.Message') .
                Wrap(T('This message, along with your website\'s URL will be sent to each address entered above'), 'div', array('class' => 'Info')) .
                $this->Form->TextBox('Plugins.BulkInvite.Message', array_merge(array('Multiline' => TRUE, 'class' => 'Message', 'id' => 'BI_Message'), $Message)), 'li') .
        Wrap(
                $this->Form->Label(T('Invitation Code'), 'Plugins.BulkInvite.SendInviteCode') .
                $this->Form->CheckBox('Plugins.BulkInvite.SendInviteCode', T('Should an invitation code be created and sent with your message?'), array('id' => 'BI_SendInviteCode')), 'li'), 'ul');

echo $this->Form->Button(T('Send Invites!'));
echo $this->Form->Close();
?>
<div class="Footer">
  <?php
  echo Wrap(T('Feedback'), 'h3');
  ?>
  <div class="Aside Box">
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="XUMDRGEMAY9TC">
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
      <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
  </div>
  <?php
  echo Wrap('Find this plugin helpful? Want to support a freelance developer?<br/>Click the donate button to buy me a beer. :D', 'div', array('class' => 'Info'));
  echo Wrap('Confused by something? Check out the feedback thread on the official <a href="http://vanillaforums.org/discussion/24686/feedback-for-bulk-invite" target="_blank">Vanilla forums</a>.', 'div', array('class' => 'Info'));
  ?>
</div>