<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FacilityClosureController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

// ─── Public Welcome ────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Dashboard ─────────────────────────────────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Facilities ────────────────────────────────────────────────────────────
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');

Route::get('/facilities/create', [FacilityController::class, 'create'])
    ->middleware(['auth', 'role:rental_officer'])
    ->name('facilities.create');

Route::post('/facilities', [FacilityController::class, 'store'])
    ->middleware(['auth', 'role:rental_officer'])
    ->name('facilities.store');

Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])
    ->middleware(['auth', 'role:rental_officer'])
    ->name('facilities.edit');

Route::put('/facilities/{facility}', [FacilityController::class, 'update'])
    ->middleware(['auth', 'role:rental_officer'])
    ->name('facilities.update');

Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])
    ->middleware(['auth', 'role:rental_officer'])
    ->name('facilities.destroy');

Route::get('/facilities/{facility}/slots', [BookingController::class, 'availableSlots'])
    ->middleware('auth')
    ->name('facilities.slots');

// ─── Facility Closures (Rental Officer) ───────────────────────────────────
Route::middleware(['auth', 'role:rental_officer'])->group(function () {
    Route::get('/facilities/{facility}/closures',
        [FacilityClosureController::class, 'index'])->name('facilities.closures.index');

    Route::post('/facilities/{facility}/closures',
        [FacilityClosureController::class, 'store'])->name('facilities.closures.store');

    Route::delete('/facilities/{facility}/closures/date',
        [FacilityClosureController::class, 'destroyDate'])->name('facilities.closures.destroy-date');

    Route::delete('/facilities/{facility}/closures/{closure}',
        [FacilityClosureController::class, 'destroy'])->name('facilities.closures.destroy');
});

// ─── Bookings ─────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/facilities/{facility}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/facilities/{facility}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my');
    Route::post('/bookings/{booking}/request-cancel', [BookingController::class, 'requestCancel'])->name('bookings.request-cancel');
    Route::get('/bookings/{booking}/slip', [BookingController::class, 'slip'])->name('bookings.slip');
});

// ─── Rental Officer: Booking Management ───────────────────────────────────
Route::middleware(['auth', 'role:rental_officer'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'allBookings'])->name('bookings.all');
    Route::post('/bookings/{booking}/approve-cancel', [BookingController::class, 'approveCancel'])->name('bookings.approve-cancel');
    Route::post('/bookings/{booking}/reject-cancel', [BookingController::class, 'rejectCancel'])->name('bookings.reject-cancel');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancelByOfficer'])->name('bookings.cancel');
});

// ─── Payment (authenticated) ───────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/{booking}/prepare',  [PaymentController::class, 'prepare'])->name('payment.prepare');
    Route::get('/payment/{booking}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success',            [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/{booking}/cancel',   [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::get('/payment/{booking}/receipt',  [PaymentController::class, 'receipt'])->name('payment.receipt');
});

// ─── Stripe Webhook (no auth, no CSRF) ────────────────────────────────────
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])
    ->name('payment.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);


// ─── Profile ──────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/promote', [UserManagementController::class, 'promote'])->name('users.promote');
    Route::post('/users/{user}/demote', [UserManagementController::class, 'demote'])->name('users.demote');
});

// ─── Feedback (Guest / Staff / Student) ───────────────────────────────────
Route::middleware(['auth', 'role:guest,staff,student'])->group(function () {
    Route::get('/feedback/{bookingGroupId}/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback/{bookingGroupId}', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/my-feedbacks', [FeedbackController::class, 'myFeedbacks'])->name('feedback.my');
});

// ─── Feedback (Rental Officer: view all) ──────────────────────────────────
Route::middleware(['auth', 'role:rental_officer'])->group(function () {
    Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('feedback.all');
});

// ─── Announcements (read-only: all roles except admin) ────────────────────
Route::middleware(['auth', 'role:rental_officer,guest,staff,student'])->group(function () {
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
});

// ─── Announcements (Rental Officer: CRUD) ──────────────────────────────────
Route::middleware(['auth', 'role:rental_officer'])
    ->prefix('officer')
    ->name('officer.')
    ->group(function () {
        Route::get('/announcements', [AnnouncementController::class, 'officerIndex'])->name('announcements.index');
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

// ─── AI Recommendations (Staff / Student / Guest) ────────────────────────
Route::middleware(['auth', 'role:staff,student,guest'])->group(function () {
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
});

// ─── Predictive Analytics (Rental Officer only) ───────────────────────────
Route::middleware(['auth', 'role:rental_officer'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

require __DIR__ . '/auth.php';