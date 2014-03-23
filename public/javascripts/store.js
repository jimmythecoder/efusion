var cache = {};
var keystroke_timeout = null;
var search_delay = 500;

$(document).ready(function()
{
	$('#live-search').keyup(function()
	{
		if(keystroke_timeout)
			clearTimeout(keystroke_timeout);
		
		keystroke_timeout = setTimeout(live_search,search_delay);
	});
	
	$('#newsletters .text').bind('focus',function(){
		$(this).css('background-image','none');
	});
});

function live_search()
{
	live_search_input = $('#live-search').get(0);
	
	if(live_search_input.value.length <= 2)
		return;

	escaped_search_string = escape($.trim(live_search_input.value));
	
	if(escaped_search_string in cache)
		return $('#live-search-results').html(cache[escaped_search_string]);
	
	$.get('/services/live-search?q=' + escaped_search_string,function(response){
		cache[escaped_search_string] = $('html',response).text();
		$('#live-search-results').html(cache[escaped_search_string]);
	});
}