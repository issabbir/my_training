/*$(".alert").fadeTo(2000, 2000).slideUp(500, function(){
    $(".alert").slideUp(500);
});*/

/*--Text Editor Start---*/
//editor Option
var toolbarOptions = [
    ['bold', 'italic', 'underline'],        // toggled buttons
    [{'list': 'ordered'}, {'list': 'bullet'}],
    [{'align': []}],
    //[{'size': ['small', false, 'large', 'huge']}]  // custom dropdown
];

/*var quill1 = new Quill('#editor1', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow',
});
var quill2 = new Quill('#editor2', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
});
var quill3 = new Quill('#editor3', {
    modules: {
        toolbar: toolbarOptions,
    },
    theme: 'snow'
});
var quill4 = new Quill('#editor4', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow',
});*/

var incidenceDescriptionEditor = new Quill('#course_content_editor', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
});

var incidenceDescriptionBnEditor = new Quill('#traininginfo_objectives_editor', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
});

var incidenceDescriptionBnEditor = new Quill('#training_facalities_editor', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
});

var assignDeptDescriptionEditor = new Quill('#assign_dept_desc_editor', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
});

var objectivesEditor = new Quill('#objectives_editor', {
    modules: {
        toolbar: toolbarOptions
    },
    theme: 'snow'
});

function buttonShowClearTextAreaAndEditor() {
    $(".ql-toolbar").append('<span class="ql-formats bx-pull-right"><button type="button" style="width:50px;" class="clearTextEditor">Clear</button></span>');
}

$(document).ready(function () {
    buttonShowClearTextAreaAndEditor();
});

function replacePtagToBrTag(editor) {
    $('p').each(function () {
        editor = editor.replace("<p>", " ");
        editor = editor.replace("</p>", "<br>");
    });
    return editor;
}

/*Text Editor Start*/

function districts(elem, container, url, decendentElem)
{
    $(elem).on('change', function() {
        let divisionId = $(this).val();
        if( ((divisionId !== undefined) || (divisionId != null)) && divisionId) {
            $.ajax({
                type: "GET",
                url: url+divisionId,
                success: function (data) {
                    $(container).html(data.html);
                    $(decendentElem).html('');
                },
                error: function (data) {
                    alert('error');
                }
            });
        } else {
            $(container).html('');
            $(decendentElem).html('');
        }
    });
}

function thanas(elem, url, container)
{
    $(elem).on('change', function() {
        let districtId = $(this).val();

        if( ((districtId !== undefined) || (districtId != null)) && districtId) {
            $.ajax({
                type: "GET",
                url: url+districtId,
                success: function (data) {
                    $(container).html(data.html);
                },
                error: function (data) {
                    alert('error');
                }
            });
        } else {
            $(container).html('');
        }
    });
}

function selectCpaEmployees(selector, allEmployeesFilterUrl, selectedEmployeeUrl,callback)
{
    $(selector).select2({
        placeholder: "Select",
        allowClear: false,
        ajax: {
            url: allEmployeesFilterUrl, // '/ajax/employees'
            data: function (params) {
                if(params.term) {
                    if (params.term.trim().length  < 1) {
                        return false;
                    }
                } else {
                    return false;
                }

                return params;
            },
            dataType: 'json',
            processResults: function(data) {
                var formattedResults = $.map(data, function(obj, idx) {
                    obj.id = obj.emp_id;
                    obj.text = obj.emp_code+' ('+obj.emp_name+')';
                    return obj;
                });
                return {
                    results: formattedResults,
                };
            },
        }
    });

    if(
        ($(selector).attr('data-emp-id') !== undefined) && ($(selector).attr('data-emp-id') !== null) && ($(selector).attr('data-emp-id') !== '')
    ) {
        selectDefaultCpaEmployee($(selector), selectedEmployeeUrl, $(selector).attr('data-emp-id'));
    }

    $(selector).on('select2:select', function (e) {
        var selectedEmployee = e.params.data;
        var that = this;

        if(selectedEmployee.emp_code) {
            $.ajax({
                type: "GET",
                url: selectedEmployeeUrl+selectedEmployee.emp_id, // '/ajax/employee/'
                success: function (data) {
                    callback(that, data);
                },
                error: function (data) {
                    alert('error');
                }
            });
        }
    });
}

