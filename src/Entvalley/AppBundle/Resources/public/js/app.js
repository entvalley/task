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

                    var fixHeight = function () {
                        var contentCopy = $('<div style="position: absolute; left: -9999px; top: -9999px;">').html(body.html());
                        $('body').append(contentCopy);
                        var oldMinHeight = parseInt('0' + iframe.style.minHeight, 10);
                        if (oldMinHeight !== contentCopy.height()) {
                            $(iframe).clearQueue().animate({ "min-height": contentCopy.height() }, 'fast');
                        }
                        contentCopy.remove();
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

