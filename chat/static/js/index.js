
(function(){

    var current_user_info, message_input_area, message_container, 
        message_template_compiled, record_template_compiled, user_info_template_compiled,
        user_list_scroll, msg_wrap_list_scroll,
        users_list = [], msg_wrap_list = [],

    // 长轮询接收消息
    // 获取消息50毫秒后重新接收
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

            setTimeout( polling, 50 );
        })
        .fail(function( result ){
        });
    }

    // 长轮询接收消息回调函数
    function on_message_recieve( messages ){

        var users_missed = [];

        var selected_user = $('.select').attr('user_id');

        for ( var i = 0; i < messages.length; ++i ){

            var user_id = messages[i]['from_uid'];
            var user = users_list[ user_id ];

            user.children('.unread-count').show();

            new_message = {
                'classname': 'from',
                'content': messages[i]['content'],
                'photo': user.children('.photo').first().attr('src')
            }

            // 用户未在聊天列表中
            if ( user == undefined ){
                if ( users_missed[ user_id ] == undefined ){
                    users_missed[ user_id ] = [ new_message ];
                }else{
                    users_missed[ user_id ].push( new_message );
                }
                continue;
            }

            add_message( user_id, new_message );
        }

        // 处理未在聊天列表中的用户的
        if ( users_missed.length ){
            add_new_user( users_missed );
        }
    }

    // 添加新用户信息
    function add_new_user( users_missed ){
        for( user_id in users_missed ){
            $.ajax({
                url: '/chat/get_user_info',
                type: 'GET',
                dataType: 'json',
                data: {
                    user_id: user_id
                }
            })
            .done(function( result ) {
                on_get_user_info( user_id, result.user_info, users_missed[user_id] );
            });
        }
    }

    // 获取新用户信息回调函数
    function on_get_user_info( user_id, user_info, messages ){
        // 添加到聊天列表
        var new_user = $( user_info_template_compiled( user_info ) );
        new_user.appendTo( '.users-list' );
        new_user.children('.unread-count').show();
        users_list[ user_id ] = new_user;

        // 添加相应聊天记录模块
        var new_msg_wrap = $( record_template_compiled( { from_uid: user_id } ) );
        new_msg_wrap.appendTo( '.msg-list' );
        msg_wrap_list[ user_id ] = new_msg_wrap;

        // 添加消息
        for ( i in messages ){
            new_msg_wrap.append(message_template_compiled(messages[i]));
        }
    }

    // 发送消息
    function send_message(){
        var selected_user = $('.select');
        var content = message_input_area.val();

        // 未选择用户 或者 输入区内容为空时不发送
        if ( selected_user.length == 0 || content.length == 0 ){
            return;
        }

        message_input_area.val( '' );

        add_message( $('.select').attr('user_id'), {
            'classname': 'self',
            'content': content,
            'photo': current_user_info['photo']
        });

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
        })
        .fail(function( data ) {
        });
    }

    // 添加消息到聊天记录
    function add_message( user_id, message ){

        var target_msg_wrap = msg_wrap_list[ user_id ];
        var target_msg_inner = target_msg_wrap.children('.msg-inner');

        target_msg_inner.append( message_template_compiled( new_message ) );

        // 聊天记录框往下滚动
        if ( target_msg_wrap.prop('id') == 'active' ){
            var overflow_height = target_msg_wrap.height() - target_msg_inner.height();
            if ( overflow_height > 0 ){
                overflow_height = 0;
            }
            msg_wrap_list_scroll.scrollTo( 0, overflow_height );
        }
    }

    // 计时器
    function clock(){
        var current_date = new Date();

        var h, m, s;
        h = add_pre_zero( current_date.getHours() );
        m = add_pre_zero( current_date.getMinutes() );

        $('.cur-time').html( h + ':' + m );
        setTimeout( clock, 500 );
    }

    function add_pre_zero( i ){

        return i < 10 ? '0' + i : i;
    }

    $(document).ready(function() {

        user_list_scroll = new IScroll('#users-list-wrapper', {
            mouseWheel: true
        });

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
        
        // 点击用户名字，显示相应聊天记录框
        $('.user').on('click', '.name', function(event) {

            event.preventDefault();

            var parent = $(this).parent('.user');

            parent.siblings('.select').removeClass('select');

            var unread_count_ele = $(this).children('.unread-count');
            unread_count_ele.html('0');
            unread_count_ele.hide();

            parent.addClass('select');

            $.each( msg_wrap_list, function(index, element) {
                $(element).prop('id', '');
                $(element).addClass('hidden');
            });

            var target_msg_wrap = msg_wrap_list[ parent.attr('user_id') ];

            target_msg_wrap.removeClass('hidden');
            target_msg_wrap.prop('id', 'active');

            var overflow_height = target_msg_wrap.height() - target_msg_wrap.children('.msg-inner').height();
            if ( overflow_height > 0 ){
                overflow_height = 0;
            }

            msg_wrap_list_scroll = new IScroll('#active', {
                mouseWheel: true,
                startY: overflow_height
            });
        });

        // ctrl + enter 发送消息
        $(document).keypress( function( event ){

            // 按下的是不是ctrl+enter则不处理
            if ( !( event.ctrlKey && ( event.which == 13 || event.which == 10 ) ) ){
                return true;
            }

            send_message();
        });

        // 点击“发送”按钮发送消息
        $('.send-btn').on('click', function(event) {

            send_message();
        });

        // 时钟
        clock();
        
        // 消息长轮询
        polling();
    });

})()
