{* Activate a banner *}
{assign var="page_title" value="Activate banner"}

Please confirm you wish to activate the banner <strong>{$banner.name}</strong>

<form action="{$https_location}/admin/banners/activate/{$banner.id}" method="post">
<input type="submit" name="confirm" value="Confirm" />
<input type="submit" name="cancel" value="Cancel" />
</form>