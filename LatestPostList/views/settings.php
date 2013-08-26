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
		echo Wrap(T($this->Data['Title']), 'h3');
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
				'both'          => 'Discussions & Announcements',
				'announcements' => 'Just Announcements',
				'discussions'   => 'Just Discussions',
				'all'           => 'Every frontend page'
			)),
			'li').
		Wrap(
			$this->Form->Label(T('Link'), 'Plugins.LatestPostList.Link').
			Wrap(T('The url of the page the module header points to; leave blank if you don\'t want a link'),
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
<div class="Footer">
	<?php
	echo Wrap(T('Feedback'), 'h3');
	?>
	<div class="Aside Box">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="text-align: center; margin-top: 20px; margin-bottom: 10px;">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="3Y72SHPRN4K3S">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	</div>
	<?php
	echo Wrap('Find this plugin helpful? Want to support a freelance developer?<br/>Click the donate button to buy me a beer. :D', 'div', array('class' => 'Info')); 
	echo Wrap('Confused by something? <strong><a href="http://vanillaforums.org/post/discussion?AddonID=923">Ask a question</a></strong> about Latest Post List on the official <a href="http://vanillaforums.org/discussions" target="_blank">Vanilla forums</a>.', 'div', array('class' => 'Info'));
	?>
</div>
