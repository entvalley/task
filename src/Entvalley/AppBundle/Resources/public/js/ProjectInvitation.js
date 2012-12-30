jQuery(function () {
    "use strict";
    (function (App, Routing, undefined) {
        App.Model.ProjectInvitation = function (data) {
            var self = this;
            self.inviteeEmail = ko.observable(data.invitee_email);
            self.createdAt = ko.observable(data.created_at);

            self.isSameAs = function (another) {
                return self.inviteeEmail() === another.inviteeEmail();
            };
        };
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});