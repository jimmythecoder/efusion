"ID","Category ID","Name","Description","URL Name","Sort Order","Created At"
{foreach item=category from=$categories}
"{$category.id}","{$category.category_id}","{$category.name|replace:'"':'\"'}","{$category.description|replace:"\n":' '|replace:"\r":' '|replace:'"':"'"}","{$category.url_name}","{$category.sort_order}","{$category.created_at}"
{/foreach}