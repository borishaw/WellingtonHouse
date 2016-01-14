$('window').ready(function () {


    $(document).ajaxStart(function(){
       console.log('AJAX has started');
        $(".jquery-response p").html('Sending');
    }).ajaxSuccess(function(){
        console.log('AJAX is successful')
    }).ajaxError(function(){
        console.log('AJAX failed');
        $(".jquery-response p").html('Fail to Send, please try again');
    });

    //Masked Input
    $("#phone").mask("(999) 999-9999");

    //Validate Form
    $('#contact').validate({
        debug: true,

        rules: {
            'First Name':{
                required: true
            },
            'Last Name':{
                required: true
            },
            'Email': {
                required: true,
                email: true
            },
            'Phone': {
                required: true,
                phoneUS: true
            },
            'Role': {
                required: true,
            },
            'Heard From': {
                required: true
            }
        },

        submitHandler: function(){
            var formData = JSON.stringify($('#contact').serializeArray());
            $.post('process.php', formData, function(resp){
                console.log(resp);
                resp = $.parseJSON(resp);
                $(".jquery-response p").html(resp.message);
                if (resp.success == 1) {
                    $('#contact').trigger('reset');
                }
            });
        }
    });
});