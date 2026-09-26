
if($('#calendar').length > 0) {
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        if (!calendarEl) {
            return;
        }

        var apiBase = calendarEl.dataset.calendarApi || '/api/calendar/events';
        var defaultClass = calendarEl.dataset.calendarDefaultClass || 'badge badge-primary-transparent';
        var Draggable = FullCalendar.Draggable;

        var containerEl = document.getElementById('external-events');
        if (containerEl) {
            new Draggable(containerEl, {
                itemSelector: '.fc-event',
                eventData: function (eventEl) {
                    var className = eventEl.getAttribute('data-event-classname');
                    return {
                        title: eventEl.innerText.trim(),
                        classNames: [className || defaultClass],
                    };
                }
            });
        }
        
        var selectedEvent = null;
        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                start: 'today prev,next',
                end: 'dayGridMonth,timeGridWeek,timeGridDay',
                center: 'title'
            },
            initialView: 'dayGridMonth',
            events: function(fetchInfo, successCallback, failureCallback) {
                var params = new URLSearchParams({
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                });

                fetch(apiBase + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Gagal memuat event kalender');
                    }
                    return response.json();
                })
                .then(function(data) {
                    var events = data.events || [];
                    successCallback(events);
                    renderUpcoming(events);
                })
                .catch(function(error) {
                    console.error(error);
                    failureCallback(error);
                });
            },
            editable: true,
            droppable: true,
            eventClick: function(info) {
                selectedEvent = info.event;
                populateFormFromEvent(info.event);
                openFormModal('Edit Event', 'Update Event', true);
            },
            eventReceive: function(info) {
                persistEvent(apiBase, info.event, defaultClass).then(function () {
                    renderUpcomingFromCalendar(calendar);
                }).catch(function (error) {
                    console.error(error);
                    info.revert();
                });
            },
            eventDrop: function(info) {
                syncEvent(apiBase, info.event, defaultClass).then(function () {
                    renderUpcomingFromCalendar(calendar);
                }).catch(function (error) {
                    console.error(error);
                    info.revert();
                });
            },
            eventResize: function(info) {
                syncEvent(apiBase, info.event, defaultClass).then(function () {
                    renderUpcomingFromCalendar(calendar);
                }).catch(function (error) {
                    console.error(error);
                    info.revert();
                });
            }
        });

        bindCreateForm(calendar, apiBase, defaultClass);
        calendar.render();

        $('#add_event').on('show.bs.modal', function () {
            if (!selectedEvent) {
                resetForm();
                openFormModal('Add New Event', 'Add Event', false);
            }
        });

        $('#add_event').on('hidden.bs.modal', function () {
            selectedEvent = null;
            resetForm();
            openFormModal('Add New Event', 'Add Event', false);
        });

        function setText(elementId, value) {
            var element = document.getElementById(elementId);
            if (element) {
                element.textContent = value || '-';
            }
        }

        function formatDatePart(dateObj) {
            var year = dateObj.getFullYear();
            var month = String(dateObj.getMonth() + 1).padStart(2, '0');
            var day = String(dateObj.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function formatTimePart(dateObj) {
            return String(dateObj.getHours()).padStart(2, '0') + ':' +
                String(dateObj.getMinutes()).padStart(2, '0') + ':' +
                String(dateObj.getSeconds()).padStart(2, '0');
        }

        function formatDateRange(event) {
            if (!event || !event.start) {
                return '-';
            }
            var start = event.start;
            var end = event.end ? new Date(event.end) : null;
            if (end && event.allDay) {
                end.setDate(end.getDate() - 1);
            }
            var startText = typeof moment !== 'undefined' ? moment(start).format('DD MMM YYYY') : formatDatePart(start);
            var endText = end ? (typeof moment !== 'undefined' ? moment(end).format('DD MMM YYYY') : formatDatePart(end)) : null;
            return endText && endText !== startText ? startText + ' - ' + endText : startText;
        }

        function formatTimeRange(event) {
            if (!event || event.allDay) {
                return 'All day';
            }
            var start = event.start ? formatTimePart(event.start) : '';
            var end = event.end ? formatTimePart(event.end) : '';
            if (start && end) {
                return start + ' - ' + end;
            }
            return start || end || '-';
        }

        function buildPayloadFromEvent(event, fallbackClass) {
            var start = event.start ? new Date(event.start) : null;
            var end = event.end ? new Date(event.end) : null;
            var isAllDay = !!event.allDay;

            if (end && isAllDay) {
                end.setDate(end.getDate() - 1);
            }

            return {
                title: event.title || 'Untitled',
                start_date: start ? formatDatePart(start) : '',
                end_date: end ? formatDatePart(end) : (start ? formatDatePart(start) : ''),
                start_time: isAllDay ? null : (start ? formatTimePart(start) : null),
                end_time: isAllDay ? null : (end ? formatTimePart(end) : null),
                all_day: isAllDay,
                location: event.extendedProps && event.extendedProps.location ? event.extendedProps.location : null,
                description: event.extendedProps && event.extendedProps.description ? event.extendedProps.description : null,
                background_color: event.backgroundColor || null,
                text_color: event.textColor || null,
                class_name: event.classNames && event.classNames.length > 0 ? event.classNames[0] : fallbackClass
            };
        }

        function persistEvent(api, event, fallbackClass) {
            var payload = buildPayloadFromEvent(event, fallbackClass);

            return fetch(api, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Gagal menyimpan event ke server.');
                }
                return response.json();
            })
            .then(function(data) {
                if (data.event && data.event.id) {
                    event.setProp('id', data.event.id);
                }
                if (data.event && data.event.className) {
                    event.setProp('classNames', Array.isArray(data.event.className) ? data.event.className : [data.event.className]);
                }
                if (data.event && data.event.backgroundColor) {
                    event.setProp('backgroundColor', data.event.backgroundColor);
                }
                if (data.event && data.event.textColor) {
                    event.setProp('textColor', data.event.textColor);
                }

                return data.event || payload;
            });
        }

        function syncEvent(api, event, fallbackClass) {
            if (!event.id) {
                return persistEvent(api, event, fallbackClass);
            }

            var payload = buildPayloadFromEvent(event, fallbackClass);

            return fetch(api + '/' + event.id, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Gagal memperbarui event.');
                }
                return response.json();
            });
        }

        function bindCreateForm(calendar, api, fallbackClass) {
            var form = document.getElementById('calendarEventForm');
            if (!form) {
                return;
            }

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                var formData = new FormData(form);
                var eventId = formData.get('event_id');
                var payload = {
                    title: formData.get('title') || '',
                    start_date: formData.get('start_date') || '',
                    start_time: formData.get('start_time') || '',
                    end_time: formData.get('end_time') || '',
                    location: formData.get('location') || '',
                    description: formData.get('description') || '',
                    all_day: formData.get('all_day') ? true : false,
                    class_name: fallbackClass
                };

                if (payload.all_day) {
                    payload.start_time = '';
                    payload.end_time = '';
                }

                var url = api;
                var method = 'POST';
                if (eventId) {
                    url = api + '/' + eventId;
                    method = 'PATCH';
                }

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Gagal menyimpan event.');
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (data.event) {
                        if (eventId) {
                            var existing = calendar.getEventById(eventId);
                            if (existing) {
                                existing.remove();
                            }
                        }
                        calendar.addEvent(data.event);
                        renderUpcomingFromCalendar(calendar);
                    }
                    $('#add_event').modal('hide');
                    form.reset();
                })
            .catch(function(error) {
                console.error(error);
            });
        });

            var deleteBtn = document.getElementById('deleteEventBtn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    var id = document.getElementById('calendar_event_id').value;
                    if (!id) {
                        $('#add_event').modal('hide');
                        return;
                    }
                    fetch(api + '/' + id, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Gagal menghapus event.');
                        }
                        var existing = calendar.getEventById(id);
                        if (existing) {
                            existing.remove();
                        }
                        renderUpcomingFromCalendar(calendar);
                        $('#add_event').modal('hide');
                    })
                    .catch(function(error) {
                        console.error(error);
                    });
                });
            }
        }

        function renderUpcoming(events) {
            var container = document.getElementById('upcoming-events');
            var badge = document.getElementById('upcoming-count');
            var empty = document.getElementById('upcoming-empty');
            var wrapper = document.getElementById('upcoming-events-wrapper');

            if (!container || !badge || !empty) {
                return;
            }

            container.innerHTML = '';

            if (!Array.isArray(events) || !events.length) {
                badge.textContent = '0';
                empty.style.display = 'block';
                return;
            }

            var limit = parseInt((wrapper && wrapper.dataset.upcomingLimit) || '5', 10);
            var sorted = events
                .slice()
                .map(function(ev) {
                    return {
                        title: ev.title || 'Untitled',
                        start: ev.start ? new Date(ev.start) : null,
                        className: Array.isArray(ev.className) ? ev.className[0] : ev.className || ''
                    };
                })
                .filter(function(ev) { return ev.start; })
                .sort(function(a, b) { return a.start - b.start; })
                .slice(0, limit);

            badge.textContent = sorted.length;
            empty.style.display = sorted.length ? 'none' : 'block';

            sorted.forEach(function(ev) {
                var borderClass = 'border-primary';
                if (ev.className.indexOf('success') >= 0) borderClass = 'border-success';
                else if (ev.className.indexOf('warning') >= 0) borderClass = 'border-warning';
                else if (ev.className.indexOf('danger') >= 0) borderClass = 'border-danger';
                else if (ev.className.indexOf('pink') >= 0) borderClass = 'border-pink';
                else if (ev.className.indexOf('purple') >= 0) borderClass = 'border-purple';

                var line = document.createElement('div');
                line.className = 'border-start ' + borderClass + ' border-3 mb-3';
                var inner = document.createElement('div');
                inner.className = 'ps-3';

                var title = document.createElement('h6');
                title.className = 'fw-medium mb-1';
                title.textContent = ev.title;

                var date = document.createElement('p');
                date.className = 'fs-12 mb-0';
                var icon = document.createElement('i');
                icon.className = 'ti ti-calendar-check text-info me-2';
                date.appendChild(icon);
                date.appendChild(document.createTextNode(formatDatePart(ev.start)));

                inner.appendChild(title);
                inner.appendChild(date);
                line.appendChild(inner);
                container.appendChild(line);
            });
        }

        function renderUpcomingFromCalendar(calendarInstance) {
            var events = calendarInstance.getEvents().map(function(ev) {
                return {
                    title: ev.title,
                    start: ev.start,
                    className: ev.classNames
                };
            });
            renderUpcoming(events);
        }

        function populateFormFromEvent(event) {
            var form = document.getElementById('calendarEventForm');
            if (!form) return;

            var dateInput = form.querySelector('[name=\"start_date\"]');
            var startTimeInput = form.querySelector('[name=\"start_time\"]');
            var endTimeInput = form.querySelector('[name=\"end_time\"]');
            var titleInput = form.querySelector('[name=\"title\"]');
            var locationInput = form.querySelector('[name=\"location\"]');
            var descInput = form.querySelector('[name=\"description\"]');
            var allDayInput = form.querySelector('[name=\"all_day\"]');
            var idInput = form.querySelector('[name=\"event_id\"]');

            if (titleInput) titleInput.value = event.title || '';
            if (locationInput) locationInput.value = (event.extendedProps && event.extendedProps.location) ? event.extendedProps.location : '';
            if (descInput) descInput.value = (event.extendedProps && event.extendedProps.description) ? event.extendedProps.description : '';
            if (idInput) idInput.value = event.id || '';

            if (dateInput && event.start) {
                dateInput.value = formatToDisplayDate(event.start);
            }

            var isAllDay = !!event.allDay;
            if (allDayInput) {
                allDayInput.checked = isAllDay;
            }

            if (startTimeInput) startTimeInput.value = isAllDay ? '' : formatToDisplayTime(event.start);

            if (endTimeInput) {
                if (isAllDay || !event.end) {
                    endTimeInput.value = '';
                } else {
                    var end = new Date(event.end);
                    if (isAllDay) {
                        end.setDate(end.getDate() - 1);
                    }
                    endTimeInput.value = formatToDisplayTime(end);
                }
            }
        }

        function formatToDisplayDate(dateObj) {
            var day = String(dateObj.getDate()).padStart(2, '0');
            var month = String(dateObj.getMonth() + 1).padStart(2, '0');
            var year = dateObj.getFullYear();
            return day + '-' + month + '-' + year;
        }

        function formatToDisplayTime(dateObj) {
            var hours = dateObj.getHours();
            var minutes = dateObj.getMinutes();
            var suffix = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            if (hours === 0) hours = 12;
            return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ' ' + suffix;
        }

        function openFormModal(title, saveLabel, showDelete) {
            var titleEl = document.getElementById('calendarModalTitle');
            var saveBtn = document.getElementById('saveEventBtn');
            var deleteBtn = document.getElementById('deleteEventBtn');
            if (titleEl) titleEl.textContent = title;
            if (saveBtn) saveBtn.textContent = saveLabel;
            if (deleteBtn) {
                deleteBtn.classList[showDelete ? 'remove' : 'add']('d-none');
            }
            $('#add_event').modal('show');
        }

        function resetForm() {
            var form = document.getElementById('calendarEventForm');
            if (!form) return;
            form.reset();
            var idInput = form.querySelector('[name=\"event_id\"]');
            if (idInput) idInput.value = '';
        }
    });			
}



// Static demo calendar removed to avoid template data showing in production
