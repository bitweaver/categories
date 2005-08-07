{strip}
<ul>
	{if $gBitSystem->isPackageActive( 'categories' )}
		<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php">{tr}Admin Categories{/tr}</a></li>
	{/if}
</ul>
{/strip}