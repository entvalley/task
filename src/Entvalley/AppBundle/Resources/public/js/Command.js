(function (App, undefined) {
    "use strict";
    App.Model = App.Model || {};
    App.Model.Command = function () {
        var self = this;
        this.contextType = ko.observable();
        this.contextId = ko.observable();
        this.placeholder = ko.observable();

        this.setContext = function (type, id) {
            self.contextType(type);
            self.contextId(id || null);
        };
    };
})(window.App = window.App || {});