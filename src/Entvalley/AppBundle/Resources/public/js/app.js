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

        App.UI.prototype = {
            run: function () {
                this.initCommands();
                this.initEvents();

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
                App.UI._toggle('#page');
                App.UI._toggle('#new_project');
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

            initEvents: function () {
                var that = this;

                $('.command_form').on('submit', function (e) {
                    var $form = $(this);

                    App.UI.showStatus('Sending...');

                    $('#command_send')
                        .prop('disabled', true)
                        .addClass('disabled')
                        .html('Sending...');

                    $('#' + that.inputControlId).prop('readonly', true);

                    $.post($form.prop('action'), $form.serialize(), undefined, 'script')
                        .success(function () {
                            App.UI.hideStatus();
                            $('#' + that.inputControlId).val('');
                        })
                        .error(function () {
                            App.UI.showStatus('Something is broken ;(', true);
                        })
                        .complete(function () {
                            $('#' + that.inputControlId).prop('readonly', false);
                            $('#command_send')
                                .prop('disabled', false)
                                .removeClass('disabled')
                                .html('Send');
                        });

                    e.stopPropagation();
                    e.preventDefault();
                });


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

                $('button[data-role~="cancel"]').on('click', function () {
                    App.UI._toggle('#page');
                    var container = $(this).closest('div[data-behaviour~="expandable"]');
                    container.find('form').each(function () {
                        this.reset();
                    });
                    App.UI._toggle(container);
                });

                $('body').on('mouseenter mouseleave', 'li[data-role~="hovercontainer"]', function () {
                    $(this).find('*[data-behaviour~="showonhover"]').toggle();
                });

                $('body').on('click', '.remove-comment', function (e) {
                    e.preventDefault();

                    if (!window.confirm("Are you sure you want to delete the comment?")) {
                        return;
                    }

                    $.ajax(this.href, {
                        dataType: "script"
                    });
                });

                $('a[data-role~="create_project"]').on('click', function (e) {
                    e.preventDefault();
                    App.UI.createProjectWorkspace();
                });
            },




            initEditBehaviours: function () {
                $('*[data-behaviour~="autosize"]').each(function () {
                    $(this).autosize();
                });

                $('textarea[data-behaviour~="wysiwyg"]').each(function () {
                    $(this).wysihtml5();
                    var iframe = window.editor.currentView.iframe;
                    iframe.height = $(this).height();
                    var body = $(iframe).contents().find('body');
                    var width = $(this).width();

                    var fixHeight = function () {
                        var contentCopy = $('<div style="width: ' + width + 'px; position: absolute; left: -9999px; top: -9999px;">').html(body.html());
                        var oldMinHeight = parseInt('0' + iframe.style.minHeight, 10);
                        $(body).append(contentCopy);
                        var newMinHeight = contentCopy.height();
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

                        $("#" + me.inputControlId).autosize();
                        $('#' + me.inputControlId).keypress(function (e) {
                            if (e.ctrlKey && e.which === 10) {
                                $(this).parents('form').submit();
                            }
                        });
                    }
                });
            },

            _toggle: function (id) {
                if($(id).is(':hidden')) {
                    $(id).fadeToggle();
                } else {
                    $(id).hide();
                }
            }
        };

        App.UI = new App.UI("command_text");
        App.UI.run();
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});

