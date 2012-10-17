jQuery(function ($) {
    "use strict";
    (function (App, Routing, undefined) {
        var TaskListViewModel = function () {
            // Data
            var self = this;
            self.tasks = ko.observableArray([]);
            self.chosenTask = ko.observable();
            self.command = new App.Model.Command();
            self.lastVisited = null;

            self.chosenTask.subscribe(function (task) {
                if (!task) {
                    return;
                }
                var tasks = self.tasks();
                for (var i = 0; i < tasks.length; i++) {
                    if (tasks[i].id === task.id) {
                        self.tasks().splice(i, 1, task);
                        break;
                    }
                }

                self.tasks.valueHasMutated();
            });

            self.goToTask = function (task) {
                window.location = Routing.generate('app_task_view', { id: task.id });
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

                $(elem).animate({ height: 'toggle', opacity: 'toggle' }, 'slow', function () {
                    $(this).remove();
                });
            };

            self.goToTaskList = function () {
                if (self.chosenTask()) {
                    self.lastVisited = self.chosenTask().id;
                }
                window.location = Routing.generate('app_task_list');
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
                return ko.utils.arrayFilter(self.tasks(), function(task) {
                    if (!task.assignedTo()) {
                        return false;
                    }
                    return parseInt(task.assignedTo().id, 10) === App.Me.id &&
                        task.hasStatus('accepted');
                });
            });

            self.unassigned = ko.computed(function () {
                return ko.utils.arrayFilter(self.tasks(), function(task) {
                    return task.hasStatus('unassigned') || task.hasStatus('reopened');
                });
            });


            self.switchToTask = function (id) {
                $.get(Routing.generate('app_task_view', { id: id }), {}, function (data) {
                    App.UI.hideStatus();
                    self.chosenTask(new App.Model.Task(data));
                    self.command.setContext('task', id);
                    self.command.placeholder('Leave a comment...');
                    App.UI.scroll('0', 50);
                });
            };


        };

        ko.bindingHandlers.toggleStatusIcon = (function () {
            var _oldValue = null;
            var _duration = 150;
            var _classPrefix = 'status-';

            var init = function(element, valueAccessor, allBindingsAccessor, viewModel) {
                    var status = valueAccessor();
                    _oldValue = status();
                },
                update = function(element, valueAccessor, allBindingsAccessor, viewModel) {
                    var status = valueAccessor();
                    if (status() < 1 || status() > App.Model.TaskStatus.Names.length) {
                        return;
                    }

                    var $element = $(element);
                    var storedValue = _oldValue;
                    var duration = _oldValue === status() ? 0 : _duration;
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
        ko.applyBindings(App.UI.taskListViewModel = new TaskListViewModel());
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});

