jQuery(function ($) {
    "use strict";
    (function (App, Routing, undefined) {
        $.sammy('body > section > .container', function () {
            var currentProject = App.UI.project;
            var loadTasks = function (filter) {
                return $.get((Routing.generate('app_task_list', {
                    filterByType: filter,
                    project: App.UI.project.id,
                    project_name: App.UI.project.canonicalName
                }) + window.location.search), {}, function (allData) {
                    currentProject.tasks.removeAll();
                    currentProject.nextPage(allData.pagination.next);
                    currentProject.previousPage(allData.pagination.previous);
                    currentProject.currentPage = allData.pagination.currentPage;

                    $.map(allData.tasks, function (item) {
                        currentProject.tasks.push(new App.Model.Task(item));
                    });

                    App.UI.hideStatus();
                });
            };
            var loadSettings = function () {
                currentProject = currentProject || App.CurrentProject;
                return $.get((Routing.generate('app_project_collaborators', {
                    project: currentProject.id,
                    project_name: currentProject.canonicalName
                })), {}, function (projectUsers) {
                    $.map(projectUsers.invitations, function (invitation) {
                        App.UI.settings.invitations.push(new App.Model.ProjectInvitation(invitation));
                    });
                    App.UI.settings.collaborators(projectUsers.collaborators);
                });
            };


            this.get(/\/(\d+)-([^\/]+)\/tasks\/(\d+)/, function () {
                var id = this.params.splat[2];
                currentProject.switchToTask(id);
            });

            this.post(/\/(\d+)-([^\/]+)\/tasks\/(\d+)\/edit/, function (context) {
                var id = this.params.splat[2];
                delete this.params.id;
                delete this.params.project;
                delete this.params['_wysihtml5_mode'];

                $.post(context.path, context.params.toHash(), function () {
                    App.UI.removeWYSIWYG();
                    currentProject.switchToTask(id);
                });
            });

            this.post('/comments/:id/edit', function (context) {
                delete this.params.id;
                delete this.params['_wysihtml5_mode'];

                $.post(context.path, context.params.toHash(), App.UI.removeWYSIWYG);
            });


            this.post('/projects/create', function (context) {
                $.post(context.path, context.params.toHash(), function (data, status, xhr) {
                    var type = xhr.getResponseHeader("content-type");
                    if (type !== "application/javascript") {
                        $('#new_project form').replaceWith(data);
                    }
                });
            });

            this.get(/(\d+)-([^\/]+)\/tasks(\/(\w+))?/, function () {
                if (!App.UI.project) {
                    window.location = window.location;
                }
                if (App.UI.project.fullCanonicalName() !== this.params.splat[0] + '-' + this.params.splat[1]) {
                    window.location = Routing.generate('app_task_list', {
                        filterByType: this.params.splat[3],
                        project: this.params.splat[0],
                        project_name: this.params.splat[1]
                    });
                    return;
                }
                currentProject.chosenTask(null);
                App.UI.scroll('#task-item-' + currentProject.lastVisited, 0);
                var dfd;
                if (this.params.splat[2]) {
                    dfd = loadTasks(this.params.splat[3]);
                } else {
                    dfd = loadTasks();
                }
                dfd.done(function () {
                    App.UI.hideStatus();
                    currentProject.command.setContext('tasks');
                    currentProject.command.placeholder('Create a new task...');
                });
            });

            this.get(/(\d+)-([^\/]+)\/settings/, function () {
                if (!App.UI.settings) {
                    window.location = window.location;
                }
                var dfd = loadSettings();
                dfd.done(function () {
                    App.UI.hideStatus();
                });
            });

            this.post(/\/(\d+)-([^\/]+)\/collaborators\/invite/, function (context) {
                $('.project-invitation .alert').fadeOut();
                $.post(context.path, context.params.toHash(), function (response) {
                    var invitees = [];
                    $.map(response.invitees, function (invitee) {
                        invitees.push(new App.Model.ProjectInvitation(invitee));
                    });

                    App.UI.settings.updateInvitations(invitees);
                    $('.project-invitation .control-group').not(':last').fadeOut(function () {
                        $(this).remove();
                    });
                });
            });
        }).run();
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});
