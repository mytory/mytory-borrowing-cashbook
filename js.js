jQuery(document).ready(function($){
    if($('.mbc-borrowing-list-container').length > 0){
        mytory_borrowing.load_borrowing_list();
    }

    if($('.mbc-basic-form').length > 0){
        $('.mbc-basic-form').ajaxForm({
            success: function(data){
                if(data == 1){
                    alert('save!');
                    mytory_borrowing.load_borrowing_list();
                }else{
                    alert('error!');
                }
            }
        });
    }
});

var $ = jQuery;

var mytory_borrowing = {
    load_borrowing_list: function(args){
        if( ! args){
            var args = {};
        }
        $.extend(args, {action:'print_borrowing_list'});
        $.post(mytory_ajax.ajaxurl, args, function(data){
            $('.mbc-borrowing-list-container').html(data);
        });
    },
    delete_one_borrowing: function(post_id){
        $.post(mytory_ajax.ajaxurl, {action:'delete_one_borrowing', post_id: post_id}, function(data){
            if(data == 1){
                $('.mbc-borrowing-list-container').find('tr[data-post-id="' + post_id + '"]').remove();
            }else{
                alert('error!');
            }
        });
    },
};