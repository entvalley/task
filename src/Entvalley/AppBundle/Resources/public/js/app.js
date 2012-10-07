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

        var abbrMonthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        var weekdayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'];


        var statusNames = ['unassigned', 'accepted', 'closed', 'reopened', 'rejected', 'wontfix'];


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
                tooltipTimeout = setTimeout(closeTooltip, 5000);

                $('.command_tooltip').one('click', closeTooltip);
            },

            scroll: function (elm, duration) {
                $.scrollTo(elm, duration, { offset: {left: 0, top: -60 } });
            },

            initUi: function () {

                var Comment = function (data) {
                    var self = this;
                    self.text = data.text;
                    self.username = data.author.username;
                    self.createdAt = data.created_at;

                    var statusChange = data.status_change || {
                        status: null,
                        created_at: null
                    };

                    self.statusChanged = typeof data.status_change !== 'undefined';

                    self.statusChange = ko.observable(statusChange.status);
                    self.statusChangeDate = ko.observable(statusChange.created_at);

                    self.statusName = ko.computed(function () {
                        return statusNames[self.statusChange() - 1];
                    });
                    self.statusNameCapitalized = ko.computed(function () {
                        var statusName = self.statusName();
                        if (!statusName) {
                            return;
                        }
                        return statusName.toUpperCase();
                    });
                };

                var Task = function (data) {
                    var self = this;
                    this.author = data.author.username;
                    this.title = data.title;
                    this.body = data.body;
                    this.id = data.id;
                    this.assignedTo = ko.observable(data.assigned_to);
                    this.status = ko.observable(data.status);
                    this.date = ko.observable(Date.parse(data.created_at));
                    this.url = Routing.generate('app_task_view', {id: this.id});
                    this.comments = ko.observableArray([]);

                    var numberComments = data.number_comments || 0;

                    if (data.comments) {
                        var mappedComments = $.map(data.comments, function (item) {
                            return new Comment(item);
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
                        self.comments.push(new Comment(data));
                    };

                    this.formatDate = function (date) {
                        var hours = date().getHours();
                        var minutes = date().getMinutes();
                        return (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
                    };

                    this.createdAt = ko.computed(function () {
                        return ko.utils.unwrapObservable(self.date).getDate() + ' ' +
                            abbrMonthNames[ko.utils.unwrapObservable(self.date).getMonth()] + ' ' +
                            ko.utils.unwrapObservable(self.date).getFullYear();
                    });

                    this.createdAtMonth = ko.computed(function () {
                        return abbrMonthNames[ko.utils.unwrapObservable(self.date).getMonth()];
                    });

                    this.createdAtDate = ko.computed(function () {
                        return weekdayNames[ko.utils.unwrapObservable(self.date).getDay()];
                    });

                    this.statusName = ko.computed(function () {
                        return statusNames[this.status() - 1];
                    }, this);

                    this.hasStatus = function (statusName) {
                        return self.status() - 1 === statusNames.indexOf(statusName);
                    };

                    this.editTask = function () {
                        console.log(123);
                    };
                };

                var Command = function () {
                    var self = this;
                    this.contextType = ko.observable();
                    this.contextId = ko.observable();
                    this.placeholder = ko.observable();

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
                        self.tasks.unshift(new Task(data));
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
                                        self.tasks.push(new Task(item));
                                    } else {
                                        self.tasks.unshift(new Task(item));
                                    }
                                }
                            });
                            App.UI.hideStatus();
                        });
                    };
                    $.sammy('body > .container', function () {
                        this.before(function (callback) {
                            App.UI.showStatus('Loading...');
                        });

                        this.get('\/task/:id', function () {
                            var id = this.params.id;
                            $.get(Routing.generate('app_task_view', { id: id }), {}, function (data) {
                                App.UI.hideStatus();
                                self.chosenTask(new Task(data));
                                self.command.setContext('task', id);
                                self.command.placeholder('Leave a comment...');
                                App.UI.scroll('0', 50);
                            });
                        });
                        this.get(/\/tasks(\/(\w+))?/, function () {
                            console.log(123);
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
                            if (status() < 1 || status() > statusNames.length) {
                                return;
                            }

                            var $element = $(element);
                            var storedValue = _oldValue;
                            var duration = _oldValue === status() ? 0 : _duration;
                            $element.removeClass(_classPrefix + statusNames[_oldValue - 1]);
                            $element.slideUp(duration, function () {
                                $element.html(statusNames[status() - 1].toUpperCase())
                                    .addClass(_classPrefix + statusNames[status() - 1])
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
            },

            initEvents: function () {
                var me = this;

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

                $('*[data-behaviour~="autosize"]').each(function () {
                    $(this).autosize();
                });

                $('textarea[data-behaviour~="wysiwyg"]').each(function () {
                    $(this).wysihtml5();
                    var iframe = window.editor.currentView.iframe;
                    iframe.height = $(this).height();
                    window.editor.on('newword:composer', function () {
                        var body = $(iframe).contents().find('body');
                        body.css('overflow', 'hidden');

                        $(iframe).animate({ height: body.height() }, 'fast');
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
                                    for (var command in commands) {

                                        if (command.toLowerCase().indexOf(text.toLowerCase()) === 0 &&
                                            commands[command].is_visible) {
                                            words.push(command);
                                        }
                                    }
                                    words.sort();
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

