
$(document).ready(function() {

    var is_init = false;
    var patient_mask = $('.patient-mask');
    var patient_details = $('.patient-details');
    var patient_record_wrap = $('.patient-record-wrap');
    var patient_details_mask = $('.patient-details-mask');
    var patient_pagination_list = $('.patient-pagination-list');
    var patient_detail_table = $('.patient-details-table');
    var patient_record_inner = $('.patient-record-inner');

    var pagination_tempalte_compiled = _.template( $('#pagination-template').html() );
    var patient_template_compiled = _.template( $('#patient-template').html() );
    var record_template_compiled = _.template( $('#patient-record-template').html() );
    var return_template_compiled =_.template( $('#patient-return-template').html() );
    var return_add_template_compiled = _.template( $('#return-add-template').html() );

    // 主模块“复诊时间”按钮点击
    $('.chat-top .return').on('click', function(event) {

        render_patient_list( 1 );
    });

    patient_mask.on('click', function(event) {
        is_init = false;
        $(this).fadeOut();
        patient_details_mask.fadeOut();
    });

    // 下一页
    $('.patient-page-next').on('click', function(event) {
        var current_active_ele = $('.active');
        var next_active_ele = current_active_ele.next();

        var current_page = current_active_ele.attr('page');
        var next_page = next_active_ele.attr('page');

        if ( next_page == undefined ){
            return true;
        }

        current_active_ele.removeClass('active');
        next_active_ele.addClass('active');

        render_patient_list( next_page );
    });

    // 上一页
    $('.patient-page-prev').on('click', function(){
        var current_active_ele = $('.active');
        var prev_active_ele = current_active_ele.prev();

        var current_page = current_active_ele.attr('page');
        var prev_page = prev_active_ele.attr('page');

        if ( prev_page == undefined ){
            return true;
        }

        current_active_ele.removeClass('active');
        prev_active_ele.addClass('active');

        render_patient_list( prev_page );
    });

    // 分页点击-- 事件委托
    patient_pagination_list.on('click', '.patient-page-num', function(event) {
        var current_active_ele = $('.active');

        var target_page = $(this).attr('page');

        console.log( current_active_ele );
        console.log( $(this) );

        $(this).addClass('active');
        current_active_ele.removeClass('active');

        render_patient_list( target_page );
    });

    // 病人列表按钮 -- 事件委托
    patient_detail_table.on('click', '.patient-set-btn', function(event) {

        $.ajax({
            url: '/doc/get_record_detail',
            type: 'GET',
            dataType: 'json',
            data: {
                record_id: $(this).attr('record_id')
            }
        })
        .done(function( result ) {
            if ( result.error_code == 0 ){

                var result = result.result;
                var datetime = result.datetime.split(' ');
                patient_record_inner.children().remove();
                patient_record_inner.append(record_template_compiled({
                    date: datetime[0],
                    time: datetime[1],
                    period: result.period ? '下午' : '上午',
                    doctor: result.doctor
                }));

                if ( result.return_date ){
                    patient_record_inner.append(return_template_compiled({
                        date: result.return_date,
                        doctor: result.doctor
                    }));
                }else{
                    patient_record_inner.append(return_add_template_compiled({
                        record_id: result.record_id
                    }));
                }

                // 隐藏病人列表浮层页  
                patient_details.hide();

                // 显示病人挂号记录浮层页
                patient_record_wrap.show();
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });

    // 设置复诊时间 -- 事件委托
    patient_record_inner.on('click', '.patient-item-btn', function(event) {

        var year = $('.select-year').val();
        var month = $('.select-month').val();
        var day = $('.select-day').val();

        $.ajax({
            url: '/doc/modify_return',
            type: 'POST',
            dataType: 'json',
            data: {
                record_id: $(this).attr('record_id'),
                date: year + '-' + month + '-' + day
            },
        })
        .done(function( result ) {
            if ( result.error_code == 0 ){
                // 隐藏病人挂号记录浮层页
                patient_record_wrap.hide();

                // 显示病人列表浮层页
                patient_details.show();
            }else{
                alert( result.message );
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });

    // 渲染病人列表 
    function render_patient_list( page ){
        $.ajax({
            url: '/doc/get_records',
            type: 'GET',
            dataType: 'json',
            data: {
                page: page
            }
        })
        .done(function( result ) {
            if ( result.error_code == 0 ){
                
                var records = result.records;
                // 渲染病人列表
                patient_detail_table.children('.patient-details-content').remove();
                for( i in records ){
                    var record = records[i];
                    patient_detail_table.append(patient_template_compiled(record));
                }

                // 初始化分页列表
                if ( !is_init ){

                    is_init = true;
                    var last_page = result.last_page;
                    patient_pagination_list.children().remove();
                    for ( var i = 0; i != last_page; ++i ){
                        patient_pagination_list.append( pagination_tempalte_compiled({ page: i + 1 }) );
                    }
                    patient_pagination_list.children().first().addClass('active');

                    patient_mask.fadeIn();
                    patient_details_mask.fadeIn();

                    patient_details.show();
                    patient_record_wrap.hide();
                }
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        }); 
    }

    patient_details_mask.on('change', '.select-month', function(event) {
        var month = parseInt( $(this).val() );
        var year = parseInt( $('.select-year').val() );

        var mds = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        if ( month == 2 ){
            if ( year % 4 == 0 ){
                console( year % 4 );
                mds[1] = 29;
            }
        }

        var day_select = $('.select-day');
        var options = day_select.children();

        if ( mds[month-1] < options.length ){

            day_select.children('option:gt(' + (mds[month-1] - 1) + ')').remove();

        }else if ( mds[month-1] > options.length ){

            for ( var i = options.length + 1; i <= mds[month-1]; ++i ){
                day_select.append('<option value="' + i + '">' + i + '</option>');
            }
        }
    });

    patient_details_mask.on('click', '.close-btn', function(event){
        patient_record_wrap.fadeOut();
        patient_details.show();
    });

});