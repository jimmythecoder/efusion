{* Edit customer *}
{assign var="page_title" value="Change customers details"}

<fieldset><legend>Account Information</legend>
{include file="components/form.tpl" form=$account_form form_summary="Customer account form" with_delete=$allow_delete}
</fieldset>