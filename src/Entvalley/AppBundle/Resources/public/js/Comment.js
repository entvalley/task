(function (App, undefined) {
    "use strict";
    App.Model = App.Model || {};
    App.Model.Comment = function (data) {
        var self = this;
        self.text = ko.observable(data.text);
        self.safeText = ko.observable(data.safe_text);
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

        self.edit = function () {
            $.ajax(Routing.generate('app_comment_edit', {
                id: self.id
            }), {
                dataType: "script"
            });

            var cancel = function (e) {
                if (e.type !== 'keyup') {
                    e.preventDefault();
                }
                $('#comment-' + self.id).find('.edit-comment').remove();
                $('#comment-' + self.id).find('.text').show();
                App.UI.removeWYSIWYG();
            };

            $('#comment-' + self.id).one('click', '.cancel', cancel);

            App.UI.registerEscHandler(cancel);
        };
    };
})(window.App = window.App || {});