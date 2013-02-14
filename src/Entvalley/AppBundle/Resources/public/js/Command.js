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
                        case "wontfix":
                        case "reject":
                        case "close":
                        case "done":
                        case "take":
                        case "open":
                        case "abandon":
                            $.each(result[command], function (index, singleResult) {
                                if (App.Utils.parseNumber(App.UI.project.chosenTask().id) === App.Utils.parseNumber(singleResult['updatedId'])) {
                                    App.UI.project.chosenTask().status(singleResult['status']);
                                }
                            });
                            break;
                    }
                });
                console.log(result);
            }
        };
    })(window.App = window.App || {});
});