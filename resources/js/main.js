// To be done on everypage regardless
$(function() {
	$('.hidden').hide();
	$('.tooltip_me').tooltip();
});

// Variable Locks (variables used to prevent repeated or conflicting AJAX calls; e.g. double tapping the subscribe button)
locks = {
	follow: false,
	favorite: false
};

// Misc Variables
var time = null;

var success_message = {
		'username': '<strong>Success!</strong> Your username has been updated.',
		'email': '<strong>Success!</strong> Check your new email address to confirm change',
		'profile_pic': '<strong>Success!</strong> Your picture has been updated.'
	};

// views/account.php; admin/user_admin.php
// Handles dispaying the error or success message
function message(type, mssg)
{
	if(type == 'error')
	{
		$('#success').hide();
		$('#error').html('<strong>Oops!</strong> '+mssg).show();
	}
	if(type == 'success')
	{
		$('#error').hide();
		$('#success').html(mssg).stop(true, true).animate({opacity: "show"}, "slow").delay(1500).animate({opacity: "hide"}, "slow", function() {  });
	}
}

// view/account.php; admin/user_admin.php
// Run on completion of standard update AJAX requests
	function update_complete(data, type)
	{
		if(data == 'Success!')
    	{
    		console.log(type+' Updated!');
    		message('success', success_message[type]);
    	}
    	else
    	{
    		console.log('Error: ');
    		console.log(data);
    		message('error', data);
    	}
	}

// views/account.php
// Toggles and animates the profile picture upload button
function display_img_uploader()
{
	$('.update_profile_pic').stop(true, true).animate({opacity: "toggle"}, "slow");
}

// views/my_stream.php; /stream.php
// Loads the stream thumbnails
function load_stream_thumbs(ele, amt, offset, stream_id)
{
	$.post("/stream/load_stream_thumbs", {amt: amt, offset: offset, stream_id: stream_id}).done(function(data) { 
        		console.log('Stream Thumbnails Loaded');
        		$('#stream_box').empty();
        		$('#stream_box').append(data);
        		offset += 9;
        		if(offset > total_num_following) { offset = 0; }
        		$(ele).attr('onclick', 'load_stream_thumbs(this, 9, '+offset+', '+stream_id+')');
   	});
}

// views/stream.php
// Toggles a user following a stream
// Assumes the logged in user, if the stream is private will set the follow status as 'requested'
function follow()
{
	if(locks['follow']) {console.log('Waiting for follow toggle to complete before calling server again.'); return; }
	locks['follow'] = true;
	$.post("/stream/follow", {leader: stream_id}).done(function(data) { 
		if(data != 'Success!') 
		{ 
			$('#follow_button').removeClass('green_bg').addClass('red_bg')
			$('#follow_button').html('error');
			console.log(data);
		}
		$('#follow_button').toggleClass('subscribed');
		if($('#follow_button').html() == 'following') 
		{
			$('#follow_button').html('follow').removeClass('green_bg');
		}
		else
		{
			$('#follow_button').html('following').addClass('green_bg');
		}
		// $('#follow_button').toggleClass('green_bg');
		locks['follow'] = false;
   	});
}

// views/stream.php; /my_stream.php;
function favorite(ele, post_id)
{
	if(locks['favorite']) { console.log('Waiting for favorite toggle to complete before calling the server again');  return; }
	locks['favorite'] = true;
	$.post("/post/favorite", { post_id: post_id}).done(function(data) {
		if(data == 'Favorited!')
		{
			console.log('You have favorited post: '+post_id); 
    		$(ele).toggleClass('green_bg').html('Favorited');
			}
		else if(data == 'Unfavorited')
		{
			console.log('You have unfavorited post: '+post_id);
    		$(ele).toggleClass('green_bg').html('Favorite?');
		}
		else if(data == 'is_my_own')
		{
			console.log('You are trying to favorite your own post');
		}
		else
		{
			console.log('Error Data:');
			console.log(data);    	
			$(ele).toggleClass('red_bg').html('Error');	
		}
		locks['favorite'] = false;
	});
}

