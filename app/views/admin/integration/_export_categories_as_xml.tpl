<?xml version="1.0" ?>
<categories>
	{foreach item=category from=$categories}
	<category>
		<id>{$category.id}</id>
		<category_id>{$category.category_id}</category_id>
		<name><![CDATA[{$category.name}]]></name>
		<description><![CDATA[{$category.description}]]></description>
		<url_name>{$category.url_name}</url_name>
		<sort_order>{$category.sort_order}</sort_order>
		<created_at>{$category.created_at}</created_at>
	</category>
	{/foreach}
</categories>