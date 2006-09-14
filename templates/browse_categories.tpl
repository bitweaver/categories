{* $Header: /cvsroot/bitweaver/_bit_categories/templates/browse_categories.tpl,v 1.5 2006/09/14 20:23:48 squareing Exp $ *}
<div class="floaticon">{bithelp}</div>
<div class="display category">
<div class="header">
<h1>{tr}Categories{/tr}</h1>
</div>

<div class="body">

<form method="post" action="{$smarty.const.CATEGORIES_PKG_URL}index.php">
<table class="find">
  <tr>
    <td>{tr}search category{/tr}</td>
    <td><input type="text" name="find" value="{$find|escape}" size="35" /></td>
    <td>{tr}deep{/tr}</td>
    <td><input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}/></td>
    <td><input type="submit" value="{tr}find{/tr}" name="search" />
    <input type="hidden" name="parent_id" value="{$parent_id|escape}" />
    <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" /></td>
  </tr>
</table>
</form>

<h3 class="boxtitle">{tr}Current category{/tr}: {$path}</h3>
<div class="boxcontent">

{if $parent_id ne '1'}
<div class="navbar above">
  {tr}go to{/tr} <a href="{$smarty.const.CATEGORIES_PKG_URL}index.php?parent_id=1">{tr}top{/tr}</a>
  {tr}go up{/tr} <a href="{$smarty.const.CATEGORIES_PKG_URL}index.php?parent_id={$father}" title="Upper level">{tr}one level{/tr}</a>
</div>
{/if}
{$tree}
</div>
<br />
{* List of object in category *}

<h2>{tr}Objects{/tr} ({$cantobjects})</h2>
{if $cantobjects > 0}
<table class="data">
<tr><th>{tr}Link{/tr}</th><th>{tr}Description{/tr}</th></tr>
{cycle values="even,odd" print=false}
{section name=ix loop=$objects}
<tr class="{cycle}">
  <td>
    <a href="{$objects[ix].href}">{$objects[ix].name}</a><br />
    ({tr}{$objects[ix].type}{/tr})
  </td>
  <td>{$objects[ix].description}</td>
</tr>
{/section}
</table>

{pagination parent_id=$parent_id}
{/if}

</div><!-- end .body -->
</div>
