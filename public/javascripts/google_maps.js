$(document).ready(function()
{
	//Get orders via ajax
	$.get('/services/order-geocoding-data',{},function(response)
	{
		$('#map').removeClass('loading');
		
		var map = new GMap2(document.getElementById("map"));
		
		map.addControl(new GSmallMapControl());
		map.addControl(new GMapTypeControl());
		map.setCenter(new GLatLng(-38.530979, 174.814453), 6);	
		
		function wheel_zoom(a) 
		{ 
			(a.detail || -a.wheelDelta) < 0 ? map.zoomIn() : map.zoomOut(); 
		}
		
		map.enableContinuousZoom();
		map.enableDoubleClickZoom();
		
		GEvent.addDomListener(document.getElementById("map"), "DOMMouseScroll", wheel_zoom);
 		GEvent.addDomListener(document.getElementById("map"), "mousewheel", wheel_zoom);
 				
		$('order',response).each(function(){
			var point = new GLatLng($('latitude',this).text(),$('longitude',this).text());
			var marker = new GMarker(point);
			var total_order_value = $('total',this).text();
			
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml("Order value $" + total_order_value);
			});
			
			map.addOverlay(marker);
		});
	});
});