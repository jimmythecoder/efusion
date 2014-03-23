$(document).ready(function()
{
	//If js enabled, we dont need the sort button
	$('#sort-button').hide();

	$('#sort-select').bind('change',function(){
		this.form.submit();
	});
});