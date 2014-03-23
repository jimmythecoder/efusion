{if !empty($address.company)}{$address.company}, {/if}{$address.first_name} {$address.last_name}
{$address.street}
{$address.suburb}
{$address.city}{if !empty($address.post_code)}, {$address.post_code}{/if}

{$address.country.name|upper}