function selectDefaultCpaEmployee(selector, selectedEmployeeUrl, empId)
{
    $.ajax({
        type: 'GET',
        url: selectedEmployeeUrl+empId, //  '/ajax/employee/'
    }).then(function (data) {
        // create the option and append to Select2
        var option = new Option(data.emp_code+' ('+data.emp_name+')', data.emp_id, true, true);
        selector.append(option).trigger('change');

        // manually trigger the `select2:select` event
        selector.trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
    });
}


function branches(elem, url, container)
{
    $(elem).on('change', function() {
        let branchId = $(this).val();

        if( ((branchId !== undefined) || (branchId != null)) && branchId) {
            $.ajax({
                type: "GET",
                url: url+branchId,
                success: function (data) {
                    $(container).html(data.html);
                },
                error: function (data) {
                    alert('error');
                }
            });
        } else {
            $(container).html('');
        }
    });
}

function datePickerUsingDiv(divSelector) { // divSelector is the targeted parent div of date input field
    var elem = $(divSelector);
    elem.datetimepicker({
        format: 'YYYY-MM-DD',
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });
}

function dateRangePicker(Elem1, Elem2){
    let minElem = $(Elem1);
    let maxElem = $(Elem2);

    minElem.datetimepicker({
        format: 'YYYY-MM-DD',
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });
    maxElem.datetimepicker({
        useCurrent: false,
        format: 'YYYY-MM-DD',
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });
    minElem.on("change.datetimepicker", function (e) {
        maxElem.datetimepicker('minDate', e.date);
    });
    maxElem.on("change.datetimepicker", function (e) {
        minElem.datetimepicker('maxDate', e.date);
    });

    let preDefinedDateMin = minElem.attr('data-predefined-date');
    let preDefinedDateMax = maxElem.attr('data-predefined-date');

    if (preDefinedDateMin) {
        let preDefinedDateMomentFormat = moment(preDefinedDateMin, "YYYY-MM-DD").format("YYYY-MM-DD");
        minElem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }

    if (preDefinedDateMax) {
        let preDefinedDateMomentFormat = moment(preDefinedDateMax, "YYYY-MM-DD").format("YYYY-MM-DD");
        maxElem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }

}

function timeRangePicker(Elem1, Elem2){
    let minElem = $(Elem1);
    let maxElem = $(Elem2);

    minElem.datetimepicker({
        format: 'LT',
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });
    maxElem.datetimepicker({
        useCurrent: false,
        format: 'LT',
        ignoreReadonly: true,
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });
    minElem.on("change.datetimepicker", function (e) {
        maxElem.datetimepicker('minDate', e.date);
    });
    maxElem.on("change.datetimepicker", function (e) {
        minElem.datetimepicker('maxDate', e.date);
    });

    let preDefinedDateMin = minElem.attr('data-predefined-date');
    let preDefinedDateMax = maxElem.attr('data-predefined-date');

    if (preDefinedDateMin) {
        let preDefinedDateMomentFormat = moment(preDefinedDateMin, "YYYY-MM-DD HH:mm").format("HH:mm A");
        minElem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }

    if (preDefinedDateMax) {
        let preDefinedDateMomentFormat = moment(preDefinedDateMax, "YYYY-MM-DD HH:mm").format("HH:mm A");
        maxElem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }

}

function datePicker(selector) {
    var elem = $(selector);

    elem.datetimepicker({
        format: 'YYYY-MM-DD',
        ignoreReadonly: true,
        widgetPosiFtioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });
    let preDefinedDate = elem.attr('data-predefined-date');

    if (preDefinedDate) {
        let preDefinedDateMomentFormat = moment(preDefinedDate, "YYYY-MM-DD").format("YYYY-MM-DD");
        elem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }
}

