{* Edit product variant *}
{assign var="page_title" value="Modify `$variant_group_form.name.value`"}

<fieldset><legend>Product Variation</legend>
{include file="components/form.tpl" form=$variant_group_form form_summary="Product variation form" with_delete=true}
</fieldset>