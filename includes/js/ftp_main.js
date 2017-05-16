jQuery(function ($) {
	$(document).ready(function(){
		var inputValues = [];
		$("#myform").submit(function( event ) {
	  	if($( "#myform" ).valid()){
	  		var inputs = $( "#myform input" );
	  		inputs.each(function(index){
	  			if($(this).attr('type') !== 'submit')
	  				inputValues.push($(this).val());
	  		});

	  		}
	  		//alert('somethinf');
	  		var formObj = {
			action 		: "users_inputs",
			userValues 	: inputValues
			};
			//alert(formObj.userValues[0]);
			jQuery.post(users_obj.ajax_url, formObj);
	  	

		

	  	event.preventDefault();
		});

	});

});

//});
/*
	document.getElementById('myform').onsubmit = function(){
		if()
	  var inputs = document.querySelectorAll('#myform input');
	  inputs.forEach(function(item, index){
	    alert(item.value);
	  });
	}
}

*/