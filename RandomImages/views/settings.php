<?php if (!defined("APPLICATION")) exit(); 
/* Copyright (C) 2013 Zachary Doll*/

echo Wrap(T('Random Images Settings'), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(
	Wrap($this->Form->Label(T('Maximum Image List Length'), 'Plugins.RandomImages.MaxLength').
		Wrap(T('The maximum number of images that will be shown in the list'),
			'div',
			array('class' => 'Info')).
		$this->Form->Textbox('Plugins.RandomImages.MaxLength'),
		'li'),
	'ul');

echo $this->Form->Close("Save");
?>
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
	<div class="Info">
		Find this plugin helpful? Want to support a freelance developer?<br/>Hit the donate button today. :D
	</div>
	<div class="Info">
		Confused by something? <strong><a href="http://vanillaforums.org/post/discussion?AddonID=1092">Ask a question</a></strong> about RandomImages at the official <a href="http://vanillaforums.org/addon/randomimages-plugin" target="_blank">Vanilla forums</a>.
	</div>
</div>
