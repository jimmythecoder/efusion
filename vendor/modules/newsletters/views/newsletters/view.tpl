{* View an email newsletter online *}
{if $newsletter}
	{assign var="page_title" value=$newsletter.subject}
	
	{if $newsletter_available_as_html}
	<iframe id="newsletter-preview">{$newsletter.html_content}<iframe>
	{else}
	<pre>{$newsletter.text_content}</pre>
	{/if}
{else}
	{assign var="page_title" value="Newsletters Available Online"}
	<p>All newsletters sent are shown below, click on one to view it in HTML format.</p>
{/if}