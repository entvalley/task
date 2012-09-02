jQuery(function ($) {
    "use strict";
    (function (App, Routing, undefined) {

        App.UI = function (inputControlId) {
            this.inputControlId = inputControlId;
        };

        var monthNames = [
            "January", "February", "March",
            "April", "May", "June",
            "July", "August", "September",
            "October", "November", "December"
        ];

        var weekdayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'];


        App.Task = {};
        App.Task.Status = {
            UNASSIGNED: 1,
            ACCEPTED: 2,
            CLOSED: 3,
            REOPENED: 4,
            REJECTED: 5,
            WONTFIX: 6
        };

        App.UI.prototype = {
            run: function () {
                this.initCommands();
                this.initEvents();
                this.initUi();
            },
            showStatus: function (text, isError) {
                $('#status').fadeIn().find('.text').html(text);
                if (isError) {
                    $('#status').addClass('error');
                } else {
                    $('#status').removeClass('error');
                }
            },

            hideStatus: function () {
                $('#status').fadeOut();
            },

            showCommandResult: function (text) {
                $('.command_tooltip').html(text).slideDown(300);

                var tooltipTimeout;
                var closeTooltip = function () {
                    $('.command_tooltip').slideUp(300).empty();
                    if (tooltipTimeout !== null) {
                        clearTimeout(tooltipTimeout);
                    }
                };
                tooltipTimeout = setTimeout(closeTooltip, 3000);

                $('.command_tooltip').one('click', closeTooltip);
            },

            scroll: function (elm, duration) {
                $.scrollTo(elm, duration, { offset: {left: 0, top: -60 } });
            },

            initUi: function () {

                var Comment = function (data) {
                    this.text = data.text;
                    this.username = data.author.username;
                };

                var Task = function (data) {
                    var self = this;
                    this.author = data.author.username;
                    this.title = data.title;
                    this.text = data.text;
                    this.id = data.id;
                    this.status = ko.observable(data.status);
                    this.date = ko.observable(Date.parse(data.created_at));
                    this.url = Routing.generate('app_task_view', {id: this.id});
                    this.comments = ko.observableArray([]);

                    var mappedComments = $.map(data.comments, function (item) {
                        return new Comment(item);
                    });

                    this.status.subscribe(function (newStatus) {
                    console.log('STATUS HAS CHANGES');
                    });

                    this.comments(mappedComments);

                    this.addComment = function (data) {
                        self.comments.push(new Comment(data));
                    };

                    this.formatDate = function (date) {
                        return date().getHours() + ':' + date().getMinutes();
                    };

                    this.createdAtDay = ko.computed(function () {
                        return ko.utils.unwrapObservable(self.date).getDate();
                    });

                    this.createdAtMonth = ko.computed(function () {
                        return monthNames[ko.utils.unwrapObservable(self.date).getMonth()];
                    });

                    this.createdAtDate = ko.computed(function () {
                        return weekdayNames[ko.utils.unwrapObservable(self.date).getDay()];
                    });

                };

                var Command = function () {
                    var self = this;
                    this.contextType = ko.observable();
                    this.contextId = ko.observable();

                    this.setContext = function (type, id) {
                        self.contextType(type);
                        self.contextId(id || null);
                    };
                };

                var TaskListViewModel = function () {
                    // Data
                    var self = this;
                    self.tasks = ko.observableArray([]);
                    self.chosenTask = ko.observable();
                    self.command = new Command();
                    self.lastVisited = null;

                    self.goToTask = function (task) {
                        location.hash = 'task/' + task.id;
                    };

                    self.addTask = function (data) {
                        self.tasks.unshift(new Task(data));
                    };

                    self.removeTask = function (id) {
                        self.tasks.remove(function (task) {
                            return task.id === parseInt(id, 10);
                        });
                    };

                    self.hideTask = function (elem) {
                        $(elem).fadeOut(function () {
                            $(this).remove();
                        });
                    };

                    self.goToTaskList = function () {
                        if (self.chosenTask()) {
                            self.lastVisited = self.chosenTask().id;
                        }
                        location.hash = 'tasks';
                    };

                    self.addTaskComment = function (data) {
                        var chosenTask = self.chosenTask();
                        if (!chosenTask) {
                            return;
                        }
                        self.chosenTask().addComment(data);
                        App.UI.scroll('.comments ul li:last-child', 50);
                    };

                    var loadTasks = function () {
                        $.get(Routing.generate('app_task_list', {}), {}, function (allData) {
                            var mappedTasks = $.map(allData, function (item) {
                                return new Task(item);
                            });
                            self.tasks(mappedTasks);
                            App.UI.hideStatus();
                        });
                    };
                    $.sammy(function () {
                        this.before(function (callback) {
                            App.UI.showStatus('Loading...');
                        });

                        this.get('#task/:id', function () {
                            var id = this.params.id;
                            $.get(Routing.generate('app_task_view', { id: id }), {}, function (data) {
                                App.UI.hideStatus();
                                self.chosenTask(new Task(data));
                                self.command.setContext('task', id);
                                App.UI.scroll('0', 50);
                            });
                        });
                        this.get('#tasks', function () {
                            self.chosenTask(null);
                            App.UI.hideStatus();
                            self.command.setContext('tasks');
                            App.UI.scroll('#task-item-' + self.lastVisited, 0);
                        });
                        this.get('', function () {
                            self.goToTaskList();
                        });
                        loadTasks();
                    }).run();

                };
                ko.bindingHandlers.toggleStatusIcon = (function () {
                    var _oldValue = null;
                    var _duration = 150;
                    var _classPrefix = 'status-header-';
                    var _map = ['', 'unassigned', 'accepted', 'closed', 'reopened', 'rejected', 'wontfix'];

                    var init = function(element, valueAccessor, allBindingsAccessor, viewModel) {
                            var status = valueAccessor();
                            _oldValue = status();
                        },
                        update = function(element, valueAccessor, allBindingsAccessor, viewModel) {
                            var status = valueAccessor();
                            if (status() < 1 || status() >= _map.length) {
                                return;
                            }

                            console.log(_oldValue, status());

                            var $element = $(element);
                            var storedValue = _oldValue;
                            var duration = _oldValue === status() ? 0 : _duration;
                            $element.removeClass(_classPrefix + _map[_oldValue]);
                            $element.slideUp(duration, function () {
                                $element.html(_map[status()].toUpperCase())
                                    .addClass(_classPrefix + _map[status()])
                                    .slideDown(duration);


                                if (storedValue !== status()) {
                                    $element.addClass('status-header-notify');
                                    setTimeout(function () {
                                        $element.removeClass('status-header-notify');
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
            },

            initEvents: function () {
                var me = this;

                console.log($('#' + me.inputControlId));
                $('.command_form').submit(function (e) {
                    var $form = $(this);

                    App.UI.showStatus('Sending...');

                    $('#command_send')
                        .prop('disabled', true)
                        .addClass('disabled')
                        .html('Sending...');

                    $('#' + me.inputControlId).prop('readonly', true);

                    $.post($form.prop('action'), $form.serialize(), undefined, 'script')
                        .success(function () {
                            App.UI.hideStatus();
                            $('#' + me.inputControlId).val('');
                        })
                        .error(function () {
                            App.UI.showStatus('Something is broken ;(', true);
                        })
                        .complete(function () {
                            $('#' + me.inputControlId).prop('readonly', false);
                            $('#command_send')
                                .prop('disabled', false)
                                .removeClass('disabled')
                                .html('Send');
                        });

                    e.stopPropagation();
                    e.preventDefault();
                });


                $('.sign-in-link').click(function () {
                    $('.sign-up-block').fadeOut(200, function () {
                        $('.sign-in-block').fadeIn(200);
                    });
                });

                $('.sign-up-link').click(function () {
                    $('.sign-in-block').fadeOut(200, function () {
                        $('.sign-up-block').fadeIn(200);
                    });
                });
            },

            initCommands: function () {
                var me = this;
                $.ajax(Routing.generate('app_command_list'), {
                    success: function (commands) {
                        $("#" + me.inputControlId).autocomplete({
                            wordCount: 1,
                            on: {
                                query: function (text, cb) {
                                    var words = [];
                                    commands.sort();
                                    for (var i = 0; i < commands.length; i++) {
                                        if (commands[i].toLowerCase().indexOf(text.toLowerCase()) === 0) {
                                            words.push(commands[i]);
                                        }
                                        if (words.length > 12) {
                                            break;
                                        }
                                    }
                                    cb(words);
                                }
                            }
                        });

                        $('#' + me.inputControlId).keypress(function (e) {
                            if (e.ctrlKey && e.which === 10) {
                                $(this).parents('form').submit();
                            }
                        });
                    }
                });
            }
        };


        App.UI = new App.UI("command_text");
        App.UI.run();
    })(window.App = window.App || {}, window.Routing = window.Routing || {});

});

