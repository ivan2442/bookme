<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pre-prevadzky', [HomeController::class, 'forBusinesses'])->name('for-businesses');
Route::get('/clanky', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/clanky/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/prevadzka/{slug}', [HomeController::class, 'showProfile'])->name('profiles.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.submit');
Route::post('/register-business', [AuthController::class, 'registerBusiness'])->name('auth.register.business');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/services', [AdminController::class, 'services'])->name('services');
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
    Route::post('/services/{service}', [AdminController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('services.delete');
    Route::post('/services/{service}/variants', [AdminController::class, 'storeVariant'])->name('services.variants.store');
    Route::post('/services/{service}/variants/{variant}', [AdminController::class, 'updateVariant'])->name('services.variants.update');
    Route::delete('/services/{service}/variants/{variant}', [AdminController::class, 'deleteVariant'])->name('services.variants.delete');

    Route::get('/employees', [AdminController::class, 'employees'])->name('employees');
    Route::post('/employees', [AdminController::class, 'storeEmployee'])->name('employees.store');
    Route::post('/employees/{employee}', [AdminController::class, 'updateEmployee'])->name('employees.update');

    Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
    Route::post('/appointments/{appointment}/confirm', [AdminController::class, 'confirmAppointment'])->name('appointments.confirm');
    Route::delete('/appointments/{appointment}', [AdminController::class, 'deleteAppointment'])->name('appointments.delete');

    Route::get('/profiles', [AdminController::class, 'profiles'])->name('profiles');
    Route::post('/profiles', [AdminController::class, 'storeProfile'])->name('profiles.store');
    Route::post('/profiles/{profile}', [AdminController::class, 'updateProfile'])->name('profiles.update');
    Route::delete('/profiles/{profile}', [AdminController::class, 'deleteProfile'])->name('profiles.delete');
    Route::post('/profiles/{profile}/publish', [AdminController::class, 'publishProfile'])->name('profiles.publish');

    Route::get('/schedules', [AdminController::class, 'schedules'])->name('schedules');
    Route::post('/schedules', [AdminController::class, 'storeSchedule'])->name('schedules.store');
    Route::delete('/schedules/{schedule}', [AdminController::class, 'deleteSchedule'])->name('schedules.delete');

    Route::get('/calendar-settings', [AdminController::class, 'calendarSettings'])->name('calendar.settings');
    Route::post('/calendar-settings', [AdminController::class, 'storeCalendarSettings'])->name('calendar.settings.store');

    Route::get('/holidays', [AdminController::class, 'holidays'])->name('holidays');
    Route::post('/holidays', [AdminController::class, 'storeHoliday'])->name('holidays.store');
    Route::post('/holidays/{holiday}', [AdminController::class, 'updateHoliday'])->name('holidays.update');
    Route::delete('/holidays/{holiday}', [AdminController::class, 'deleteHoliday'])->name('holidays.delete');

    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::get('/invoices', [AdminController::class, 'invoices'])->name('invoices');
    Route::post('/invoices', [AdminController::class, 'storeInvoice'])->name('invoices.store');
    Route::post('/invoices/{invoice}/status', [AdminController::class, 'updateInvoiceStatus'])->name('invoices.status.update');
    Route::delete('/invoices/{invoice}', [AdminController::class, 'deleteInvoice'])->name('invoices.delete');
    Route::get('/invoices/{invoice}/preview', [AdminController::class, 'previewInvoice'])->name('invoices.preview');
    Route::post('/invoices/{invoice}/send', [AdminController::class, 'sendInvoice'])->name('invoices.send');

    Route::get('/billing-settings', [AdminController::class, 'billingSettings'])->name('billing.settings');
    Route::post('/billing-settings', [AdminController::class, 'storeBillingSettings'])->name('billing.settings.store');

    Route::get('/api-settings', [AdminController::class, 'apiSettings'])->name('api.settings');
    Route::post('/api-settings', [AdminController::class, 'updateApiSettings'])->name('api.settings.update');
});

Route::prefix('owner')->name('owner.')->middleware('owner')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

    // Služby
    Route::get('/services', [OwnerDashboardController::class, 'services'])->name('services');
    Route::post('/services', [OwnerDashboardController::class, 'storeService'])->name('services.store');
    Route::post('/services/{service}', [OwnerDashboardController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [OwnerDashboardController::class, 'deleteService'])->name('services.delete');
    Route::post('/services/{service}/variants', [OwnerDashboardController::class, 'storeVariant'])->name('services.variants.store');
    Route::post('/services/{service}/variants/{variant}', [OwnerDashboardController::class, 'updateVariant'])->name('services.variants.update');
    Route::delete('/services/{service}/variants/{variant}', [OwnerDashboardController::class, 'deleteVariant'])->name('services.variants.delete');

    // Zamestnanci
    Route::get('/employees', [OwnerDashboardController::class, 'employees'])->name('employees');
    Route::post('/employees', [OwnerDashboardController::class, 'storeEmployee'])->name('employees.store');
    Route::post('/employees/{employee}', [OwnerDashboardController::class, 'updateEmployee'])->name('employees.update');

    // Rezervácie
    Route::get('/appointments', [OwnerDashboardController::class, 'appointments'])->name('appointments');
    Route::get('/appointments/day', [OwnerDashboardController::class, 'getAppointmentsForDay'])->name('appointments.day');
    Route::get('/appointments/day-full', [OwnerDashboardController::class, 'getAppointmentsForDayFull'])->name('appointments.day-full');
    Route::get('/appointments/free-slots', [OwnerDashboardController::class, 'getFreeSlots'])->name('appointments.free-slots');
    Route::get('/appointments/calendar-status', [OwnerDashboardController::class, 'getCalendarStatus'])->name('appointments.calendar-status');
    Route::post('/appointments', [OwnerDashboardController::class, 'storeManualAppointment'])->name('appointments.manual.store');
    Route::post('/appointments/{appointment}/confirm', [OwnerDashboardController::class, 'confirmAppointment'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/status', [OwnerDashboardController::class, 'updateAppointmentStatus'])->name('appointments.status.update');
    Route::post('/appointments/{appointment}/update', [OwnerDashboardController::class, 'updateAppointment'])->name('appointments.update');
    Route::post('/appointments/{appointment}/reschedule', [OwnerDashboardController::class, 'rescheduleAppointment'])->name('appointments.reschedule');
    Route::delete('/appointments/{appointment}', [OwnerDashboardController::class, 'deleteAppointment'])->name('appointments.delete');

    // Rozvrhy
    Route::get('/schedules', [OwnerDashboardController::class, 'schedules'])->name('schedules');
    Route::post('/schedules', [OwnerDashboardController::class, 'storeSchedule'])->name('schedules.store');
    Route::post('/schedules/{schedule}', [OwnerDashboardController::class, 'updateSchedule'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [OwnerDashboardController::class, 'deleteSchedule'])->name('schedules.delete');

    // Nastavenia kalendára
    Route::get('/calendar-settings', [OwnerDashboardController::class, 'calendarSettings'])->name('calendar.settings');
    Route::post('/calendar-settings', [OwnerDashboardController::class, 'storeCalendarSettings'])->name('calendar.settings.store');

    // Sviatky a uzávierky
    Route::get('/holidays', [OwnerDashboardController::class, 'holidays'])->name('holidays');
    Route::post('/holidays', [OwnerDashboardController::class, 'storeHoliday'])->name('holidays.store');
    Route::post('/holidays/{holiday}', [OwnerDashboardController::class, 'updateHoliday'])->name('holidays.update');
    Route::delete('/holidays/{holiday}', [OwnerDashboardController::class, 'deleteHoliday'])->name('holidays.delete');

    // Platby (zatial len prehlad)
    Route::get('/payments', [OwnerDashboardController::class, 'payments'])->name('payments');

    // Faktúry
    Route::get('/invoices', [OwnerDashboardController::class, 'invoices'])->name('invoices');
    Route::get('/invoices/{invoice}/preview', [OwnerDashboardController::class, 'previewInvoice'])->name('invoices.preview');

    // Fakturačné údaje
    Route::get('/billing-settings', [OwnerDashboardController::class, 'billingSettings'])->name('billing.settings');
    Route::post('/billing-settings', [OwnerDashboardController::class, 'storeBillingSettings'])->name('billing.settings.store');
});
