jQuery(document).ready(function($){	
	$(".role-select").change(function(){
		var divId = $(this).data('id');
		$(".registration-single-row").hide();
		$(".registration-single-row input").prop('required',false);
		$("."+divId).show();	
		$("."+divId+ " input").prop('required',true);
	});
});

