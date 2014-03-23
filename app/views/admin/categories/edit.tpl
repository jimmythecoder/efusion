{* Edit category *}
{assign var="page_title" value="Modify Category: `$category_form.name.value`"}

<fieldset><legend>Category Information</legend>
{include file="components/form.tpl" form=$category_form form_summary="Category Form" with_delete=$allow_delete}
</fieldset>