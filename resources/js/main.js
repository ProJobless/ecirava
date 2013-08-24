// To be done on everypage regardless
$(function() {
	$('.hidden').hide();
});

// Variable Locks (variables used to prevent repeated or conflicting AJAX calls; e.g. double tapping the subscribe button)
locks = {
	follow: false,
	favorite: false,
	submit: false,
	comment: false
};

// Misc Variables
var time = null;

var success_message = {
		'username': '<strong>Success!</strong> Your username has been updated.',
		'email': '<strong>Success!</strong> Check your new email address to confirm change',
		'profile_pic': '<strong>Success!</strong> Your picture has been updated.'
	};

// /views/stream.php; /my_stream.php
// Tiles the image posts
function tile_images()
{
	// WHEN THIS IS UPGARDED GO BACK TO TILING

	// $('.images').tilesGallery({
	// 	width: 650,
	// 	// height: 500,
	// 	tileMinHeight: 250,
	// 	margin: 10,
	// 	captionOnMouseOver: false,
	// 	verticalAlign: 'top',
	// 	horizontalAlign: 'center',
	// 	callback: function() {
	// 		console.log('Tiled');
	// 		$('.lightbox').lightbox();
	// 	}
	// })

	$('.lightbox').lightbox();
}

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
function uploadifive_account()
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

