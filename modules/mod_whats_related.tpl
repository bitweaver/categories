{bitmodule title="$moduleTitle" name="whats_related"}
<table class="other">
{foreach key=key item=item from=$WhatsRelated}
<tr><td><a href="{$key}">{$item}</a></td></tr>
{foreachelse}
<tr><td>&nbsp;</td></tr>
{/foreach}
</table>
{/bitmodule}
