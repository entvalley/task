(function (App, undefined) {
    "use strict";
    App.Model = App.Model || {};
    App.Model.Comment = function (data) {
        var self = this;
        self.text = data.text;
        self.username = data.author.username;
        self.createdAt = data.created_at;
        self.id = data.id;

        var statusChange = data.status_change || {
            status: null,
            created_at: null
        };

        self.generateDeletionLink = function () {
            return Routing.generate('app_comment_delete', { id: self.id });
        };

        self.statusChanged = typeof data.status_change !== 'undefined';

        self.statusChange = ko.observable(statusChange.status);
        self.statusChangeDate = ko.observable(statusChange.created_at);

        self.statusName = ko.computed(function () {
            return App.Model.TaskStatus.Names[self.statusChange() - 1];
        });

        self.statusNameCapitalized = ko.computed(function () {
            var statusName = self.statusName();
            if (!statusName) {
                return;
            }
            return statusName.toUpperCase();
        });
    };
})(window.App = window.App || {});