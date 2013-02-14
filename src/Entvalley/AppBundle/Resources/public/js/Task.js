(function (App, undefined) {
    "use strict";
    App.Model = App.Model || {};
    App.Model.Task = function (data) {

        var numberOfComments,
            self;

        self = this;
        this.author = data.author.username;
        this.title = data.title;
        this.body = data.body;
        this.excerpt = data.excerpt;
        this.safeBody = data.safe_body;
        this.id = data.id;
        this.assignedTo = ko.observable(data.assigned_to);
        this.status = ko.observable(data.status);
        this.date = ko.observable(Date.parse(data.created_at));
        this.url = Routing.generate('app_task_view', {id: this.id, project: App.UI.project.id,
            project_name: App.UI.project.canonicalName});
        this.comments = ko.observableArray([]);

        numberOfComments = data.number_of_comments || 0;

        if (data.comments) {
            var mappedComments = $.map(data.comments, function (item) {
                return new App.Model.Comment(item);
            });
            this.comments(mappedComments);
        }

        this.numberOfComments = ko.computed(function () {
            return Math.max(numberOfComments, self.comments() ? self.comments().length : 0);
        });

        this.shortCreatedOnDate = ko.computed(function () {
            return App.AbbrMonthNames[ko.utils.unwrapObservable(self.date).getMonth()] + ' ' +
                ko.utils.unwrapObservable(self.date).getDate() + ' ' +
                ko.utils.unwrapObservable(self.date).getFullYear();
        });

        this.createdAtMonth = ko.computed(function () {
            return App.AbbrMonthNames[ko.utils.unwrapObservable(self.date).getMonth()];
        });

        this.createdAtDate = ko.computed(function () {
            return App.WeekdayNames[ko.utils.unwrapObservable(self.date).getDay()];
        });

        this.statusName = ko.computed(function () {
            return App.Model.TaskStatus.Names[this.status() - 1];
        }, this);
    };

    App.Model.TaskStatus = {
        UNASSIGNED: 1,
        ACCEPTED: 2,
        CLOSED: 3,
        REOPENED: 4,
        REJECTED: 5,
        WONTFIX: 6,

        Names: ['unassigned', 'accepted', 'closed', 'reopened', 'rejected', 'wontfix']
    };

    App.Model.Task.prototype = (function () {
        return {
            hasStatus: function (statusName) {
                return this.status() - 1 === App.Model.TaskStatus.Names.indexOf(statusName);
            },

            editTask: function () {
                $.ajax(Routing.generate('app_task_edit', {
                    id: this.id,
                    project: App.UI.project.id,
                    project_name: App.UI.project.canonicalName
                }), {
                    dataType: "script"
                });

                var me = this,
                    cancel = function (e) {
                        if (e.type !== 'keyup') {
                            e.preventDefault();
                        }
                        App.UI.project.switchToTask(me.id);
                        App.UI.removeWYSIWYG();
                    };

                $('#task-' + this.id).one('click', '.cancel', cancel);
                App.UI.registerEscHandler(cancel);
            },

            addComment: function (data) {
                this.comments.push(new App.Model.Comment(data));
            },

            deleteComment: function (id) {
                this.comments.remove(function (comment) {
                    return comment.id === parseInt(id, 10);
                });
            },

            hideComment: function (elem) {
                if (elem.nodeType === 3) { // skip text nodes
                    return;
                }

                $(elem).animate({ height: 'toggle', opacity: 'toggle' }, '400', function () {
                    $(this).remove();
                });
            },

            afterCommentAdded: function (elem) {
                if (elem.nodeType === 3) { // skip text nodes
                    return;
                }
                $(elem).filter("li").effect("highlight");
            },

            formatDate: function (date) {
                var hours = date().getHours(),
                    minutes = date().getMinutes();
                return (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
            },

            updateComment: function (id, text, safeText) {
                $.grep(this.comments(), function (elm) {
                    if (elm.id === id) {
                        elm.text(text);
                        elm.safeText(safeText);
                    }
                });
            }
        };
    }());
})(window.App = window.App || {});