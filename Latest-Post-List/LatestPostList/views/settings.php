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
		echo Wrap(T($this->Data['PluginDescription']), 'div', array('class' => 'Info'));
	?>
</div>
<div class="Content"><?php
	echo $this->Form->Open();
	echo $this->Form->Errors();
	
	echo Wrap(T('Appearance Settings'), 'h3');
	echo Wrap(
		Wrap(
			$this->Form->Label(T('List Length'), 'Plugins.LatestPostList.Count').
			Wrap(T('The maximum number of discussions that will be shown in the panel'),
				'div',
				array('class' => 'Info')).
			$this->Form->Textbox('Plugins.LatestPostList.Count'),
			'li').
		Wrap(
			$this->Form->Label(T('Pages'), 'Plugins.LatestPostList.Pages').
			Wrap(T('The pages the module will be shown on'),
				'div',
				array('class' => 'Info')).
			$this->Form->DropDown('Plugins.LatestPostList.Pages', array(
				'all'             => 'Discussions & Announcements',
				'announcements'   => 'Just Announcements',
				'discussions'     => 'Just Discussions'
			)),
			'li').
		Wrap(
			$this->Form->Label(T('Link'), 'Plugins.LatestPostList.Link').
			Wrap(T('The url of the page the module header points to'),
				'div',
				array('class' => 'Info')).
			Wrap(Url('/', TRUE), 'strong').
			$this->Form->Textbox('Plugins.LatestPostList.Link'),
			'li'),
		'ul');

	echo Wrap(T('Refresh Settings'), 'h3');
	echo Wrap(
		Wrap(T('Animation Preview'), 'h4').
		Wrap(
			Wrap('Sample item 1','li', array('class' => 'Warning')).
			Wrap('Sample item 2','li', array('class' => 'Info')).
			Wrap('Sample item 3','li', array('class' => 'Warning')).
			Wrap('Sample item 4','li', array('class' => 'Info')).
			Wrap('Sample item 5','li', array('class' => 'Warning')),
			'ul',
			array('class' => 'PanelInfo', 'id' => 'LPLPreview')).
		Wrap(
			Wrap('Sample item 1','li', array('class' => 'Warning')).
			Wrap('Sample item 2','li', array('class' => 'Info')).
			Wrap('Sample item 3','li', array('class' => 'Warning')).
			Wrap('Sample item 4','li', array('class' => 'Info')).
			Wrap('Sample item 5','li', array('class' => 'Warning')),
			'ul',
			array('style' => 'display:none', 'id' => 'LPLNewItems')),
		'div',
		array('class' => 'Aside Box'));
	echo Wrap(
		Wrap(
			$this->Form->Label(T('Frequency'), 'Plugins.LatestPostList.Frequency').
			Wrap(T('The number of seconds to wait between checking for updates. Enter 0 to disable this feature.'),
				'div',
				array('class' => 'Info')).
			$this->Form->Textbox('Plugins.LatestPostList.Frequency'),
			'li').
		Wrap(
			$this->Form->Label(T('Animation'), 'Plugins.LatestPostList.Effects').
			Wrap(T('The effect used to update the list. Select "None" to update with no animation.'),
				'div',
				array('class' => 'Info')).
			$this->Form->DropDown('Plugins.LatestPostList.Effects', array(
				'none' => 'None',
				'1'    => 'Rolling Hide',
				'2'    => 'Full Fade',
				'3'    => 'Rolling Fade',
				'4'    => 'Rolling Slide',
				'5'    => 'Rolling Width Fade'
			) ),
			'li'),
		'ul');
		
	echo $this->Form->Close('Save');
	?>
</div>
