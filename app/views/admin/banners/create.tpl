{* Create banner *}
{assign var="page_title" value="Upload a new site banner"}

<fieldset><legend>Banner Information</legend>
{include file="components/form.tpl" form=$banner_form form_summary="Banner upload form"}
</fieldset>