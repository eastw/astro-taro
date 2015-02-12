$(document).ready(function(){
	$('select').selectbox();
	$('#auth-submit').click(function(){
		$.post(
				'/user/login',
				$('#auth-form').serialize(),
				function(data){
					if(data.result == 'success'){
						location.reload();
					}else{
						$('.wrong_text').html('');
						$('.enter_form_text').removeClass('wrong_form');
						for(var i in data.errors){
							for(var j in data.errors[i]){
								$('#'+i + '-error').html(data.errors[i][j]);
								$('input[name="'+i+'"]').addClass('wrong_form');
								if(i == 'login_error'){
									$('#pass-error').html(data.errors[i][j]);
								}
							}
						}
					}
				},
				'json'
				);
		return false;
	});

	$('#poll').click(function(){
		var values = '';
		$('.vote_content input[type="checkbox"]').each(function(i){
			if($(this).is(':checked')){
				values += $(this).attr('name') + ';';
			}
		});
		if(values != '') {
			$.post(
				'/common/poll-result',
				{
					'values': values
				},function(data){
					if(data.status == 'success'){
						var poll_header = $('.vote_header').text();
						var html = '<div class="vote_header">' + poll_header + '</div>';
						$('.vote_header, .vote_content').remove();
						html += '<div class="vote_content">'
						for (var i in data.options){
							html += '<div><span>' + data.options[i]['value'] + '</span> - ' +
							data.options[i]['name'] + '</div>'
						}
						html += '</div>';
						$('.vote_ask').attr('class','vote_answer');
						$('.vote_answer').html(html);
						//$.cook
					}
				},'json'
			);
		}
		return false;
	});
	
	$('a.ident-enter').click(function(){
		$('#enter_form').stop().fadeToggle(200,function(){
			$('html, body').animate({
		        scrollTop: $('#enter').offset().top
		    }, 1000);
		});
		return false;
	});
	
	$('#iemd,#footer-feedback').click(function(){
		$('#top').click();
		$('#feedback_button').click();
		return false;
	});

	$('#word-search-button').click(function(){
		var query = jQuery.trim($('#word-search').val());
		if(query != ''){
			window.location.href = '/sonnik/search/' + encodeURIComponent(query);
		}
		return false;
	});

	$('#word-search').keyup(function(e){
		var query = jQuery.trim($('#word-search').val());
		if(e.keyCode == 13 && query != ''){
			window.location.href = '/sonnik/search/' + encodeURIComponent(query);
		}
	});

	
	$('#feedback-send').click(function(){
		$.post('/index/feedback',
				{
					'name': $('#feedback-name').val(),
					'email': $('#feedback-email').val(),
					'content': $('#feedback-content').val()
				},function(data){
					if(data !== undefined){
						$('#feedback-name').removeClass('wrong_form');
						$('#feedback-email').removeClass('wrong_form');
						$('#feedback-content').removeClass('wrong_form');
						
						if(data.length === 0){
							$('#feedback-sended').show();
							$('#feedback-content').css('margin-bottom','0');
							$('#feedback-name').val('');
							$('#feedback-email').val('');
							$('#feedback-content').val('');
							
							setTimeout(function(){
								$('#feedback-content').css('margin-bottom','20px');
								$('#feedback-sended').fadeOut();
								$('#feedback_button').click();
							},4000);
						}else{
							for(var i in data){
								$('#feedback-' + i).addClass('wrong_form');
							}
						}
					}
				},'json');
		return false;
	});
	$('.forget').click(function(){
		if($('#auth-form').is(':visible')){
			$('#auth-form').fadeOut(function(){
				$('#recover-form').fadeIn();
			});
		}else{
			$('#auth-form').fadeIn(function(){
				$('#recover-form').fadeOut();
			});
		}
	});
	
	$('#recover-submit').click(function(){
		$.post(
				'/user/recover',
				$('#recover-form').serialize(),
				function(data){
					$('.wrong_text').remove();
					if(data.length === 0){
						$('#password-sended').fadeIn();
						setTimeout(function(){
							$('#password-sended').fadeOut();
						},4000);
					}else{
						var error = '';
						for(var i in data){
							error += '<div class="wrong_text">' + data[i]+ '</div>';
						}
						$('input[name="remail"]').after(error);
					}
				},
				'json'
				);
		return false;
	});
	
	
	$('body').keydown(function(e){
		if ((e.keyCode == 10 || e.keyCode == 13) && e.ctrlKey){
			var text = getSelectionText();
			if(text.length > 0){
				$.post(
						'/article/send-typo',
						{
							'typo' : text,
							'location' : window.location.href
						});
				alert('Спасибо за участие, мы исправим опечатку в самое ближайшее время');
			}
		}
	});
	
});

function getSelectionText() {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }
    return text;
}


function divVote(id){
	var voteCount = $('#vote-count-' + id).text();
	var voteCountInt = parseInt(voteCount);
	$('#vote-count-' + id).text(++voteCountInt);
	
	$('#like_' + id).after('<img src="/files/images/like.png"/>');
	$('#like_' + id).remove();
	
	$.post(
			'/gadaniya/vote',
			{
				'id': id
			},
			function(data){
			});
}
function divVoteBig(id){
	var voteCount = $('#vote-count-' + id).text();
	var voteCountInt = parseInt(voteCount);
	$('#vote-count-' + id).text(++voteCountInt);
	$('#head-vote-count').text(voteCountInt);
	
	$('.big_like').attr('onclick','');
	$('.big_like').attr('style','cursor:default');
	
	$.post(
			'/gadaniya/vote',
			{
				'id': id
			},
			function(data){
			});
}
function vote(id){
	var voteCount = $('#vote-count-' + id).text();
	var voteCountInt = parseInt(voteCount);
	$('#vote-count-' + id).text(++voteCountInt);
	
	$('#like_' + id).after('<img src="/files/images/like.png"/>');
	$('#like_' + id).remove();
	
	$.post(
			'/article/vote',
			{
				'id': id
			},
			function(data){
				
			});
}
function bigArticleVote(id){
	var voteCount = $('#vote-count-' + id).text();
	var voteCountInt = parseInt(voteCount);
	$('#vote-count-' + id).text(++voteCountInt);
	$('#head-vote-count').text(voteCountInt);
	
	$('.big_like').attr('onclick','');
	$('.big_like').attr('style','cursor:default');
	
	$.post(
			'/article/vote',
			{
				'id': id
			},
			function(data){
			});
}

