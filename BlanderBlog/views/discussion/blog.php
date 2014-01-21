<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::Session(); 
if (!function_exists('WriteComment'))
   include $this->FetchViewLocation('helper_functions', 'discussion');

// Wrap the discussion related content in a div.
echo '<div class="MessageList Discussion">';

// Write the page title.
echo '<!-- Page Title -->
<div id="Item_0" class="PageTitle">';

echo '<div class="Options">';

//$this->FireEvent('BeforeDiscussionOptions');
WriteBookmarkLink();
WriteDiscussionOptions();
WriteAdminCheck();

echo '</div>';

echo '<h1>'.$this->Data('Discussion.Name').'</h1>';

echo "</div>\n\n";

//$this->FireEvent('AfterDiscussionTitle');

// Write the initial discussion.
if ($this->Data('Page') == 1) {
$UserPhotoFirst = C('Vanilla.Comment.UserPhotoFirst', TRUE);

$Discussion = $this->Data('Discussion');
$Author = Gdn::UserModel()->GetID($Discussion->InsertUserID);

// Prep event args.
$CssClass = CssClass($Discussion, FALSE);
// $this->EventArguments['Discussion'] = &$Discussion;
// $this->EventArguments['Author'] = &$Author;
// $this->EventArguments['CssClass'] = &$CssClass;

// DEPRECATED ARGUMENTS (as of 2.1)
// $this->EventArguments['Object'] = &$Discussion; 
// $this->EventArguments['Type'] = 'Discussion';

// Discussion template event
// $this->FireEvent('BeforeDiscussionDisplay');
?>
<div id="<?php echo 'Discussion_'.$Discussion->DiscussionID; ?>" class="<?php echo $CssClass; ?>">
   <div class="Discussion">
      <div class="Item-Header DiscussionHeader">
         <div class="AuthorWrap">
            <span class="Author">
               <?php
               echo UserAnchor($Author, 'Username');
               ?>
            </span>
            <span class="AuthorInfo">
               <?php
               echo WrapIf(htmlspecialchars(GetValue('Title', $Author)), 'span', array('class' => 'MItem AuthorTitle'));
               echo WrapIf(htmlspecialchars(GetValue('Location', $Author)), 'span', array('class' => 'MItem AuthorLocation'));
               // $this->FireEvent('AuthorInfo'); 
               ?>
            </span>
         </div>
         <div class="Meta DiscussionMeta">
            <span class="MItem DateCreated">
               <?php
               echo Anchor(Gdn_Format::Date($Discussion->DateInserted, 'html'), $Discussion->Url, 'Permalink', array('rel' => 'nofollow'));
               ?>
            </span>
            <?php
               echo DateUpdated($Discussion, array('<span class="MItem">', '</span>'));
            ?>
            <?php
            // Include source if one was set
            if ($Source = GetValue('Source', $Discussion))
               echo ' '.Wrap(sprintf(T('via %s'), T($Source.' Source', $Source)), 'span', array('class' => 'MItem MItem-Source')).' ';
            
            ?>
         </div>
      </div>
      <?php // $this->FireEvent('BeforeDiscussionBody'); ?>
      <div class="Item-BodyWrap">
         <div class="Item-Body">
            <div class="Message">   
               <?php
                  echo FormatBody($Discussion);
               ?>
            </div>
            <?php 
            // $this->FireEvent('AfterDiscussionBody');
            WriteReactions($Discussion);
            ?>
         </div>
      </div>
   </div>
</div>
</div>
<?php
   // $this->FireEvent('AfterDiscussion');
} else {
   echo '</div>'; // close discussion wrap
}

echo '<div class="CommentsWrap">';

// Write the comments.
$this->Pager->Wrapper = '<span %1$s>%2$s</span>';
echo '<span class="BeforeCommentHeading">';
// $this->FireEvent('CommentHeading');
echo $this->Pager->ToString('less');
echo '</span>';

echo '<div class="DataBox DataBox-Comments">';
if ($this->Data('Comments')->NumRows() > 0)
	echo '<h2 class="CommentHeading">'.$this->Data('_CommentsHeader', T('Comments')).'</h2>';
?>
<ul class="MessageList DataList Comments">
	<?php
	//$this->FireEvent('BeforeCommentsRender');
	if (!function_exists('WriteComment'))
	   include($this->FetchViewLocation('helper_functions', 'discussion'));

	$CurrentOffset = $this->Offset;

	//$this->EventArguments['CurrentOffset'] = &$CurrentOffset;
	//$this->FireEvent('BeforeFirstComment');

	// Only prints individual comment list items
	$Comments = $this->Data('Comments')->Result();
	foreach ($Comments as $Comment) {
	   if (is_numeric($Comment->CommentID))
		  $CurrentOffset++;
	   $this->CurrentComment = $Comment;
	   WriteComment($Comment, $this, $Session, $CurrentOffset);
	}
	
	?>
</ul>
<?php
// $this->FireEvent('AfterComments');
if($this->Pager->LastPage()) {
   $LastCommentID = $this->AddDefinition('LastCommentID');
   if(!$LastCommentID || $this->Data['Discussion']->LastCommentID > $LastCommentID)
      $this->AddDefinition('LastCommentID', (int)$this->Data['Discussion']->LastCommentID);
   $this->AddDefinition('Vanilla_Comments_AutoRefresh', Gdn::Config('Vanilla.Comments.AutoRefresh', 0));
}
echo '</div>';

echo '<div class="P PagerWrap">';
$this->Pager->Wrapper = '<div %1$s>%2$s</div>';
echo $this->Pager->ToString('more');
echo '</div>';
echo '</div>';

WriteCommentForm();