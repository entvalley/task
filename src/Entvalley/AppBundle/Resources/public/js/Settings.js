jQuery(function () {
    "use strict";
    (function (App, Routing, undefined) {
        var Settings = function () {
            var self = this;
            self.collaborators = ko.observableArray([]);
            self.invitations = ko.observableArray([]);

            self.updateInvitations = function (invitees) {
                ko.utils.arrayForEach(invitees, function (newInvitee) {
                    var exists = false;

                    ko.utils.arrayForEach(self.invitations(), function (invitee) {
                        if (invitee.isSameAs(newInvitee)) {
                            exists = true;
                            invitee.createdAt(newInvitee.createdAt());
                        }
                    });

                    if (exists === false) {
                        self.invitations.unshift(newInvitee);
                    }
                });
                var result;
                if (invitees.length === 1) {
                    result = 'The invitation has been sent to ' + invitees[0].inviteeEmail() + '.';
                } else if (invitees.length === 0) {
                    result = 'No invitation has been sent. Please type at least one email.';
                } else {
                    result = 'The invitations have been sent to ' + invitees.length + ' people.';
                }

                $('.project-invitation .alert')
                    .text(result)
                    .slideDown();
            };
        };

        var settings = document.getElementById('settings');
        if (settings) {
            ko.applyBindings(App.UI.settings = new Settings(), settings);
        }
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});