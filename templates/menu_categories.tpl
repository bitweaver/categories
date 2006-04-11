{strip}
{if $gBitUser->hasPermission('p_categories_view')}
	<ul>
		<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}index.php">{tr}View Categories{/tr}</a></li>
	</ul>
{/if}
{/strip}
