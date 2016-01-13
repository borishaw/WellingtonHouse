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

    //Send form with AJAX
    $('#contact').on('submit', function(e){
        e.preventDefault();
        var formData = JSON.stringify($('#contact').serializeArray());
        $.post('process.php', formData, function(resp){
            //resp = $.parseJSON(resp);
            //$(".jquery-response p").html(resp.message);
            console.log(resp);
        });
    });

    //Masked Input
    $("#phone").mask("(999) 999-9999");

});