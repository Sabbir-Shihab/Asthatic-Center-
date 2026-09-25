<?php

namespace App\Http\Controllers;

use App\Models\{BeforeAfter, BlogPost, Doctor, FocusArea, Product, ServiceCategory, SiteSetting, Testimonial, Treatment};

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'doctor' => Doctor::where('is_active', true)->where('is_featured', true)->first(),
            'heroBanner' => SiteSetting::where('key', 'hero_banner')->first()?->value,
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'treatments' => Treatment::where('is_active', true)->where('is_featured', true)->latest()->take(4)->get(),
            'allTreatments' => Treatment::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::with('brand')->where('is_active', true)->where('is_featured', true)->take(4)->get(),
            'testimonial' => Testimonial::where('is_featured', true)->first(),
            'testimonials' => Testimonial::where('is_approved', true)->latest()->get(),
            'results' => BeforeAfter::where('is_published', true)->latest()->get(),
        ]);
    }

    public function wellness()
    {
        return view('wellness', [
            'wellnessTreatments' => Treatment::where('is_active', true)
                ->whereIn('category', ['ayurveda-panchakarma', 'naturopathy-natural-therapy', 'holistic-health-lifestyle', 'detox-hijama-wellness', 'wellness-ayurveda'])
                ->orderByDesc('is_featured')
                ->orderBy('name')
                ->get(),
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function about()
    {
        return view('about', [
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'featuredDoctors' => Doctor::where('is_active', true)->where('is_featured', true)->orderBy('name')->get(),
            'treatments' => Treatment::where('is_active', true)->orderByDesc('is_featured')->take(6)->get(),
        ]);
    }

    public function bookAppointment()
    {
        return view('book', [
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'serviceCategories' => ServiceCategory::query()
                ->where('is_active', true)
                ->with(['focusAreas' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('name')])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'allTreatments' => Treatment::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function results()
    {
        return view('results.index', [
            'results' => BeforeAfter::where('is_published', true)->latest()->get(),
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'allTreatments' => Treatment::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function testimonials()
    {
        return view('testimonials.index', [
            'testimonials' => Testimonial::where('is_approved', true)->latest()->get(),
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'allTreatments' => Treatment::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function team()
    {
        return view('team', [
            'doctors' => Doctor::where('is_active', true)->orderByDesc('is_featured')->orderBy('name')->get(),
        ]);
    }

    public function gallery()
    {
        return view('gallery', [
            'results' => BeforeAfter::where('is_published', true)->latest()->get(),
            'testimonials' => Testimonial::where('is_approved', true)->latest()->take(6)->get(),
        ]);
    }

    public function blog()
    {
        return view('blog', [
            'categories' => ServiceCategory::publishedList(),
            'featuredTreatments' => Treatment::where('is_active', true)->orderByDesc('is_featured')->take(6)->get(),
            'posts' => BlogPost::where('is_published', true)->where(function ($query) { $query->whereNull('published_at')->orWhere('published_at', '<=', now()); })->latest('published_at')->latest('id')->get(),
        ]);
    }

    public function blogPost(BlogPost $blogPost)
    {
        abort_unless($blogPost->is_published && (! $blogPost->published_at || $blogPost->published_at->isPast()), 404);
        return view('blog.show', ['post' => $blogPost]);
    }

    public function doctor(Doctor $doctor)
    {
        abort_unless($doctor->is_active, 404);

        return view('doctors.show', [
            'doctor' => $doctor,
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'allTreatments' => Treatment::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function treatments()
    {
        $treatments = Treatment::where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('treatments.index', [
            'treatments' => $treatments,
            'featuredTreatment' => $treatments->first(),
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'allTreatments' => $treatments,
        ]);
    }

    public function treatmentCategory(string $category)
    {
        $category = Treatment::legacyCategoryMap()[$category] ?? $category;
        $serviceCategory = ServiceCategory::where('slug', $category)->where('is_active', true)->first();
        abort_unless($serviceCategory || array_key_exists($category, Treatment::categories()), 404);
        $legacyKeys = array_keys(array_filter(Treatment::legacyCategoryMap(), fn ($mapped) => $mapped === $category));

        $treatments = Treatment::where('is_active', true)
            ->whereIn('category', array_merge([$category], $legacyKeys))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('treatments.category', [
            'serviceCategory' => $serviceCategory,
            'focusAreas' => $serviceCategory
                ? $serviceCategory->focusAreas()->where('is_active', true)->get()
                : collect(),
            'treatments' => $treatments,
            'activeCategory' => $category,
            'categoryTitle' => $serviceCategory->name ?? Treatment::categories()[$category],
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function focusArea(string $category, string $focus)
    {
        $category = Treatment::legacyCategoryMap()[$category] ?? $category;
        $serviceCategory = ServiceCategory::where('slug', $category)->where('is_active', true)->firstOrFail();
        $focusArea = FocusArea::where('service_category_id', $serviceCategory->id)
            ->where('slug', $focus)
            ->where('is_active', true)
            ->firstOrFail();

        return view('treatments.focus', [
            'serviceCategory' => $serviceCategory,
            'focusArea' => $focusArea,
            'activeCategory' => $category,
            'categoryTitle' => $serviceCategory->name,
            'focusAreas' => FocusArea::where('service_category_id', $serviceCategory->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'related' => FocusArea::where('service_category_id', $serviceCategory->id)
                ->where('is_active', true)
                ->whereKeyNot($focusArea->id)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function treatment(Treatment $treatment)
    {
        abort_unless($treatment->is_active, 404);

        return view('treatments.show', [
            'treatment' => $treatment,
            'relatedTreatments' => Treatment::where('is_active', true)
                ->whereKeyNot($treatment->id)
                ->orderByDesc('is_featured')
                ->take(3)
                ->get(),
            'doctors' => Doctor::where('is_active', true)->orderBy('name')->get(),
            'allTreatments' => Treatment::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
