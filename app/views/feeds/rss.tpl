<?xml-stylesheet type="text/xsl" href="{$http_location}/rss.xsl" version="1.0"?>
<rss version="2.0"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
xmlns:admin="http://webns.net/mvcb/"
xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<channel>
	<title>{$config.content.title} Products</title>
    <description>Latest products from {$config.content.title}</description>
	<link>{$http_location}</link>
    <language>en-nz</language>
    <copyright>Copyright {$smarty.now|date_format:"%Y"} {$config.content.title}</copyright>
	<base_href>{$current_location}</base_href>
    <webMaster>{$config.contact.email}</webMaster>
    <managingEditor>{$config.contact.email}</managingEditor>
    <ttl>720</ttl>
	{foreach item=product from=$products}
	<item>
		<guid isPermaLink='false'>{$product.url_name}</guid>
	    <title>{$product.name|escape:'html'}</title>
	    <description>{$product.description|escape:'html'|truncate:'300'}</description>
		<link>{$http_location}/product/{$product.url_name}</link>
	    <pubDate>{$product.created_at|date_format:'%a, %e %b %Y %T'}</pubDate>
	    {if $product.image_id != DEFAULT_IMAGE_ID}
	    <image>
	    	<url>{get_image_src_resized_and_cropped_to width="130" height="85" filename=$product.image_filename}</url>
	    	<title>{$product.name|escape:'html'}</title>
	    	<link>{$http_location}/product/{$product.url_name}</link>
	    </image>{/if}
	</item>
	{/foreach}
</channel>
</rss>