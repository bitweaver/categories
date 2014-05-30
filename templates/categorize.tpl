{strip}
{jstab title="Categorize"}
	{legend legend="Categorize"}
		{if $gBitSystem->isPackageActive( 'categories' ) and (count($categories) gt 0 or $gBitUser->hasPermission( 'p_categories_admin' ))}
			<div class="form-group">
				{formlabel label="Pick Categories"}
				{forminput}
					{section name=ix loop=$categories}
						<label>
							<input type="checkbox" name="cat_categories[]" value="{$categories[ix].category_id}" {if $categories[ix].incat == 'y'}checked="checked"{/if} /> 
							{$categories[ix].categpath}
						</label><br />
					{/section}
					{if $gBitUser->hasPermission( 'p_categories_admin' )}
						<br /><a href="{$smarty.const.CATEGORIES_PKG_URL}admin/index.php">{tr}Admin categories{/tr}</a>
					{/if}
				{/forminput}
			</div>
		{/if}
	{/legend}
{/jstab}
{/strip}
