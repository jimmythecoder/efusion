<rss version="2.0" 
  xmlns:media="http://search.yahoo.com/mrss"
  xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
    	{foreach item=product from=$products}
        <item>
            <title>{$product.name|escape:'html'}</title>
            <link>{$http_location}/product/{$product.url_name}</link>
            <media:thumbnail url="{get_image_src_resized_and_cropped_to width="130" height="85" filename=$product.image_filename}"/>
            <media:content url="{$http_location}/images/products/{$product.image_filename}"/>
        </item>
        {/foreach}                          
    </channel>
</rss>