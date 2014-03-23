{* Create page *}
{assign var="page_title" value="Create new Page"}

Please remember to link to this page so people can find it. You can get the 
full URL by first saving, then clicking the preview link for this page under content management.<br /><br />

<fieldset><legend>Page Information</legend>
{include file="components/form.tpl" form=$content_form form_summary="Content form"}
</fieldset>