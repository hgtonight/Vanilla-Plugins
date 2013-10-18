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
	switch($this->BulkEditAction) {
	default:
	case 'verify':
		$Completed = 'You verified these users:';
		$WorkingOn = 'You are <strong>verifying</strong> the following users:';
		$ButtonAction = 'Verify Users';
		break;
	case 'unverify':
		$Completed = 'You unverified these users:';
		$WorkingOn = 'You are <strong>unverifying</strong> the following users:';
		$ButtonAction = 'Unverify Users';
		break;
	}
	
	// Construct a username list
	$UserNames = '';
	foreach($this->BulkEditUsers as $User) {
		$UserNames .= $User['Name'].', ';
	}
	$UserNames = '<br />'.rtrim($UserNames, ', ');
	
	if($this->BulkEditActionComplete) {
		echo Wrap(T($Completed).'<br />'.$UserNames,
			'div',
			array('class' => 'BulkEditUserList Info'));
			
		echo Wrap(Anchor(T('Return to User List'), '/dashboard/user'), 'div', array('class' => 'Info'));
	}
	else {
      echo Wrap(
			Wrap(T('Help'), 'h2').
			Wrap('Verifying users skips spam checking on their posts. This will save server resources as well as reduce the time between clicking post and it displaying.', 'div'),
			'div',
			array('class' => 'Help Aside')
		);
      
		echo $this->Form->Open();
		echo $this->Form->Errors();
		
		echo Wrap(
			T($WorkingOn).'<br />'.$UserNames,
			'div',
			array('class' => 'BulkEditUserList Info Confirm'));
		
		echo $this->Form->Button(T('Cancel'), array(
			'Type' => 'button',
			'onclick' => 'history.go(-1)'
			));
		echo $this->Form->Button(T($ButtonAction));
		
		echo $this->Form->Close();
	}
?>
</div>

