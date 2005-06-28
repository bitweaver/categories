{strip}
<ul>
	{if $gBitUser->hasPermission('bit_p_view_categories')}
	<li><a class="item" href="{$gBitLoc.CATEGORIES_PKG_URL}index.php">{tr}View Categories{/tr}</a></li>
	{/if}
</ul>
{/strip}
