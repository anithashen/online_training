$(document).ready(function () {
    $('#search_button').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/course_search_word',
            data: {data: $('#topic_search').val()},
            success: function (response) {
                var jsonData = JSON.parse(response);
                var searchCourseRows = '';
                if (jsonData != '') {
                    $.each(jsonData, function (i, val) {
                        searchCourseRows += '<tr>';
                        searchCourseRows += '<td>' + val.topic + '</td>';
                        searchCourseRows += '<td>' + val.description + '</td>';
                        searchCourseRows += '<td>' + val.startdatetime + ' to ' + val.enddatetime + '</td>';
                        searchCourseRows += '<td><a data-id=' + val.id + ' class="reservation" title="OptionToReserve" data-toggle="tooltip"><i class="fa fa-ticket"></i></a></td>';
                        searchCourseRows += '</tr>';
                    });
                    $("#featured_listing_tbody").html(searchCourseRows);
                } else {
                    $("#featured_listing_tbody").empty().append().html('<tr><td colspan="4">No record Found</td></tr>');
                }


            }
        });
    });
    $(document).on("click", ".reservation", function () {
        var courseId = $(this).data('id');
        $.confirmModal('Are you sure want to reserve this course?', function (e) {
            handleData(courseId);
        });
    });

    function handleData(courseId) {
        $.ajax({
            url: '/reserved_course',
            type: 'POST',
            data: {courseId: courseId},
            success: function (response) {
                if (response == 'success') {
                    $("#alert").css({display: "block", visibility: ""});
                    window.location.reload();
                }

            }
        })
    }

    $(document).on('click', '.alert-close', function () {
        $(this).parent().hide();
    })

});