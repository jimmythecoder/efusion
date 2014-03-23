{* Edit administrator *}
{assign var="page_title" value="Modify Account"}

To update your password, please enter your current password for security, your newly chosen password, and 
confirm your new password by entering it again. If you leave the Current/New/Confirm password fields blank, 
your password will not be changed. You are not allowed to modify another administrators password.

<fieldset><legend>Account Information</legend>
{include file="components/form.tpl" form=$account_form form_summary="Administrator account form" with_delete=$allow_delete}
</fieldset>