$(document).ready(function()
{
	$('#delivery-addresses').find('input[type=radio]').bind('click',function()
	{
		//Recalculate shipping costs
		var deliver_to_address_book_id = this.value;
		var delivery_weight = parseFloat($('#delivery_weight').html());
		var selected_delivery_address_li = $(this).parent('li');
		
		$.get('/services/get-delivery-charges',{'deliver-to-address-book-id': deliver_to_address_book_id, 'weight': delivery_weight},function(response)
		{
			//Show user address has changed
			$('#delivery-addresses li.selected').removeClass('selected');
			$(selected_delivery_address_li).addClass('selected');
			
			//Update delivery information table
			$('#delivery_from').html($('response>from',response).text());
			$('#delivery_to').html($('response>to',response).text());
			$('#delivery_zone').html($('response>zone',response).text());
			$('#delivery_discount').html('$' +  $('response>discount_amount',response).text());
			$('#delivery_total').html('$' + $('response>total',response).text());
		});
	});
});