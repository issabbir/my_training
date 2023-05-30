<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'UserController@index')->name('login');

Route::post('/authorization/login', 'Auth\LoginController@authorization')->name('authorization.login');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('/user/change-password', function () {
        return view('resetPassword');
    })->name('change-password');

    Route::post('/user/change-password', 'Auth\ResetPasswordController@resetPassword')->name('user.reset-password');
    Route::post('/report/render/{title}', 'Report\OraclePublisherController@render')->name('report');
    Route::get('/report/render/{title?}', 'Report\OraclePublisherController@render')->name('report-get');
    Route::post('/authorization/logout', 'Auth\LoginController@logout')->name('logout');

    // For News
    Route::get('/get-top-news', 'NewsController@getNews')->name('get-top-news');
    Route::get('/news-download/{id}', 'NewsController@downloadAttachment')->name('news-download');

    //----------------------------------------------------------------------------------Ajax------------------

    Route::group(['prefix' => 'ajax', 'name' => 'ajax', 'as' => 'ajax.'], function () {
        Route::get('/employees', 'Training\AjaxController@employees')->name('employees');
        Route::get('/employee/{empId}', 'Training\AjaxController@employee')->name('employee');

        Route::get('/districts/{divisionId}', 'Training\AjaxController@districts')->name('districts');
        Route::get('/thanas/{districtId}', 'Training\AjaxController@thanas')->name('thanas');

        Route::get('/training-details/{trainingId}', 'Training\AjaxController@trainingDetails')->name('training-details');

        Route::get('/get-exam-type/{batch_id}', 'Training\AjaxController@getExamType')->name('get-exam-type');
        Route::get('/get-dept-wise-emp/{dept_id}', 'Training\AjaxController@getDeptWiseEmp')->name('get-dept-wise-emp');
        Route::get('/get-dept-head/{dept_id}', 'Training\AjaxController@getDeptHead')->name('get-dept-head');

        Route::get('/get-training-wise-emp/{training_id}', 'Training\AjaxController@getTrainingWiseEmp')->name('get-training-wise-emp');
        Route::get('/get-training-wise-emp-for-all/{training_id}', 'Training\AjaxController@getTrainingWiseEmpForAll')->name('get-training-wise-emp-for-all');
        Route::get('/all-trainee-info/{schedule_id}', 'Training\AjaxController@getAllTraineeInfo')->name('all-trainee-info');
        Route::get('/all-batch-wise-feed-back-trainee/{schedule_id}', 'Training\AjaxController@getAllBatchWiseFeedBackTrainee')->name('all-batch-wise-feed-back-trainee');
        Route::get('/total-days/{from_date}/{to_date}', 'Training\AjaxController@getTotalDays')->name('total-days');
//        Route::get('/select-date/{batch_id}/{schedule_dtl_id}', 'Training\AjaxController@selectDate')->name('select-date');
        Route::get('/select-date', 'Training\AjaxController@selectDate')->name('select-date');

        Route::get('/trainee-type/{traineeTypeId}', 'Training\AjaxController@getTraineeType')->name('trainee-type');
        Route::get('/dept-wise-trainee', 'Training\AjaxController@deptWiseTrainee')->name('dept-wise-trainee');
        Route::get('/dept-wise-trainee-dtl/{deptId}', 'Training\AjaxController@deptWiseTraineeDtl')->name('dept-wise-trainee-dtl');
        Route::get('/all-employee', 'Training\AjaxController@allEmployee')->name('all-employee');

        //Report Component
        Route::get('/dept-name', 'Training\AjaxController@deptName')->name('dept-name');
        Route::get('/training-no', 'Training\AjaxController@trainingNo')->name('training-no');
        Route::get('/emp-code', 'Training\AjaxController@empCode')->name('emp-code');
        Route::get('/trainer-name', 'Training\AjaxController@trainerName')->name('trainer-name');
        Route::get('/schedule-no', 'Training\AjaxController@scheduleNo')->name('schedule-no');
        Route::get('/training-batch-no/{trainingInfoId}', 'Training\AjaxController@trainingBatchNo')->name('training-batch-no');
        Route::get('/training-exist-no/{batchNO}', 'Training\AjaxController@trainingExistNo')->name('training-exist-no');
        Route::get('/batch-trainee-name/{scheduleId}', 'Training\AjaxController@batchTraineeName')->name('batch-trainee-name');
    });

    //----------------------------------------------------------------------------------Ajax------------------

    //Report Route
    Route::group(['name' => 'report-generator', 'as' => 'training-report-generator.'], function () {
        Route::get('/report-generators', 'Training\ReportGeneratorController@index')->name('index');
        Route::get('/report-generator-params/{id}', 'Training\ReportGeneratorController@reportParams')->name('report-params');
    });

    //----------------------------------------------------------------------------------Setup------------------

    Route::group(['name' => 'training-type', 'as' => 'training-type.'], function () {
        Route::get('/training-type', 'Training\Setup\TrainingTypeController@index')->name('training-type-index');
        Route::get('/training-type/{id}', 'Training\Setup\TrainingTypeController@edit')->name('training-type-edit');
        Route::post('/training-type', 'Training\Setup\TrainingTypeController@post')->name('training-type-post');
        Route::put('/training-type/{id}', 'Training\Setup\TrainingTypeController@update')->name('training-type-update');
        Route::post('/training-type-datatable-list', 'Training\Setup\TrainingTypeController@dataTableList')->name('training-type-datatable-list');
    });

    Route::group(['name' => 'course-entry', 'as' => 'course-entry.'], function () {
        Route::get('/course-entry', 'Training\Setup\CourseEntryController@index')->name('course-entry-index');
        Route::get('/course-entry/{id}', 'Training\Setup\CourseEntryController@edit')->name('course-entry-edit');
        Route::post('/course-entry', 'Training\Setup\CourseEntryController@post')->name('course-entry-post');
        Route::put('/course-entry/{id}', 'Training\Setup\CourseEntryController@update')->name('course-entry-update');
        Route::post('/course-entry-datatable-list', 'Training\Setup\CourseEntryController@dataTableList')->name('course-entry-datatable-list');
        Route::get('/course-entry/download/{id}', 'Training\DownloaderController@courseEntryDownload')->name('course-entry-file-download');
    });

    Route::group(['name' => 'exam-type', 'as' => 'exam-type.'], function () {
        Route::get('/exam-type', 'Training\Setup\ExamTypeController@index')->name('exam-type-index');
        Route::get('/exam-type/{id}', 'Training\Setup\ExamTypeController@edit')->name('exam-type-edit');
        Route::post('/exam-type', 'Training\Setup\ExamTypeController@post')->name('exam-type-post');
        Route::put('/exam-type/{id}', 'Training\Setup\ExamTypeController@update')->name('exam-type-update');
        Route::post('/exam-type-datatable-list', 'Training\Setup\ExamTypeController@dataTableList')->name('exam-type-datatable-list');
    });

    Route::group(['name' => 'trainee-type', 'as' => 'trainee-type.'], function () {
        Route::get('/trainee-type', 'Training\Setup\TraineeTypeController@index')->name('trainee-type-index');
        Route::get('/trainee-type/{id}', 'Training\Setup\TraineeTypeController@edit')->name('trainee-type-edit');
        Route::post('/trainee-type', 'Training\Setup\TraineeTypeController@post')->name('trainee-type-post');
        Route::put('/trainee-type/{id}', 'Training\Setup\TraineeTypeController@update')->name('trainee-type-update');
        Route::post('/trainee-type-datatable-list', 'Training\Setup\TraineeTypeController@dataTableList')->name('trainee-type-datatable-list');
    });

    Route::group(['name' => 'training-media', 'as' => 'training-media.'], function () {
        Route::get('/training-media', 'Training\Setup\TrainingMediaController@index')->name('training-media-index');
        Route::get('/training-media/{id}', 'Training\Setup\TrainingMediaController@edit')->name('training-media-edit');
        Route::post('/training-media', 'Training\Setup\TrainingMediaController@post')->name('training-media-post');
        Route::put('/training-media/{id}', 'Training\Setup\TrainingMediaController@update')->name('training-media-update');
        Route::post('/training-media-datatable-list', 'Training\Setup\TrainingMediaController@dataTableList')->name('training-media-datatable-list');
    });

    Route::group(['name' => 'location-entry', 'as' => 'location-entry.'], function () {
        Route::get('/location-entry', 'Training\Setup\LocationEntryController@index')->name('location-entry-index');
        Route::get('/location-entry/{id}', 'Training\Setup\LocationEntryController@edit')->name('location-entry-edit');
        Route::post('/location-entry', 'Training\Setup\LocationEntryController@post')->name('location-entry-post');
        Route::put('/location-entry/{id}', 'Training\Setup\LocationEntryController@update')->name('location-entry-update');
        Route::post('/location-entry-datatable-list', 'Training\Setup\LocationEntryController@dataTableList')->name('location-entry-datatable-list');
    });

    Route::group(['name' => 'expertise-entry', 'as' => 'expertise-entry.'], function () {
        Route::get('/expertise-entry', 'Training\Setup\ExpertiseEntryController@index')->name('expertise-entry-index');
        Route::get('/expertise-entry/{id}', 'Training\Setup\ExpertiseEntryController@edit')->name('expertise-entry-edit');
        Route::post('/expertise-entry', 'Training\Setup\ExpertiseEntryController@post')->name('expertise-entry-post');
        Route::put('/expertise-entry/{id}', 'Training\Setup\ExpertiseEntryController@update')->name('expertise-entry-update');
        Route::post('/expertise-entry-datatable-list', 'Training\Setup\ExpertiseEntryController@dataTableList')->name('expertise-entry-datatable-list');
    });

    Route::group(['name' => 'pre-requisite-entry', 'as' => 'pre-requisite-entry.'], function () {
        Route::get('/pre-requisite-entry', 'Training\Setup\LPreRequisiteEntryController@index')->name('pre-requisite-entry-index');
        Route::get('/pre-requisite-entry/{id}', 'Training\Setup\LPreRequisiteEntryController@edit')->name('pre-requisite-entry-edit');
        Route::post('/pre-requisite-entry', 'Training\Setup\LPreRequisiteEntryController@post')->name('pre-requisite-entry-post');
        Route::put('/pre-requisite-entry/{id}', 'Training\Setup\LPreRequisiteEntryController@update')->name('pre-requisite-entry-update');
        Route::post('/pre-requisite-entry-datatable-list', 'Training\Setup\LPreRequisiteEntryController@dataTableList')->name('pre-requisite-entry-datatable-list');
    });

    Route::group(['name' => 'foreign-tour-sponsor', 'as' => 'foreign-tour-sponsor.'], function () {
        Route::get('/foreign-tour-sponsor', 'Training\Setup\ForeignTourSponsorController@index')->name('foreign-tour-sponsor-index');
        Route::get('/foreign-tour-sponsor/{id}', 'Training\Setup\ForeignTourSponsorController@edit')->name('foreign-tour-sponsor-edit');
        Route::post('/foreign-tour-sponsor', 'Training\Setup\ForeignTourSponsorController@post')->name('foreign-tour-sponsor-post');
        Route::put('/foreign-tour-sponsor/{id}', 'Training\Setup\ForeignTourSponsorController@update')->name('foreign-tour-sponsor-update');
        Route::post('/foreign-tour-sponsor-datatable-list', 'Training\Setup\ForeignTourSponsorController@dataTableList')->name('foreign-tour-sponsor-datatable-list');
    });

    Route::group(['name' => 'tour-type-entry', 'as' => 'tour-type-entry.'], function () {
        Route::get('/tour-type-entry', 'Training\Setup\TourTypeEntryController@index')->name('tour-type-entry-index');
        Route::get('/tour-type-entry/{id}', 'Training\Setup\TourTypeEntryController@edit')->name('tour-type-entry-edit');
        Route::post('/tour-type-entry', 'Training\Setup\TourTypeEntryController@post')->name('tour-type-entry-post');
        Route::put('/tour-type-entry/{id}', 'Training\Setup\TourTypeEntryController@update')->name('tour-type-entry-update');
        Route::get('/tour-type-entry/delete/{id}', 'Training\Setup\TourTypeEntryController@delete')->name('tour-type-entry-delete');
        Route::post('/tour-type-entry-datatable-list', 'Training\Setup\TourTypeEntryController@dataTableList')->name('tour-type-entry-datatable-list');
    });
    //----------------------------------------------------------------------------------Setup End------------------

    Route::group(['name' => 'training-requisition', 'as' => 'training-requisition.'], function () {
        Route::get('/requisition', 'Training\RequisitionController@index')->name('training-requisition-index');
        Route::get('/requisition/{id}', 'Training\RequisitionController@edit')->name('training-requisition-edit');
        Route::post('/requisition', 'Training\RequisitionController@post')->name('training-requisition-post');
        Route::put('/requisition/{id}', 'Training\RequisitionController@update')->name('training-requisition-update');
        Route::post('/requisition-datatable-list', 'Training\RequisitionController@dataTableList')->name('training-requisition-datatable-list');
        //Route::get('/requisition-datatable-list', 'Training\RequisitionController@dataTableList')->name('training-requisition-datatable-list');
    });

    Route::group(['name' => 'training-information', 'as' => 'training-information.'], function () {
        Route::get('/training-information', 'Training\TrainingInfoController@index')->name('training-information-index');
        Route::get('/training-information/{id}', 'Training\TrainingInfoController@edit')->name('training-information-edit');
        Route::post('/training-information', 'Training\TrainingInfoController@post')->name('training-information-post');
        Route::put('/training-information/{id}', 'Training\TrainingInfoController@update')->name('training-information-update');
        Route::post('/training-information-datatable-list', 'Training\TrainingInfoController@dataTableList')->name('training-information-datatable-list');
        //Route::get('/training-information-datatable-list', 'Training\TrainingInfoController@dataTableList')->name('training-information-datatable-list');
        Route::get('/training-information/download/{id}', 'Training\DownloaderController@trainingInfoDownload')->name('training-info-file-download');

    });

    Route::group(['name' => 'trainee-information', 'as' => 'trainee-information.'], function () {
        Route::get('/trainee-information', 'Training\TraineeInformationController@index')->name('trainee-information-index');
        Route::get('/trainee-information/{id}', 'Training\TraineeInformationController@edit')->name('trainee-information-edit');
        Route::post('/trainee-information', 'Training\TraineeInformationController@post')->name('trainee-information-post');
        Route::put('/trainee-information/{id}', 'Training\TraineeInformationController@update')->name('trainee-information-update');
        Route::post('/trainee-information-datatable-list', 'Training\TraineeInformationController@dataTableList')->name('trainee-information-datatable-list');
    });

    Route::group(['name' => 'trainee-evaluation', 'as' => 'trainee-evaluation.'], function () {
        Route::get('/trainee-evaluation', 'Training\TraineeEvaluationController@index')->name('trainee-evaluation-index');
        Route::post('/trainee-evaluation-datatable', 'Training\TraineeEvaluationController@datatable')->name('trainee-evaluation-datatable');
        Route::post('/trainee-evaluation', 'Training\TraineeEvaluationController@post')->name('trainee-evaluation-post');
    });

    Route::group(['name' => 'trainee-evaluation-update', 'as' => 'trainee-evaluation-update.'], function () {
        Route::get('/trainee-evaluation-update', 'Training\TraineeEvaluationUpdateController@index')->name('trainee-evaluation-update-index');
        Route::post('/trainee-evaluation-update-datatable', 'Training\TraineeEvaluationUpdateController@datatable')->name('trainee-evaluation-update-datatable');
        Route::post('/trainee-evaluation-update', 'Training\TraineeEvaluationUpdateController@post')->name('trainee-evaluation-update-post');
    });

    Route::group(['name' => 'trainer-information', 'as' => 'trainer-information.'], function () {
        Route::post('/trainer-info', 'Training\TrainerInfoController@post')->name('trainer-info-post');
        Route::get('/trainer-info', 'Training\TrainerInfoController@form')->name('trainer-info-get');
        Route::put('/trainer-info/{id}', 'Training\TrainerInfoController@update')->name('trainer-info-update');
        Route::get('/trainer-info-datatable', 'Training\TrainerInfoController@dataTableIndex')->name('trainer-info-datatable');
        Route::post('/trainer-info-datatable-list', 'Training\TrainerInfoController@dataTableList')->name('trainer-info-datatable-list');
        Route::get('/trainer-info-photo/download/{id}', 'Training\DownloaderController@trainerPhotoDownload')->name('trainer-info-photo-download');

        Route::get('/trainer-address/{id}', 'Training\TrainerInfoAddressController@form')->name('trainer-address-get');
        Route::post('/trainer-address/{id}', 'Training\TrainerInfoAddressController@post')->name('trainer-address-post');
        Route::put('/trainer-address/{trainerId}/{aid}', 'Training\TrainerInfoAddressController@update')->name('trainer-address-update');

        Route::get('/trainer-education/{id}', 'Training\TrainerInfoEducationController@form')->name('trainer-education-get');
        Route::post('/trainer-education/{id}', 'Training\TrainerInfoEducationController@post')->name('trainer-education-post');
        Route::put('/trainer-education/{trainerId}/{teid}', 'Training\TrainerInfoEducationController@update')->name('trainer-education-update');
        Route::get('/trainer-education-cert/download/{id}', 'Training\DownloaderController@trainerCertDownload')->name('trainer-education-cert-download');
        Route::get('/trainer-education-trans/download/{id}', 'Training\DownloaderController@trainerTransDownload')->name('trainer-education-trans-download');

        Route::get('/trainer-experience/{id}', 'Training\TrainerInfoExperienceController@form')->name('trainer-experience-get');
        Route::post('/trainer-experience/{id}', 'Training\TrainerInfoExperienceController@post')->name('trainer-experience-post');
        Route::put('/trainer-experience/{trainerId}/{eid}', 'Training\TrainerInfoExperienceController@update')->name('trainer-experience-update');
        Route::get('/trainer-experience-exp/download/{id}', 'Training\DownloaderController@trainerExpeDownload')->name('trainer-experience-exp-download');
        Route::get('/trainer-experience-rel/download/{id}', 'Training\DownloaderController@trainerRelsDownload')->name('trainer-experience-rel-download');

        Route::get('/trainer-training/{id}', 'Training\TrainerInfoTrainingController@form')->name('trainer-training-get');
        Route::post('/trainer-training/{id}', 'Training\TrainerInfoTrainingController@post')->name('trainer-training-post');
        Route::put('/trainer-training/{trainerId}/{ttid}', 'Training\TrainerInfoTrainingController@update')->name('trainer-training-update');
        Route::get('/trainer-training-attach/download/{id}', 'Training\DownloaderController@trainerTrainingAttaDownload')->name('trainer-training-attach-download');

        Route::get('/trainer-expertise/{id}', 'Training\TrainerInfoExpertiseController@form')->name('trainer-expertise-get');
        Route::post('/trainer-expertise/{id}', 'Training\TrainerInfoExpertiseController@post')->name('trainer-expertise-post');
        Route::put('/trainer-expertise/{trainerId}/{texid}', 'Training\TrainerInfoExpertiseController@update')->name('trainer-expertise-update');
    });

    Route::group(['name' => 'training-schedule', 'as' => 'training-schedule.'], function () {
        Route::get('/training-schedule', 'Training\TrainingScheduleController@index')->name('training-schedule-index');
        Route::get('/training-schedule/{id}', 'Training\TrainingScheduleController@edit')->name('training-schedule-edit');
        Route::post('/training-schedule', 'Training\TrainingScheduleController@post')->name('training-schedule-post');
        Route::put('/training-schedule/{id}', 'Training\TrainingScheduleController@update')->name('training-schedule-update');
        Route::post('/training-schedule-datatable-list', 'Training\TrainingScheduleController@dataTableList')->name('training-schedule-datatable-list');
        Route::get('/get-trainee-data', 'Training\TrainingScheduleController@traineeData')->name('get-trainee-data');
        Route::get('/trainee-data-remove', 'Training\TrainingScheduleController@removeTraineeData')->name('trainee-data-remove');
        Route::get('/trainer-data-remove', 'Training\TrainingScheduleController@removeTrainerData')->name('trainer-data-remove');
        Route::get('/exam-type-data-remove', 'Training\TrainingScheduleController@removeExamTypeData')->name('exam-type-data-remove');
        Route::get('/support-member-remove', 'Training\TrainingScheduleController@removeSupportingStuffData')->name('support-member-remove');
    });

    Route::group(['name' => 'foreign-tour', 'as' => 'foreign-tour.'], function () {
        Route::get('/foreign-tour', 'Training\ForeignTourController@index')->name('foreign-tour-index');
        Route::get('/foreign-tour/{id}', 'Training\ForeignTourController@edit')->name('foreign-tour-edit');
        Route::post('/foreign-tour', 'Training\ForeignTourController@post')->name('foreign-tour-post');
        Route::put('/foreign-tour/{id}', 'Training\ForeignTourController@update')->name('foreign-tour-update');
        Route::post('/foreign-tour-datatable-list', 'Training\ForeignTourController@dataTableList')->name('foreign-tour-datatable-list');
        Route::get('/foreign-tour/download/{id}', 'Training\DownloaderController@foreignTourOrderDownload')->name('foreign-tour-file-download');
    });

    Route::group(['name' => 'trainee-feedback', 'as' => 'trainee-feedback.'], function () {
        Route::get('/trainee-feedback', 'Training\TraineeFeedbackController@index')->name('trainee-feedback-index');
        Route::get('/trainee-feedback/{id}', 'Training\TraineeFeedbackController@edit')->name('trainee-feedback-edit');
        Route::post('/trainee-feedback', 'Training\TraineeFeedbackController@post')->name('trainee-feedback-post');
        Route::put('/trainee-feedback/{id}', 'Training\TraineeFeedbackController@update')->name('trainee-feedback-update');
        Route::get('/trainee-feedback-datatable-list', 'Training\TraineeFeedbackController@dataTableList')->name('trainee-feedback-datatable-list');
    });

    Route::group(['name' => 'calendar-mst', 'as' => 'calendar-mst.'], function () {
        Route::get('/calendar-mst', 'Training\CalendarMstController@index')->name('calendar-mst-index');
        Route::get('/calendar-mst/{id}', 'Training\CalendarMstController@edit')->name('calendar-mst-edit');
        Route::post('/calendar-mst', 'Training\CalendarMstController@post')->name('calendar-mst-post');
        Route::put('/calendar-mst/{id}', 'Training\CalendarMstController@update')->name('calendar-mst-update');
        Route::post('/calendar-mst-datatable-list', 'Training\CalendarMstController@dataTableList')->name('calendar-mst-datatable-list');
        Route::get('/get-training-info-data', 'Training\CalendarMstController@getTrainingInfoData')->name('get-training-info-data');
        Route::get('/detail-data-remove', 'Training\CalendarMstController@removeDetailData')->name('detail-data-remove');

    });

    Route::group(['name' => 'create-calender', 'as' => 'create-calender.'], function () {
        Route::get('/create-calender', 'Training\CreateCalendarController@index')->name('create-calender-index');
        Route::get('/create-calender/{id}', 'Training\CreateCalendarController@edit')->name('create-calender-edit');
        Route::post('/create-calender', 'Training\CreateCalendarController@post')->name('create-calender-post');
        Route::put('/create-calender/{id}', 'Training\CreateCalendarController@update')->name('create-calender-update');
        Route::post('/create-calender-datatable', 'Training\CreateCalendarController@dataTableList')->name('create-calender-datatable');
    });

    Route::group(['name' => 'trainee-attendance', 'as' => 'trainee-attendance.'], function () {
        Route::get('/trainee-attendance', 'Training\TraineeAttendanceController@index')->name('trainee-attendance-index');
        Route::post('/trainee-attendance', 'Training\TraineeAttendanceController@post')->name('trainee-attendance-post');
        Route::post('/trainee-attendance-datatable-list', 'Training\TraineeAttendanceController@dataTableList')->name('trainee-attendance-datatable-list');
    });

    Route::group(['name' => 'trainee-attendance-update', 'as' => 'trainee-attendance-update.'], function () {
        Route::get('/trainee-attendance-update', 'Training\TraineeAttendanceUpdateController@index')->name('trainee-attendance-update-index');
        Route::post('/trainee-attendance-update', 'Training\TraineeAttendanceUpdateController@post')->name('trainee-attendance-update-post');
        Route::post('/trainee-attendance-update-datatable-list', 'Training\TraineeAttendanceUpdateController@dataTableList')->name('trainee-attendance-update-datatable-list');
    });

    Route::group(['name' => 'training-re-schedule', 'as' => 'training-re-schedule.'], function () {
        Route::get('/training-re-schedule', 'Training\TrainingReScheduleController@index')->name('training-re-schedule-index');
        Route::post('/training-re-schedule', 'Training\TrainingReScheduleController@post')->name('training-re-schedule-post');
        Route::get('/get-schedule-data', 'Training\TrainingReScheduleController@scheduleData')->name('get-schedule-data');
        Route::post('/get-schedule-dtl-data', 'Training\TrainingReScheduleController@scheduleDtlData')->name('get-schedule-dtl-data');
        Route::post('/get-reschedule-dtl-data', 'Training\TrainingReScheduleController@rescheduleDtlData')->name('get-reschedule-dtl-data');
    });

    Route::group(['name' => 'assign-dept', 'as' => 'assign-dept.'], function () {
        Route::get('/assign-dept', 'Training\AssignDeptController@index')->name('assign-dept-index');
        Route::get('/assign-dept/{id}', 'Training\AssignDeptController@edit')->name('assign-dept-edit');
        Route::post('/assign-dept', 'Training\AssignDeptController@post')->name('assign-dept-post');
        Route::put('/assign-dept/{id}', 'Training\AssignDeptController@update')->name('assign-dept-update');

        Route::get('/assign-dept-data-remove', 'Training\AssignDeptController@removeDetailData')->name('assign-dept-data-remove');
        Route::post('/assign-dept-datatable-list', 'Training\AssignDeptController@dataTableList')->name('assign-dept-datatable-list');
    });

    Route::group(['name' => 'assign-trainee', 'as' => 'assign-trainee.'], function () {
        Route::get('/assign-trainee', 'Training\AssignTraineeController@index')->name('assign-trainee-index');
        Route::post('/assign-trainee-datatable-list', 'Training\AssignTraineeController@dataTableList')->name('assign-trainee-datatable-list');
        Route::get('/assign-trainee/{id}', 'Training\AssignTraineeController@edit')->name('assign-trainee-edit');
        //Route::post('/assign-trainee', 'Training\AssignTraineeController@post')->name('assign-trainee-post');
        Route::get('/check-dept-capacity', 'Training\AssignTraineeController@chkDepCapacity')->name('check-dept-capacity');
        Route::get('/assign-trainee-data-remove', 'Training\AssignTraineeController@removeDetailData')->name('assign-trainee-data-remove');
        Route::put('/assign-trainee/{id}', 'Training\AssignTraineeController@update')->name('assign-trainee-update');
    });

    Route::group(['name' => 'assign-trainee-update', 'as' => 'assign-trainee-update.'], function () {
        Route::get('/assign-trainee-update', 'Training\AssignTraineeUpdateController@index')->name('assign-trainee-update-index');
        Route::post('/assign-trainee-update-datatable-list', 'Training\AssignTraineeUpdateController@dataTableList')->name('assign-trainee-update-datatable-list');
        Route::get('/assign-trainee-update/{id}', 'Training\AssignTraineeUpdateController@edit')->name('assign-trainee-update-edit');
        //Route::post('/assign-trainee', 'Training\AssignTraineeUpdateController@post')->name('assign-trainee-post');
        Route::get('/assign-trainee-update-data-remove', 'Training\AssignTraineeUpdateController@removeDetailData')->name('assign-trainee-update-data-remove');
        Route::put('/assign-trainee-update/{id}', 'Training\AssignTraineeUpdateController@update')->name('assign-trainee-update-update');
    });

    Route::group(['name' => 'bill-preparation', 'as' => 'bill-preparation.'], function () {
        Route::get('/bill-preparation', 'Training\TraineeBillPreparationController@index')->name('bill-preparation-index');
        Route::post('/bill-preparation-datatable', 'Training\TraineeBillPreparationController@datatable')->name('bill-preparation-datatable');
        Route::post('/bill-preparation', 'Training\TraineeBillPreparationController@post')->name('bill-preparation-post');
    });

    Route::group(['name' => 'trainer-bill-preparation', 'as' => 'trainer-bill-preparation.'], function () {
        Route::get('/trainer-bill-preparation', 'Training\TrainerBillPreparationController@index')->name('trainer-bill-preparation-index');
        Route::post('/trainer-bill-preparation-datatable', 'Training\TrainerBillPreparationController@datatable')->name('trainer-bill-preparation-datatable');
        Route::post('/trainer-bill-preparation', 'Training\TrainerBillPreparationController@post')->name('trainer-bill-preparation-post');
    });

    Route::group(['name' => 'sstuff-bill-preparation', 'as' => 'sstuff-bill-preparation.'], function () {
        Route::get('/sstuff-bill-preparation', 'Training\SstuffBillPreparationController@index')->name('sstuff-bill-preparation-index');
        Route::post('/sstuff-bill-preparation-datatable', 'Training\SstuffBillPreparationController@datatable')->name('sstuff-bill-preparation-datatable');
        Route::post('/sstuff-bill-preparation', 'Training\SstuffBillPreparationController@post')->name('sstuff-bill-preparation-post');
    });

    Route::group(['name' => 'bill-preparation-update', 'as' => 'bill-preparation-update.'], function () {
        Route::get('/bill-preparation-update', 'Training\TraineeBillPreparationUpdateController@index')->name('bill-preparation-update-index');
        Route::post('/bill-preparation-update-datatable', 'Training\TraineeBillPreparationUpdateController@datatable')->name('bill-preparation-update-datatable');
        Route::post('/bill-preparation-update', 'Training\TraineeBillPreparationUpdateController@post')->name('bill-preparation-update-post');
    });

});

