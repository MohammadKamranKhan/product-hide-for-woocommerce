/**
 *  This JS file will get run AJAX for contact form
 */
jQuery(document).ready(function($){
    $(".click-class").on('click',function(){
        var pdt_id = $(this).data('id');
        $('#'+pdt_id).toggleClass("active");
    });
    // removing active class on close button
    $('.close-btn').on('click',function(){
        $('.popup').removeClass('active');
    });
    // removing active class on close button
    $('.closebtn').on('click',function(){
        $('.alert').hide();
    });

    // onclick popup submit button
    $(".xs-wpah-btn2").on('click',function(event){
        // Avoid reloading page
        event.preventDefault();
        // getting form values
        var product_id = $(this).data('id');
        var username   = $("#xs-wpah-user-name-"+product_id).val();
        var email      = $("#xs-email-"+product_id).val();
        // Validation of poup elements (Name and Email)
        if( username == "" && email == "") {
            $('.xs-wpah-message').text('Please enter your email and name!');
            $(".alert").show();
        }
        else if( username == "" ) {
            $('.xs-wpah-message').text('Please enter your name!');
            $(".alert").show();   
        }
        else if(email == ""){
            $('.xs-wpah-message').text('Please enter your email!');
            $(".alert").show();
        }
        else {
             // Ajax Call
            $.ajax({
                url: xs_wpah_ajax_object.ajax_url,
                type:"POST",
                dataType:'text',
                data: {
                    'action' : 'xs_wpah_popup_form',
                    xs_product_id : product_id,
                    xs_username   : username,
                    xs_email      : email,
                }, 
                // when button is clicked , make it disable temporary
                beforeSend: function() { 
                    $("#xs-wpah-popup-cf").text(' Wait...'); // change text of popup form button
                    $("#xs-wpah-popup-cf").prop('disabled', true); // disable button
                },
                // success callback function if return somthing true
                success: function( response ){
                    
                    if( response == "success") {
                        $('.alert').css({"background-color": "green", "color": "white"});
                        $('.xs-wpah-message').text("Email has been submitted successfully!");
                        $(".alert").show();
                    }
                    else if(response == "failed") {
                        $('.xs-wpah-message').text("OOPS! Your emial could not be submitted.");
                        $(".alert").show();
                    }
                    else if(response == "product_exists") {
                        // if same email and same product already exists then show already have request message
                        $('.alert').css({"background-color": "red", "color": "white"});
                        $('.xs-wpah-message').text("You have already requested for same product with same email ID.");
                        $(".alert").show();
                    }
                    // resetting  form values
                    $("#xs-wpah-user-name-"+product_id).val("");
                    $("#xs-email-"+product_id).val("");
                    // change text of popup form button
                    $("#xs-wpah-popup-cf").text(' Submit '); 
                    // enable button
                    $("#xs-wpah-popup-cf").prop('disabled', false); 
                },
                
                error: function(jqXHR, textStatus, errorThrown){
                    alert("Something went wrong! "+errorThrown);
                }
            });
        }
        
    });
    
    // if X button on message is clicked then disappear message
    $("#xs-wpah-closable-btn").on('click',function(){
        $(".alert").hide();
    });
    
});