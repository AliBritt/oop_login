$(document).ready(function(){

	$('#loginForm').validate({
		rules:{
		
			email:{
			 	required:true,
				email:true
			},
			pass:{
				required:true,
				minlength: 4,
				maxlength: 60
			}
		}
	});
	
	
	$('#regiForm').validate({
		rules:{
			
			first_name:{
				required:true,
				minlength: 2,
				maxlength: 20
			},
			
			last_name:{
				required:true,
				minlength: 2,
				maxlength: 40
			},
			
			email:{
			 	required:true,
				email:true,
			},
			
			pass:{
				required:true,
				minlength: 4,
				maxlength: 60
			},
			
			pass_confirm:{
				required:true,
				equalTo:'pass'
			}
			
		}
	});
	
	
	$('#chgPassForm').validate({
		rules:{
			
			pass:{
				required:true,
				minlength: 4,
				maxlength: 60
			},
			
			pass_confirm:{
				required:true,
				equalTo:'pass'
			}
		}
	});
	
	
	$('#forgPassForm').validate({
		rules:{
			
			email:{
				required:true,
				email:true
			}
		}
	});
	
});
