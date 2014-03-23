{* Edit product variant *}
{assign var="page_title" value="Modify `$product_variant_form.name.value`"}

<fieldset><legend>Product Variation</legend>
{include file="components/form.tpl" form=$product_variant_form form_summary="Product variation form" with_delete=true}
</fieldset>