// view/posting/post_images.php
// Sets up the images uploader script
function uploadifive_images()
{
    $('#file_upload').uploadifive({
    	'auto'		   :  true,
        'uploadScript' : '/posting/upload_image',
        'buttonClass'  : 'hidden',
        'buttonText'   : 'Add Images',
        'width'		   :  220,
        'queueID'	   : 'upload_images_area',
        'itemTemplate' : '<div class="uploadifive-queue-item" style="display:none;"></div>',
        'fileSizeLimit': '5MB',
        'fileType'     : 'image',
        'multi'        :  true, // No multiple uploads
        'queueSizeLimit' : 0,	// Prevents stupid alerts if you try to change your pic very rapidly twice or more times
        'simUploadLimit' : 1,	// One at a time to help preserve ordering
        'removeCompleted' : true,

        'onError'      : function(errorType) { 
        	console.warn('Error Uploading File: ' + errorType); 
        	update_complete(data, 'profile_pic');
        },
        'onAddQueueItem' : function(file) { console.log('The file ' + file.name + ' was added to the queue.'); },
        'onUpload'     : function(filesToUpload) { console.log(filesToUpload + ' files will be uploaded.'); },
        'onUploadFile'     : function(file) { console.log(file.name + ' files will be uploaded.'); },
         'onUploadComplete' : function(file, data) {
         	// Log it
            console.log('The file ' + file.name + ' uploaded successfully.');
            console.log('Data Retrieved: ' + data);
            // Add the preview back to your page
            $('<li class="unselectable img_li" src="'+data+'"><img src="/resources/temp_uploads/thumb_' + data + '" class="image_upload" /></li>').appendTo('#sortable1');
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

// Creates a comment box for a user to submit a comment 
function comment(ele, post_id, comment_id)
{
	// Remove other comment boxes
	$('#cb_'+post_id).remove();

	// Because JS is the worst
	post_id = post_id.toString();

	// Create the new comment element
	comment_box = '<div id="cb_'+post_id+'"class="comment_box">'
                       	+'<textarea id="ta_'+post_id+'" class="trans_bg"></textarea>'
                        +'<h4 onclick="submit_comment(this, '+post_id+','+comment_id+')" class="comment_submit">Submit</h4>'
                        +'<h4 id="cm_'+post_id+'" class="comment_message"></h4>'
                        +'<h4 id="rc_'+post_id+'" class="remaining_chars">500</h4>'
                    +'</div>';
    // If comment_id == 0 then it is the original comment post
    // Special styling rules apply for positioning
    if(comment_id == 0)
    {
    	$(ele).parent().parent().next().after(comment_box);
   		$('#ta_'+post_id).autogrow().css('padding-top', '5px');
    }
    else
    {
    	$(ele).parent().after(comment_box);
    	$('#ta_'+post_id).autogrow().css('padding-top', '5px');
    }
    char_count($('#ta_'+post_id), 500);

}

// Makes a textarea keep count of it's characters and autogrow
// Used with comments, otherwise the display of the count makes no sense
function char_count(ele, max_char)
{
    $(ele).keyup(function()
    {
        $(this).siblings('.remaining_chars').css('color', '#FFF');
        $(this).css('border', '1px #FFF solid');
        $(this).siblings('.remaining_chars').html(max_char - this.value.length);
        if(max_char - this.value.length <= 0)
        {
            $(this).siblings('.remaining_chars').css('color', 'red');
        }
    });
}

// Submits a comment for a post
function submit_comment(ele, post_id, comment_id)
{
    // Only one comment submit request at a time
    if(locks['comment'])
    {
        console.log('User tried to submit a new comment too soon');
        $('#cm_'+post_id).addClass('error').html('You\'re current comment is still pending');
    }
    locks[comment] = true;
    // Retrieve the comment content
    content = $('#ta_'+post_id).val();
    $.post('/comment/new_comment', {post_id: post_id, content: content, reply_id: comment_id }).done(function(data) {
        if(data == 'Success!')
        {
            console.log('Comment submitted successfully for post: '+post_id);
            $('#cm_'+post_id).removeClass('error').html('Success!');
            $('#ta_'+post_id).css('border', '1px #51A351 solid');
            // Fade the post out and remove it
            $('#cb_'+post_id).slideUp	(3000, function() { $('#cb_'+post_id).remove(); });
        }
        else
        {
            console.log(data);
            $('#cm_'+post_id).addClass('error').html(data);
            $('#ta_'+post_id).css('border', '1px red solid');
        }
        locks[comment] = false;
    });
}

// Formats the date into "August 4th, 2013" format given a date object
// Returns a string
function format_date(date)
{
	months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	month = months[date.getMonth()];

	day = date.getDate();
	day = day.toString();

	suffix = day.substr(-1);
	if(suffix == '1') { suffix = 'st'; }
	else if(suffix == '2') { suffix = 'nd'; }
	else if(suffix == '3') { suffix = 'rd'; }
	else { suffix = 'th'; }
	// Exceptions
	if( day == '11' || day == '12' || day == '13') { suffix = 'th'; }

	year = date.getFullYear().toString();

	return month+' '+day+suffix+',&nbsp;&nbsp;'+year;
}

// Loads and displays the comments
function show_comments(ele, post_id, offset)
{
	// Disable repearted calls
	$(ele).attr('onclick', '');
	// Set the text to a loading bar
	$(ele).html('<img src="/resources/img/ajax-loader.gif" />');

	// Make the AJAX call
	$.post('/comment/show_comments', { post_id: post_id, offset: offset}).done(function(data) {
		console.log(data);
		data = JSON.parse(data);
		$.each(data, function(index, comment) {
			date = new Date(Number(comment.created_on)*1000);
			date = format_date(date);
			$('#comments_'+post_id).append('<div id="com_'+comment.id+'" class="comment">'+
                '<img src="/resources/profile_pics/'+comment.author_id+'.png" />'+
				'<h4 class="post_meta button unselectable" onclick="comment(this, '+comment.post_id+', '+comment.id+')">Reply</h4>'+
                '<div class="content trans_bg">'+
                '<div class="comment_meta">'+
           		    '<h4 class="date post_meta">'+date+'</h4>'+
           		    '<h4 class="username post_meta"><a href="/stream/view/'+comment.author_id+'">'+comment.username+'</a></h4>'+
           		'</div>'+
                comment.content+
                '</div>'+
            '</div>'+
            '<div style="clear:both;></div>');
		});
		$(ele).html('');
	});

	// Add a button to load more posts
	$('#lc_'+post_id).append('img');
}

// Loads the next 10 posts
function load_more_posts(type)
{
	$.post("/stream/load_posts", {stream_id: stream_id, offset: offset, type: type}).done(function(data){
		$('#content').append(data);
		// Change the offset
		offset += 10;
		// If we've reached the end of the stream remove the 'load more posts button'
		if(offset > total_posts)
		{
			$('.load_more').html('<h4 style="text-align:center;">End of Stream</h4>');
			console.log('End of stream reached');
		}
	});
}