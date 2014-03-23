{assign var="page_title" value="Orders by location"}
{javascript_include_tag file="google_maps"}

A geographic representation of your orders is shown on the map below.

<div id="map" class="loading"></div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}" type="text/javascript"></script>