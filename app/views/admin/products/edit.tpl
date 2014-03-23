{* Edit product *}
{assign var="page_title" value="Modify `$product_form.name.value`"}

<fieldset><legend>Product Information</legend>
{include file="components/form.tpl" form=$product_form form_summary="Product form" with_delete=$allow_delete}
</fieldset>