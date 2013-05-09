<?php 

if (!defined('APPLICATION')) exit(); 
echo $this->Form->Open();
echo $this->Form->Errors();

?>

<h1><?php echo T($this->Data['Title']); ?></h1>

<div class="Info">
<?php echo T("Enter the question to be asked:"); ?>
</div>

<div class="FilterMenu">

<table style = 'width:400px;'>
<?php
echo("<tr><td>");
echo T("Enter the question to be asked:");
echo "</td><td>";
echo $this->Form->TextBox('Plugins.BotStop.Question');
echo("</td><tr><td>");
echo T("Enter the first acceptable answer:");
echo "</td><td>";
echo $this->Form->TextBox('Plugins.BotStop.Answer1');
echo("</td><tr><td>");
echo T("Enter the second acceptable answer:");
echo "</td><td>";
echo $this->Form->TextBox('Plugins.BotStop.Answer2');
echo("</td><tr><td colspan = 2>");
echo $this->Form->Close('Save');
?>
</td></tr>
</table>
</div>
