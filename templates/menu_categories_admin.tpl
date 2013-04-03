{strip}
<li class="dropdown-submenu">
    <a href="#" onclick="return(false);" tabindex="-1" class="sub-menu-root">{tr}{$smarty.const.{$smarty.const.CATEGORIES_PKG_NAME|capitalize}{/tr}</a>
	<ul class="dropdown-menu sub-menu">
		<li><a class="item" href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php">{tr}Admin Categories{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=categories">{tr}Categories Settings{/tr}</a></li>
	</ul>
</li>
{/strip}
