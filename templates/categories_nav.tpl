
{if $gBitSystem->isPackageActive( 'categories' ) }
	{if $gBitSystem->isFeatureActive( 'feature_categorypath' )}
	<div class="category">
		<div class="path">{$display_catpath.linked}</div>
	</div> {* end category *}
	{/if}
{/if}

