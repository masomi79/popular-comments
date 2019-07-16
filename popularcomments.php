<?php
/*
Plugin Name: Popular Comments
Plugin URI: 
Description: はじめての三国志でコメントへの投票、集計と表示、ソートができるようにします。
Author: 
Version: 7.5.8
Author URI: https://hajimete-sangokushi.com
*/



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


//コメント投稿時にカスタムフィールド _commentsvote を作成
function register_votecount($commentid) {
    update_comment_meta($commentid, '_commentsvote', 1);
}
add_filter( 'wp_insert_comment', 'register_votecount' );

// fill custom field _commentsvote
function add_metata(){
    $postid = get_the_ID();
    $commentsargs = array(
        'post_id' => $postid);
    $comments = get_comments($commentsargs);
    foreach($comments as $comm){
        $comment_ID = $comm->comment_ID;
        if(!($commentsvote = get_comment_meta($comment_ID, '_commentsvote', true))){
            register_votecount($comment_ID);
        }else{

        }
    }
}
add_action( 'wp_head', add_metata);

// 投票数の表示
function commentsvote_showlink() {
    $link = "";
    $nonce = wp_create_nonce("commentsvote_nonce");
    $current_CommentID =  get_comment_ID();
    // カスタムフィールド  '_commentsvote' を取得
    $votes = get_comment_meta($current_CommentID, '_commentsvote', true) != '' ? get_comment_meta($current_CommentID, '_commentsvote', true) : '0';
    
    $arguments = $current_CommentID.",'".$nonce."'";

    // 投票ボタンを生成
    $link = '<span class="total-votes">' . $votes.'</span><a onclick="commentsvote_add('.$arguments.');" class="suki">'.'+'.'</a>';
    $link .= ' <a onclick="commentsvotedislike_add('.$arguments.');" class="kirai">'.'-'.'</a>';
    $page_id     = get_queried_object_id();
    $current_url = $_SERVER["REQUEST_URI"];

    $completelink = '<div id="commentsvote-'.$current_CommentID.'" class="popularcomments-area">';
    $completelink .= '<span>'.$link.'</span>';
    $completelink .= '<a rel="nofollow" class="comment-reply-link henshin" href="' . $current_url . '?replytocom=' . $current_CommentID . '#respond" data-commentid="' . $current_CommentID . '" data-postid="' . $page_id . '" data-belowelement="div-comment-' . $current_CommentID . '" data-respondelement="respond" aria-label="Inuha に返信">返</a>';
    $completelink .= '</div>';
    $completelink .= '';
    return $completelink;
}
function commentsvote_comment_text($content) {
//    $current_PostID = $post->ID;
    return $content.commentsvote_showlink();
}
add_filter('comment_text', commentsvote_comment_text);


//コメント表示のソート

function edit_comment_query( $comment_args ) {
    $sort_key = $_GET['sortKey'];
    if($sort_key < 1){
        $comment_args['orderby'] = 'comment_date_gmt';
        $comment_args['order'] = 'ASC';
    }elseif($sort_key > 0){
//      $comment_args['orderby'] = 'comment_author';
        $comment_args['orderby'] = 'meta_value_num';
        $comment_args['meta_key'] = '_commentsvote';
        $comment_args['order'] = 'ASC';
    }else{
        $comment_args['orderby'] = 'comment_date_gmt';
        $comment_args['order'] = 'ASC';
    }
    return $comment_args;
}
add_filter( 'comments_template_query_args', 'edit_comment_query' );





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



// edit Comment form
function my_comment_form($form){
    // Hide title
    $form['title_reply'] = '';
    // Hide URL form
    $form['fields']['url'] = '';
    // Hide title
    $form['comment_notes_before'] = '';
    $form['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="あなたのコメントを入れてね"></textarea></p>';
    return $form;
}
add_filter('comment_form_defaults', 'my_comment_form');


//コメントがあればコメント数を表示する
function show_comments_number(){
    if ( $comments_number = get_comments_number() ) {     
        $title_meta_comments ='<span class="comments-number-wrap"><a href="#comments" class="comments-number"><span class="comments-number-number">' . $comments_number . "</span>件のコメントがあります</a></span>";
        add_meta_box($title_meta_comments);
    }
}
add_action('postmeta', 'show_comments_number');


?>