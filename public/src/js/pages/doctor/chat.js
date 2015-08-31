
$(document).ready(function() {

    var patient_mask = $('.patient-mask');
    var patient_details = $('.patient-details');
    var patient_record_wrap = $('.patient-record-wrap');
    var patient_details_mask = $('.patient-details-mask');

    var patient_template_compiled = _.template( $('#patient-template').html() );

    $('.chat-top .return').on('click', function(event) {
        
        patient_mask.fadeIn();
        patient_details_mask.fadeIn();

        render_patient_list( 1 );
    });

    $('.patient-page-next').on('click', function(event) {
        var current_active_ele = $('.active');
        var next_active_ele = current_active_ele.next();

        var current_page = current_active_ele.attr('page');
        var next_page = next_active_ele.attr('page');

        if ( current_page == next_page ){
            return true;
        }

        current_active_ele.removeClass('active');
        next_active_ele.addClass('active');

        render_patient_list( next_page );
    });

    $('.patient-page-prev').on('click', function(){
        var current_active_ele = $('.active');
        var prev_active_ele = current_active_ele.prev();

        var current_page = current_active_ele.attr('page');
        var next_page = next_active_ele.attr('page');

        if ( current_page == next_page ){
            return true;
        }

        current_active_ele.removeClass('active');
        next_active_ele.addClass('active');

        render_patient_list( prev_page );
    });

    $('.patient-page-num').on('click', function(event) {
        var current_active_ele = $('.active');

        var target_page = $(this).attr('page');

        $(this).addClass('.active');
        current_active_ele.removeClass('.active');

        render_patient_list( target_page );
    });

    $('.patient-set-btn').on('click', function(event) {
            
        patient_details.hide();

        patient_record_wrap.show();

        // 3. ajax获取数据，渲染设置复诊时间浮层
    });

    function render_patient_list( page ){

    }

});