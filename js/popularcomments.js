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

    console.log(comment_id);

    jQuery.ajax({
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
            var findClass = '.total-votes-up-' + comment_id;
            jQuery(findClass).html(data);

            console.log('ok');
        //    jQuery(linkofcomment).html('');
        //    jQuery(linkofcomment).append(data);
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            console.log('no');
            alert(errorThrown);
        }
    });
}

function commentsvotedislike_add(comment_id, nonce) {

    jQuery.ajax({
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
            var findClass = '.total-votes-up-' + comment_id;
            jQuery(findClass).html(data);
            console.log(comment_id);
        //    jQuery(linkofcomment).html('');
        //    jQuery(linkofcomment).append(data);
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
jQuery(document).ready(function(){

    /**
     *  GETパラメータを配列にして返す
     *  
     *  @return     パラメータのObject
     *
     */
    var getUrlVars = function(){
        var vars = {}; 
        var param = location.search.substring(1).split('&');
        for(var i = 0; i < param.length; i++) {
            var keySearch = param[i].search(/=/);
            var key = '';
            if(keySearch != -1) key = param[i].slice(0, keySearch);
            var val = param[i].slice(param[i].indexOf('=', 0) + 1);
            if(key != '') vars[key] = decodeURI(val);
        } 
        return vars; 
    }

    jQuery('.comment').each(function(){
        jQuery(this).addClass('opened');
        var replyContents = jQuery(this).find('reply');
        jQuery(this).find('.popularcomments-area').after(replyContents);
    })

    jQuery('.comment-body').each(function(){
        var totalVotes = jQuery(this).find('.total-votes').html();
        var commentBodyID = jQuery(this).attr('id');
        var commentID = commentBodyID.replace('div-comment-','');
        var addHtml = '<span class="total-votes-up total-votes-up-' + commentID + '">' + totalVotes + '</span>';
        jQuery(this).find('.comment-metadata').after(addHtml);
        var val1 = jQuery('.total-votes-up').html();
        if(totalVotes > 19){
            jQuery(this).find('.comment-content').find('p').addClass('over20');
        }
        if(totalVotes < 0){
            jQuery(this).find('.comment-content').find('p').addClass('under0');
        }
    })
    
    jQuery('.comment-author').click(function(){

        var classValue = jQuery(this).parent().parent().parent('.comment').attr('class');

        if(classValue.match('closed')){
            jQuery(this).parent().parent().parent('.comment').addClass('opened');
            jQuery(this).parent().parent().parent('.comment').removeClass('closed');
        }else if(classValue.match('opened')){
            jQuery(this).parent().parent().parent('.comment').addClass('closed');
            jQuery(this).parent().parent().parent('.comment').removeClass('opened');
        }

    });


    var dir1 = getUrlVars();
    var SKS = dir1['sortKey'];

    var sortForm ='<div class="sort-button-wrap"><div class="sort-botton"><form class="comments-sort" action="" method="post"><select name="sort-comments" class="sort-comments">';

    if(SKS == 0){
        var sortForm = sortForm + '<option value="0" selected>日付順</option><option value="1">人気順</option>';
    }else if(SKS == 1){
        var sortForm = sortForm + '<option value="0">日付順</option><option value="1" selected>人気順</option>';
    }else {
        var sortForm = sortForm + '<option value="0" selected>日付順</option><option value="1">人気順</option>';
    }
    sortForm = sortForm + '</select></form></div><p class="comment-header__note"> なるべくマイナスは悪質なコメントに対してのみ押してください</p></div>';
    jQuery('#comment_header').after(sortForm);


    // ソートを選択したらページをリロード
    jQuery('.sort-comments').change(function() {
        var text = jQuery('option:selected').text();  
        var val = jQuery('.sort-comments').val();
        var dir0 = location.href;
        dir0 = dir0.replace('#comments', '');
        var dir = dir0.split("?");

        var relocateUrl = dir[0] + '?sortKey=' + val + '#comments';
        location.href = relocateUrl;
    });

    jQuery('.suki').click(function(){
        jQuery(this).removeAttr('onclick').addClass('pushed').css('background','#389867');
    //    jQuery(this).next('.kirai').removeAttr('onclick').addClass('pushed').css('background','#389867');
    });

    jQuery('.kirai').click(function(){
        jQuery(this).removeAttr('onclick').addClass('pushed').css('background','#389867');
    //    jQuery(this).prev('.suki').removeAttr('onclick').addClass('pushed').css('background','#389867');
    });

});












