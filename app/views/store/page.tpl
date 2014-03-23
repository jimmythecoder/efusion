{* Dynamic Page Content *}
{assign var="page_heading" value=$content.title|default:'No Title'}
{assign var="page_title" value=$content.page_title|default:$page_heading}
{assign var="meta_keywords" value=$content.keywords}
{assign var="meta_description" value=$content.description}

{$content.content}