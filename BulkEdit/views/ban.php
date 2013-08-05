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
	
	if($this->BulkEditActionComplete) {
		echo Wrap(T('You banned these users:').'<br />'.$UserNames,
			'div',
			array('class' => 'BulkEditUserList Info'));
			
		echo Wrap(Anchor(T('Return to User List'), '/dashboard/user'), 'div', array('class' => 'Info'));
	}
	else {
		echo $this->Form->Open();
		echo $this->Form->Errors();
		
		echo Wrap(
		T('You are going to <strong>ban</strong> the following users: ').
			$UserNames,
		'div',
		array('class' => 'BulkEditUserList Info Confirm'));
		
		echo Wrap(
			$this->Form->CheckBox('Plugins.BulkEdit.Confirm', 'Are you sure you want to ban these users?'),
		'div',
		array('class' => 'BulkEditUserList Info'));
		
		echo $this->Form->Button('Cancel', array(
			'Type' => 'button',
			'onclick' => 'history.go(-1)'
			));
		echo $this->Form->Button('Ban Users');
		
		echo $this->Form->Close();
	}
?>
</div>
