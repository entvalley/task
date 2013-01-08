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
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});