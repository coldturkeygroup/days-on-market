jQuery('document').ready(function ($) {
    // Simple AJAX listeners
    $(document).bind("ajaxSend", function () {
        $('.btn-primary').attr('disabled', 'disabled');
    }).bind("ajaxComplete", function () {
        $('.btn-primary').removeAttr('disabled');
    });

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

            var retargeting = $('#retargeting').val();
            if (retargeting !== '') {
                (function () {
                    var _fbq = window._fbq || (window._fbq = []);
                    if (!_fbq.loaded) {
                        var fbds = document.createElement('script');
                        fbds.async = true;
                        fbds.src = '//connect.facebook.net/en_US/fbds.js';
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(fbds, s);
                        _fbq.loaded = true;
                    }
                    _fbq.push(['addPixelId', retargeting]);
                })();
                window._fbq = window._fbq || [];
                window._fbq.push(['track', 'PixelInitialized', {}]);
            }
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
                var conversion = $('#conversion').val();

                if (conversion !== '') {
                    (function () {
                        var _fbq = window._fbq || (window._fbq = []);
                        if (!_fbq.loaded) {
                            var fbds = document.createElement('script');
                            fbds.async = true;
                            fbds.src = '//connect.facebook.net/en_US/fbds.js';
                            var s = document.getElementsByTagName('script')[0];
                            s.parentNode.insertBefore(fbds, s);
                            _fbq.loaded = true;
                        }
                    })();
                    window._fbq = window._fbq || [];
                    window._fbq.push(['track', conversion, {'value': '0.00', 'currency': 'USD'}]);
                }

                setTimeout(function () {
                    $('#get-results-modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('#days-on-market,.modal-backdrop').remove();
                    $('.results').show();
                }, 1000);
            }
        });
    });
});