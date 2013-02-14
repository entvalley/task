jQuery(function () {
    "use strict";
    (function (App, Routing, undefined) {
        App.Model.ProjectInvitation = function (data) {
            var self = this;
            self.inviteeEmail = ko.observable(data.invitee_email);
            self.invitedAt = ko.observable(data.invited_at);

            self.isSameAs = function (another) {
                return self.inviteeEmail().toLowerCase() === another.inviteeEmail().toLowerCase();
            };
        };

        $('body').on('keydown mousedown', '.project-invitation input[type="email"]', function (e) {
            var INVITEE_SELECTOR = 'input[type="email"]',
                $self = $(this),
                $container = $self.parents('.control-group'),
                $next = $container.next().find(INVITEE_SELECTOR),
                $prev = $container.prev().find(INVITEE_SELECTOR),
                $last = $container.parent().find(INVITEE_SELECTOR).last(),
                noPrevOrPrevHasValue,
                nextIsLastAndHasNoValue,
                applyPrototype;

            applyPrototype = function () {
                var $form = $self.parents('form');
                var count = parseInt('0' + $form.data('invitee-count'), 10);
                var proto = $self.parents('form').data('invitee-email-prototype');
                var $proto = $(proto.replace(/__name__/g, ++count));
                $form.data('invitee-count', count);
                //$container.parent().append(proto);
                $proto.css('display', 'none');

                $proto.insertBefore($form.find('div.submit')).fadeIn();
            };

            noPrevOrPrevHasValue = ($prev.length === 0 || $.trim($prev.val()) !== '');
            if ($.trim($last.val()) !== '' || (($next.length === 0) && noPrevOrPrevHasValue)) {
                applyPrototype();
                return;
            }

            // for TAB-key
            nextIsLastAndHasNoValue = ($last.get(0) === $next.get(0) || $.trim($last.val()) !== '');
            if (e.which === 9 && $.trim($self.val()) !== '' && nextIsLastAndHasNoValue) {
                applyPrototype();
            }
        });

    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});