function dateTimePicker(selector) {
    var elem = $(selector);
    elem.datetimepicker({
        format: 'YYYY-MM-DD LT',
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });

    let preDefinedDate = elem.attr('data-predefined-date');

    if (preDefinedDate) {
        let preDefinedDateMomentFormat = moment(preDefinedDate, "YYYY-MM-DD HH:mm").format("YYYY-MM-DD HH:mm A");
        elem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }
}

function timePicker(selector) {
    var elem = $(selector);
    elem.datetimepicker({
        format: 'LT',
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            time: 'bx bx-time',
            date: 'bx bxs-calendar',
            up: 'bx bx-up-arrow-alt',
            down: 'bx bx-down-arrow-alt',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right',
            today: 'bx bxs-calendar-check',
            clear: 'bx bx-trash',
            close: 'bx bx-window-close'
        }
    });

    let preDefinedDate = elem.attr('data-predefined-date');

    if (preDefinedDate) {
        let preDefinedDateMomentFormat = moment(preDefinedDate, "YYYY-MM-DD HH:mm").format("HH:mm A");
        elem.datetimepicker('defaultDate', preDefinedDateMomentFormat);
    }
}

function selectBookings(selector, allBookingsFilterUrl, selectedBookingUrl, callback, excludesCallback)
{
    $(selector).select2({
        placeholder: "Select",
        allowClear: false,
        ajax: {
            url: allBookingsFilterUrl,
            data: function (params) {
                var query = {
                    term: params.term,
                    exclude: excludesCallback
                }

                return query;
            },
            dataType: 'json',
            processResults: function(data) {
                var formattedResults = $.map(data, function(obj, idx) {
                    obj.id = obj.booking_mst_id;
                    obj.text = obj.booking_no;
                    return obj;
                });
                return {
                    results: formattedResults,
                };
            },
        }
    });

    if(
        ($(selector).attr('data-booking-id') !== undefined) && ($(selector).attr('data-booking-id') !== null) && ($(selector).attr('data-booking-id') !== '')
    ) {
        selectDefaultBooking($(selector), selectedBookingUrl, $(selector).attr('data-booking-id'));
    }

    $(selector).on('select2:select', function (e) {
        var selectedBooking = e.params.data;
        var that = this;

        if(selectedBooking.booking_no) {
            $.ajax({
                type: "GET",
                url: selectedBookingUrl+selectedBooking.booking_mst_id,
                success: function (data) {
                    callback(that, data);
                },
                error: function (data) {
                    alert('error');
                }
            });
        }
    });
}

function selectDefaultBooking(selector, selectedBookingUrl, bookingId)
{
    $.ajax({
        type: 'GET',
        url: selectedBookingUrl+bookingId,
    }).then(function (data) {
        var info = data.booking;
        // create the option and append to Select2
        var option = new Option(info.booking_no, info.booking_mst_id, true, true);
        selector.append(option).trigger('change');

        // manually trigger the `select2:select` event
        selector.trigger({
            type: 'select2:select',
            params: {
                data: info
            }
        });
    });
}

function formSubmission(formElem, clickedElem, callback, message)
{
    $(clickedElem).click(function(e) {
        e.preventDefault();
        callback(formElem);
        var isValid = $(formElem).valid();

        if(isValid) {
            var confirmation = confirm(message);
            if(confirmation == true) {
                $(formElem).submit();
            }
        }
    });
}

function selectTraining(selector, allTrainingFilterUrl, selectedTrainingUrl, callback)
{
    $(selector).select2({
        placeholder: "Select",
        allowClear: false,
        ajax: {
            url: allTrainingFilterUrl, // '/ajax/employees'
            data: function (params) {
                if(params.term) {
                    if (params.term.trim().length  < 1) {
                        return false;
                    }
                } else {
                    return false;
                }

                return params;
            },
            dataType: 'json',
            processResults: function(data) {
                var formattedResults = $.map(data, function(obj, idx) {
                    obj.id = obj.training_id;
                    //obj.text = obj.training_number;
                    obj.text = obj.training_number+' ('+obj.training_title+')';
                    return obj;
                });
                return {
                    results: formattedResults,
                };
            },
        }
    });

    if(
        ($(selector).attr('data-training-id') !== undefined) && ($(selector).attr('data-training-id') !== null) && ($(selector).attr('data-training-id') !== '')
    ) {
        selectDefaultTraining($(selector), selectedTrainingUrl, $(selector).attr('data-training-id'));
    }

    $(selector).on('select2:select', function (e) {
        var selectedTraining = e.params.data;
        var that = this;

        if(selectedTraining.training_number) {
            $.ajax({
                type: "GET",
                url: selectedTrainingUrl+selectedTraining.training_id, // '/ajax/employee/'
                success: function (data) {
                    callback(that, data);
                },
                error: function (data) {
                    alert('error');
                }
            });
        }
    });
}

