<?php
/*
Plugin Name: Popular Comments
Plugin URI: 
Description: comments vote comment_metaを使用して投票できるようにします。
Author: Massumi Fukuda
Version: 3.59.4
Author URI: massumifukuda.work
*/


// アクションフック一覧
/*
add_action('comment_form_logged_in_after',function($piyo, $fuga){
    echo $piyo + $fuga;
},10,2);
add_action('comment_form_after_fields',function($piyo, $fuga){
    echo $piyo + $fuga;
},10,2);
add_action('preprocess_comment',function($piyo, $fuga){
    echo $piyo + $fuga;
},10,2);
add_action('wp_insert_comment',function($piyo, $fuga){
    echo $piyo + $fuga;
},10,2);
add_action('edit_comment',function($piyo, $fuga){
    echo $piyo + $fuga;
},10,2);*/
add_filter('comment_meta',function($piyo, $fuga){
    echo "umco";
});


// プラグインフォルダー
define('VOTECOMMENTSURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('VOTECOMMENTPATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );


// cssファイル読み込み
function voteme_enqueuestyle() {
    wp_enqueue_style('votecomment', VOTECOMMENTSURL.'/css/popularcomments.css');
}
add_action('wp_enqueue_scripts', voteme_enqueuestyle);

// jsファイル読み込み
function voteme_enqueuescripts() {
    wp_enqueue_script('votecomment', VOTECOMMENTSURL.'/js/popularcomments.js', array('jquery'));
    wp_localize_script( 'votecomment', 'votecommentajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts', voteme_enqueuescripts);


// 投票数の表示
function commentsvote_showlink() {
    $link = "";
    $nonce = wp_create_nonce("commentsvote_nonce");
    $current_CommentID =  get_comment_ID();
    $votes = get_comment_meta($current_CommentID, '_commentsvote', true) != '' ? get_comment_meta($current_CommentID, '_commentsvote', true) : '0';
    $arguments = $current_CommentID.",'".$nonce."'";
    $link = '<span class="total-votes">' . $votes.'</span><a onclick="commentsvote_add('.$arguments.');" class="suki">'.'+'.'</a>';
    $link .= ' <a onclick="commentsvotedislike_add('.$arguments.');" class="kirai">'.'-'.'</a>';
    $completelink = '<div id="commentsvote-'.$current_CommentID.'" class="popularcomments-area">';
    $completelink .= '<span>'.$link.'</span>';
//    $completelink .= '<span><span class="reply-button"><a class="henshin">返</a></span></span>';
    $completelink .= '<a rel="nofollow" class="comment-reply-link" href="/wp/2011/07/uploader/?replytocom=5#respond" data-commentid="5" data-postid="81" data-belowelement="div-comment-5" data-respondelement="respond" aria-label="Inuha に返信"><svg class="icon icon-mail-reply" aria-hidden="true" role="img"> <use href="#icon-mail-reply" xlink:href="#icon-mail-reply"></use> </svg>返</a>';
    $completelink .= '</div>';
 /*   $completelink .= '
        <div class="comment-respond">
        <form action="https://massumifukuda.work/wp/wp-comments-post.php" method="post" id="commentform" class="comment-form" novalidate="">
        <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required">あなたのコメントを入れてね</textarea>
        <p class="form-submit"><input name="submit" type="submit" id="submit" class="submit" value="コメントを送信"> <input type="hidden" name="comment_post_ID" value="' . $current_CommentID . '" id="comment_post_ID"><input type="hidden" name="comment_parent" id="comment_parent" value="0"></p>
        </form>
        </div>';
*/
    $completelink .= '';
    return $completelink;
}


function commentsvote_comment_text($content) {
    return $content.commentsvote_showlink();
}

function say_umco(){
    $umco = "<h1>umco</h1>";
    return $umco;
}
function hey_say_umco($content) {
    return $content.say_umco();
}
add_filter('wp_list_comments', hey_say_umco);
add_filter('comment_meta', commentsvote_comment_text);
// add_filter('comment_form_default_fields', commentsvote_comment_text);
add_filter('comment_text', commentsvote_comment_text);


function commentsvote_ajaxhandler() {
    if ( !wp_verify_nonce( $_POST['nonce'], "commentsvote_nonce")) {
        exit("Something Wrong");
    }
 
    $results = '';
    global $wpdb;
 
    $commentid = $_POST['commentid'];
    $votecount = get_comment_meta($commentid, '_commentsvote', true) != '' ? get_comment_meta($commentid, '_commentsvote', true) : '0';
    $votecountNew = $votecount + 1;
    update_comment_meta($commentid, '_commentsvote', $votecountNew);
 
    $results.='<div class="votescore" >'.$votecountNew.'</div>';
 
    //Return the String
    die($results);
}

function commentsvotedislike_ajaxhandler() {
    if ( !wp_verify_nonce( $_POST['nonce'], "commentsvote_nonce")) {
        exit("Something Wrong");
    }
 
    $results = '';
    global $wpdb;
 
    $commentid = $_POST['commentid'];
    $votecount = get_comment_meta($commentid, '_commentsvote', true) != '' ? get_comment_meta($commentid, '_commentsvote', true) : '0';
    $votecountNew = $votecount - 1;
    update_comment_meta($commentid, '_commentsvote', $votecountNew);
 
    $results.='<div class="votescore" >'.$votecountNew.'</div>';
 
    //Return the String
    die($results);
}
// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_commentsvote_ajaxhandler', 'commentsvote_ajaxhandler' );
add_action( 'wp_ajax_commentsvote_ajaxhandler', 'commentsvote_ajaxhandler' );

// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_commentsvotedislike_ajaxhandler', 'commentsvotedislike_ajaxhandler' );
add_action( 'wp_ajax_commentsvotedislike_ajaxhandler', 'commentsvotedislike_ajaxhandler' );


function my_comment_form($form){
    $form['title_reply'] = '';
    $form['fields']['url'] = '';
    $form['comment_notes_before'] = '';
    $form['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="あなたのコメントを入れてね"></textarea></p>';
    return $form;
}
add_filter('comment_form_defaults', 'my_comment_form');
?>