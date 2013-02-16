jQuery(function ($) {
    "use strict";
    (function (App, undefined) {
        App.Model = App.Model || {};
        App.Model.Command = function () {
            this.contextType = ko.observable();
            this.contextProject = ko.observable();
            this.contextId = ko.observable();
            this.placeholder = ko.observable();

            this.setContext = function (type, id) {
                this.contextType(type);
                this.contextId(id || null);
                this.contextProject(App.UI.project.id);
            };
        };

        App.Model.Command.prototype = {
            handle: function (result) {
                $.each(result, function(command) {
                    switch (command) {
                        case 'wontfix':
                        case 'reject':
                        case 'close':
                        case 'done':
                        case 'take':
                        case 'open':
                        case 'abandon':
                            $.each(result[command], function (index, singleResult) {
                                if (singleResult.error) {
                                    App.UI.showCommandResult(singleResult.error);
                                    return;
                                }

                                if (App.Utils.parseNumber(App.UI.project.chosenTask().id) === App.Utils.parseNumber(singleResult['updatedId'])) {
                                    App.UI.project.chosenTask().status(singleResult['status']);
                                    App.UI.project.addTaskComment(singleResult['comment']);

                                    if (command === 'abandon') {
                                        App.UI.project.chosenTask().assignedTo(undefined);
                                    } else if (command === 'take') {
                                        App.UI.project.chosenTask().assignedTo({username: App.Me.username, id: App.Me.id});
                                    }
                                }
                            });
                            break;
                        case 'remove':
                            if (result[command][0].error) {
                                App.UI.showCommandResult(result[command][0].error);
                            } else {
                                App.UI.project.goToTaskList();
                                var removedId = result[command][0]['removed_id']
                                App.UI.project.removeTask(removedId);
                                App.UI.showCommandResult('The task #' + removedId + ' has been removed');
                            }
                            break;
                        case 'create':
                            if (result[command][0].error) {
                                App.UI.showCommandResult(result[command][0].error);
                            } else {
                                App.UI.project.addTask(result[command][0].task);
                                App.UI.project.goToTask(result[command][0].task.id);
                            }
                            break;
                        case 'comment':
                            if (result[command][0].error) {
                                App.UI.showCommandResult(result[command][0].error);
                            } else {
                                App.UI.project.addTaskComment(result[command][0].comment);
                            }
                            break;
                    }
                });
            }
        };
    })(window.App = window.App || {});
});