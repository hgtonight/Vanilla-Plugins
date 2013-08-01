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
	echo Wrap(T('You deleted these users'),
				'div',
				array('class' => 'Warning'));

	foreach($this->BulkEditUsers as $User) {
		$UserNames .= $User['Name'].', ';
	}
	$UserNames = rtrim($UserNames, ', ');
	echo Wrap($UserNames,
		'div',
		array('class' => 'BulkEditUserList Info'));
	echo Anchor(T('Return to User List'), '/dashboard/user');
	
	?>
</div>
