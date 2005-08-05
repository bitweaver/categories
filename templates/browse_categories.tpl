{* $Header: /cvsroot/bitweaver/_bit_categories/templates/browse_categories.tpl,v 1.1.1.1.2.1 2005/08/05 22:59:52 squareing Exp $ *}
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

<div class="content">
<h2>{tr}Current category{/tr}: {$path}</h2>
{* Don't show 'TOP' button if we already on TOP but reserve space to avoid visual effects on change view *}
<div class="navbar" style="visibility:{if $parent_id ne '0'}visible{else}hidden{/if}">
  {tr}go to{/tr} <a class="linkbut" href="{$smarty.const.CATEGORIES_PKG_URL}index.php?parent_id=0">{tr}top{/tr}</a>
</div>

{* Show tree *}
{ * If not TOP level, append '..' as first node :) *}
{if $parent_id ne '0'}
<div class="navbar">
  {tr}go up{/tr} <a class="linkbut" href="{$smarty.const.CATEGORIES_PKG_URL}index.php?parent_id={$father}" title="Upper level">{tr}one level{/tr}</a>
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

</div>
</div>
