<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */


header("Access-Control-Allow-Origin: *");

View::addExtension('html', 'php');

Route::get('/', function() {
    return View::make('index');
});

use Illuminate\Support\Facades\Input;
use App\Subject;
use App\Theme;

Route::get('/information/create/ajax-grade-subject', function() {
    $grade_id = Input::get('grade_id');
    $subjects_selected = DB::table('grades_subjects')
            ->where('grades_subjects.grade_id', '=', $grade_id)
            ->orderby('name', 'ASC')
            ->lists('subject_id');
    $subjects = Subject::whereIn('id', $subjects_selected)
            ->orderby('name','ASC')->get();

    return $subjects;
});

Route::get('/information/create/ajax-grade-subject-theme', function() {
    $grade_id = Input::get('grade_id');
    $subject_id = Input::get('subject_id');
    $themes_selected = DB::table('grades_subjects_themes')
            ->where('grades_subjects_themes.grade_id', '=', $grade_id)
            ->where('grades_subjects_themes.subject_id', '=', $subject_id)
            ->lists('theme_id');
    $themes = Theme::whereIn('id', $themes_selected)
                    ->orderby('name', 'ASC')->get();

    return $themes;
});

/**
 * Admin routes
 */
Route::auth();
Route::get('/admin', 'Auth\AuthController@login');
Route::get('/LessonUser/createTempPdfData', 'Api\ApiLessonUserController@createTempPdfData');

Route::get('/lessons/pdf/{id}', 'HomeController@index');
Route::get('/pdf/invoice/{id}', 'HomeController@invoice');
Route::get('/pdf/download/{id}', 'Api\ApiPaypalPaymentController@download_invoice');
Route::post('/excute', 'Admin\DashboardController@executeProcess');

Route::get('/checkLastStatus', 'HomeController@checkLastStatus');

Route::group(array('prefix' => 'admin', 'middlewareGroups' => 'web', 'before' => 'auth'), function() {

    Route::auth();
    Route::get('/', function() {
        return Redirect::to("admin/login");
    });

    //Change password
    Route::get('/changepassword', 'Admin\ChangePasswordController@index');
    Route::post('/changepassword/update', 'Admin\ChangePasswordController@update');

    Route::resource('dashboard', 'Admin\DashboardController');
    Route::post('/dashboard/csvUpload', 'Admin\DashboardController@csvUpload');

    Route::get('/country/getData', 'Admin\CountryController@getData');
    Route::resource('country', 'Admin\CountryController');

    //Route::get('/lessons/{id}/showContent', 'Admin\LessonsController@showContent');
    Route::get('/lessons/{id}/lessonsContent', 'Admin\LessonsController@lessonsContent');
    Route::get('/lessons/userLessons/{id}', 'Admin\LessonsController@userLessons');
    Route::get('/lessons/getData', 'Admin\LessonsController@getData');
    Route::resource('lessons', 'Admin\LessonsController');

    Route::get('/grades/getData', 'Admin\GradesController@getData');
    Route::resource('grades', 'Admin\GradesController');

    Route::get('/subjects/getData', 'Admin\SubjectsController@getData');
    Route::resource('subjects', 'Admin\SubjectsController');

    Route::get('/themes/getData', 'Admin\ThemesController@getData');
    Route::resource('themes', 'Admin\ThemesController');

    Route::get('/keyconcepts/getData', 'Admin\KeyConceptsController@getData');
    Route::resource('keyconcepts', 'Admin\KeyConceptsController');

    Route::get('/learningtargets/getData', 'Admin\LearningTargetsController@getData');
    Route::resource('learningtargets', 'Admin\LearningTargetsController');

    Route::get('/learningtargetsname/getData', 'Admin\LearningtargetsNameController@getData');
    Route::resource('learningtargetsname', 'Admin\LearningtargetsNameController');
    
    Route::post('/learningtargets/getSubjectsByGrade', 'Admin\LearningTargetsController@getSubjectsByGrade');
    Route::post('/learningtargets/getThemesByGradeAndSubjects', 'Admin\LearningTargetsController@getThemesByGradeAndSubjects');
    Route::post('/learningtargets/getKeyconceptByTheme', 'Admin\LearningTargetsController@getKeyconceptByTheme');

    Route::get('/user/getMyPlans/{id}', 'Admin\UserController@getMyPlans');
    Route::get('/user/viewPlans/{id}', 'Admin\UserController@viewPlans');
    Route::get('/user/getData', 'Admin\UserController@getData');
    Route::post('/user/changeAgreementStatus', 'Admin\UserController@changeAgreementStatus');
    Route::resource('user', 'Admin\UserController');

    Route::get('/paymentplan/getData', 'Admin\PaymentPlanController@getData');
    Route::get('/paymentplan/getExpiredSubscriptions', 'Admin\PaymentPlanController@getExpiredSubscriptions');
    Route::resource('paymentplan', 'Admin\PaymentPlanController');

    Route::get('/cms/getData', 'Admin\CmsController@getData');
    Route::resource('cms', 'Admin\CmsController');

    Route::get('/emailtemplates/getData', 'Admin\EmailTemplatesController@getData');
    Route::resource('emailtemplates', 'Admin\EmailTemplatesController');

    Route::get('/educationalquotes/getData', 'Admin\EducationalQuotesController@getData');
    Route::resource('educationalquotes', 'Admin\EducationalQuotesController');

    Route::get('/language/getData', 'Admin\GlobelController@getData');
    Route::get('/language/refresh', 'Admin\GlobelController@refresh');
    Route::resource('language', 'Admin\GlobelController');

    Route::get('/grades/delete/{id}', 'GradesController@delete');
});

