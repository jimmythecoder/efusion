<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{$page_title|default:$controller.action|escape:"html"} at {$config.content.title}</title>
	{if isset($meta_description)}<meta name="description" content="{$meta_description|strip_tags|escape|truncate:300}" />{/if}
	{if isset($meta_keywords)}<meta name="keywords" content="{$meta_keywords|strip_tags|escape:"html"}" />{/if}

	<base href="{$current_location}/" />
	
	<link rel="stylesheet" type="text/css" href="{$current_location}/stylesheets/application.css" media="all" />
	<link rel="stylesheet" type="text/css" href="{$current_location}/stylesheets/print.css" media="print" />
{section name="file" loop=$stylesheet_files}
	<link rel="stylesheet" type="text/css" href="{$current_location}/stylesheets/{$stylesheet_files[file]}.css" media="all" />
{/section}
	<!--[if lt IE 7]>
		<link rel="stylesheet" type="text/css" title="iehacks" href="{$current_location}/stylesheets/iehacks.css" />
	<![endif]-->

	<script type="text/javascript" src="{$current_location}/javascripts/lib/jquery.js"></script>
	<script type="text/javascript" src="{$current_location}/javascripts/application.js"></script>
{section name="file" loop=$javascript_files}
	<script type="text/javascript" src="{$current_location}/javascripts/{$javascript_files[file]}.js"></script>
{/section}

{if $controller.name == 'store'}
	<link rel="alternate" type="application/rss+xml" href="{$current_location}/feeds/rss" title="Latest Products" />
	<link rel="alternate" type="application/rss+xml" title="Images via piclens" href="{$current_location}/feeds/media" id="piclens" />
{/if}
</head>
<body>
	<div id="container">
		<div id="header">
			<div id="banner" style="background-image:url('{$current_location}/images/banners/{$config.content.banner.image.filename}');"></div>
		</div>