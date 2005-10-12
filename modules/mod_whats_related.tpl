{* $Header: /cvsroot/bitweaver/_bit_categories/modules/mod_whats_related.tpl,v 1.2 2005/10/12 15:13:49 spiderr Exp $ *}
{strip}
{bitmodule title="$moduleTitle" name="whats_related"}
	<ul>
		{section name=ix loop=$whatsRelated}
			<li>
				{$whatsRelated[ix]}
			</li>
		{/section}
	</ul>
{/bitmodule}
{/strip}
