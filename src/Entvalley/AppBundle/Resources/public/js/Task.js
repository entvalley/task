(function (App, undefined) {
    "use strict";
    App.Model = App.Model || {};
    App.Model.Task = function (data) {
        var self = this;
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

        var numberOfComments = data.number_of_comments || 0;

        if (data.comments) {
            var mappedComments = $.map(data.comments, function (item) {
                return new App.Model.Comment(item);
            });
            this.comments(mappedComments);
        }

        this.numberOfComments = ko.computed(function () {
            return Math.max(numberOfComments, self.comments() ? self.comments().length : 0);
        });

        this.addComment = function (data) {
            self.comments.push(new App.Model.Comment(data));
        };

        this.deleteComment = function (id) {
            self.comments.remove(function (comment) {
                return comment.id === parseInt(id, 10);
            });
        };

        this.hideComment = function (elem) {
            if (elem.nodeType === 3) { // skip text nodes
                return;
            }

            $(elem).animate({ height: 'toggle', opacity: 'toggle' }, '400', function () {
                $(this).remove();
            });
        };


        this.afterCommentAdded = function (elem) {
            if (elem.nodeType === 3) { // skip text nodes
                return;
            }
            $(elem).filter("li").effect("highlight");
        };

        this.afterRender = function (elem) {
        };

        this.formatDate = function (date) {
            var hours = date().getHours();
            var minutes = date().getMinutes();
            return (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
        };

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

        this.hasStatus = function (statusName) {
            return self.status() - 1 === App.Model.TaskStatus.Names.indexOf(statusName);
        };

        this.editTask = function () {
            $.ajax(Routing.generate('app_task_edit', {
                id: self.id,
                project: App.UI.project.id,
                project_name: App.UI.project.canonicalName
            }), {
                dataType: "script"
            });

            var cancel = function (e) {
                if (e.type !== 'keyup') {
                    e.preventDefault();
                }
                App.UI.project.switchToTask(self.id);
                App.UI.removeWYSIWYG();
            };

            $('#task-' + self.id).one('click', '.cancel', cancel);
            App.UI.registerEscHandler(cancel);
        };

        this.updateComment = function (id, text, safeText) {
            $.grep(self.comments(), function (elm) {
                if (elm.id === id) {
                    elm.text(text);
                    elm.safeText(safeText);
                }
            });
        };
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
})(window.App = window.App || {});