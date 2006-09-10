{* $Header: /cvsroot/bitweaver/_bit_categories/templates/admin_categories.tpl,v 1.11 2006/09/10 17:30:35 squareing Exp $ *}
{strip}
{form legend="Category Settings"}
	<input type="hidden" name="page" value="{$page}" />
	{foreach from=$formFeaturesHelp key=feature item=output}
		<div class="row">
			{formlabel label=`$output.label` for=$feature}
			{forminput}
				{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
				{formhelp note=`$output.note` page=`$output.page`}
			{/forminput}
		</div>
	{/foreach}

	<div class="row submit">
		<input type="submit" name="CategoryTabSubmit" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
