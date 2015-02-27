//
// Namespace - Module Pattern.
//
var JQD = (function ($, window, document, undefined) {
// Expose innards of JQD.
    return {
        go: function () {
            for (var i in JQD.init) {
                JQD.init[i]();
            }
        },
        init: {
            frame_breaker: function () {
                if (window.location !== window.top.location) {
                    window.top.location = window.location;
                }
            },
            //
            // Initialize the clock.
            //
            clock: function () {
                var clock = $('#clock');
                if (!clock.length) {
                    return;
                }

                // Date variables.
                var date_obj = new Date();
                var hour = date_obj.getHours();
                var minute = date_obj.getMinutes();
                var day = date_obj.getDate();
                var year = date_obj.getFullYear();
                var suffix = '';
//        var suffix = 'AM';

//        // Array for weekday.
//        var weekday = [
//          'Sunday',
//          'Monday',
//          'Tuesday',
//          'Wednesday',
//          'Thursday',
//          'Friday',
//          'Saturday'
//        ];
                // Array for weekday.
                var weekday = [
                    'Søndag',
                    'Mandag',
                    'Tirsdag',
                    'Onsdag',
                    'Torsdag',
                    'Fredag',
                    'Lørdag'
                ];
//        // Array for month.
//        var month = [
//          'January',
//          'February',
//          'March',
//          'April',
//          'May',
//          'June',
//          'July',
//          'August',
//          'September',
//          'October',
//          'November',
//          'December'
//        ];

                // Array for month.
                var month = [
                    'Januar',
                    'Februar',
                    'Marts',
                    'April',
                    'Maj',
                    'Juni',
                    'Juli',
                    'August',
                    'September',
                    'Oktober',
                    'November',
                    'December'
                ];
                // Assign weekday, month, date, year.
                weekday = weekday[date_obj.getDay()];
                month = month[date_obj.getMonth()];
//        // AM or PM?
//        if (hour >= 12) {
//          suffix = 'PM';
//        }
//
//        // Convert to 12-hour.
//        if (hour > 12) {
//          hour = hour - 12;
//        }
//        else if (hour === 0) {
//          // Display 12:XX instead of 0:XX.
//          hour = 12;
//        }

                // Leading zero, if needed.
                if (minute < 10) {
                    minute = '0' + minute;
                }

                // Build two HTML strings.
                var clock_time = weekday + ' ' + hour + ':' + minute + ' ' + suffix;
                var clock_date = day + '. ' + month + ' ' + year;
                // Shove in the HTML.
                clock.html(clock_time).attr('title', clock_date);
                // Update every 60 seconds.
                setTimeout(JQD.init.clock, 60000);
            },
            //
            // Initialize the desktop.
            //
            desktop: function () {
                // Alias to document.
                var d = $(document);
                // Cancel mousedown.
                d.mousedown(function (ev) {
                    var tags = [
                        'a',
                        'button',
                        'input',
                        'select',
                        'textarea',
                        'tr'
                    ].join(',');
                    if (!$(ev.target).closest(tags).length) {
                        JQD.util.clear_active();
                        ev.preventDefault();
                        ev.stopPropagation();
                    }
                });
                // Cancel right-click.
                d.on('contextmenu', function () {
                    return false;
                });
                // Relative or remote links?
                d.on('click', 'a', function (ev) {
                    var url = $(this).attr('href');
                    this.blur();
                    if (url.match(/^#/)) {
                        ev.preventDefault();
                        ev.stopPropagation();
                    }
                    else {
                        $(this).attr('target', '_blank');
                    }
                });
                // Make top menus active.
                d.on('mousedown', 'a.menu_trigger', function () {
                    if ($(this).next('ul.menu').is(':hidden')) {
                        JQD.util.clear_active();
                        $(this).addClass('active').next('ul.menu').show();
                    }
                    else {
                        JQD.util.clear_active();
                    }
                });
                // Transfer focus, if already open.
                d.on('mouseenter', 'a.menu_trigger', function () {
                    if ($('ul.menu').is(':visible')) {
                        JQD.util.clear_active();
                        $(this).addClass('active').next('ul.menu').show();
                    }
                });

                // Transfer focus, if already open.
                d.on('mouseenter', 'a.submenu_trigger', function () {
                    if ($(this).next('ul.submenu').is(':hidden')) {
                        JQD.util.clear_active_sub();
                        $(this).addClass('active').next('ul.submenu').show();
                    }
                    else {
                        JQD.util.clear_active_sub();
                    }
                    if ($('ul.submenu').is(':visible')) {
                        JQD.util.clear_active_sub();
                        $(this).addClass('active').next('ul.submenu').show();
                    }
                });
                d.on('mouseenter', 'a.punkt', function () {
                    JQD.util.clear_active_sub();
                });
                // Respond to menu-click.
                d.on('mousedown', 'a.punkt, a.subpunkt', function () {
                    // Get the link's target.
                    var x = $(this).attr('href');
                    var y = $(x).find('a').attr('href');

                    var actionType = x.substr(1, 4);
                    var formname = x.substr(6, 100);
                    if (actionType === 'dock') {
                        if (document.getElementById('window_' + formname) === null) {
                            $('<div id="window_' + formname + '" class="abs window"></div>').insertBefore("#bar_top");
                            ensure({js: '/FormKit/JS/' + formname + '.js', html: '/FormKit/HTML/' + formname + '.html', parent: 'window_' + formname}, function ()
                            {
                                window[formname + '_mvm' ] = new window[formname + '_MasterViewModel' ];
                                //var FKSYS001_mvm = new FKSYS001_MasterViewModel;
                                ko.applyBindings(window[formname + '_mvm' ], $('#window_' + formname)[0]);


                                $.get('/FormKit/CSS/' + formname + '.css', function (data) {
                                    $('#global_dyn_style').append(data);
                                });
                            });
                        }

                        // Show the taskbar button.
                        if ($(x).is(':hidden')) {
                            $(x).remove().appendTo('#dock');
                            $(x).find('img.minimize_window').show();
                            $(x).show('fast');
                        }

                        // Bring window to front.
                        JQD.util.window_flat();
                        $(y).addClass('window_stack').show();

                        if ($('ul.menu').is(':visible')) {
                            JQD.util.clear_active();
                            $(this).addClass('active').next('ul.menu').show();
                        }
                    } else {
                        if ($('ul.menu').is(':visible')) {
                            JQD.util.clear_active();
                            $(this).addClass('active').next('ul.menu').show();
                        }
                    }
                });
                // Cancel single-click.
                d.on('mousedown', 'a.icon', function () {
                    // Highlight the icon.
                    JQD.util.clear_active();
                    $(this).addClass('active');
                });
                // Respond to double-click.
                d.on('dblclick', 'a.icon', function () {
                    // Get the link's target.
                    var x = $(this).attr('href');
                    var y = $(x).find('a').attr('href');
                    // Show the taskbar button.
                    if ($(x).is(':hidden')) {
                        $(x).remove().appendTo('#dock');
                        $(x).show('fast');
                    }

                    // Bring window to front.
                    JQD.util.window_flat();
                    $(y).addClass('window_stack').show();
                });
                // Make icons draggable.
                d.on('mouseenter', 'a.icon', function () {
                    $(this).off('mouseenter').draggable({
                        revert: true,
                        containment: 'parent'
                    });
                });
                // Taskbar buttons.
                d.on('click', '#dock a', function () {
                    // Get the link's target.
                    var x = $($(this).attr('href'));
                    // Hide, if visible.
                    if (x.is(':visible') && x.hasClass('window_stack')) {
                        $(this).find('img.minimize_window').hide();
                        $(this).find('img.resize_window').show();
                        $(this).find('img.tofront_window').hide();
                        x.hide();
                    }
                    else if (x.is(':hidden')) {
                        // Display window.
                        $(this).find('img.minimize_window').show();
                        $(this).find('img.resize_window').hide();
                        $(this).find('img.tofront_window').hide();
                        JQD.util.window_flat();
                        x.show().addClass('window_stack');
                    }
                    else if (!x.hasClass('window_stack')) {
                        // Bring window to front.
                        $(this).find('img.tofront_window').show();
                        $(this).find('img.minimize_window').hide();
                        $(this).find('img.resize_window').hide();
                        JQD.util.window_flat();
                        x.show().addClass('window_stack');
                    }
                    else {
                        // Bring window to front.
                        $(this).find('img.minimize_window').show();
                        $(this).find('img.resize_window').hide();
                        $(this).find('img.tofront_window').hide();
                        JQD.util.window_flat();
                        x.show().addClass('window_stack');
                    }
                });
                // Focus active window.
                d.on('mousedown', 'div.window', function () {
                    // Bring window to front.
                    JQD.util.window_flat();
                    $(this).addClass('window_stack');
                });
                // Make windows draggable.
                d.on('mouseenter', 'div.window', function () {
                    $(this).off('mouseenter').draggable({
                        // Confine to desktop.
                        // Movable via top bar only.
                        cancel: 'a',
                        containment: 'parent',
                        handle: 'div.window_top'
                    }).resizable({
                        containment: 'parent',
                        minWidth: 400,
                        minHeight: 200
                    });
                });
                // Double-click top bar to resize, ala Windows OS.
                d.on('dblclick', 'div.window_top', function (e) {
//                    if (e.target !== this) return; // Gør at child elementer ikke udløser resize...
                    JQD.util.window_resize(this);
                });
                // Double click top bar icon to close, ala Windows OS.
                d.on('dblclick', 'div.window_top img', function () {
                    // Traverse to the close button, and hide its taskbar button.
                    $($(this).closest('div.window_top').find('a.window_close').attr('href')).hide('fast');
                    // Close the window itself.
                    $(this).closest('div.window').hide();
                    // Stop propagation to window's top bar.
                    return false;
                });
                // Minimize the window.
                d.on('click', 'a.window_min', function () {
                    $(this).closest('div.window').hide();
                });
                // Maximize or restore the window.
                d.on('click', 'a.window_resize', function () {
                    JQD.util.window_resize(this);
                });
                // Close the window.
                d.on('click', 'a.window_close', function () {
                    $(this).closest('div.window').hide();
                    $($(this).attr('href')).hide('fast');
                });
                // Show desktop button, ala Windows OS.
                d.on('mousedown', '#show_desktop', function () {
                    // If any windows are visible, hide all.
                    if ($('div.window:visible').length) {
                        $('div.window').hide();
                    }
                    else {
                        // Otherwise, reveal hidden windows that are open.
                        $('#dock li:visible a').each(function () {
                            $($(this).attr('href')).show();
                        });
                    }
                });
                $('table.data').each(function () {
                    // Add zebra striping, ala Mac OS X.
                    $(this).find('tbody tr:odd').addClass('zebra');
                });
                // Previus row.
                d.on('click', 'button.prev', function () {
                    if (!$(this).hasClass('inactive')) {
                        var row = $(this).closest('.window_inner').find('tr.active');
                        var cell = $(this).closest('.window_inner').find('tr td.active');
                        var cellIndex = cell.index();
                        var rowIndex = row.index();
                        rowIndex = rowIndex - 1;

                        var target = $('table tbody tr').eq(rowIndex).find('td').eq(cellIndex).find("input:text");
                        if (target !== undefined) {
                            target.focus();
                            target.select();
                            // Clear active state.
                            JQD.util.clear_active();
                            // Highlight row, ala Mac OS X.
                            target.closest('tr').addClass('active');
                            target.closest('td').addClass('active');
                        }

//                        var rowCount = $(this).closest('tbody').find('tr').length;
//                        var row = $(this).closest('tbody').find('tr.active');
//                        var rowIndex = row.index();
//                        if (rowCount > 0) {
//                            if (rowIndex === 0) {
//                                $(this).closest('.window_inner').find('button.prev').addClass('inactive');
//                            }
//                            else if ($(this).closest('.window_inner').find('button.prev').hasClass('inactive')) {
//                                $(this).closest('.window_inner').find('button.prev').removeClass('inactive');
//                            }
//                            if (rowCount - 1 === rowIndex) {
//                                $(this).closest('.window_inner').find('button.next').addClass('inactive');
//                            }
//                            else if ($(this).closest('.window_inner').find('button.next').hasClass('inactive')) {
//                                $(this).closest('.window_inner').find('button.next').removeClass('inactive');
//                            }
//                        } else {
//                            $(this).closest('.window_inner').find('button.prev').addClass('inactive');
//                            $(this).closest('.window_inner').find('button.next').addClass('inactive');
//                        }
                    }
                });
                // Next row.
                d.on('click', 'button.next', function () {
                    if (!$(this).hasClass('inactive')) {
                        var row = $(this).closest('.window_inner').find('tr.active');
                        var cell = $(this).closest('.window_inner').find('tr td.active');
                        var cellIndex = cell.index();
                        var rowIndex = row.index();
                        rowIndex = rowIndex + 1;

                        var target = $('table tbody tr').eq(rowIndex).find('td').eq(cellIndex).find("input:text");
                        if (target !== undefined) {
                            target.focus();
                            target.select();
                            // Clear active state.
                            JQD.util.clear_active();
                            // Highlight row, ala Mac OS X.
                            target.closest('tbody tr').addClass('active');
                            target.closest('tbody tr td').addClass('active');
                        }

//                        var rowCount = $(this).closest('tbody').find('tr').length;
//                        var row = $(this).closest('tbody').find('tr.active');
//                        var rowIndex = row.index();
//                        if (rowCount > 0) {
//                            if (rowIndex === 0) {
//                                $(this).closest('.window_inner').find('button.prev').addClass('inactive');
//                            }
//                            else if ($(this).closest('.window_inner').find('button.prev').hasClass('inactive')) {
//                                $(this).closest('.window_inner').find('button.prev').removeClass('inactive');
//                            }
//                            if (rowCount - 1 === rowIndex) {
//                                $(this).closest('.window_inner').find('button.next').addClass('inactive');
//                            }
//                            else if ($(this).closest('.window_inner').find('button.next').hasClass('inactive')) {
//                                $(this).closest('.window_inner').find('button.next').removeClass('inactive');
//                            }
//                        } else {
//                            $(this).closest('.window_inner').find('button.prev').addClass('inactive');
//                            $(this).closest('.window_inner').find('button.next').addClass('inactive');
//                        }
                    }
                });
//                d.on('mousedown', 'table.data tr td', function () {
//                    // Clear active state.
//                    JQD.util.clear_active();
//                    // Highlight row, ala Mac OS X.
//                    var focus = $(this).closest('tbody').find('tr td input:focus');
//                    $(this).closest('tr').addClass('active');
//                    $(this).closest('td').addClass('active');
//                });
//                d.on('mousedown', 'table.data tr td', function () {
//                        // Clear active state.
//                        JQD.util.clear_active();
//                        $(this).closest('td').addClass('active');
//                });
                d.on('keydown', 'table.data tr td input:text', function (event) {
//                    var rowCount = $(this).closest('tbody').find('tr').length + 1;
//                    var cellCount = $(this).closest('tbody tr').find('td').length;
                    // detect arrows pressing
                    if (event.keyCode !== 38 && event.keyCode !== 40) // up & down
                        return;
                    event.preventDefault();
                    var target;
                    var cellAndRow = $(this).parents('td,tr');
                    var cellIndex = cellAndRow[0].cellIndex;
                    var rowIndex = cellAndRow[1].rowIndex;
                    switch (event.keyCode) {              
                        case 40:
                            rowIndex = rowIndex + 1;
                            break;
                            // down arrow
                        case 38:
                            rowIndex = rowIndex - 1;
                            break;
                            // up arrow
                    }
                    target = $('table tr').eq(rowIndex).find('td').eq(cellIndex).find("input:text");
                    if (target !== undefined) {
                        target.focus();
                    }
                });
                d.on('focus', 'table.data tr td input:text', function (e) {

                    $(this).select();
                    // Clear active state.
                    JQD.util.clear_active();
                    // Highlight row, ala Mac OS X.

//                    $(this).closest('tr').addClass('active');
//                    $(this).closest('td').addClass('active');

                    var rowCount = $(this).closest('tbody').find('tr').length;
                    var row = $(this).closest('tbody').find('tr.active');
                    var rowIndex = row.index();
                    if (rowCount > 0) {
                        if (rowIndex === 0) {
                            $(this).closest('.window_inner').find('button.prev').addClass('inactive');
                        }
                        else if ($(this).closest('.window_inner').find('button.prev').hasClass('inactive')) {
                            $(this).closest('.window_inner').find('button.prev').removeClass('inactive');
                        }
                        if (rowCount - 1 === rowIndex) {
                            $(this).closest('.window_inner').find('button.next').addClass('inactive');
                        }
                        else if ($(this).closest('.window_inner').find('button.next').hasClass('inactive')) {
                            $(this).closest('.window_inner').find('button.next').removeClass('inactive');
                        }
                    } else {
                        $(this).closest('.window_inner').find('button.prev').addClass('inactive');
                        $(this).closest('.window_inner').find('button.next').addClass('inactive');
                    }

                    var id = $(this).closest('.window_inner').find('tr.active td span.id').text();
                    var window_id = $(this).closest('.window').attr('id').replace('window_','');
                    window[window_id + '_mvm' ].vm().selectedId(id);

                    $(this).closest('.window_inner').find('.window_bottom .left').text((rowIndex + 1) + '/' + rowCount);
//                    }
                });
                d.on('blur', 'table.data tr td input:text', function (e) {
                    JQD.util.clear_active();

                    var window_id = $(this).closest('.window').attr('id');
                    window[window_id.replace('window_','') + '_mvm' ].vm().selectedId(0);

                    $(this).closest('.window_inner').find('.window_bottom .left').text('');
//                    }
                });
            },
            wallpaper: function () {
                // Add wallpaper last, to prevent blocking.
//        if ($('#desktop').length) {
//          $('body').prepend('<img id="wallpaper" class="abs" src="/FormKit/Public/Images/Backgrounds/'+$('#desktop').attr('data-wallpaper')+'" />');
//        }
            }
        },
        util: {
            //
            // Clear active states, hide menus.
            //
            clear_active: function () {
                $('a.active, tr.active, td.active').removeClass('active');
                $('ul.menu').hide();
            },
            //
            // Clear active states, hide menus.
            //
            clear_active_sub: function () {
                $('a.submenu_trigger.active').removeClass('active');
                $('ul.submenu').hide();
            },
            //
            // Clear active states, hide menus.
            //
            clear_active_tool: function () {
                $('a.toolmenu_trigger.active').removeClass('active');
            },
            //
            // Zero out window z-index.
            //
            window_flat: function () {
                $('div.window').removeClass('window_stack');
            },
            //
            // Resize modal window.
            //
            window_resize: function (el) {
                // Nearest parent window.
                var win = $(el).closest('div.window');
                // Is it maximized already?
                if (win.hasClass('window_full')) {
                    // Restore window position.
                    win.removeClass('window_full').css({
                        'top': win.attr('data-t'),
                        'left': win.attr('data-l'),
                        'right': win.attr('data-r'),
                        'bottom': win.attr('data-b'),
                        'width': win.attr('data-w'),
                        'height': win.attr('data-h')
                    });
                }
                else {
                    win.attr({
                        // Save window position.
                        'data-t': win.css('top'),
                        'data-l': win.css('left'),
                        'data-r': win.css('right'),
                        'data-b': win.css('bottom'),
                        'data-w': win.css('width'),
                        'data-h': win.css('height')
                    }).addClass('window_full').css({
                        // Maximize dimensions.
                        'top': '0',
                        'left': '0',
                        'right': '0',
                        'bottom': '0',
                        'width': '100%',
                        'height': '100%'
                    });
                }

                // Bring window to front.
                JQD.util.window_flat();
                win.addClass('window_stack');
            }
        }
    };
// Pass in jQuery.
})(jQuery, this, this.document);
//
// Kick things off.
//
jQuery(document).ready(function () {
    JQD.go();
});