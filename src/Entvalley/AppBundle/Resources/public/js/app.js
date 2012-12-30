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

                ko.bindingHandlers.dateString = {
                    update: function (element, valueAccessor) {
                        var value = valueAccessor(), valueUnwrapped;
                        value['type'] = value.type || 'calendar';

                        valueUnwrapped = ko.utils.unwrapObservable(value.date);
                        if (element.tagName === 'TIME') {
                            element.setAttribute('datetime', valueUnwrapped);
                        }
                        element.setAttribute('title', valueUnwrapped);
                        var newValue = valueUnwrapped;
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
                }
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

                $('body').on('keydown mousedown', '.project-invitation input[type="email"]', function (e) {
                    var INVITEE_SELECTOR = 'input[type="email"]',
                        $self = $(this),
                        $container = $self.parents('.control-group'),
                        $next = $container.next().find(INVITEE_SELECTOR),
                        $prev = $container.prev().find(INVITEE_SELECTOR),
                        $last = $container.parent().find(INVITEE_SELECTOR).last();

                    var applyPrototype = function () {
                        var $form = $self.parents('form');
                        var count = parseInt('0' + $form.data('invitee-count'), 10);
                        var proto = $self.parents('form').data('invitee-email-prototype');
                        var $proto = $(proto.replace(/__name__/g, ++count));
                        $form.data('invitee-count', count);
                        //$container.parent().append(proto);
                        $proto.css('display', 'none');

                        $proto.insertBefore($form.find('input[type="submit"]')).fadeIn();
                    };

                    var noPrevOrPrevHasValue = ($prev.length === 0 || $.trim($prev.val()) !== '');
                    if ($.trim($last.val()) !== '' || (($next.length === 0) && noPrevOrPrevHasValue)) {
                        applyPrototype();
                        return;
                    }

                    // for TAB-key
                    var nextIsLastAndHasNoValue = ($last.get(0) === $next.get(0) || $.trim($last.val()) !== '');
                    if (e.which === 9 && $.trim($self.val()) !== '' && nextIsLastAndHasNoValue) {
                        applyPrototype();
                    }
                });

                var cancel = function () {
                    App.UI.toggle('#page', false);
                    var container = $(this).closest('div[data-behaviour~="expandable"]');
                    container.find('form').each(function () {
                        this.reset();
                    });
                    App.UI.toggle(container, true);
                };

                $('button[data-role~="cancel"]').on('click', cancel);

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
                        var newMinHeight = parseInt(contentCopy.height(), 10);
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
                                            commands[command]['is_visible']) {
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
        };

        App.UI = new App.UI("command_text");
        App.UI.run();
    })(window.App = window.App || {}, window.Routing = window.Routing || {});
});

