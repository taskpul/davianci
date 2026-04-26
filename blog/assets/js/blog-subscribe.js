(function($) {
    'use strict';

    function getVars() {
        if (window.adswthBlogSubscribeVars) {
            return window.adswthBlogSubscribeVars;
        }

        return {
            ajaxurl: '',
            i18n: {
                invalidEmail: 'Please enter a valid email address.',
                unavailable: 'Subscription is currently unavailable. Please try again later.',
                success: 'Thanks for subscribing.',
            }
        };
    }

    $(document).on('submit', '.adswth-blog-subscribe-form', function(event) {
        event.preventDefault();

        var vars = getVars();
        var $form = $(this);
        var $email = $form.find('input[name="EMAIL"]');
        var $message = $form.find('.adapsubmit');
        var $button = $form.find('button[type="submit"]');
        var email = $.trim($email.val());

        if (!email || email.indexOf('@') === -1) {
            $message.text(vars.i18n.invalidEmail);
            return;
        }

        $button.prop('disabled', true);
        $message.text('');

        $.post(vars.ajaxurl, {
            action: 'adswth_blog_subscribe',
            email: email,
            nonce: vars.nonce
        }).done(function(response) {
            if (response && response.success) {
                $message.text((response.data && response.data.message) ? response.data.message : vars.i18n.success);
                $form.trigger('reset');
                return;
            }

            $message.text((response && response.data && response.data.message) ? response.data.message : vars.i18n.unavailable);
        }).fail(function() {
            $message.text(vars.i18n.unavailable);
        }).always(function() {
            $button.prop('disabled', false);
        });
    });
})(jQuery);