function dreamVote(id){
	var voteCount = $('#vote-count-' + id).text();
	var voteCountInt = parseInt(voteCount);
	$('#vote-count-' + id).text(++voteCountInt);
	$('#head-vote-count').text(voteCountInt);

	$('.big_like').attr('onclick','');
	$('.big_like').attr('style','cursor:default');

	$.post(
		'/dream/vote',
		{
			'id': id
		},
		function(data){
		});
}
function addFavorite(id,type,subtype){
	$('#favorite-link').html('<img src="/files/images/upr_img1.png"/>В избранном');
	$.post('/profile/favorite',
			{
				'id'		: id,
				'type'		: type,
				'subtype' 	: subtype
			},function(data){
				
			});
			
	return false;
}
function addComment(){
	var type = $('#comment_type').val();
	var subtype = $('#comment_subtype').val();
	var sign = $('#comment_sign').val();
	var resource_id = $('#comment_resource_id').val();
	var content = $('#comment_field').val();
	
	$.post('/comments/add',
			{
				'type' 			: type,
				'subtype'		: subtype,
				'sign'			: sign,
				'resource_id'	: resource_id,
				'content'		: content
			},function(data){
				if(data.id != ''){
					$('#comment_field').val('');
					var html = '<div id="comm_'+data.id+'" class="comment_item">';
					if(data.avatar == null || data.avatar == ''){
						html += '<img src="/files/images/comment_profile.png" style="width:41px" alt="" />';
					}else{
						html += '<img src="/files/avatar/'+data.avatar+'" style="width:41px" alt="" />';
					}
					html += '<div class="comment_content">';
					html += '<div class="comment_header">' + data.name;
					html += '<span>'+data.date_created+'</span>';
					html += '</div><div class="comment_text">'+data.body+'</div>';
					html += '<div class="dialog_line">';
					html += '<a class="answer" onclick="answer(\''+data.name+'\')" href="javascript:void(0)">ответить</a>';
					html += '<a href="javascript:void(0)">пожаловаться</a><a href="javascript:void(0)">это спам</a><div class="clear"></div></div>';
					html += '</div></div>';
					if($('#comments-items .comment_item').length == 0){
						$('#comments-items').html(html);
					}else{
						$('#comments-items .comment_item:last').after(html);
					}
					var commentsCount = $('#comments_count').text();
					var commentsCountInt = parseInt(commentsCount);
					$('#comments_count').text(++commentsCountInt);
					
					$('html, body').animate({
				        scrollTop: $("#comm_" + data.id).offset().top
				    }, 1000);
				    
				}
			},'json');
	//*/		
	return false;
}

function addPayServiceComment(){
	var type = $('#payservice_comment_type').val();
	var content = $('#payservice_comment_field').val();
	
	$.post('/comments/add-payservice-comment',
			{
				'type' 			: type,
				'subtype'		: '',
				'sign'			: '',
				'resource_id'	: '',
				'content'		: content
			},function(data){
				$('#ring_do').html(data);
				$('#payservice_comment_field').val('');
				$('#ring_do').animate({
			        scrollTop: $('.left_block .comment_item:last').offset().top
			    }, 1000);
			    
			});
	return false;
}

function answer(name){
	$('#comment_field').val(name + ', ');
	$('#comment_field').focus();
}
function payservice_answer(name){
	$('#payservice_comment_field').val(name + ', ');
	$('#payservice_comment_field').focus();
}
function abuseComment(id,pos){
	$('#abuse-comment-form').show();
    $('#abuse-comment-form').animate({'top': pos.top+"px",'left':pos.left+'px'});
    $('#comment').val(id);
}
function sendAbuse(){
	var id = $('#comment').val();
	$('#abuse-inform_' + id).show();
	$.post('/comments/abuse',
			{
				'id'	: id,
				'type'	: 'abuse',
				'abuse'	: $('#abuse-field').val()
			},function(){
				$('#abuse-inform_' + id).delay(2000).fadeOut('slow');
				closeAbuse();
			});
}
function sendSpam(id){
	$('#abuse-inform_'+id).show();
	$.post('/comments/abuse',
			{
				'id'	: id,
				'type'	: 'spam'
			},function(){
				$('#abuse-inform_'+id).delay(2000).fadeOut('slow');
				closeAbuse();
			});
}
function closeAbuse(){
	$('#abuse-comment-form').hide();
	$('#abuse-field').val('');
    $('#abuse-comment-form').css('top',0).css('left',0);
}

function closePayservicePopupAndAutorize(){
	$('#popup').css({'display' : 'none'});
	setTimeout(function(){
		$('#enter').click();
	},1000);
}

function showPaymentService(){
	$('#popup').css({"display" : "block"});
	
	//load comments
	setTimeout(function(){
		loadPayserviceComments();
	},1000);
}

function loadPayserviceComments(){
	$.post(
			'/index/payservice-comments',
			{
			},
			function(data){
				$('#ring_do').html(data);
			});
}
