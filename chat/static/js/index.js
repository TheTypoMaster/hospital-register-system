
(function(){

    var current_user_info, message_input_area, message_container, 
        search_template_compiled, message_template_compiled, 
        record_template_compiled, user_info_template_compiled,
        user_list_scroll, msg_wrap_list_scroll,
        users_list = [], msg_wrap_list = [];

    // 长轮询接收消息
    // 获取消息50毫秒后重新接收
    function polling(){
        $.ajax({
            url: '/chat/receive',
            type: 'GET',
            dataType: 'json',
            timout: 50 * 1000
        })
        .done(function( result ){

            if ( result.error_code == 0 ){
               on_message_receive( result.messages );
            }

            setTimeout( polling, 50 );
        })
        .fail(function( result ){
        });
    }

    // 长轮询接收消息回调函数
    function on_message_receive( messages ){

        var users_missed = [];
        var selected_user = $('.select').attr('user_id');

        for ( var i = 0; i < messages.length; ++i ){

            var user_id = messages[i]['from_uid'];
            var user = users_list[ user_id ];

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

            // 未读计数器
            var unread_count_ele = user.children('.unread-count');
            var unread_count = parseInt( unread_count_ele.html() );
            unread_count_ele.html( unread_count + 1 );
            
            // 移动用户到顶部
            user.remove();
            user.prependTo('.users-list');

            // 显示未读计数器
            if ( !user.hasClass('select') ){
                user.children('.unread-count').show();
            }

            // 添加消息到对应聊天记录框
            add_message( user_id, new_message );
        }

        // 处理未在聊天列表中的用户
        if ( users_missed.length ){
            add_new_user( users_missed );
        }
    }

    // 添加新用户信息
    function add_new_users( new_users, callback ){
        for( user_id in new_users ){
            $.ajax({
                url: '/chat/user_info',
                type: 'GET',
                dataType: 'json',
                data: {
                    user_id: user_id
                }
            })
            .done(function( result ) {
                on_get_user_info( user_id, result.user_info, new_users[user_id] );

                if ( callback ){
                    callback();
                }
            });
        }
    }

    // 获取新用户信息回调函数
    function on_get_user_info( user_id, user_info, messages ){
        // 添加到聊天列表
        var new_user = $( user_info_template_compiled( user_info ) );
		var unread_count_ele = new_user.children('.unread-count');
        new_user.appendTo( '.users-list' );
        unread_count_ele.show();
        users_list[ user_id ] = new_user;

        // 添加相应聊天记录模块
        var new_msg_wrap = $( record_template_compiled( { from_uid: user_id } ) );
        new_msg_wrap.appendTo( '.msg-list' );
        msg_wrap_list[ user_id ] = new_msg_wrap;

        // 未读消息数
		unread_count_ele.html( messages.length );

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

        selected_user.remove();
        selected_user.prependTo('.users-list');

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

    // 加载聊天记录
    function load_chat_records( user ){
        $.ajax({
            url: '/chat/records',
            type: 'GET',
            dataType: 'json',
            data: {
                tar_uid: user.attr('user_id')
            }
        })
        .done(function( result ) {
            var records = result.records;

            for( r in records ){

                add_message( user.attr('user_id'), {
                    'classname': records[r].type,
                    'content': records[r].content,
                    'photo': records[r].type == 'from' ? user.children('.photo').first().attr('src') : current_user_info['photo']
                });
            }
        })
        .fail(function() {
            console.log("error");
        });
    }

    // 添加消息到聊天记录
    function add_message( user_id, message ){

        var target_msg_wrap = msg_wrap_list[ user_id ];

        target_msg_wrap.children('.msg-inner').append( message_template_compiled( message ) );

        // 聊天记录框往下滚动
        if ( target_msg_wrap.prop('id') == 'active' ){
            refresh_msg_scroll( compute_scroll_y( target_msg_wrap ) );
        }
    }

    function compute_scroll_y( msg_element ){

        var msg_inner = msg_element.children('.msg-inner');
        var overflow_height = msg_element.height() - msg_inner.height();

        if ( overflow_height > 0 ){
            overflow_height = 0;
        }

        return overflow_height;
    }

    function refresh_msg_scroll( overflow_height ){
        msg_wrap_list_scroll = new IScroll('#active', {
            mouseWheel: true,
            startY: overflow_height
        });
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

        console.log( '赶出来的东西，太挫...' );

        user_list_scroll = new IScroll('#users-list-wrapper', {
            mouseWheel: true
        });

        message_input_area = $('#message-input');
        current_user_info = $.parseJSON( $('#current_user_info').html() );

        search_template_compiled = _.template( $('#search-result-template').html() );
        message_template_compiled = _.template( $('#message-template').html() );
        record_template_compiled = _.template( $('#message-record-template').html() );
        user_info_template_compiled = _.template( $('#user-info-template').html() );

        $('.user').each(function(index, element) {
            users_list[ $(element).attr('user_id') ] = $(element);
        });

        $('.msg-wrap').each(function(index, element) {
            msg_wrap_list[ $(element).attr('from_uid') ] = $(element);
        });
        
        // 点击用户名字，显示相应聊天记录框
        $('.users-list').on('click', '.user', function(event) {

            event.preventDefault();

            var user = $(this);

            user.siblings('.select').removeClass('select');

            // 隐藏未读计数器
            var unread_count_ele = user.children('.unread-count');
            unread_count_ele.html('0');
            unread_count_ele.hide();

            user.addClass('select');

            $.each( msg_wrap_list, function(index, element) {
                $(element).prop('id', '');
                $(element).addClass('hidden');
            });

            var target_msg_wrap = msg_wrap_list[ user.attr('user_id') ];

            target_msg_wrap.removeClass('hidden');
            target_msg_wrap.prop('id', 'active');

            // 初次点击时加载聊天记录
            if ( user.attr('unload') != undefined ){
                load_chat_records( user );
                user.removeAttr('unload');
            }

            // 聊天记录框滑到顶部
            refresh_msg_scroll( compute_scroll_y( target_msg_wrap ) );
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

        // 搜索
        $('.search-form').on('submit', function(event){
            event.preventDefault();

            $.ajax({
                url: '/chat/search',
                type: 'GET',
                dataType: 'json',
                data: $(this).serialize()
            })
            .done(function( result ){
                $('.search-result').empty();
                
                if ( result.error_code == 0 ){
                    var users = result.users;
                    for ( u in users ){
                        $('.search-result').append( search_template_compiled(  users[u] ) );
                    }
                }
            })
            .fail(function() {
                console.log("error");
            });
        });

        $('.search-form').on('change', '.search-input', function(event){
            event.preventDefault();

            if ( $(this).val() == '' ){
                $('.search-result').empty();
            }
        });

        $('.search-result').on('click', '.search-result-li', function(event){
            var _user_id = $(this).attr('user_id');

            if ( users_list[ _user_id ] ){
                users_list[ _user_id ].click();
            }else{
                var jj = [];
                jj[ _user_id ] = [];
                add_new_users(jj, function(){
                    var user = users_list[ _user_id ];
                    users_list[ _user_id ].click();
                    load_chat_records( user );

                    // 移动到顶部
                    user.remove();
                    user.prependTo('.users-list');
                });
            }

            $('.search-result').empty();
        });

        // 时钟
        clock();
        
        // 消息长轮询
        polling();

        //test();
    });

    function test(){
        $.ajax({
            url: '/chat/test',
            type: 'GET',
            dataType: 'json',
            data: {
                user_id: [1, 2, 3, 4]
            }
        })
        .done(function( result ) {
            console.log( result );
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    }

})();
