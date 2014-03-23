{* Edit page *}
{assign var="page_title" value="Modify Page: `$content_form.title.value`"}

<fieldset><legend>Page Information</legend>
{include file="components/form.tpl" form=$content_form form_summary="Content Form" with_delete=$allow_delete}
</fieldset>