<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Auth::login');

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('authenticate', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');
$routes->get('change-password', 'Auth::changePassword', ['filter' => 'auth']);
$routes->post('update-password', 'Auth::updatePassword', ['filter' => 'auth']);

// Admin Routes
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Organization Management
    $routes->get('organizations', 'Admin\Organizations::index');
    $routes->get('organizations/create', 'Admin\Organizations::create');
    $routes->post('organizations/store', 'Admin\Organizations::store');
    $routes->get('organizations/edit/(:num)', 'Admin\Organizations::edit/$1');
    $routes->post('organizations/update/(:num)', 'Admin\Organizations::update/$1');
    $routes->get('organizations/delete/(:num)', 'Admin\Organizations::delete/$1');
    $routes->get('organizations/view/(:num)', 'Admin\Organizations::view/$1');
    $routes->post('organizations/checklist/(:num)', 'Admin\Organizations::updateChecklist/$1');
    $routes->get('organizations/print', 'Admin\Organizations::printOrgList');

    // Document Management
    $routes->get('documents', 'Admin\Documents::index');
    $routes->get('documents/view/(:num)', 'Admin\Documents::view/$1');
    $routes->get('documents/preview/(:num)', 'Admin\Documents::preview/$1');
    $routes->get('documents/download/(:num)', 'Admin\Documents::download/$1');
    $routes->post('documents/comment/(:num)', 'Admin\Documents::addComment/$1');
    $routes->post('documents/status/(:num)', 'Admin\Documents::updateStatus/$1');
    $routes->get('documents/print', 'Admin\Documents::printDocs');

    // Commitment Forms
    $routes->get('commitment-forms', 'Admin\CommitmentForms::index');
    $routes->get('commitment-forms/download/(:num)', 'Admin\CommitmentForms::download/$1');
    $routes->get('commitment-forms/print/(:num)', 'Admin\CommitmentForms::print/$1');

    // Org Records (Download/Print)
    $routes->get('calendar-activities/download/(:num)/(:segment)', 'Admin\CalendarActivities::download/$1/$2');
    $routes->get('calendar-activities/print/(:num)/(:segment)', 'Admin\CalendarActivities::print/$1/$2');
    $routes->get('program-expenditures/download/(:num)/(:segment)', 'Admin\ProgramExpenditures::download/$1/$2');
    $routes->get('program-expenditures/print/(:num)/(:segment)', 'Admin\ProgramExpenditures::print/$1/$2');
    $routes->get('financial-reports/download/(:num)', 'Admin\FinancialReports::download/$1');
    $routes->get('financial-reports/print/(:num)', 'Admin\FinancialReports::print/$1');
    $routes->get('accomplishment-reports/download/(:num)', 'Admin\AccomplishmentReports::download/$1');
    $routes->get('accomplishment-reports/print/(:num)', 'Admin\AccomplishmentReports::print/$1');

    // Statistics
    $routes->get('statistics', 'Admin\Statistics::index');
    $routes->get('statistics/organization/(:num)', 'Admin\Statistics::organizationView/$1');
    $routes->post('statistics/comparison', 'Admin\Statistics::comparison');

    // Certificates
    $routes->get('organizations/certificate/(:num)/(:segment)', 'Admin\Organizations::certificate/$1/$2');
    $routes->get('organizations/certificate/print/(:num)/(:segment)', 'Admin\Organizations::printCertificate/$1/$2');
});

