(function (App, undefined) {
    "use strict";
    App.Model = App.Model || {};
    App.Model.Task = function (data) {
        var self = this;
        this.author = data.author.username;
        this.title = data.title;
        this.body = data.body;
        this.safeBody = data.safe_body;
        this.id = data.id;
        this.assignedTo = ko.observable(data.assigned_to);
        this.status = ko.observable(data.status);
        this.date = ko.observable(Date.parse(data.created_at));
        this.url = Routing.generate('app_task_view', {id: this.id, project: data.project.id, project_name: App.Project.CanonicalName});
        this.comments = ko.observableArray([]);

        var numberComments = data.number_comments || 0;

        if (data.comments) {
            var mappedComments = $.map(data.comments, function (item) {
                return new App.Model.Comment(item);
            });
            this.comments(mappedComments);
        }

        this.numberComments = ko.computed(function () {
            return Math.max(numberComments, self.comments() ? self.comments().length : 0);
        });

        this.status.subscribe(function (newStatus) {
            console.log('STATUS HAS CHANGES');
        });

        this.addComment = function (data) {
            self.comments.push(new App.Model.Comment(data));
        };

        this.formatDate = function (date) {
            var hours = date().getHours();
            var minutes = date().getMinutes();
            return (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
        };

        this.createdAt = ko.computed(function () {
            return ko.utils.unwrapObservable(self.date).getDate() + ' ' +
                App.AbbrMonthNames[ko.utils.unwrapObservable(self.date).getMonth()] + ' ' +
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
                project: App.Project.Id,
                project_name: App.Project.CanonicalName
            }), {
                dataType: "script"
            });

            $('#task-' + self.id).one('click', '.cancel', function(e) {
                e.preventDefault();
                App.UI.taskListViewModel.switchToTask( self.id);
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