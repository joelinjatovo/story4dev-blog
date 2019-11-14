jQuery(document).ready(function(){
    jQuery(".resend_order_email").on('click', function(e){
        e.preventDefault();
        var data = {
            'action': 'resend_order_email',
            'order_id' : jQuery(this).attr('data-id')
        };
        var options   = {
            url : ajax_object.ajax_url,
            method: "POST",
            data: data,
            beforeSend: function( xhr ) {
                jQuery('.preloader').fadeIn();
            }
        }
        
        jQuery.ajax(options)
        .done(function(result) {
            console.log(result);
            jQuery('.preloader').fadeOut();
            var data = JSON.parse(result);
            if(data.state==1){
                swal(data.message, {icon: "success",});
            }else{
                swal(data.message, {icon: "error",dangerMode: true,});
            }
        })
        .fail(function(err) {
            jQuery('.preloader').fadeOut();
            swal(ajax_object.error_message, {icon: "error",dangerMode: true,});
            console.log(err);
        });
    });

});