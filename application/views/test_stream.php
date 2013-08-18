<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?PHP echo $site_title.' - '.$page_title; ?></title>
        <link rel="shortcut icon" href="favicon.ico" type="img/blank.icon">
    </head>
    <body>
    <?PHP echo $this->load->view('header'); ?>
    <div class="container stream">
        <div class="row">
            <div id="main" class="span9">
                <h1>Testing Stream</h1>
                <div id="63" class="span9 stream_post">
                     <div class="poster_pic">
                        <a href="/stream/view/7"><img src="/resources/profile_pics/7.png"></a>
                    </div><div class="edit_post">
                            <div class="hidden" style="display: none;"><a href="#">edit</a> | <a href="#">delete</a></div>
                        </div>
                    <div class="post_title">
                        <a href="#">/d2g/ Dump</a>
                        
                    </div>
                    <div class="link_src">
                        <a href="#">Perma-link to post</a>
                    </div>
                    <div> <!--Use this to collapse to title only-->
                        <div class="images">
                        <a href="/resources/uploads/2013/08/0740191a423ae9a2d96b3ebab7e2d497.gif" class="lightbox" rel="63"><img src="/resources/uploads/2013/08/tb_0740191a423ae9a2d96b3ebab7e2d497.gif" class="stream_img"></a><a href="/resources/uploads/2013/08/2c98ec5ed853879d9bb5338d6386599c.jpg" class="lightbox" rel="63"><img src="/resources/uploads/2013/08/tb_2c98ec5ed853879d9bb5338d6386599c.jpg" class="stream_img"></a><a href="/resources/uploads/2013/08/a4db526c82e8d20639c496e1896287fb.jpg" class="lightbox" rel="63"><img src="/resources/uploads/2013/08/tb_a4db526c82e8d20639c496e1896287fb.jpg" class="stream_img"></a>
                        </div>
                        <div class="content">
                        As the name implies, only the shittiest pictures allowed
                        </div>
                        <div class="post_meta">
                            <div class="pull-left">
                                <a href="javascript_void(0)" onclick="show_comment(this, 63)" style="float:left">0 Comments</a>
                            </div>
                            <div class="pull-right unselectable">
                                <h4 onclick="comment(this, 63, 0)">Comment</h4>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div style="clear:both;"></div>
                        <div id="comments_63" class="comments">
                            <div id="com_13" class="comment">
                                <img src="/resources/profile_pics/7.png" />
                                <div class="content trans_bg">
                                    testtesttesttes ttesttesttes ttestte sttestte sttesttestt esttesttesttesttest testtestt esttesttesttestt esttesttesttestte sttesttesttestte sttesttestt esttesttesttesttest testtesttes ttesttestte sttestte stte sttest testtesttes ttesttesttesttes ttesttesttest testtest testte sttesttesttestte sttesttest testtesttestt esttesttesttest testtesttesttesttesttesttes ttesttesttes ttestte sttestte sttesttestt esttesttesttesttest testtestt esttesttesttestt esttesttesttestte sttesttesttestte sttesttestt esttesttesttesttest testtesttes ttesttestte sttestte stte sttest testtesttes ttesttesttesttes ttesttesttest testtest testte sttesttesttestte sttesttest testtesttestt esttesttesttest testtesttest
                                </div>
                                <h4 class="date post_meta">August 17th, 2013</h4>
                                <h4 class="post_meta button" onclick="comment(this, 63, 13)">Reply</h4>
                            </div>
                        </div>
                        <div id="lc_63" class="load_comments">
                        </div>
                        <div style="clear:both;"></div>
                        <div class="post_meta">
                            <div class="pull-left"><a href="#">Full Post</a></div>
                            <div class="pull-right unselectable">2 Days Ago</div>
                        </div>
                        <div style="clear:both;margin-bottom:10px;"></div>
                    </div>
                </div>
            </div>
            <div id="user_info" class="span3">
                <img id="stream_prof_pic" src="<?PHP echo profile_pic_path($stream['id']); ?>" width="220px" height="220px" />
                <div class="row">
                    <div class="span3">
                    
                    </div>
                    <div id="info" class="span3">
                        <?PHP echo $stream['following_num']; ?> following
                    </div>
                </div>
                
                <div class="row">
                    <div id="my_streams" class="span3">
                        <div id="stream_box">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    <?PHP echo $this->load->view('head'); ?> <!--Load CSS and JS after body to improve page performance-->
    <!--Custom JS for this page. CANNOT be added to main.js after development-->
    <script>
    var total_num_following = <?PHP echo $stream['following_num']; ?>;
    var stream_id = <?PHP echo $stream['id']; ?>;
    window.onLoad = tile_images();

    

    </script>
    <style>
        .comment {
            margin-left: 20px;
        }
        .comment img {
            width: 100px;
            height: 100px;
            margin: 10px;
            margin-bottom: 7px;
            float: left;
            cursor: pointer;
            text-align: center;
            border: 5px #FFF solid;
        }
        .comment .post_meta.date {
            float: left;
            margin: 0px;
        }
        .comment .post_meta.button {
            float: right;
            margin: 0px 15px;
            cursor: pointer;

        }
        .comment .content {
            min-height: 100px;
            margin-left: 0px;   
            color: #AAA;
            padding: 7px;
            margin-bottom: 0px;
        }
    </style>
</html>

