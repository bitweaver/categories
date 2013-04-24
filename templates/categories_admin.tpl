{* $Header$ *}
<div class="floaticon">{bithelp}</div>
<div class="admin category">
<div class="header">
<h1>{tr}Admin categories{/tr}</h1>
</div>

<div class="body">

<div class="admin box">
<h3>{tr}Current category{/tr}: {$catInfo.path}</h3>
<div class="boxcontent">

{if $parent_id ne '1'}
<div class="navbar above">
  {tr}go to{/tr} <a href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php?parent_id=1">{tr}top{/tr}</a>
  {tr}go up{/tr} <a href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php?parent_id={$catInfo.father}" title="Upper level">{tr}one level{/tr}</a>
</div>
{/if}
{$tree}
</div>
</div>
</div>

<a name="editcreate"></a>
{if $category_id > 0}
  <h2>{tr}Edit this category:{/tr} {$name}</a>
 {else}
  <h2>{tr}Add new category{/tr}</h2>
{/if}
<form action="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php" method="post">
<table class="panel">
 {if $category_id > 0}
  <tr><td colspan="2">
    <a href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php?parent_id={$parent_id}#editcreate">{tr}create new{/tr}</a>
  </td></tr>
 {/if}
 <tr><td>
  <input type="hidden" name="category_id" value="{$category_id|escape}" />
  {tr}Parent{/tr}
  </td><td>
  <select name="parent_id">
    {section name=ix loop=$categories}
    <option value="{$categories[ix].category_id|escape}" {if $categories[ix].category_id eq $parent_id}selected="selected"{/if}>{$categories[ix].name}</option>
    {/section}
  </select>
  </td></tr>
 <tr><td>{tr}Name{/tr}</td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
 <tr><td>{tr}Description{/tr}</td><td><textarea rows="4" cols="16" name="description">{$description|escape}</textarea></td></tr>
 <tr class="panelsubmitrow"><td colspan="2"><input type="submit" class="btn" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<div class="admin box">
<h3 class="boxtitle">{tr}Current category{/tr}: {$catInfo.path}</h3>

<h2>{tr}Add objects to category{/tr}: {$catInfo.path}</h2>
<table class="find">
  <tr><td>{tr}Find{/tr}</td>
  <td>
    <form method="get" action="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php">
    <input type="text" name="find_objects" />
    <input type="hidden" name="parent_id" value="{$parent_id|escape}" />
    <input type="submit" class="btn" value="{tr}filter{/tr}" name="search_objects" />
    <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
    <input type="hidden" name="offset" value="{$offset|escape}" />
    <input type="hidden" name="find" value="{$find|escape}" />
    </form>
  </td>
  </tr>
</table>

<form action="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php" method="post">
<input type="hidden" name="parent_id" value="{$parent_id|escape}" />
  <table class="panel">
  {if $gBitSystem->isPackageActive( 'wiki' )}
    <tr>
      <td>{tr}page{/tr}:</td>
      <td><select name="class_content[]" multiple="multiple" size="5">{section name=ix loop=$pages}<option value="{$pages[ix].content_id}">{$pages[ix].title|escape|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addpage" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'articles' )}
    <tr>
      <td>{tr}article{/tr}:</td>
      <td><select name="article_id">{section name=ix loop=$articles}<option value="{$articles[ix].article_id|escape}">{$articles[ix].title|escape|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addarticle" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'blogs' )}
    <tr>
      <td>{tr}blog{/tr}:</td>
      <td><select name="blog_id">{section name=ix loop=$blogs}<option value="{$blogs[ix].blog_id|escape}">{$blogs[ix].title|escape|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addblog" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'directory' )}
    <tr>
      <td>{tr}directory{/tr}:</td>
      <td><select name="directoryId">{section name=ix loop=$directories}<option value="{$directories[ix].category_id|escape}">{$directories[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="adddirectory" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'galleries' )}
    <tr>
      <td>{tr}image gal{/tr}:</td>
      <td><select name="gallery_id">{section name=ix loop=$galleries}<option value="{$galleries[ix].gallery_id|escape}">{$galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addgallery" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'file_galleries' )}
    <tr>
      <td>{tr}file gal{/tr}:</td>
      <td><select name="file_gallery_id">{section name=ix loop=$file_galleries}<option value="{$file_galleries[ix].gallery_id|escape}">{$file_galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addfilegallery" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'tiki_forums' )}
    <tr>
      <td>{tr}forum{/tr}:</td>
      <td><select name="forum_id">{section name=ix loop=$forums}<option value="{$forums[ix].forum_id|escape}">{$forums[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addforum" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'polls' )}
    <tr>
      <td>{tr}poll{/tr}:</td>
      <td><select name="poll_id">{section name=ix loop=$polls}<option value="{$polls[ix].poll_id|escape}">{$polls[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addpoll" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'faqs' )}
    <tr>
      <td>{tr}faq{/tr}:</td>
      <td><select name="faq_id">{section name=ix loop=$faqs}<option value="{$faqs[ix].faq_id|escape}">{$faqs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addfaq" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'trackers' )}
   <tr>
      <td>{tr}tracker{/tr}:</td>
      <td><select name="tracker_id">{section name=ix loop=$trackers}<option value="{$trackers[ix].tracker_id|escape}">{$trackers[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addtracker" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  {if $gBitSystem->isPackageActive( 'quizzes' )}
    <tr>
      <td>{tr}quiz{/tr}:</td>
      <td><select name="quiz_id">{section name=ix loop=$quizzes}<option value="{$quizzes[ix].quiz_id|escape}">{$quizzes[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
      <td><input type="submit" class="btn" name="addquiz" value="{tr}add{/tr}" /></td>
    </tr>
  {/if}
  </table>
</form>

<h2>{tr}Objects in category{/tr}</h2>
<table class="find">
  <tr><td>{tr}Find{/tr}</td>
  <td>
    <form method="get" action="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php">
    <input type="text" name="find" />
    <input type="hidden" name="parent_id" value="{$parent_id|escape}" />
    <input type="submit" class="btn" value="{tr}find{/tr}" name="search" />
    <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
    <input type="hidden" name="find_objects" value="{$find_objects|escape}" />
    </form>
  </td>
  </tr>
</table>

<table class="table data">
  <tr>
    <th><a href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php?parent_id={$parent_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
    <th><a href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php?parent_id={$parent_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></th>
    <th>{tr}Description{/tr}</th>
    <th>{tr}Remove{/tr}</th>
  </tr>
{cycle values="even,odd" print=false}
  {section name=ix loop=$objects}
  <tr class="{cycle}">
    <td><a href="{$objects[ix].href}" title="{$objects[ix].name}">{$objects[ix].name|truncate:25:"(...)":true}</a></td>
    <td>{$objects[ix].object_type}</td>
    <td>{$objects[ix].description}</td>
    <td align="right"><a href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php?parent_id={$parent_id}&amp;removeObject={$objects[ix].cat_object_id}&amp;fromCateg={$parent_id}" title="{tr}Delete item from category?{/tr}" onclick="return confirm('{tr}Are you sure you want to remove {$objects[ix].name} from {$catInfo.name|escape}?{/tr}')">{booticon iname="icon-trash" ipackage="icons" iexplain="remove"}</a></td>
  </tr>
  {sectionelse}
  <tr class="norecords">
  	  <td colspan="3">{tr}no records found{/tr}</td></tr>
  {/section}
</table>

{pagination parent_id=$catInfo.father}

</div> <!-- end .body -->
</div> <!-- end .categories -->
