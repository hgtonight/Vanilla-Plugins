<?php if (!defined('APPLICATION')) exit();
/*	Copyright 2012 Zachary Doll
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
?>
<div class="Header"><?php
		echo Wrap(T($this->Data['Title']), 'h1');
	?>
</div>
<div class="Content"><?php
	echo $this->Form->Open();
	echo $this->Form->Errors();
	//var_dump($this);
	echo Wrap(T('Are you sure you want to permanently delete these users?'),
				'div',
				array('class' => 'Warning'));

	foreach($this->BulkEditUsers as $User) {
		$UserNames .= $User['Name'].', ';
	}
	$UserNames = rtrim($UserNames, ', ');
	echo Wrap($UserNames,
		'div',
		array('class' => 'BulkEditUserList Info'));
	
	echo $this->Form->Label(T('What do you want to do with their content?'), 'Plugins.BulkEdit.RemoveType');
	echo $this->Form->RadioList('Plugins.BulkEdit.RemoveType', array(
		'keep' => 'Keep User Content',
		'wipe' => 'Blank User Content',
		'delete' => 'Remove User Content'
		), array('list' => TRUE));
	
	// $this->Form->Button('Cancel', array(
		// 'Type' => 'button',
		// 'onclick' => 'history.go(-1)'));
	echo $this->Form->Close('Delete Users Forever');
	?>
</div>
