
$(document).ready(function() {
    $('.login-form').on( 'click', function(event) {
        event.preventDefault();
        
        $.ajax({
            url: '/doc/login',
            type: 'POST',
            dataType: 'json',
            data: $('.login-form').serialize(),
        })
        .done(function( data ) {
            if ( data.error_code ){
                alert( data.message );
            }else{
                alert( '登陆成功' );
                window.location.href = '/doc/home/account';
            }
        });
        
    });
});