<?php if (!defined('APPLICATION')) exit();
$CategoryID = isset($this->_Sender->CategoryID) ? $this->_Sender->CategoryID : '';

if ($this->_Categories !== FALSE) {
var_dump($this->_Categories->Result());
	?>
	<div id="CategoryNavigation">
		<ul class="CatNavBar Root">
			<li><?php echo Anchor(T('Home'), 'categories/all'); echo '<span class="Count">'.number_format($CountDiscussions).'</span>';?></li>
			<?php
			$MaxDepth = C('Vanilla.Categories.MaxDisplayDepth');
			$DoHeadings = C('Vanilla.Categories.DoHeadings');

			foreach ($this->_Categories->Result() as $Category) {
				if ($Category->CategoryID < 0 || $MaxDepth > 0 && $Category->Depth > $MaxDepth) {
					continue;
				}
				if($Category->ParentCategoryID > 0) {
					$

				$CssClass = 'Depth'.$Category->Depth.($CategoryID == $Category->CategoryID ? ' Active' : '');

				echo '<li class="'.$CssClass.'">';

				if ($DoHeadings && $Category->Depth == 1) {
					echo Gdn_Format::Text($Category->Name);
				}
				else {
					echo Anchor(($Category->Depth > 1 ? '↳ ' : '').Gdn_Format::Text($Category->Name), '/categories/'.rawurlencode($Category->UrlCode)).'<span class="Count">'.number_format($Category->CountAllDiscussions).'</span>';
				}
				echo "</li>\n";
			}
			?>
		</ul>
	</div>
	<?php
}