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

echo Wrap(Wrap(T($this->Data['Title']), 'h1'), 'div', array('class' => 'Header'));

?>
<div class="Content"><?php
	// Construct a username list
	$UserNames = '';
	foreach($this->BulkEditUsers as $User) {
		$UserNames .= $User['Name'].', ';
	}
	$UserNames = '<br />'.rtrim($UserNames, ', ');
	
	// Helpful description
	if($this->BulkEditActionComplete) {
		echo Wrap(T('You removed these users:').'<br />'.$UserNames,
			'div',
			array('class' => 'BulkEditUserList Info'));
			
		echo Wrap(Anchor(T('Return to User List'), '/dashboard/user'), 'div', array('class' => 'Info'));
	}
	else {
		echo Wrap(
			Wrap(T('Help'), 'h2').
			Wrap(
				Wrap(T('UserKeep', 'Keep User Content').': '.T('UserKeepMessage', 'Delete the user but keep the user\'s content.'), 'li').
				Wrap(T('UserWipe', 'Blank User Content').': '.T('UserWipeMessage', 'Delete the user and replace all of the user\'s content with a message stating the user has been deleted. This gives a visual cue that there is missing information.'), 'li').
				Wrap(T('UserDelete', 'Remove User Content').': '.T('UserDeleteMessage', 'Delete the user and completely remove all of the user\'s content. This may cause discussions to be disjointed. Best option for removing spam.'), 'li'),
				'ul'),
			'div',
			array('class' => 'Help Aside')
		);
		
		echo $this->Form->Open();
		echo $this->Form->Errors();
		
		echo Wrap(
		T('You are going to <strong>remove</strong> the following users: ').
			$UserNames,
		'div',
		array('class' => 'BulkEditUserList Info Confirm'));
		
			echo Wrap(
		$this->Form->Label(T('What do you want to do with their content?'), 'Plugins.BulkEdit.RemoveType').
		$this->Form->RadioList('Plugins.BulkEdit.RemoveType', array(
			'keep' => T('UserKeep', 'Keep User Content'),
			'wipe' => T('UserWipe', 'Blank User Content'),
			'delete' => T('UserDelete', 'Remove User Content')
			), array('list' => TRUE)).
		$this->Form->CheckBox('Plugins.BulkEdit.Confirm', T('Are you sure you want to remove these users?')),
		'div',
		array('class' => 'BulkEditUserList Info'));
		
		echo $this->Form->Button(T('Cancel'), array(
			'Type' => 'button',
			'onclick' => 'history.go(-1)'
			));
		echo $this->Form->Button(T('Delete Users Forever'));
		
		echo $this->Form->Close();
	}
?>
</div>
