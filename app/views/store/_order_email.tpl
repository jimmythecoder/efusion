Qty     |Product                    |Price (x1)      |Sub Total
................................................................
{foreach name=cart item=product key=SKU from=$cart.products}
{capture name="product_variants" assign="product_name_and_variants"}
{$product.name}{if isset($product.variants)} - {foreach name=variants item=variant from=$product.variants}{$variant}{if !$smarty.foreach.variants.last}, {/if}{/foreach}{/if}{/capture}
{$product.quantity|str_pad:8:' ':'right'}{$product_name_and_variants|str_pad:28:' ':'right'}${$product.sale_price|number_format:2|str_pad:16:' ':'right'}${$product.subtotal|number_format:2|str_pad:10:' ':'left'}
{/foreach}
................................................................
                                          Sub Total: ${$cart.price_total|number_format:2|str_pad:10:' ':'left'}
                                           Delivery: ${$total_cost_of_shipping|number_format:2|str_pad:10:' ':'left'}
                                         GST Amount: ${$order.gst_component|number_format:'2'|str_pad:10:' ':'left'}
                                        Grand Total: ${$cart.price_total+$total_cost_of_shipping|number_format:'2'|str_pad:10:' ':'left'}
                                       

Delivery Address:                    
.................                   
{include file="store/_address_as_text.tpl" address=$delivery_address}


Billing Address:
................
{include file="store/_address_as_text.tpl" address=$billing_address}


{if !empty($order_comments)}
Special Requests:
.................
{$order_comments}
{/if}