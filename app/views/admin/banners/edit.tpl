{* Update banner *}
{assign var="page_title" value="Update banner"}

<fieldset><legend>Banner Information</legend>
{include file="components/form.tpl" form=$banner_form form_summary="Banner upload form" with_delete=$allow_delete}
</fieldset>