$(document).ready(function()
{
	$('#star-rating a').bind('click',function()
	{
		var selected_rating = parseInt(this.innerHTML);
		var selected_rating_as_percentage = selected_rating * 20;
		
		$('#rating').attr('value',selected_rating);
		$('#current-rating').css('width',selected_rating_as_percentage + '%');
		
		return false;
	});
	
	$('#review-form').bind('submit',function()
	{
		if(!validate_form())
			return false;
		
		var post_params = {
			product_id: $('#product_id').val(),
			rating: $('#rating').val(),	
			comment: $('#comment').val(),
			submit_review: true,
			method: 'ajax'
		};
		
		$.post(this.action,post_params,function(response)
		{	
			var successfull = parseInt($('successfull',response).text());
			$('#flash-errors').remove();
			
			if(successfull)
			{
				var selected_rating_as_percentage = $('#rating').val() * 20;
				var review_html = '<li><ul class="star-rating"><li class="current-rating" style="width:' + selected_rating_as_percentage + '%;">' + $('#rating').val() + '/5</li></ul>';
				review_html += '<p>' + $('#comment').val() + '</p>';
				review_html += '<small>Reviewed today</small></li>';

				$('#user-submitted-reviews').prepend(review_html).fadeIn('slow');
				
				$('#review-form').fadeOut('slow');
				$('#no-reviews-note').hide();
			}
			else
			{
				var error_display_html = '<ul id="flash-errors">';
				
				$('error',response).each(function(){
					error_display_html += '<li>' + $(this).text() + '</li>';
				});
				
				error_display_html += '</ul>';
				
				$('#reviews').prepend(error_display_html);
			}
		});
		
		return false;
	});
	
	var comment_box_cleared = false;
	
	$('#comment').bind('focus',function(){
		if(!comment_box_cleared)
		{
			this.value = '';
			comment_box_cleared = true;
		}
	});
});