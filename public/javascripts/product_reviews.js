$(document).ready(function()
{
	$('form.ajax').bind('submit',function(){
		var post_vars = {
			comment: $('.comment',this).val(),
			save: true,
			save_by_ajax: true
		};
		
		obj_form = this;
		
		$.post(this.action,post_vars,function(response){
			$('.review-comment',obj_form).html(response);
		});
		
		return false;
	});
	
	$('.review-comment').bind('dblclick',function(){	
		$(this).html('<textarea id="comment" name="review[comment]" rows="8" style="width: 100%;" class="text comment">' + $(this).html() + '</textarea><br /><input type="submit" value="Save changes" /><input id="cancel" type="button" value="Cancel" />');
		
		$('#cancel').bind('click',function(){
			$('.review-comment').html($('#comment').val());
		});
	});
});