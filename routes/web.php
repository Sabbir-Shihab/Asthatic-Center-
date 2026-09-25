<?php

use App\Http\Controllers\{AdminAuthController, AdminController, AppointmentController, CheckoutController, HomeController, NewsletterController, ShopController};
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'bn'], true), 404);
    session(['locale' => $locale]);
    $previous = url()->previous();

    return redirect(str_starts_with($previous, url('/')) ? $previous : route('home'));
})->name('locale.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/treatments', [HomeController::class, 'treatments'])->name('treatments.index');
Route::get('/treatments/category/{category}/{focus}', [HomeController::class, 'focusArea'])->name('treatments.focus');
Route::get('/treatments/category/{category}', [HomeController::class, 'treatmentCategory'])->name('treatments.category');
Route::get('/treatments/{treatment:slug}', [HomeController::class, 'treatment'])->name('treatments.show');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/wellness', [HomeController::class, 'wellness'])->name('wellness');
Route::get('/our-team', [HomeController::class, 'team'])->name('team');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');
Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
Route::get('/blog/{blogPost:slug}', [HomeController::class, 'blogPost'])->name('blog.post');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/book-appointment', [HomeController::class, 'bookAppointment'])->name('book');
Route::get('/results', [HomeController::class, 'results'])->name('results.index');
Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('testimonials.index');
Route::get('/doctors/{doctor:slug}', [HomeController::class, 'doctor'])->name('doctors.show');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/admin/login', [AdminAuthController::class,'form'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class,'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class,'logout'])->name('admin.logout');
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [AdminController::class,'dashboard'])->name('dashboard');
    Route::get('/settings', [AdminController::class,'settings'])->name('settings');
    Route::post('/settings', [AdminController::class,'updateSettings'])->name('settings.update');
    Route::get('/users', [AdminController::class,'users'])->name('users.index');
    Route::post('/users', [AdminController::class,'storeUser'])->name('users.store');
    Route::delete('/users/{user}', [AdminController::class,'destroyUser'])->name('users.destroy');
    Route::get('/password', [AdminController::class,'passwordForm'])->name('password');
    Route::put('/password', [AdminController::class,'updatePassword'])->name('password.update');
    Route::get('/website-hero', [AdminController::class,'settings'])->name('hero');
    Route::post('/website-hero', [AdminController::class,'updateSettings'])->name('hero.update');
    Route::post('/orders/{id}/confirm', [AdminController::class,'confirmOrder'])->name('orders.confirm');
    Route::get('/ecommerce', [AdminController::class,'ecommerce'])->name('ecommerce');
    Route::post('/service-categories/{id}/focus-areas', [AdminController::class,'storeCategoryFocusArea'])->name('service-categories.focus-areas.store');
    Route::delete('/service-categories/{service}/focus-areas/{focus}', [AdminController::class,'destroyCategoryFocusArea'])->name('service-categories.focus-areas.destroy');
    Route::get('/{resource}', [AdminController::class,'index'])->name('resource.index');
    Route::get('/{resource}/create', [AdminController::class,'create'])->name('resource.create');
    Route::post('/{resource}', [AdminController::class,'store'])->name('resource.store');
    Route::get('/{resource}/{id}/edit', [AdminController::class,'edit'])->name('resource.edit');
    Route::put('/{resource}/{id}', [AdminController::class,'update'])->name('resource.update');
    Route::delete('/{resource}/{id}', [AdminController::class,'destroy'])->name('resource.destroy');
});
