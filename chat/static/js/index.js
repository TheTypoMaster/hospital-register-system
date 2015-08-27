
(function(){

    var current_user_info, message_input_area, message_container, 
        message_template_compiled, record_template_compiled, user_info_template_compiled ;

    var users_list = [];
    var msg_wrap_list = [];

    function polling(){
        $.ajax({
            url: '/chat/recieve',
            type: 'GET',
            dataType: 'json',
            timout: 50 * 1000
        })
        .done(function( result ){

            if ( result.error_code == 0 ){
               on_message_recieve( result.messages );
            }

            console.log( result );
            setTimeout( polling, 50 );
        })
        .fail(function( result ){
            console.log( result );
        });
    }

    function on_message_recieve( messages ){

        for ( var i = 0; i < messages.length; ++i ){

            console.log( messages[i] );

            var user_id = messages[i]['from_uid'];
            var user = users_list[ user_id ];

            new_message = {
                'classname': 'from',
                'content': messages[i]['content'],
                'photo': user.children('.photo').first().attr('src')
            }
            msg_wrap_list[ user_id ].append( message_template_compiled( new_message ) );
        }
        
    }

    function send_message(){
        var user = $('.select');
        var selected_user = user.first();
        var content = message_input_area.val();

        // 未选择用户 或者 输入区内容为空时不发送
        if ( user.length == 0 || content.length == 0 ){
            return;
        }

        message_input_area.val( '' );

        var new_message = { 
            'classname': 'self',
            'content': content,
            'photo': current_user_info['photo']
        };

        var user_id = $('.select').attr('user_id');

        console.log( msg_wrap_list[user_id] );

        console.log( message_template_compiled( new_message ) );

        msg_wrap_list[user_id].append( message_template_compiled( new_message ) );

        console.log( msg_wrap_list[user_id] );

            // 发送消息
        $.ajax({
            url: '/chat/send',
            type: 'POST',
            dataType: 'json',
            data: {
                to_uid: selected_user.attr('user_id'),
                content: content
            }
        })
        .done(function( data ) {
            console.log( data );
        })
        .fail(function( data ) {
            console.log( data );
        });
    }

    function clock(){
        var current_date = new Date();

        var h, m, s;
        h = add_pre_zero( current_date.getHours() );
        m = add_pre_zero( current_date.getMinutes() );
        s = add_pre_zero( current_date.getSeconds() );

        $('.cur-time').html( h + ':' + m + ':' + s );
        setTimeout( clock, 500 );
    }

    function add_pre_zero( i ){

        return i < 10 ? '0' + i : i;
    }

    $(document).ready(function() {

        message_input_area = $('#message-input');
        
        current_user_info = $.parseJSON( $('#current_user_info').html() );

        message_template_compiled = _.template( $('#message-template').html() );
        record_template_compiled = _.template( $('#message-record-template').html() );
        user_info_template_compiled = _.template( $('#user-info-template').html() );

        $('.user').each(function(index, element) {
            users_list[ $(element).attr('user_id') ] = $(element);
        });

        $('.msg-wrap').each(function(index, element) {
            msg_wrap_list[ $(element).attr('user_id') ] = $(element);
        });

        console.log( users_list );
        console.log( msg_wrap_list );
        
        $('.user').on('click', function(event) {

            event.preventDefault();

            $(this).siblings('.select').removeClass('select');

            $(this).addClass('select');

            $.each( msg_wrap_list, function(index, element) {
                $(element).addClass('hidden');
            });

            msg_wrap_list[ $(this).attr('user_id') ].removeClass('hidden');
        });

        $(document).keypress( function( event ){

            // 按下的是不是ctrl+enter则不处理
            if ( !( event.ctrlKey && ( event.which == 13 || event.which == 10 ) ) ){
                return true;
            }

            send_message();
        });

        // 时钟
        clock();
        
        // 消息长轮询
        polling();
    });

})()
