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
	// TODO: Separate views in a non-redundant manner
	switch($this->BulkEditAction) {
	default:
	case 'set':
		$Completed = 'You set these roles:';
		$WorkingOn = 'You are <strong>setting roles</strong> on the following users:';
		$ButtonAction = 'Set Roles';
		break;
	case 'remove':
		$Completed = 'You removed these roles:';
		$WorkingOn = 'You are <strong>removing roles</strong> from the following users:';
		$ButtonAction = 'Remove Roles';
		break;
	case 'add':
		$Completed = 'You added these roles:';
		$WorkingOn = 'You are <strong>adding roles</strong> to the following users:';
		$ButtonAction = 'Add Roles';
		break;
	}
	// Construct a username list
	$UserNames = '';
	foreach($this->BulkEditUsers as $User) {
		$UserNames .= $User['Name'].', ';
	}
	$UserNames = rtrim($UserNames, ', ');
	
	$RoleNames = implode(', ', $this->BulkEditRoles);
	if($this->BulkEditActionComplete) {
		echo Wrap(T($Completed).'<br />'.$RoleNames.'<br />'.T('for these users:').'<br />'.$UserNames,
			'div',
			array('class' => 'BulkEditUserList Info'));
			
		echo Wrap(Anchor(T('Return to User List'), '/dashboard/user'), 'div', array('class' => 'Info'));
	}
	else {
		echo $this->Form->Open();
		echo $this->Form->Errors();
		
		echo Wrap(
		T($WorkingOn).'<br />'.$UserNames,
		'div',
		array('class' => 'BulkEditUserList Info Confirm'));
		
		echo $this->Form->CheckBoxList("Plugins.BulkEdit.RoleIDs", array_flip($this->RoleData), array_flip($this->UserRoleData)); 

		echo $this->Form->Button(T('Cancel'), array(
			'Type' => 'button',
			'onclick' => 'history.go(-1)'
			));
		echo $this->Form->Button(T($ButtonAction));
		
		echo $this->Form->Close();
	}
?>
</div>

