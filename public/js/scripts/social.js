$(document).ready(function(){
	VK.init({
		apiId: 4359943
		//apiId: 4361464,
		
	});
	function authInfo(response) {
		if (response.session) {
        	VK.Api.call('users.get', 
	        	{
        			uids: response.session.user.id,
        			fields:'bdate,sex'
	        	},
	        	function(r) {
		        	var fname = '';
		        	var lname = '';
		        	var sex = '';
		        	var bdate = '';
		        	
		        	if(r.response[0].sex !== undefined){
		        		sex = r.response[0].sex; 
		        	}
		        	if(r.response[0].bdate !== undefined){
		        		bdate = r.response[0].bdate; 
		        	}
		        	var fname = r.response[0].first_name;
		        	var lname = r.response[0].last_name;
		        	
	        		$.post('/user/vk-auth',
			        	{
							'fname' : fname,
							'lname'	: lname,
							'sex'	: sex,
							'bdate'	: bdate	
			        	},
				        function(data){
			        		if(data.result == 'success'){
			        			window.location.href="/profile";
			        		}else{
			        			alert('Не удалось авторизоваться!');
			        		}
			        	},'json');
				}); 
		}
	}
	
	if($("#vk_auth_register").length) {
		$('#vk_auth_register').click(function(){
			VK.Auth.login(
				authInfo
				);
		});
	}
	
	if($("#vk_auth").length) {
		$('#vk_auth').click(function(){
			VK.Auth.login(
				authInfo
				);
		});
	}
	
	FB.init({
	    appId      : '1502387886647001',
	    cookie     : true,  // enable cookies to allow the server to access 
	                        // the session
	    xfbml      : true,  // parse social plugins on this page
	    version    : 'v2.0' // use version 2.0
	  });
	
	var accesToken = '';
	if($("#fb_auth_register").length) {
		$('#fb_auth_register').click(function(){
			FB.login(function(response) {
				$.post('/user/fb-auth',
			        	{
							'acess_token' : response.authResponse.accessToken
			        	},
				        function(data){
			        		if(data.result == 'success'){
			        			window.location.href = '/profile';
			        		}
			        	},'json');
			    },{scope: 'public_profile,user_birthday,email'});
		});
	}
	if($("#fb_auth").length) {
		$('#fb_auth').click(function(){
			FB.login(function(response) {
				$.post('/user/fb-auth',
			        	{
							'acess_token' : response.authResponse.accessToken
			        	},
				        function(data){
			        		if(data.result == 'success'){
			        			window.location.href = '/profile';
			        		}
			        	},'json');
			    },{scope: 'public_profile,user_birthday,email'});
		});
	}
	
	//if ((keyCode == 10 || keyCode == 13) && event.ctrlKey)
	
	
});