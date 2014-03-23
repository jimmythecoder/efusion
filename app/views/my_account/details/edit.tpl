{* Edit product *}
{assign var="page_title" value="Update My Account Details"}

Enter your updated details in the form below and click save. To update your password, please enter your current password for security, your newly chosen password, and 
confirm your new password by entering it again. If you leave the Current/New/Confirm password fields blank, 
your password will not be changed.

<fieldset><legend>Personal Details</legend>
{include file="components/form.tpl" form=$account_form form_summary="My Account Details"}
</fieldset>