{strip}
<ul>
	{if $gBitSystem->isPackageActive( 'categories' )}
		<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php">{tr}Admin Categories{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=categories">{tr}Categories Settings{/tr}</a></li>
	{/if}
</ul>
{/strip}
