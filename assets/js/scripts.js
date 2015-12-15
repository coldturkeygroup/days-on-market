jQuery('document').ready(function ($) {
    // Simple AJAX listeners
    $(document).bind("ajaxSend", function () {
        $('.btn-primary').attr('disabled', 'disabled');
    }).bind("ajaxComplete", function () {
        $('.btn-primary').removeAttr('disabled');
    });

    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    // Mailcheck
    $('#email').on('keyup', function () {
        var input = $(this);
        if (input.val().length < 10) {
            return false;
        }

        delay(function () {
            input.mailcheck({
                suggested: function (element, suggestion) {
                    $('.mailcheck-suggestion').remove();
                    $(element).after('<div class="mailcheck-suggestion" style="margin-top:5px;">Did you mean <a href="#">' + suggestion.full + '</a>?</div>');
                },
                empty: function () {
                    $('.mailcheck-suggestion').remove();
                }
            });
        }, 500);
    });

    $('.form-group').on('click', '.mailcheck-suggestion a', function (e) {
        e.preventDefault();

        $('#email').val($(this).text());
        $('.mailcheck-suggestion').remove();
    });

    // Show results modal
    $('#get-results').click(function () {
        $('.alert').remove();

        var validated = 1;

        $('.validate').each(function () {
            if ($(this).val() === '') {
                $('#subtitle').after('<div class="alert alert-danger"><strong>Whoops!</strong> You must fill out all of the fields before searching!</div>');
                validated = 0;
                return false;
            }

            $('#' + $(this).attr('id') + '-answer').text($(this).val());
        });

        $('#num_baths,#num_beds,#desired_price,#sq_ft').each(function () {
            var numbers_comma = /^[0-9,]*$/;

            if (!numbers_comma.test($(this).val())) {
                var label = $("label[for='" + $(this).attr('id') + "']").text();
                $('#subtitle').after('<div class="alert alert-danger"><strong>Whoops!</strong> The value of ' + label + ' must be a number!</div>');
                validated = 0;
            }
        });

        if (validated == 1) {
            $('#get-results-modal').modal('show');
        }

        return false;
    });

    // Submit quiz results
    $('#submit-results').click(function (e) {
        e.preventDefault();
        var form = $('#days-on-market');

        $.ajax({
            type: 'POST',
            url: DaysOnMarket.ajaxurl,
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('#submit-results').html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            },
            async: true,
            success: function (response) {
                setTimeout(function () {
                    $('#get-results-modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('#days-on-market,.modal-backdrop').remove();
                    $('.results').show();

                    var retargeting = $('#retargeting').val(),
                        conversion = $('#conversion').val();
                    if (conversion != '') {
                        if (conversion !== retargeting) {
                            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
                            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
                            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                            document,'script','//connect.facebook.net/en_US/fbevents.js');

                            fbq('init', conversion);
                        }

                        fbq('track', "Lead");
                    }
                }, 1000);
            }
        });
    });
});