/**
 * For API
 */
    Route::group(['prefix' => 'api', 'middleware' => 'api', 'before' => 'auth'], function() {
    Route::auth();
    Route::post('logout', 'Api\ApiUserController@logout');
    Route::post('user/forgotPassword', 'Api\ApiUserController@getForgotPassword');
    Route::post('user/passwordReset', 'Api\ApiUserController@getPasswordReset');
    Route::post('user/changePassword', 'Api\ApiUserController@changePassword');
    Route::post('user/myProfile', 'Api\ApiUserController@myProfile');
    Route::post('user/updateProfile', 'Api\ApiUserController@updateProfile');
    Route::post('user/checkpayment', 'Api\ApiPaypalPaymentController@getPaymentStatus');
    Route::get('/user/getFormToken', 'Api\ApiController@getFormToken');
    Route::get('/user/getByToken', 'Api\ApiController@getByToken');
    Route::post('/user', 'Api\ApiUserController@users');
    Route::post('/user/getSubscribedUserData', 'Api\ApiUserController@getSubscribedUserData');

    // User Module For User [Login, Register, Logout, Forgotpassword]
    Route::post('/user/login', 'Api\ApiUserController@login');
    Route::post('/user/register', 'Api\ApiUserController@register');
    Route::get('/user/verification/{number}/{id}', 'Api\ApiUserController@verification');

    //grade
    Route::post('/grade/getAllGrades', 'Api\ApiGradeController@getAllGrades');

    //country
    Route::post('/country/getAllCountries', 'Api\ApiCountryController@getAllCountries');

    //Subjects
    Route::post('/subjects/getAllSubjects', 'Api\ApiSubjectsController@getAllSubjects');
    Route::post('/subjects/getSubjectsByGrade', 'Api\ApiSubjectsController@getSubjectsByGrade');

    //Theme
    Route::post('/theme/getAllThemes', 'Api\ApiThemeController@getAllThemes');
    Route::post('/theme/getThemesByGradeAndSubjects', 'Api\ApiThemeController@getThemesByGradeAndSubjects');

    //Lesson
    Route::post('/lesson/create', 'Api\ApiLessonController@create');
    Route::post('/lesson/getAllLessons', 'Api\ApiLessonController@getAllLessons');
    Route::post('/lesson/getLessonDetails', 'Api\ApiLessonController@getLessonDetails');
    Route::post('/lesson/getLearningTargetDetails', 'Api\ApiLessonController@getLearningTargetDetails');
    Route::post('/lesson/getFixedcontent', 'Api\ApiLessonController@getFixedcontent');
    Route::post('/lesson/getMyAllLessons', 'Api\ApiLessonController@getMyAllLessons');
    Route::post('/lesson/savesummary', 'Api\ApiLessonController@saveSummary');
    Route::post('/lesson/savestandrad', 'Api\ApiLessonController@saveStandrad');
    Route::post('/lesson/saveessential', 'Api\ApiLessonController@saveEssential');
    Route::post('/lesson/savecoreideas', 'Api\ApiLessonController@saveCoreIdeas');
    Route::post('/lesson/savevocabulary', 'Api\ApiLessonController@saveVocabulary');
    Route::post('/lesson/savesequence', 'Api\ApiLessonController@savesequence');
    Route::post('/lesson/getLessonsUserData', 'Api\ApiLessonController@getLessonsUserData');

    //LearningTarget
    Route::post('/learningtarget/getAllLearningTargets', 'Api\ApiLearningTargetController@getAllLearningTargets');
    Route::post('/learningtarget/getLearningTargetById', 'Api\ApiLearningTargetController@getLearningTargetById');
    Route::post('/learningtarget/getLearningTargetByUser', 'Api\ApiLearningTargetController@getLearningTargetByUser');
    Route::post('/learningtarget/getEditorContent', 'Api\ApiLearningTargetController@getEditorContent');
    Route::post('/learningtarget/modification', 'Api\ApiLearningTargetController@getModificationContent');

    //UserLesson
    Route::post('/LessonUser/createLessonByUser', 'Api\ApiLessonUserController@createLessonByUser');
    Route::post('/LessonUser/savemodification', 'Api\ApiLessonUserController@saveModificationTab');
    Route::post('/LessonUser/getUserlesson', 'Api\ApiLessonUserController@getUserlesson');
    Route::post('/LessonUser/createPDF', 'Api\ApiLessonUserController@createPDF');
    Route::post('/LessonUser/createTempPdfData', 'Api\ApiLessonUserController@createTempPdfData');


    /************************* ** Lesson Sequence ****************************/
    Route::post('/bloom/getAllBlooms', 'Api\ApiBloomController@getAllBlooms');
    Route::post('/webb/getAllWebbs', 'Api\ApiWebbController@getAllWebbs');
    Route::post('/webb/getWebbByBloom', 'Api\ApiWebbController@getWebbByBloom');
    Route::post('/activity/getAllActivities', 'Api\ApiActivityController@getAllActivities');
    Route::post('/activity/getActivitiesByBloomAndWebb', 'Api\ApiActivityController@getActivitiesByBloomAndWebb');
    Route::post('/delivery/getAllDeliveries', 'Api\ApiLessonDeliveryController@getAllDeliveries');
    Route::post('/delivery/getDeliveriesByBloomWebbActivity', 'Api\ApiLessonDeliveryController@getDeliveriesByBloomWebbActivity');
    Route::post('/standard/getAllBeyonds', 'Api\ApiBeyondStandardController@getAllBeyonds');
    Route::post('/standard/getStandardsByDelivery', 'Api\ApiBeyondStandardController@getStandardsByDelivery');
    Route::post('/modification/getAllModifications', 'Api\ApiModificationController@getAllModifications');
    Route::post('/modification/getModificationsByStandards', 'Api\ApiModificationController@getModificationsByStandards');

    /************************* ** Lesson Sequence ****************************/

    // Route::post('/LessonUser/getTempPdf', 'Api\ApiLessonUserController@getTempPdf');
    Route::post('/lesson/savesequence', 'Api\ApiLessonController@saveSequence');
    Route::post('/LessonUser/saveTempPdfData', 'Api\ApiLessonUserController@saveTempPdfData');
    Route::post('/LessonUser/deleteUserlesson', 'Api\ApiLessonUserController@deleteUserlesson');

    // Cms
    Route::post('/cms/getAllCms', 'Api\ApiCmsController@getAllCms');

    //Educational Quotes
    Route::post('/educationalquotes/getAllEducationalQuotes', 'Api\ApiEducationalQuotesController@getAllEducationalQuotes');

    //Paypal
    Route::get('/paypalpayment/getData', 'Api\ApiPaypalPaymentController@getData');
    Route::post('/paypalpayment/store', 'Api\ApiPaypalPaymentController@store');
    // Route::resource('payments', 'PaypalPaymentController');
    // Route::resource('payments', 'StripePaymentController');

    // Stripe
    Route::post('/store', 'StripePaymentController@store');

    //Stripe
    Route::get('/subscribe', 'ApiStripePaymentController@getData');
    Route::post('/subscribe', 'ApiStripePaymentController@store');
    // Route::post('/checkout', 'ApiStripePaymentController@createCheckoutSession');
    // Route::get('getData', 'ApiStripePaymentController@getData');
    // Route::post('stripeCharge', 'ApiStripePaymentController@store');
    // Route::get('stripe', 'StripePaymentController@stripe');
    // Route::post('/charge', 'StripePaymentController@charge');
    // Route::post('stripe', 'StripePaymentController@stripePost')->name('stripe.post');

    //Stripe Payment Plans
    Route::post('/getAllPlans', 'StripePaymentController@getAllPlans');
    Route::post('/getPlanDetails', 'StripePaymentController@getPlanDetails');
    Route::post('/createCheckoutSession', 'StripePaymentController@createCheckoutSession');
    // Route::post('/getAllPlans', 'Api\ApiStripePaymentController@getAllPlans');
    

    //Payment Plan
    Route::post('/paypalpayment/getAllPlans', 'Api\ApiPaypalPaymentController@getAllPlans');
    Route::post('/paypalpayment/getPlanDetails', 'Api\ApiPaypalPaymentController@getPlanDetails');
    Route::post('/users/getMyAllPayments', 'Api\ApiUserController@getMyAllPayments');
    Route::post('/paypalpayment/changeRecurringStatus', 'Api\ApiPaypalPaymentController@changeRecurringStatus');
});