// view/account.php
// Sets up the profile uplaoder script
function uploadifive()
{
    $('#file_upload').uploadifive({
    	'auto'		   :  true,
        'uploadScript' : '/user/update_profile_pic',
        'buttonClass'  : 'update_profile_pic hidden',
        'buttonText'   : 'Update Profile Picture',
        'width'		   :  220,
        'queueID'	   : 'account_profile_pic',
        'itemTemplate' : '<div class="uploadifive-queue-item" style="display:none;"></div>',
        'fileSizeLimit': '5MB',
        'fileType'     : 'image',
        'multi'        :  false, // No multiple uploads
        'queueSizeLimit' : 0,	// Prevents stupid alerts if you try to change your pic very rapidly twice or more times
        'simUploadLimit' : 1,	// One at a time since you're just changing your profile picture
        'removeCompleted' : true,

        'onError'      : function(errorType) { 
        	console.warn('Error Uploading File: ' + errorType); 
        	update_complete(data, 'profile_pic');
        },
        'onAddQueueItem' : function(file) { console.log('The file ' + file.name + ' was added to the queue.'); },
        'onUpload'     : function(filesToUpload) { console.log(filesToUpload + ' files will be uploaded.'); },
        'onUploadFile'     : function(file) { console.log(file.name + ' files will be uploaded.'); },
         'onUploadComplete' : function(file, data) {
            console.log('The file ' + file.name + ' uploaded successfully.');
            console.log('Data Retrieved: ' + data);
            now = new Date(); // Forces image to be refreshed from the server when appended
            $('#account_profile_pic').css('background-image', 'url("/resources/profile_pics/'+data+'.png?'+now+'")');
            update_complete('Success!', 'profile_pic');
         }
    });
}

// view/stream.php; /my_stream.php; 
// Reblogs a post for a user
function reblog(ele, post_id)
{
	$.post("/post/reblog", { post_id: post_id}).done(function(data) {
		if(data == 'Success!')
		{
			console.log('Reblog for post: '+post_id+' complete');
			$(ele).html('Posted');
			$(ele).toggleClass('green_bg');
		}
		else if(data == 'my_own_post')
		{
			console.log('You tried reblogging your own post. Reblog cancelled.');
		}
		else
		{
			console.log('There was an error reblogging post: '+post_id);
			console.log(data);
			$(ele).html('error');
			$(ele).addClass('red_bg').removeClass('green_bg');
			$(ele).attr('onclick', 'void(0)');
		}
	});
}

// view/stream.php; /my_stream.php
// Loads the image and name of the original author of a post to be inserted into a tooltip
function get_repost_author_pic(ele, post_id)
{
	$(ele).attr('onmouseover', '');
	$.post("/user/post2user", { post_id: post_id}).done(function(data) {
		if(data == 'error')
		{
			$(ele).attr('data-title', 'Something went wrong. We\'re looking into it.');
			console.log('Error: Retrieving the author picture failed for unknown reasons.');
			$(ele).tooltip();
		}
		else
		{
			console.log('Picture loaded into tooltip');
			data = JSON.parse(data);
			data = '<img class="repost_img" src="'+data[0]+'" /><h2>'+data[1]+'</h2>';
			$(ele).tooltip('hide').attr('data-original-title', data).tooltip('fixTitle').tooltip('show');
			$(ele).tooltip({html: true, title: data});
		}
		change_tooltip_color('#000');
	});
}

// Changes the Bootstrap Tooltip color for all tooltips on a page
function change_tooltip_color(color) {
    $('.tooltip-inner').css('background-color', color)
    $('.tooltip.top .tooltip-arrow').css('border-top-color', color);
    $('.tooltip.right .tooltip-arrow').css('border-right-color', color);
    $('.tooltip.left .tooltip-arrow').css('border-left-color', color);
    $('.tooltip.bottom .tooltip-arrow').css('border-bottom-color', color);
}
