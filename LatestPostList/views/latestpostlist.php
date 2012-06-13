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
<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">
   <?php echo T($this->Data['PluginDescription']); ?>
</div>
<h3><?php echo T('Settings'); ?></h3>
<?php
   echo $this->Form->Open();
   echo $this->Form->Errors();
?>
<ul>
   <li><?php
      echo $this->Form->Label('Number of posts', 'Plugin.LatestPostList.Count');
      echo $this->Form->Textbox('Plugin.LatestPostList.Count');
   ?></li>
   <li><?php
      echo $this->Form->Label('Display on which pages?', 'Plugin.LatestPostList.Pages');
      echo $this->Form->DropDown('Plugin.LatestPostList.Pages',array(
         'all'             => 'Discussions & Announcements',
         'announcements'   => 'Just Announcements',
         'discussions'     => 'Just Discussions'
      ));
   ?></li>
   <li><?php
      echo $this->Form->Label('Frequency of list refresh (in seconds)', 'Plugin.LatestPostList.Frequency');
      echo $this->Form->Textbox('Plugin.LatestPostList.Frequency');
   ?></li>
</ul>
<?php
   echo $this->Form->Close('Save');
?>