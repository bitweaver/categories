{strip}
{if $gBitSystem->isPackageActive( 'categories' ) and (count($categories) gt 0 or $gBitUser->hasPermission( 'bit_p_admin_categories' ))}
	<div class="row">
		{formlabel label="Pick Categories"}
		{forminput}
			{section name=ix loop=$categories}
				<label>
					<input type="checkbox" name="cat_categories[]" value="{$categories[ix].category_id}" {if $categories[ix].incat == 'y'}checked="checked"{/if} /> 
					{$categories[ix].categpath}
				</label><br />
			{/section}
			{if $gBitUser->hasPermission( 'bit_p_admin_categories' )}
				<br /><a href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php">{tr}Admin categories{/tr}</a>
			{/if}
		{/forminput}
	</div>
{/if}
{/strip}
