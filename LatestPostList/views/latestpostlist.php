<?php if (!defined('APPLICATION')) exit(); ?>
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