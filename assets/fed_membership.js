jQuery(document).ready(function ($) {
    var body = $('body');

    body.on('click', '.fed_m_add_content', function (e) {
        var click = $(this);
        var url = click.closest('.fed_data_url_container').data('url');
        $.fed_toggle_loader();
        $.ajax({
            type: 'POST',
            url: url,
            success: function (results) {
                if (results.success) {
                    click.closest('.fed_m_single_pricing_container').append(results.data.html);
                } else {
                    swal({'title': 'Something went wrong', 'type': 'error'});
                }
                $.fed_toggle_loader();
            }
        });
        e.preventDefault();
    });
    body.on('click', '.fed_m_remove_content', function (e) {
        var click = $(this);
        if (click.closest('.fed_data_url_container').find('.fed_m_single_pricing_container').length <= 1) {
            swal({'title': 'Sorry you need at least one to add', 'type': 'warning'});
        } else {
            click.closest('.fed_m_single_pricing_container').remove();
        }

        e.preventDefault();
    });

    body.on('click', '.fed_m_add_membership', function (e) {
        var click = $(this);
        click.closest('form').find('.pricingTable').removeClass('initial');
        $.fed_toggle_loader();
        $.ajax({
            type: 'POST',
            url: click.data('url'),
            success: function (results) {
                if (results.success) {
                    click.closest('.fed_m_content_container').append(results.data.html);
                }
                $.fed_toggle_loader();
            }
        });

        e.preventDefault();
    });

    body.on('click', '.fed_m_remove_membership', function (e) {
        if ($('.fed_m_template').length > 1) {
            $(this).closest('.fed_m_template').remove();
        } else {
            swal({
                'text': 'Sorry! You can not delete all the Membership Template, you need at least one to save',
                'type': 'warning'
            });
        }
        e.preventDefault();
    });

    body.on('click', '.fed_m_membership_button', function (e) {
        var click = $(this);
        $.fed_toggle_loader();
        $.ajax({
            type: 'POST',
            url: click.data('url'),
            success: function (results) {
                $.fed_toggle_loader();
                if (results.data.url.length > 0) {
                    window.location.replace(results.data.url);
                }
            }
        });
    });


    if ($('.pricingTable.initial').length) {
        $('.preview-area').addClass('hide');
    }
});