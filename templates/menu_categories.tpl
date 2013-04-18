{strip}
{if $gBitUser->hasPermission('p_categories_view')}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}index.php">{tr}View Categories{/tr}</a></li>
</ul>
{/if}
{/strip}
