
{if $gBitSystem->isPackageActive( 'categories' ) }
	{if $gBitSystem->isFeatureActive( 'categories_path' )}
	<div class="category">
		<div class="path">{$display_catpath.linked}</div>
	</div> {* end category *}
	{/if}
{/if}

