{if !empty($address.company)}{$address.company|escape:'html'}, {/if}
{$address.first_name|escape:'html'} {$address.last_name|escape:'html'}<br />
{$address.street|escape:'html'} <br />
{$address.suburb|escape:'html'}<br />
{$address.city}{if !empty($address.post_code)}, {$address.post_code}{/if}<br />
{$address.country.name|upper}