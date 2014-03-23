<?php
/**
 * Highlights keywords in a string by wrapping them within <span class="highlight"></span> tags
 * @param array params An array of keywords to highlight
 * @param string params The content to highlight
 */
function smarty_function_highlight_keywords($params, &$smarty)
{
	$result = $params['content'];
	$keywords = $params['keywords'];

    //Highlight matched keywords
    if(is_array($params['keywords']) && is_string($params['content']))
    {
		foreach($keywords as $index => $keyword)
			$result = preg_replace("/(^|[^\w]){1}($keyword)($|[^\w]){1}/i"," <span class=\"highlight\">$2</span> ",$result);
    }
    else
    	$result = $params['content'];
    
	return $result;
}
?>