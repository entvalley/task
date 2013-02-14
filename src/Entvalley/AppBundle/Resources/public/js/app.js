jQuery(function ($) {
    "use strict";
    (function (App, Routing, undefined) {

        App.UI = function (inputControlId) {
            this.inputControlId = inputControlId;
        };

        App.monthNames = [
            "January", "February", "March",
            "April", "May", "June",
            "July", "August", "September",
            "October", "November", "December"
        ];

        App.AbbrMonthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        App.WeekdayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday'];

        App.Utils = App.Utils || {};

        App.Utils.parseNumber = function (number) {
            return parseInt('0' + number, 10);
        };

        App.UI.prototype = (function () {
            var initGlobalEvents,
                initCommands;

            initCommands = function (inputControlId) {
                $.ajax(Routing.generate('app_command_list'), {
                    success: function (commands) {
                        $("#" + inputControlId).autocomplete({
                            wordCount: 1,
                            on: {
                                query: function (text, cb) {
                                    var words = [];
                                    for (var command in commands) {

                                        if (command.toLowerCase().indexOf(text.toLowerCase()) === 0 &&
                                            commands[command]['is_visible']) {
                                            words.push(command);
                                        }
                                    }
                                    words.sort();
                                    cb(words);
                                }
                            }
                        });

                        $("#" + inputControlId).autosize();
                        $('#' + inputControlId).keypress(function (e) {
                            if (e.ctrlKey && e.which === 10) {
                                $(this).parents('form').submit();
                            }
                        });
                    }
                });
            };

            initGlobalEvents = function () {
                var cancel = function () {
                    App.UI.toggle('#page', false);
                    var container = $(this).closest('div[data-behaviour~="expandable"]');
                    container.find('form').each(function () {
                        this.reset();
                    });
                    App.UI.toggle(container, true);
                };
                $('button[data-role~="cancel"]').on('click', cancel);

                $('.sign-in-link').on('click', function () {
                    $('.sign-up-block').fadeOut(200, function () {
                        $('.sign-in-block').fadeIn(200);
                    });
                });

                $('.sign-up-link').on('click', function () {
                    $('.sign-in-block').fadeOut(200, function () {
                        $('.sign-up-block').fadeIn(200);
                    });
                });

                $('body').on('mouseenter mouseleave', '*[data-role~="hovercontainer"]', function (e) {
                    var elms = $(this).find('*[data-behaviour~="showonhover"]');
                    if (e.type === 'mouseenter') {
                        elms.show();
                    } else {
                        elms.hide();
                    }
                });

                $('a[data-role~="create_project"]').on('click', function (e) {
                    e.preventDefault();
                    App.UI.createProjectWorkspace();
                });
            };

            return {
                run: function () {
                    initCommands(this.inputControlId);
                    initGlobalEvents();

                    $.ajaxSetup({
                        beforeSend: function () {
                            App.UI.showStatus('Loading...');
                        },
                        complete: function () {
                            if (!$('#status').hasClass('error')) {
                                App.UI.hideStatus();
                            }
                        }
                    });
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

                createProjectWorkspace: function () {
                    App.UI.toggle('#page');
                    App.UI.toggle('#new_project');
                    App.UI.registerEscHandler(function () {
                        $('#new_project button[data-role~="cancel"]').trigger('click');
                    });
                },

                showCommandResult: function (text) {
                    $('.command_tooltip').html(text).slideDown(300);

                    var tooltipTimeout, closeTooltip;
                    closeTooltip = function () {
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

                initEditBehaviours: function () {
                    $('*[data-behaviour~="autosize"]').each(function () {
                        $(this).autosize();
                    });
                    $('textarea[data-behaviour~="singleline"]').each(function () {
                        $(this).on('keypress', function(e) {
                            var next,
                                ENTER_KEY = 13,
                                visibility_threshold = 5;
                            if (e.which === ENTER_KEY) {
                                e.preventDefault();
                                next = $(this).parents().nextAll(':input:first');
                                // "invisible" input/textarea may have borders/paddings
                                if (next.height() < visibility_threshold && window.editor) {
                                    next = $(window.editor.currentView.iframe).contents().find('body');
                                }
                                next.focus();
                            }
                        });
                    });

                    $('textarea[data-behaviour~="wysiwyg"]').each(function () {
                        $(this).wysihtml5();
                        var iframe = window.editor.currentView.iframe,
                            body,
                            width,
                            fixHeight;
                        iframe.height = $(this).height();
                        body = $(iframe).contents().find('body');
                        width = $(this).width();

                        fixHeight = function () {
                            var contentCopy = $('<div style="width: ' + width + 'px; position: absolute; left: -9999px; top: -9999px;">').html(body.html()),
                                oldMinHeight = parseInt('0' + iframe.style.minHeight, 10),
                                newMinHeight;
                            $(body).append(contentCopy);
                            newMinHeight = parseInt(contentCopy.height(), 10);
                            contentCopy.remove();
                            if (oldMinHeight !== newMinHeight) {
                                $(iframe).css({ "min-height": newMinHeight });
                            }
                        };
                        window.editor.on('paste:composer', fixHeight);
                        window.editor.on('aftercommand:composer', fixHeight);
                        body.on('keyup', fixHeight);
                    });
                },

                removeWYSIWYG: function () {
                    $("iframe.wysihtml5-sandbox, input[name='_wysihtml5_mode'],.wysihtml5-toolbar,.autosizejs").remove();
                    $("body").removeClass("wysihtml5-supported");
                },

                registerEscHandler: function (handler) {
                    $('body').on('keyup', function cancelWithKeyup(e) {
                        if (e.which === 27) {
                            handler(e);
                            $('body').off('keyup', cancelWithKeyup);
                        }
                    });
                },

                toggle: function (id, hide) {
                    if (!hide && $(id).is(':hidden')) {
                        $(id).fadeToggle();
                    } else if (hide !== false) {
                        $(id).hide();
                    }
                }
            }
        }());

        ko.bindingHandlers.dateString = {
            update: function (element, valueAccessor) {
                var value = valueAccessor(),
                    valueUnwrapped,
                    newValue;
                value['type'] = value.type || 'calendar';

                valueUnwrapped = ko.utils.unwrapObservable(value.date);
                if (element.tagName === 'TIME') {
                    element.setAttribute('datetime', valueUnwrapped);
                }
                element.setAttribute('title', valueUnwrapped);
                newValue = valueUnwrapped;
                switch (value.type.toLowerCase()) {
                    case "calendar":
                        newValue = moment(valueUnwrapped).calendar();
                        break;
                    case "fromnow":
                        newValue = moment(valueUnwrapped).fromNow();
                        break;
                    case "format":
                        value['format'] = value.format || 'L';
                        newValue = moment(valueUnwrapped).format(value.format);
                        break;
                }
                ko.utils.setTextContent(element, newValue);
            }
        };

        App.UI = new App.UI("command_text");
        App.UI.run();
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});

