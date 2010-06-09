{* $Header$ *}
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
