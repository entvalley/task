jQuery(function ($) {
    "use strict";
    (function (App, Routing, undefined) {
        $.sammy('body > .container',function () {
            var self = App.UI.project;
            var loadTasks = function (filter) {
                var loaded = [];
                self.tasks([]);
                self.tasks().forEach(function (item) {
                    loaded.push(item.id);
                });
                return $.get(Routing.generate('app_task_list', {
                    filterByType: filter,
                    project: App.UI.project.id,
                    project_name: App.UI.project.canonicalName
                }), {}, function (allData) {
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


            this.get(/\/(\d+)-([^\/]+)\/tasks\/(\d+)/, function () {
                var id = this.params.splat[2];
                self.switchToTask(id);
            });

            this.post('/:project/tasks/:id/edit', function (context) {
                var id = this.params.id;
                var project = this.params.project;
                delete this.params.id;
                delete this.params.project;
                delete this.params._wysihtml5_mode;

                $.post(context.path, context.params.toHash(), function () {
                    App.UI.removeWYSIWYG();
                    self.switchToTask(id);
                });
            });

            this.post('/comments/:id/edit', function (context) {
                delete this.params.id;
                delete this.params._wysihtml5_mode;

                $.post(context.path, context.params.toHash(), App.UI.removeWYSIWYG);
            });


            this.post('/projects/create', function (context) {
                $.post(context.path, context.params.toHash(), function (data, status, xhr) {
                    var type = xhr.getResponseHeader("content-type");
                    if(type !== "application/javascript") {
                        $('#new_project form').replaceWith(data);
                    }
                });
            });

            this.get(/(\d+)-([^\/]+)\/tasks(\/(\w+))?/, function (context) {
                if (App.UI.project.fullCanonicalName() !== this.params.splat[0] + '-' + this.params.splat[1]) {
                    window.location = Routing.generate('app_task_list', {
                        filterByType: this.params.splat[3],
                        project: this.params.splat[0],
                        project_name: this.params.splat[1]
                    });
                    return;
                }
                self.chosenTask(null);
                App.UI.scroll('#task-item-' + self.lastVisited, 0);
                var dfd;
                if (this.params.splat[2]) {
                    dfd = loadTasks(this.params.splat[3]);
                } else {
                    dfd = loadTasks();
                }
                dfd.done(function () {
                    App.UI.hideStatus();
                    self.command.setContext('tasks');
                    self.command.placeholder('Create a new task...');
                });
            });
        }).run();
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});
