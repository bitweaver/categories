{* $Header: /cvsroot/bitweaver/_bit_categories/modules/mod_whats_related.tpl,v 1.1.1.1.2.1 2005/10/03 05:20:46 wolff_borg Exp $ *}
{strip}
{bitmodule title="$moduleTitle" name="whats_related"}
	<ol>
		{section name=ix loop=$whatsRelated}
			<li>
				<a href="{$whatsRelated[ix].display_url}" title="{$whatsRelated[ix].title} - {$whatsRelated[ix].last_modified|bit_short_datetime}, by {displayname user=$whatsRelated[ix].modifier_user real_name=$whatsRelated[ix].modifier_real_name nolink=1}{if (strlen($whatsRelated[ix].title) > $maxlen) AND ($maxlen > 0)}, {$whatsRelated[ix].title}{/if}">
					{if $maxlen gt 0}
						{$whatsRelated[ix].title|truncate:$maxlen:"...":true}
					{else}
						{$whatsRelated[ix].title}
					{/if}
				</a>
			</li>
		{sectionelse}
			<li></li>
		{/section}
	</ol>
{/bitmodule}
{/strip}
