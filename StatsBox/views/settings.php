<?php if (!defined("APPLICATION")) exit(); 
/* Copyright (C) 2013 Zachary Doll*/

echo Wrap(T('Stats Box Settings'), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(
	Wrap($this->Form->CheckBox('Plugins.StatsBox.HideFollows', T('Hide Follows Box')),'li').
	Wrap($this->Form->CheckBox('Plugins.StatsBox.HideViews', T('Hide Views Box')), 'li').
	Wrap($this->Form->CheckBox('Plugins.StatsBox.HideComments', T('Hide Comments Box')), 'li').
	Wrap($this->Form->CheckBox('Plugins.StatsBox.DisableCSS', T('Disable Stats Box default style.')), 'li'),
	'ul',
	array('class' => 'CheckBoxList'));

echo $this->Form->Close("Save");
?>
<div class="Footer">
	<?php
	echo Wrap(T('Feedback'), 'h3');
	?>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="RRCT5277X53HQ">
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
      <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
	<div class="Info">
		Find this plugin helpful? Want to support a freelance developer?<br/>Hit the donate button today. :D
	</div>
	<div class="Info">
		Confused by something? <strong><a href="http://vanillaforums.org/post/discussion?AddonID=1104">Ask a question</a></strong> about Stats Box at the official <a href="http://vanillaforums.org/addon/1104/stats-box" target="_blank">Vanilla forums</a>.
	</div>
</div>
