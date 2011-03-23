var time_verify = null;
var number_message = 0;
var time_interval = 5000;
var active_user_talk;
var maxUserTalk = 7;
var totalUserTalk = 0;
var colors = ['blue', 'green', 'red', 'purple', 'yellow', 'pink', 'orange'];
var messages_sent = 0;

$(document).ready(function(){

	// Focus in load
	$('#message').focus(); 

	// Send form
	$('#form_send').submit(function () {
		// Verify message
		if ($('#message').val() == '') {
			$('#message').focus(); 
			return false;
		}
		
		messages_sent++;
		if (messages_sent > 10) return false;
		
		$.ajax({
			type: "POST",
			url: "send_message.php",
			data: $('#form_send').serialize() + "&reserved=" +  $('#reserved').is(':checked'), 
			success: function(msg){
				checkError(msg);
				clearInterval(time_verify);
				time_verify = setTimeout('verifyMessages()', time_interval / 3);
			}
		});
		
		$('#message').val('').focus();
		number_message++;
		return false;
	});
	
	// Change user talk
	$('#menu a').click(function(){ actionUser($(this), 'menu'); return false; });
	
	$('#reserved, #scroll_page').click(function(){
		$('#message').focus();
	});
	
	// Verify messages
	clearInterval(time_verify);
	time_verify = setTimeout('verifyMessages()', time_interval);
		
	$('.box_emoticons img').click(function(){
		var title = $(this).attr('title');
		var message = $('#message').val() + ' ' + title;
		$('#message').val(message).focus();
		return false;
	});
	
	// Users tab
	$('.user_tab').click(function(){ actionUserTalk($(this)); return false; });
	$('.close_talk').click(function(){ closeUserTalk($(this)); return false; });
	
	// Resize the box users
	$(window).resize(function(){ resizeBoxUsers()} );
	resizeBoxUsers();
	
	// Logout
	$('#logout').click(function(){
		var link = $(this).attr('href');
		var wheight = $(document).height();
		$('object').hide();
		
		var box_overlay = $('<div id="box_overlay"></div>');
		$('body').append(box_overlay);
		box_overlay.hide().css({position:'absolute',top:0,left:0,width:'100%',height:wheight,background:'#000'});
		
		var box_alert = $('#box_alert');
		box_alert.css({top:'40%'});
		
		box_overlay.fadeTo(200,0.75, function(){
			box_alert.animate({top:'50%',opacity:'show'}, 400, 'easeOutBack');
		});
			
		$('#remove_box').click(function(){
			box_overlay.fadeOut(100, function() { $(this).remove() });
			box_alert.fadeOut(100);
			$('object').show();
			return false;
		});
		
		return false;
	});
	
	// Link in user name
	$('.user_name, .name_to').live('click', function() {
		var self = $(this);
		actionUser(self, 'content'); 
		actionUserTalk(self);
		return false;
	});
	
	// Remove message
	$('.remove_message').live('click', function() {
		$(this).parents('.box_msg').fadeOut(400, function() {
			$(this).remove();
		});
	});
	
	// Simple zoom image
	$('.zoom_image').live('click', function() {
		if ($(this).find('img').attr('class') != 'big_image') {
			var image = $(this).find('img');
			image
				.css({'width':'auto'})
				.addClass('big_image');
				
			var width_image = image.width();
			var width_window = $(window).width() - $('#menu').width();
			if (width_image > width_window) {
				image
					.css({'width':'40px'})
					.animate({'width':'100%'}, 500, 'easeOutSine')
			}
			else {
				image
					.css({'width':'40px'})
					.animate({width:width_image+'px'}, 500, 'easeOutSine')
			}
		}
		else {
			$(this).find('img')
				.animate({'width':'40px'}, 300, 'easeOutSine')
				.removeClass('big_image');
		}
		return false;
	});
	
});


/*
 * Verify messages
 */
function verifyMessages () {
	clearInterval(time_verify);
	
	$.ajax({
		url: "load_messages.php",
		dataType: "text/html", 
		cache: false,
		success: function(msg){
			messages_sent = 0;
			checkError(msg);
			loadUsers();
			if (msg != '' && msg != 'error') {
				$('#content').append(msg);
				if ($('#scroll_page').is(':checked')) {
					$('body').scrollTo($(document).height());
				}
			}
			// Verificar messages
			time_verify = setTimeout('verifyMessages()', time_interval);
		}
	});
}

/*
 * Load users
 */
