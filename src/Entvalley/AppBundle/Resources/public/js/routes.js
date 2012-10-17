jQuery(function ($) {
    "use strict";
    (function (App, Routing, undefined) {
        $.sammy('body > .container',function () {
            var self = App.UI.taskListViewModel;
            var loadTasks = function (filter) {
                var loaded = [];
                self.tasks([]);
                self.tasks().forEach(function (item) {
                    loaded.push(item.id);
                });
                return $.get(Routing.generate('app_task_list', { filter: filter }), {}, function (allData) {
                    $.map(allData, function (item) {
                        if (loaded.indexOf(parseInt(item.id, 10)) === -1) {
                            if (loaded.length === 0) {
                                self.tasks.push(new App.Model.Task(item));
                            } else {
                                self.tasks.unshift(new App.Model.Task(item));
                            }
                        }
                    });
                    App.UI.hideStatus();
                });
            };


            this.before(function (callback) {
                //App.UI.showStatus('Loading...');
            });

            this.get('\/task/:id', function () {
                var id = this.params.id;
                self.switchToTask(id);
            });

            this.post('\/task/:id/edit', function (context) {
                var id = this.params.id;
                delete this.params.id;
                delete this.params._wysihtml5_mode;

                $.post(context.path, context.params.toHash(), function () {
                    $("iframe.wysihtml5-sandbox, input[name='_wysihtml5_mode'],.wysihtml5-toolbar,.autosizejs").remove();
                    $("body").removeClass("wysihtml5-supported");
                    self.switchToTask(id);
                });
            });

            this.get(/\/tasks(\/(\w+))?/, function () {
                self.chosenTask(null);
                App.UI.scroll('#task-item-' + self.lastVisited, 0);
                var dfd;
                if (this.params.splat[1]) {
                    dfd = loadTasks(this.params.splat[1]);
                } else {
                    dfd = loadTasks();
                }
                dfd.done(function () {
                    App.UI.hideStatus();
                    self.command.setContext('tasks');
                    self.command.placeholder('Create a new task...');
                });
            });
            this.get('', function () {
                self.goToTaskList();
            });
        }).run();
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});
