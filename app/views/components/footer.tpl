	
		<div id="footer">
			<div id="left-footer"></div>
			<div id="footer-content">
				<p>&copy; {$smarty.now|date_format:"%Y"} {$config.content.title} 
				| <a accesskey="t" title="Terms and conditions" href="{$http_location}/page/terms-and-conditions">Terms &amp; Conditions</a>
				| <a title="Privacy Policy" href="{$http_location}/page/privacy-policy">Privacy Policy</a>
				| <a title="Contact Us" href="{$http_location}/contact-us">Contact Us</a></p>
			</div>
			<div id="right-footer"></div>
		</div>
	</div>
	
	{if count($application_errors) AND $config.host.environment == 'development'}
		<ol id="application-errors">
			{foreach item=error_types key=error_type from=$application_errors}
				<li class="error-type">{$error_type}</li>
				{foreach item=error_message from=$error_types}
				<li {cycle values=',class="alt"'}>{$error_message}</li>
				{/foreach}
			{/foreach}
		</ol>
	{/if}
</body>
</html>