/* popular comments js file */

// 動作チェック
/*
$.ajax({
     success : function(response){
         alert('成功');
     },
     error: function(){
         //通信失敗時の処
         alert('通信失敗');
     }
 });
 */

function commentsvote_add(comment_id, nonce) {

    $.ajax({
        type: 'POST',
        url: votecommentajax.ajaxurl,
        data: {
            action: 'commentsvote_ajaxhandler',
            commentid: comment_id,
            nonce: nonce,
            id: 1
        },
        success: function(data, textStatus, XMLHttpRequest) {
            var linkofcomment = '#commentsvote-' + comment_id;
            jQuery(linkofcomment).html('');
            jQuery(linkofcomment).append(data);
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}

function commentsvotedislike_add(comment_id, nonce) {

    $.ajax({
        type: 'POST',
        url: votecommentajax.ajaxurl,
        data: {
            action: 'commentsvotedislike_ajaxhandler',
            commentid: comment_id,
            nonce: nonce,
            id: 1
        },
        success: function(data, textStatus, XMLHttpRequest) {
            var linkofcomment = '#commentsvote-' + comment_id;
            jQuery(linkofcomment).html('');
            jQuery(linkofcomment).append(data);
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
$(document).ready(function(){

    $('.comment').each(function(){
        $(this).addClass('closed');
        var replyContents = $(this).find('reply');
        $(this).find('.popularcomments-area').after(replyContents);
    })

    $('.comment-body').each(function(){
        var totalVotes = $(this).find('.total-votes').html();
        var addHtml = '<span class="total-votes-up">' + totalVotes + '</span>';
        $(this).find('.comment-metadata').after(addHtml);

        var val1 = $('.total-votes-up').html();
        console.log(val1);
    })
    
    $('.comment-author').click(function(){

        var classValue = $(this).parent().parent().parent('.comment').attr('class');

        if(classValue.match('closed')){
            $(this).parent().parent().parent('.comment').addClass('opened');
            $(this).parent().parent().parent('.comment').removeClass('closed');
        }else if(classValue.match('opened')){
            $(this).parent().parent().parent('.comment').addClass('closed');
            $(this).parent().parent().parent('.comment').removeClass('opened');
        }

    });
});












