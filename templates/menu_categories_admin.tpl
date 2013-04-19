{strip}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php">{tr}Admin Categories{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=categories">{tr}Categories{/tr}</a></li>
</ul>
{/strip}
