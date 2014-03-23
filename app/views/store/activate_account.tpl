{* Activate a new customers email address *}
{assign var="page_title" value="Verifying your email address"}

{if $activation_successfull}
	<p>Your E-Mail address has been verified, you are now eligable to submit user reviews.</p>

	<a href="catalog">Start shopping &gt;&gt;</a>
{else}
	<p>The activation key on the end of your URL is invalid, please make sure you copy and paste
	the entire link into your browser, it may have wrapped in your email client, it must be on 1 line.</p>
{/if}