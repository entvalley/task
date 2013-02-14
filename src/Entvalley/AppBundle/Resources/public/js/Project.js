jQuery(function ($) {
    "use strict";
    (function (App, Routing, undefined) {
        var Project = function () {
            // Data
            var self = this;
            self.tasks = ko.observableArray([]);
            self.chosenTask = ko.observable();
            self.command = new App.Model.Command();
            self.lastVisited = null;
            self.id = App.CurrentProject.id;
            self.canonicalName = App.CurrentProject.canonicalName;
            self.fullCanonicalName = function () {
                return self.id + '-' + self.canonicalName;
            };
            self.previousPage = ko.observable(undefined);
            self.nextPage = ko.observable(undefined);
            self.showPages = ko.computed(function () {
                return !self.chosenTask() && self.previousPage !== '' && self.nextPage !== '' &&
                    self.previousPage !== undefined && self.nextPage !== undefined;
            });
            self.currentPage = 1;

            self.chosenTask.subscribe(function (task) {
                if (!task) {
                    return;
                }
                var tasks = self.tasks(),
                    length = tasks.length,
                    i;
                for (i = 0; i < length; i++) {
                    if (tasks[i].id === task.id) {
                        self.tasks().splice(i, 1, task);
                        break;
                    }
                }

                self.tasks.valueHasMutated();
            });

            self.goToTask = function (task) {
                window.history.pushState(null, '', Routing.generate('app_task_view', {
                    id: task.id,
                    project: self.id,
                    project_name: self.canonicalName
                }));
            };

            self.addTask = function (data) {
                self.tasks.unshift(new App.Model.Task(data));
            };

            self.removeTask = function (id) {
                self.tasks.remove(function (task) {
                    return task.id === parseInt(id, 10);
                });
            };

            self.hideTask = function (elem) {
                if (elem.nodeType === 3) { // skip text nodes
                    return;
                }

                $(elem).animate({ opacity: 'toggle' }, 'slow', function () {
                    $(this).remove();
                });
            };

            self.goToTaskList = function () {
                if (self.chosenTask()) {
                    self.lastVisited = self.chosenTask().id;
                }
                window.history.pushState(null, '', Routing.generate('app_task_list', {
                    project: self.id,
                    project_name: self.canonicalName
                }));
            };

            self.addTaskComment = function (data) {
                var chosenTask = self.chosenTask();
                if (!chosenTask) {
                    return;
                }
                self.chosenTask().addComment(data);
                App.UI.scroll('.comments ul li:last-child', 50);
            };

            self.assignedToMe = ko.computed(function () {
                return ko.utils.arrayFilter(self.tasks(), function (task) {
                    if (!task.assignedTo()) {
                        return false;
                    }
                    return parseInt(task.assignedTo().id, 10) === App.Me.id &&
                        task.hasStatus('accepted');
                });
            });

            self.unassigned = ko.computed(function () {
                return ko.utils.arrayFilter(self.tasks(), function (task) {
                    return task.hasStatus('unassigned') || task.hasStatus('reopened');
                });
            });


            self.switchToTask = function (id) {
                $.get(Routing.generate('app_task_view', {
                    id: id,
                    project: self.id,
                    project_name: self.canonicalName
                }), {}, function (data) {
                    App.UI.hideStatus();
                    self.chosenTask(new App.Model.Task(data));
                    self.command.setContext('task', id);
                    self.command.placeholder('Leave a comment...');
                    App.UI.scroll('0', 50);
                });
            };
        };

        ko.bindingHandlers.toggleStatusIcon = (function () {
            var _oldValue = null,
                _duration = 150,
                _classPrefix = 'status-';

            var init = function (element, valueAccessor) {
                    var status = valueAccessor();
                    _oldValue = status();
                },
                update = function (element, valueAccessor) {
                    var status = valueAccessor(),
                        $element,
                        storedValue,
                        duration;
                    if (status() < 1 || status() > App.Model.TaskStatus.Names.length) {
                        return;
                    }

                    $element = $(element);
                    storedValue = _oldValue;
                    duration = _oldValue === status() ? 0 : _duration;
                    $element.removeClass(_classPrefix + App.Model.TaskStatus.Names[_oldValue - 1]);
                    $element.slideUp(duration, function () {
                        $element.html(App.Model.TaskStatus.Names[status() - 1].toUpperCase())
                            .addClass(_classPrefix + App.Model.TaskStatus.Names[status() - 1])
                            .slideDown(duration);


                        if (storedValue !== status()) {
                            $element.addClass('status-notify');
                            setTimeout(function () {
                                $element.removeClass('status-notify');
                            }, duration * 15);
                        }
                    });
                    _oldValue = status();
                };
            return {
                init: init,
                update: update
            };
        })();

        var project = document.getElementById('project');
        if (project) {
            ko.applyBindings(App.UI.project = new Project(App.CurrentProject), project);
        }
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});

