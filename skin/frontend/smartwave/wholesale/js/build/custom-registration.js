jQuery(document).ready(function ($) {

    $('#affdate').css('width', '94%');
    $('#affprimarydate').css('width', '94%');

    $(".previous").click(function () {
        var previous = $(this).parent().parent().parent().prev().attr('id');
        var current = $(this).parent().parent().parent().attr('id');
        $('#' + previous).show();
        $('#' + current).hide();
    });

    $("#personal_info").click(function () {
        
        var current = $(this).parent().parent().parent().attr('id');
        Validation.prototype.initialize(this.form);
        //Validation.prototype.validate();
        if (Validation.prototype.validate())
        {
            $("#LoadingImage").show();
            var form_key = $("#form_key").val();
            var firstname = $("#firstname").val();
            var lastname = $("#lastname").val();
            var email_address = $("#email_address").val();
            var first_telephone = $("#first_telephone").val();
            var password = $("#password").val();
            var confirmation = $("#confirmation").val();
            var customer_id = $(".customer_id").val();
            $.ajax({
                url: "/registration/account/createpost",
                type: "POST",
                data: {'form_key': form_key, 'customer_id': customer_id, 'firstname': firstname, 'lastname': lastname, 'email': email_address, 'first_telephone': first_telephone, 'password': password, 'confirmation': confirmation},
                dataType: "json",
                success: function (response)
                {
                    $("#LoadingImage").hide();
                    if (response.length) {
                        if (response === 'email exists')
                        {
                            $('.error_msg').addClass('error-msg').html('There is already an account with this email address');
                        } else {
                            $('.error_msg').html('');
                            $(".error_msg").addClass('error-msg').append('<ul id="appended">');
                            var arr = response.toString().split(',');
                            for (var resp = 0; resp < arr.length; resp++) {
                                $('#appended').append('<li><span>' + arr[resp] + '</span></li>');
                            }

                        }
                        
                    } else if (response.result === true) {
                        $('.customer_id').val(response.cust_id);
                        $('#firstname_add').val(response.first_name);
                        $('#lastname_add').val(response.last_name);
                        $('#telephone').val(response.telephone_no);
                        $('.password_hash').val(response.password_hash);
                        $('.error_msg').removeClass('error-msg').html('');
                        $('#' + current).hide();
                        $('#tab2').show();
                    } else {
                        var json_obj = JSON.parse(JSON.stringify(response));//parse JSON
                        var output = "<ul>";
                        for (var res in json_obj)
                        {
                            output += "<li>" + json_obj[res] + "</li>";
                        }
                        output += "</ul>";
                        $('.error_msg').html('');
                        $(".error_msg").addClass('error-msg').html(output);
                        
                    }
                }
                
            });
            
        } else
        {
            return false;
            
        }
        //alert("Hello");
        verticalCenter();
    });

    $("#address_info").click(function () {

        var current = $(this).parent().parent().parent().attr('id');
        Validation.prototype.initialize(this.form);
        //Validation.prototype.validate();
        if (Validation.prototype.validate())
        {
            $("#LoadingImage").show();
            $('#create_address').val('1');
            var street = [];
            var firstname = $("#firstname_add").val();
            var lastname = $("#lastname_add").val();
            var form_key = $("#form_key").val();
            var create_address = $("#create_address").val();
            var default_shipping = $("#default_shipping").val();
            var default_billing = $("#default_billing").val();
            var company = $("#company").val();
            var telephone = $("#telephone").val();
            street[0] = $("#street_1").val();
            street[1] = $("#street_2").val();
            var city = $("#city").val();
            var region_id = $("#region_id").val();
            var zip = $("#zip").val();
            var country = $("#country").val();
            var customer_id = $(".customer_id").val();
            var password_hash = $(".password_hash").val();
            
            $('#addi_info').val('1');
            $.ajax({
                url: "/registration/account/createpost",
                type: "POST",
                data: {'form_key': form_key, 'password_hash':password_hash, 'firstname': firstname, 'lastname': lastname, 'customer_id': customer_id, 'default_shipping': default_shipping, 'default_billing': default_billing, 'create_address': create_address, 'company': company, 'telephone': telephone, 'street': street, 'city': city, 'region_id': region_id, 'postcode': zip, 'country_id': country},
                dataType: 'json',
                success: function (response)
                {
                    $("#LoadingImage").hide();
                    if (response.result === true) {
                        $('.customer_id').val(response.cust_id);

                        $('.error_msg').removeClass('error-msg').html('');
                        $('#' + current).hide();
                        $('#tab3').show();
                    } else {
                        $('.error_msg').html('');
                        $(".error_msg").addClass('error-msg').append('<ul id="appended">');
                        var arr = response.toString().split(',');
                        for (var i = 0; i < arr.length; i++) {
                            $('#appended').append('<li><span>' + arr[i] + '</span></li>');
                        }

                    }

                }
            });

        }
        //alert("Hello");
        verticalCenter();
        
    });

    $('#skip').click(function () {
        var current = $(this).parent().parent().attr('id');
        $('.error_msg').removeClass('error-msg').html('');
        $('#addi_info').val('1');
        $('#create_address').val('');
        $('#' + current).hide();
        $('#tab3').show();

    });

    $('#show_register').click(function () {
        $('.notice-msg').remove();
        $('.success-msg').remove();
        $('#login_box').hide();
        $('#registration_box').show();
        verticalCenter();

    });

    $('#back_login').click(function () {
        $('#registration_box').hide();
        $('#login_box').show();
        verticalCenter();
    });
    $('.btn-back').click(function () {
        verticalCenter();
    });

    // Js for Append error div in previouis span...............................
    $("#tab3 .validation-advice").appendTo("span").prev();


    //code executes when clicks on forgot password link
    $('#forgot-password-link').click(function () {
        $('#login_box').hide();
        $('#tab-forgot-password').show();
        verticalCenter();
    });
    $('#go_back_login').click(function () {
        $('#tab-forgot-password').hide();
        $('#login_box').show();
        verticalCenter();
    });
    
});

jQuery(window).load(function(){
   verticalCenter();
});

jQuery(window).resize(function(){
   verticalCenter();
});


function verticalCenter() {
    var winwidth = jQuery(window).width();
    var winheight = jQuery(window).height();
    var loginheight = jQuery('.login-container').outerHeight();
    
    if(winheight>loginheight){
        var vercenter = (winheight - loginheight) / 2;
        function logincenter() {
            jQuery('.login-container').css('top', vercenter);
        }
        logincenter();
    }else{
        jQuery('.login-container').css('top','10px');
    }
}

