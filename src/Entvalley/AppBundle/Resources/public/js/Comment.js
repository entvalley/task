(function (App, undefined) {
    "use strict";
    App.Model = App.Model || {};
    App.Model.Comment = function (data) {
        var self = this;
        self.text = ko.observable(data['text']);
        self.safeText = ko.observable(data['safe_text']);
        self.username = data.author.username;
        self.createdAt = data['created_at'];
        self.id = data.id;

        var statusChange = data['status_change'] || {
            status: null,
            created_at: null
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

    App.Model.Comment.prototype = (function () {
        return {
            edit: function () {
                var comment = this;
                var dfd = $.ajax(Routing.generate('app_comment_edit', {
                        id: comment.id
                    }), {
                        dataType: "script"
                    }),
                    cancel = function (e) {
                        if (e.type !== 'keyup') {
                            e.preventDefault();
                        }
                        $('#comment-' + comment.id).find('.edit-comment').remove();
                        $('#comment-' + comment.id).find('.text').show();
                        App.UI.removeWYSIWYG();
                    };

                dfd.done(function () {
                    $('#comment-' + comment.id).one('click', '.cancel', cancel);
                    App.UI.registerEscHandler(cancel);
                });
            },

            remove:  function () {
                if (!window.confirm("Are you sure you want to delete the comment?")) {
                    return;
                }

                $.ajax(Routing.generate('app_comment_delete', { id: this.id }), { dataType: "script" });
            },

            removeText: function () {
                this.text('');
                this.safeText('');
            }
        };
    }());
})(window.App = window.App || {});