function selectDefaultTraining(selector, selectedTrainingUrl, trainingId)
{
    $.ajax({
        type: 'GET',
        url: selectedTrainingUrl+trainingId, //  '/ajax/employee/'
    }).then(function (data) {
        // create the option and append to Select2
        //var option = new Option(data.training_number, data.training_id, true, true);
        var option = new Option(data.training_number+' ('+data.training_title+')', data.training_id, true, true);
        selector.append(option).trigger('change');

        // manually trigger the `select2:select` event
        selector.trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
    });
}

/*function selectAgency(selector, allAgencyFilterUrl, selectedAgencyUrl, callback)
{
    $(selector).select2({
        placeholder: "Select",
        allowClear: false,
        ajax: {
            url: allAgencyFilterUrl, // '/ajax/employees'
            data: function (params) {
                if(params.term) {
                    if (params.term.trim().length  < 1) {
                        return false;
                    }
                } else {
                    return false;
                }

                return params;
            },
            dataType: 'json',
            processResults: function(data) {
                var formattedResults = $.map(data, function(obj, idx) {
                    obj.id = obj.agency_id;
                    obj.text = obj.agency_name;
                    return obj;
                });
                return {
                    results: formattedResults,
                };
            },
        }
    });

    if(
        ($(selector).attr('data-agency-id') !== undefined) && ($(selector).attr('data-agency-id') !== null) && ($(selector).attr('data-agency-id') !== '')
    ) {
        selectDefaultAgency($(selector), selectedAgencyUrl, $(selector).attr('data-agency-id'));
    }

    $(selector).on('select2:select', function (e) {
        var selectedAgency = e.params.data;
        var that = this;

        if(selectedAgency.agency_name) {
            $.ajax({
                type: "GET",
                url: selectedAgencyUrl+selectedAgency.agency_id, // '/ajax/employee/'
                success: function (data) {
                    callback(that, data);
                },
                error: function (data) {
                    alert('error');
                }
            });
        }
    });
}

function selectDefaultAgency(selector, selectedAgencyUrl, agencyId)
{
    $.ajax({
        type: 'GET',
        url: selectedAgencyUrl+agencyId, //  '/ajax/employee/'
    }).then(function (data) {
        // create the option and append to Select2
        var option = new Option(data.agency_name, data.agency_id, true, true);
        selector.append(option).trigger('change');

        // manually trigger the `select2:select` event
        selector.trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
    });
}*/


$('.global-number-validation').on('keypress', function(e) {
    // e is event.
    var keyCode = e.which;
    /*
      8 - (backspace)
      32 - (space)
      48-57 - (0-9)Numbers
    */

    if ( (keyCode != 8 || keyCode ==32 ) && (keyCode < 48 || keyCode > 57)) {
        return false;
    }
});

/** Top bar All News Modal Js Start*/

$(".dynamicModal").on("click", function () {
    var news_id=this.getAttribute('news_id');
    $.ajax(
        {
            type: 'GET',
            url: '/get-top-news',
            data: {news_id:news_id},
            dataType: "json",
            success: function (data) {
                $("#dynamicNewsModalContent").html(data.newsView);
                $('#dynamicNewsModal').modal('show');
            }
        }
    );
});

/** Select2 Content Design Js*/

function customSelect2(){
    $('select').select2({
        width: '100%'
    });
}

