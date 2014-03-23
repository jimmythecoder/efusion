{* Auto generate a form a form field array *}
<form action="{$smarty.server.REQUEST_URI}" enctype="multipart/form-data" method="{$method|default:"post"}">
<table class="form" cellspacing="0" summary="{$form_summary}">
{foreach from=$form item=item key=field}
{if $item.type != 'hidden'}
	<tr {if NOT $item.null}class="required"{/if}>
		<td>{if $item.type != 'tinyint'}<label {if $item.type != 'checklist'}for="{$item.table}_{$field}"{/if}>{$item.label|default:$field|replace:'_':' '|capitalize}</label>{else}&nbsp;{/if}</td>
		<td>
	{if $item.type == 'varchar'}
		<input class="text" type="text" name="{$item.table}[{$field}]" id="{$item.table}_{$field}" value="{$item.value|escape:"html"}" />
	{elseif $item.type == 'password'}
		<input class="text" type="password" name="{$item.table}[{$field}]" id="{$item.table}_{$field}" value="{$item.value|escape:"html"}" />
	{elseif $item.type == 'decimal'}
		<input class="text numeric" type="text" name="{$item.table}[{$field}]" id="{$item.table}_{$field}" value="{$item.value|number_format:2}" />
	{elseif $item.type == 'double' || $item.type == 'integer'}
		<input class="text numeric" type="text" name="{$item.table}[{$field}]" id="{$item.table}_{$field}" value="{$item.value|escape:"html"}" />
	{elseif $item.type == 'wysiwyg'}
		<textarea class="wysiwyg" name="{$item.table}[{$field}]" id="{$item.table}_{$field}" rows="10" cols="60">{$item.value|escape:"html"}</textarea>
		<script type="text/javascript">
		<!--
		var oFCKeditor = new FCKeditor( "{$item.table}_{$field}" ) ;
		oFCKeditor.Height = 400;
		oFCKeditor.Width = 400;
		oFCKeditor.ReplaceTextarea() ;
		//-->
		</script>
	{elseif $item.type == 'text' || $item.type == 'blob' || $item.type == 'mediumtext' || $item.type == 'longtext' || $item.type == 'mediumblob' || $item.type == 'longblob'}
		<textarea name="{$item.table}[{$field}]" id="{$item.table}_{$field}">{$item.value|escape:"html"}</textarea>
	{elseif $item.type == 'tinyint' || $item.type == 'boolean'}
		<label><input class="checkbox" type="checkbox" name="{$item.table}[{$field}]" id="{$item.table}_{$field}" {if $item.value == 1}checked="checked"{/if} />{$item.label|default:$field|replace:'_':' '|capitalize}</label>
	{elseif $item.type == 'datetime'}
		<input class="text" type="text" name="{$item.table}[{$field}]" value="{$item.value|date_format:'%Y/%m/%d %H:%M:%S'}" id="{$item.table}_{$field}" />
		<input type="button" id="{$item.table}_{$field}_trigger" value="..." />
		<script type="text/javascript">
			  Calendar.setup(
			    {literal}{{/literal}
			      inputField  : '{$item.table}_{$field}',       
			      ifFormat    : "%Y/%m/%d %H:%M:%S", 
			      button      : '{$item.table}_{$field}_trigger',  
			      showsTime   : true,
			      singleClick : false			
			    {literal}}{/literal}
			  );
		</script>
	{elseif $item.type == 'date'}
		<input class="text" type="text" name="{$item.table}[{$field}]" value="{$item.value|date_format:'%Y/%m/%d'}" id="{$item.table}_{$field}" />
		<input type="button" id="{$item.table}_{$field}_trigger" value="..." />
		<script type="text/javascript">
			  Calendar.setup(
			    {literal}{{/literal}
			      inputField  : '{$item.table}_{$field}',       
			      ifFormat    : "%Y/%m/%d", 
			      button      : '{$item.table}_{$field}_trigger',
			      singleClick : false			
			    {literal}}{/literal}
			  );
		</script>
	{elseif $item.type == 'file'}
		<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
		<input class="file" type="file" name="{$field}" id="{$item.table}_{$field}" value="{$item.value}" />
	{elseif $item.type == 'image'}
		<img class="thumbnail" alt="{$item.table} image thumbnail" src="{get_image_src_resized_to width="130" height="85" filename=$item.image.filename|default:"image_not_available.jpg"}" /><br />
		<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
		<input class="file" type="file" name="{$field}" id="{$item.table}_{$field}" value="{$item.value}" />
	{elseif $item.type == 'select'}
		<select class="select" name="{$item.table}[{$field}]" id="{$item.table}_{$field}">
			{html_options options=$item.options selected=$item.value}
		</select>
	{elseif $item.type == 'recursive_option_select'}
		<select class="select" name="{$item.table}[{$field}]" id="{$item.table}_{$field}">
			{include file="components/recursive_option_select.tpl" categories=$item.options selected=$item.value}
		</select>
	{elseif $item.type == 'comment'}
		{$item.value}
	{elseif $item.type == 'checklist'}
		<ul class="checklist">
		{foreach item=option from=$item.options}
			<li {cycle values=',class="alt"'}><label for="{$item.table}_variant_{$option.id}"><input id="{$item.table}_variant_{$option.id}" name="{$item.table}[variant][{$option.id}]" type="checkbox" value="1" {if $item.values AND in_array($option.id,$item.values)}checked="checked"{/if} /> {$option.name}</label></li>
		{/foreach}
		</ul>
	{else}
		<input class="text" type="text" name="{$item.table}[{$field}]" id="{$item.table}_{$field}" value="{$item.value}" />
	{/if}
	{if NOT $item.null}<em>*</em>{/if}</td>
	</tr>
{else}
<tr class="hidden">
	<td colspan="2"><input type="hidden" name="{$item.table}[{$field}]" id="{$item.table}_{$field}" value="{$item.value}" /></td>
</tr>
{/if}
{/foreach}
<tr>
	<td>&nbsp;</td>
	<td><input class="button" type="submit" name="save" id="save" value="Save" />
		{if isset($with_delete) AND $with_delete == true}<input class="button" type="submit" name="delete" id="delete" value="Delete" onclick="return confirm('Are you sure you wish to delete this item?')" />{/if}
		<input class="button" type="submit" name="cancel" id="cancel" value="Cancel" onclick="window.history.go(-1);return false;" /></td>
</tr>
</table>
</form>