// Organization Routes
$routes->group('organization', ['filter' => 'organization'], function ($routes) {
    $routes->get('dashboard', 'Organization\Dashboard::index');

    // Accreditation
    $routes->get('accreditation', 'Organization\Accreditation::index');
    $routes->get('accreditation/download/(:segment)', 'Organization\Accreditation::download/$1');
    $routes->get('accreditation/print/(:segment)', 'Organization\Accreditation::print/$1');

    // Commitment Form
    $routes->get('commitment-form', 'Organization\CommitmentForm::index');
    $routes->get('commitment-form/create', 'Organization\CommitmentForm::create');
    $routes->post('commitment-form/store', 'Organization\CommitmentForm::store');
    $routes->post('commitment-form/update/(:num)', 'Organization\CommitmentForm::update/$1');
    $routes->get('commitment-form/download/(:num)', 'Organization\CommitmentForm::download/$1');
    $routes->get('commitment-form/print/(:num)', 'Organization\CommitmentForm::print/$1');
    $routes->get('commitment-form/print-list', 'Organization\CommitmentForm::printList');
    $routes->post('commitment-form/reorder', 'Organization\CommitmentForm::reorder');

    // Calendar of Activities
    $routes->get('calendar-activities', 'Organization\CalendarActivities::index');
    $routes->post('calendar-activities/store', 'Organization\CalendarActivities::store');
    $routes->post('calendar-activities/update/(:num)', 'Organization\CalendarActivities::update/$1');
    $routes->get('calendar-activities/delete/(:num)', 'Organization\CalendarActivities::delete/$1');
    $routes->get('calendar-activities/download/(:segment)', 'Organization\CalendarActivities::download/$1');
    $routes->get('calendar-activities/print/(:segment)', 'Organization\CalendarActivities::print/$1');

    // Program of Expenditures
    $routes->get('program-expenditure', 'Organization\ProgramExpenditure::index');
    $routes->post('program-expenditure/store', 'Organization\ProgramExpenditure::store');
    $routes->post('program-expenditure/update/(:num)', 'Organization\ProgramExpenditure::update/$1');
    $routes->get('program-expenditure/delete/(:num)', 'Organization\ProgramExpenditure::delete/$1');
    $routes->get('program-expenditure/download/(:segment)', 'Organization\ProgramExpenditure::download/$1');
    $routes->get('program-expenditure/print/(:segment)', 'Organization\ProgramExpenditure::print/$1');

    // Accomplishment Reports
    $routes->get('accomplishment-report', 'Organization\AccomplishmentReport::index');
    $routes->get('accomplishment-report/create', 'Organization\AccomplishmentReport::create');
    $routes->post('accomplishment-report/store', 'Organization\AccomplishmentReport::store');
    $routes->get('accomplishment-report/edit/(:num)', 'Organization\AccomplishmentReport::edit/$1');
    $routes->post('accomplishment-report/update/(:num)', 'Organization\AccomplishmentReport::update/$1');
    $routes->get('accomplishment-report/download/(:num)', 'Organization\AccomplishmentReport::download/$1');
    $routes->get('accomplishment-report/print/(:num)', 'Organization\AccomplishmentReport::print/$1');

    // Financial Reports
    $routes->get('financial-report', 'Organization\FinancialReport::index');
    $routes->get('financial-report/get/(:segment)', 'Organization\FinancialReport::getReport/$1');
    $routes->post('financial-report/store', 'Organization\FinancialReport::store');
    $routes->get('financial-report/tracking', 'Organization\FinancialReport::tracking');
    $routes->post('financial-report/comparison', 'Organization\FinancialReport::comparison');
    $routes->get('financial-report/download/(:num)', 'Organization\FinancialReport::download/$1');
    $routes->get('financial-report/print/(:num)', 'Organization\FinancialReport::print/$1');

    // Document Submissions
    $routes->get('submissions', 'Organization\DocumentSubmission::index');
    $routes->get('submissions/view/(:num)', 'Organization\DocumentSubmission::view/$1');
    $routes->get('submissions/upload', 'Organization\DocumentSubmission::uploadForm');
    $routes->post('submissions/upload', 'Organization\DocumentSubmission::upload');
    $routes->get('submissions/download/(:num)', 'Organization\DocumentSubmission::download/$1');
    $routes->get('submissions/delete/(:num)', 'Organization\DocumentSubmission::delete/$1');

    // Notifications
    $routes->get('notifications', 'Organization\Notifications::index');
    $routes->post('notifications/mark-read/(:num)', 'Organization\Notifications::markAsRead/$1');
    $routes->post('notifications/mark-all-read', 'Organization\Notifications::markAllAsRead');
});