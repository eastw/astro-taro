$(document).ready(function(){
	$('.send_profile').click(function(){
		//$('#loading').show();
		var byear = $('#byear option:selected').val();
		var text = $('#byear').prev().find('div.text').text();
		$('#byear option').each(function(){
			if($(this).text() == text){
				byear = $(this).val();
			}
		});
		var bmonth = $('#bmonth option:selected').val();
		var text = $('#bmonth').prev().find('div.text').text();
		$('#bmonth option').each(function(){
			if($(this).text() == text){
				bmonth = $(this).val();
			}
		});
		var bday = $('#bday option:selected').val();
		var text = $('#bday').prev().find('div.text').text();
		$('#bday option').each(function(){
			if($(this).text() == text){
				bday = $(this).val();
			}
		});
		var gender = $('#gender option:selected').val();
		var text = $('#gender').prev().find('div.text').text();
		$('#gender option').each(function(){
			if($(this).text() == text){
				gender = $(this).val();
			}
		});
		var formData = {
			'email' : $('#email').val(),
			'pass'	: $('#pass').val(),
			'pass_confirm'	: $('#pass_confirm').val(),
			'fname'			: $('#fname').val(),
			'mname'			: $('#mname').val(),
			'lname'			: $('#lname').val(),
			'byear'			: byear,
			'bmonth'		: bmonth,
			'bday'			: bday,
			'gender'		: gender,
			'nik'			: $('#nik').val(),
			'signature'		: $('#signature').val()
		};
		$.post(
				'/profile/check-profile',
				formData,
				function(data){
					if(data.result == 'success'){
						$('#update-success').show();
						setTimeout(function(){
							$('#update-success').fadeOut(1000)
						},2000);
					}
					$('div.wrong_text').remove();
					if(data.errors.length == 0){
						if(data.sun_sign != ''){
							$('#horoscope_today_link').attr('href','/horoscope/' + data.sun_sign + '/today');
							$('#horoscope_today_link_profile').html('<a href="/horoscope/' + data.sun_sign + '/today">Гороскоп на сегодня</a>');
							$('#profile_edit_link').hide();
						}
						if(data.lifenumber != ''){
							$('#lifenumber_profile_link').html('<a href="/profile/day-description/number">Число дня</a>');
							$('#lifenumber_profile_edit').hide();
						}
					}else{
						var html = '';
						for(var i in data.errors){
							html = '';
							for(var j in data.errors[i]){
								var style = '';
								if(i== 'byear' || i== 'bmonth' || i== 'bday'){
									style = 'style="width:135px;margin-left:5px;"';
								}
								html += '<div '+style+' class="wrong_text">' + data.errors[i][j] + '</div>';
							}
							$('#'+i).after(html);
						}
					}
					//$('#loading').hide();
					//$('#success').show();
				},
				'json'
				);
				
		return false;
	});
	

	$('#byear,#bmonth').change(function(){
		var byear = $('#byear option:selected').val();
		var text = $('#byear').prev().find('div.text').text();
		$('#byear option').each(function(){
			if($(this).text() == text){
				byear = $(this).val();
			}
		});
		var bmonth = $('#bmonth option:selected').val();
		var text = $('#bmonth').prev().find('div.text').text();
		$('#bmonth option').each(function(){
			if($(this).text() == text){
				bmonth = $(this).val();
			}
		});
		if(byear != '' && bmonth != ''){
			$.post(
					'/user/days',
					{
						'year' : byear,
					 	'month': bmonth
					 },
					 function(data){
						var html = '';
						for(var i in data){
							html += '<option value="'+i+'">' + data[i] + '</option>';
						}
						$('#bday').html(html);
						$('#bday').prev().remove();
						$('#bday').removeAttr('style');
						$('#bday').selectbox();
					},
					'json');
		}
	});
	
	$('#remove-profile').click(function(){
		if(confirm('Вы действительно хотите удалить ваш профиль?')){
			window.location.href="/profile/remove";
		}
	});
	
	var wrapper = $('<div/>').css({height:0,width:0,'overflow':'hidden'});
	var fileInput = $(':file').wrap(wrapper);
	
	fileInput.change(function(){
	    $this = $(this);
	    $('#file').html('<img src="/files/images/papka.png">Файл выбран');
	    $('#avatar_submit').attr('class','avatar-update-on');
	});

	$('#file').click(function(){
	    fileInput.click();
	}).show();
	
	var options = {
		success: function(data)
	    {
			data = jQuery.parseJSON(data);
			$('#avatar-form div.wrong_text').remove();
			if(data.result == 'success'){
				$('#avatar-img').attr('src','/files/avatar/' + data.newavatar);
			}else{
				var html = '';
				
				for(var i in data.errors){
					for(var j in data.errors[i]){
						html += '<div class="wrong_text">' + data.errors[i][j] + '</div>';
					}
				}
				$('#avatar-img').after(html);
			}
			
	    },
	    error: function()
	    {
	        $("#message").html("<font color='red'>Ошибка: невозможно загрузить файл</font>");
	    }
	};
	$('#avatar-form').ajaxForm(options);
	
	$('#cancel').click(function(){
		if(confirm('Вы действительно хотите удалить свой аватар?')){
			$.post('/profile/remove-avatar',{},function(){
				$('#avatar-img').attr('src','/files/avatar/cabinet_profile.png');
			});
		}
	});
});

function removeFavorite(id){
	if(confirm('Вы действительно хотите удалить эту ссылку?')){
		$.post('/profile/favorite-remove',
				{
					'id': id
				},function(){
					$('#fav_' + id).slideUp('slow', function(){
					    $(this).remove();
					});
				}
		);
	}
}