function loadUsers () {
	$.ajax({
		url: "load_users.php",
		dataType: "text/html", 
		cache: false,
		success: function(msg)
		{
			checkError(msg);
			
			// All id of users
			var all_ids = new Array();
			var ids_actual = new Array();

			var json = eval('(' + msg + ')');
			var total = json.length;
			
			// Loop json
			for (i = 0; i < total; i++) {
				var id_user = json[i][0];
				var name = json[i][1];
				var user_link = $('<a href="#" id="user_'+id_user+'" rel="'+id_user+'">'+name+'</a>');
				if ($('#user_'+id_user).length == 0) {
					$('#box_users').append(user_link);
					$('#box_users a').click(function(){ actionUser($(this), 'menu'); return false; });
					user_link.hide().fadeIn(500);
				}
				
				all_ids.push(id_user);
			}
			
			// Loop users get id
			$('#box_users a').each(function(){
				ids_actual.push($(this).attr('rel'));
			});
			
			// Delete users offline
			total = ids_actual.length;
			for (i =0; i < total; i++) {
				if ($.inArray(ids_actual[i], all_ids) < 0) {
					$('#user_'+ids_actual[i]).fadeOut(500, function() { $(this).remove(); });
				}
			}
			
			// Order users
			$('#box_users a').sort(function(a, b){
				return $(a).text() > $(b).text() ? 1 : -1;
			});
		}
	});
		
	resizeBoxUsers();
}

// Repotision box users and box overlay
function resizeBoxUsers () {
	var wheight = $(window).height();
	wheight -= 365;
	$('#box_users').css({height:wheight});
	
	var width_box_user_tools = $('#box_user_tools').width();
	var width_message_box = $('#message_box').width();
	var width_box_emoticons = $('.box_emoticons').width();
	// $('#message_box').width(width_window - width_box_user_tools - 40);
	$('.box_emoticons').css({'left': parseInt((width_message_box - width_box_emoticons) / 2 + width_box_user_tools)+'px'});
	$('#box_talk').css({'left': parseInt(width_box_user_tools + 10)+'px'});
	
	if ($('#box_overlay').length) {
		var wheight = $(document).height();
		$('#box_overlay').css({height:wheight});
	}
	
}

// Action user
function actionUser (e, type) {
	var rel = e.attr('rel');
	var nome = e.text();
	if ($('#user_'+rel).length > 0) {
		$('#to').text(nome);
		$('#to_user').val(rel);
		$('#message').focus();
		
		if (type == 'menu') {
			$('.active_user_talk').removeClass();
			e.addClass('active_user_talk');
		}
		
		active_user_talk = e.attr('id');
		var user_me = $('.user_me a').attr('rel');
		
		// user tab
		if ($('#user_tab_'+rel).length == 0 && rel != user_me)  {
			if (rel > 0) {
				totalUserTalk++;
				if (totalUserTalk >= (maxUserTalk + 1)) {
					$('#box_talk').find('span:first').remove();
					totalUserTalk--;
				}
			
				var span_talk = $('<span><a href="#" rel="'+rel+'" id="user_tab_'+rel+'" class="user_tab">'+nome+'</a> <a href="#" class="close_talk">x</a></span>');
				$('#box_talk').append(span_talk).show();
				span_talk.find('.user_tab').click(function(){ actionUserTalk($(this)); return false; });	
				span_talk.find('.close_talk').click(function(){ closeUserTalk($(this)); return false; });
				
				span_talk.css({width:span_talk.find('a').width()+45}); // Hack IE7
				span_talk.hide().stop().fadeIn(200);
				span_talk.hover(overUserTalk, outUserTalk);
				
				blinkActiveUserTalk(rel);
			}
			
		}
		else {
			blinkActiveUserTalk(rel);
		}
	}
}

// Over user talk
function overUserTalk() {
	$(this).css({'opacity':'1.0'});	
}

// Out user talk
function outUserTalk() {
	if ($(this).attr('class') != 'active_span_user_talk') {
		$(this).css({'opacity':'0.5'});	
	}
}

// Blink user
function blinkActiveUserTalk (rel) {
	$('.active_span_user_talk').removeClass('active_span_user_talk');
	$('#box_talk span').css({'opacity':'0.5'});
	$('#user_tab_'+rel).parents('span').css({'opacity':'1.0'}).addClass('active_span_user_talk');	
}

// Close tab
function closeUserTalk (e) {
	totalUserTalk--;
	var rel = e.attr('rel');
	e.parent('span').animate({opacity:'hide'}, 200, function(){ 
		$(this).remove(); 
		if ($('#box_talk span').length == 0) {
			$('#box_talk').hide();
		}
		actionUserTalk($('#user_0'));
		$('.active_user_talk').removeClass('active_user_talk');
		$('#user_0').addClass('active_user_talk');
	});
	return false;
}

// Action tab link
function actionUserTalk (e) {
	var rel = e.attr('rel');
	var nome = e.text();
	$('#to').text(nome);
	$('#to_user').val(rel);
	$('#message').focus();
	$('.active_user_talk').removeClass('active_user_talk');
	$('#menu a[rel="'+rel+'"]').addClass('active_user_talk');
	blinkActiveUserTalk(rel);
	return false;
}

// Check error
function checkError(msg) {
	if (msg == 'error') {
		document.location = 'logout.php';
		return false;
	}
}