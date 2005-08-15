{if $gBitSystem->isPackageActive( 'categories' ) }
	{if $gBitSystem->isFeatureActive( 'feature_categorypath' )}
	<div class="category">{$display_catpath.linked}</div>
	{/if}
	{if $gBitSystem->isFeatureActive( 'feature_categoryobjects' )}
	<div class="category">{$display_catobjects}</div>
	{/if}
{/if}