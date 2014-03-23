{* Create product *}
{assign var="page_title" value="Add a new product"}

<fieldset><legend>Product Information</legend>
{include file="components/form.tpl" form=$product_form form_summary="Product form"}
</fieldset>