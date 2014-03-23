$(document).ready(function(){
	$('#export-all').bind('change',function(){
		if(this.checked)
			$('#date-filter').hide();
		else
			$('#date-filter').show();
	});
	
	$('#date-filter').hide();

	Calendar.setup(
	    {
	      inputField  : 'start_date',       
	      ifFormat    : "%A, %e %b %Y", 
	      button      : 'start_date_trigger',  
	      singleClick : false			
	    }
	);
	  
	Calendar.setup(
	    {
	      inputField  : 'end_date',       
	      ifFormat    : "%A, %e %b %Y", 
	      button      : 'end_date_trigger',  
	      singleClick : false			
	   }
	);
});