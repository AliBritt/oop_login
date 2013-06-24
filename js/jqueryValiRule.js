$(document).ready(function(){
	//alert(1);
	$('#loginForm').validate({
		rules:{
		
			email:{
			 	required:true,
				email:true
			},
			pass:{
				required:true,
				minlength: 4,
				maxlength: 8
			}
		}
	});
	
	
	$('#regiForm').validate({
		rules:{
			
			first_name:{
				required:true,
				minlength: 2,
				maxlength: 8
			},
			
			last_name:{
				required:true,
				minlength: 2,
				maxlength: 8
			},
			
			 email:{
			 	required:true,
				email:true,
				maxlength: 8
			},
			
			pass:{
				required:true,
				minlength: 4,
				maxlength: 8
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
				maxlength: 8
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
