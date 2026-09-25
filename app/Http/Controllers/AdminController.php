<?php

namespace App\Http\Controllers;

use App\Models\{Appointment, BeforeAfter, BlogPost, Brand, Category, Doctor, FocusArea, Order, PaymentMethod, Product, ServiceCategory, SiteSetting, Testimonial, Treatment, User};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function users() { return view('admin.users', ['users' => User::orderBy('name')->get()]); }
    public function storeUser(Request $request)
    {
        $data = $request->validate(['name' => ['required','string','max:190'], 'email' => ['required','email','max:190','unique:users,email'], 'password' => ['required','string','min:8','confirmed']]);
        User::create([...$data, 'role' => 'manager']);
        return back()->with('saved', 'Manager account created.');
    }
    public function destroyUser(User $user)
    {
        abort_if($user->id === (int) session('skinoveda_admin_id'), 422, 'You cannot delete your own account.');
        abort_if($user->role === 'admin', 403, 'Administrator accounts cannot be deleted here.');
        $user->delete();
        return back()->with('saved', 'Manager account deleted.');
    }
    public function passwordForm() { return view('admin.password'); }
    public function updatePassword(Request $request)
    {
        $data = $request->validate(['current_password' => ['required','string'], 'password' => ['required','string','min:8','confirmed']]);
        $user = User::findOrFail((int) session('skinoveda_admin_id'));
        if (! Hash::check($data['current_password'], $user->password)) return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        $user->update(['password' => $data['password']]);
        return back()->with('saved', 'Password updated successfully.');
    }
    private array $resources = [
        'products'=>[Product::class,'Products',['name','slug','brand_id','category_id','description','image','price','compare_at_price','rating','review_count','stock','skin_types','is_featured','is_active']],
        'brands'=>[Brand::class,'Brands',['name','slug','description','logo','is_active']],
        'categories'=>[Category::class,'Categories',['name','slug','icon','description','sort_order','is_active']],
        'service-categories'=>[ServiceCategory::class,'Service Categories',['name','slug','icon','headline','short_description','overview','who_for','what_to_expect','care_note','image','focus_areas','sort_order','is_active']],
        'focus-areas'=>[FocusArea::class,'Focus Areas',['service_category_id','name','name_bn','slug','summary','summary_bn','details','details_bn','who_for','who_for_bn','what_to_expect','what_to_expect_bn','care_note','care_note_bn','image','duration_minutes','price','sort_order','is_active']],
        'treatments'=>[Treatment::class,'Signature Treatments',['name','slug','category','subtitle','description','image','duration_minutes','price','is_featured','is_active']],
        'doctors'=>[Doctor::class,'Doctors',['name','slug','designation','photo','hero_photo','credentials','years_experience','quote','bio','is_featured','is_active']],
        'testimonials'=>[Testimonial::class,'Testimonials',['client_name','client_photo','review','rating','is_featured','is_approved']],
        'before-afters'=>[BeforeAfter::class,'Before & Afters',['title','before_image','after_image','treatment','caption','is_published']],
        'gallery'=>[BeforeAfter::class,'Gallery',['title','before_image','after_image','treatment','caption','is_published']],
        'appointments'=>[Appointment::class,'Appointments',['doctor_id','treatment_id','category','client_name','email','phone','appointment_at','notes','status']],
        'payment-methods'=>[PaymentMethod::class,'Payment Methods',['name','slug','account_number','advance_amount','instructions','sort_order','is_active']],
        'blog'=>[BlogPost::class,'Blog Posts',['title','slug','excerpt','content','image','is_published','published_at']],
        'orders'=>[Order::class,'Orders',['order_number','customer_name','email','phone','shipping_address','city','status','subtotal','shipping','total','payment_method']],
    ];
    public function dashboard() { return view('admin.dashboard', ['counts'=>['products'=>Product::count(),'treatments'=>Treatment::count(),'appointments'=>Appointment::count(),'orders'=>Order::count()]]); }
    public function ecommerce()
    {
        return view('admin.ecommerce', [
            'sections' => [
                'products' => ['label' => 'Products', 'count' => Product::count(), 'text' => 'Shop products, prices, and stock.'],
                'brands' => ['label' => 'Brands', 'count' => Brand::count(), 'text' => 'Brands shown on the shop.'],
                'categories' => ['label' => 'Categories', 'count' => Category::count(), 'text' => 'Product categories.'],
                'orders' => ['label' => 'Orders', 'count' => Order::count(), 'text' => 'Customer orders and payments.'],
            ],
        ]);
    }
    public function index(string $resource)
    {
        [$class, $title] = $this->resource($resource);
        $query = $class::query();
        $dateInput = trim((string) request('date', ''));
        $searchDate = $this->searchDate($dateInput);
        $dateError = $dateInput !== '' && ! $searchDate;
        if ($resource === 'orders') {
            $query->with('items');
            if ($searchDate) {
                $query->whereDate('created_at', $searchDate->toDateString());
            }
            $query->orderBy('created_at', request('sort') === 'date_asc' ? 'asc' : 'desc');
        }
        if ($resource === 'appointments') {
            $query->with(['treatment', 'doctor', 'focusArea']);
            if ($searchDate) {
                $query->whereDate('appointment_at', $searchDate->toDateString());
            }
            if (request()->filled('category')) {
                $query->where(function ($appointmentQuery) {
                    $appointmentQuery->where('category', request('category'))
                        ->orWhereHas('treatment', fn ($q) => $q->where('category', request('category')));
                });
            }
            $sort = request('sort', 'date_desc');
            if (in_array($sort, ['doctor_asc', 'doctor_desc'], true)) {
                $query->orderByRaw('case when doctor_id is null then 1 else 0 end')
                    ->orderBy(Doctor::select('name')->whereColumn('doctors.id', 'appointments.doctor_id'), $sort === 'doctor_desc' ? 'desc' : 'asc');
            } else {
                $query->orderByRaw('case when appointment_at is null then 1 else 0 end')
                    ->orderBy('appointment_at', $sort === 'date_asc' ? 'asc' : 'desc');
            }
        }
        if ($resource === 'focus-areas') {
            $query->with('category');
            if (request()->filled('category')) {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', request('category')));
            }
        }
        if (! in_array($resource, ['appointments', 'orders'], true)) {
            $query->latest('id');
        }
        return view('admin.index', [
            'resource' => $resource,
            'title' => $title,
            'rows' => $query->paginate($resource === 'focus-areas' ? 30 : 20)->withQueryString(),
            'dateError' => $dateError,
            'selectedDate' => $searchDate,
        ]);
    }
    public function create(string $resource) { [, $title, $fields] = $this->resource($resource); abort_if($resource==='appointments'||$resource==='orders',404); return view('admin.form',['resource'=>$resource,'title'=>$title,'fields'=>$fields,'row'=>null]); }
    public function store(Request $request, string $resource) { [$class,$title,$fields] = $this->resource($resource); abort_if($resource==='appointments'||$resource==='orders',404); $data=$this->validated($request,$resource,$fields); $this->storeImages($request,$resource,$data); $class::create($data); return redirect()->route('admin.resource.index',['resource'=>$resource])->with('saved',"$title entry created."); }
    public function edit(string $resource, int $id) { [$class,$title,$fields] = $this->resource($resource); abort_if(in_array($resource,['appointments','orders']),404); return view('admin.form',['resource'=>$resource,'title'=>$title,'fields'=>$fields,'row'=>$class::findOrFail($id)]); }
    public function update(Request $request, string $resource, int $id) { [$class,$title,$fields] = $this->resource($resource); abort_if(in_array($resource,['appointments','orders']),404); $row=$class::findOrFail($id); $data=$this->validated($request,$resource,$fields,$row); $this->storeImages($request,$resource,$data,$row); $row->update($data); return redirect()->route('admin.resource.index',['resource'=>$resource])->with('saved',"$title entry updated."); }
    public function destroy(string $resource, int $id) { [$class,$title] = $this->resource($resource); $class::findOrFail($id)->delete(); return back()->with('saved',"$title entry deleted."); }
    public function confirmOrder(int $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'confirmed', 'payment_status' => 'confirmed']);
        return back()->with('saved', "Order {$order->order_number} confirmed.");
    }
    public function storeCategoryFocusArea(Request $request, int $id)
    {
        $service = ServiceCategory::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'name_bn' => ['nullable', 'string', 'max:190'],
            'summary' => ['nullable', 'string', 'max:12000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $slug = Str::slug($data['name']);
        $baseSlug = $slug;
        $counter = 2;
        while (FocusArea::where('service_category_id', $service->id)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $service->focusAreas()->create([
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'slug' => $slug,
            'summary' => $data['summary'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('saved', 'Included focus area added.');
    }
    public function destroyCategoryFocusArea(int $service, int $focus)
    {
        FocusArea::where('service_category_id', $service)->findOrFail($focus)->delete();

        return back()->with('saved', 'Included focus area deleted.');
    }
    public function settings()
    {
        $defaults = $this->settingDefaults();
        foreach ($defaults as $key => $value) SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        return view('admin.settings', [
            'settings' => SiteSetting::query()->get()->keyBy('key'),
            'defaults' => $defaults,
        ]);
    }
    public function updateSettings(Request $request)
    {
        $fields = array_keys($this->settingDefaults());
        $data = $request->validate([
            'site_name' => ['nullable', 'string', 'max:190'],
            'site_subtitle' => ['nullable', 'string', 'max:190'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'about_title' => ['nullable', 'string', 'max:190'],
            'about_subtitle' => ['nullable', 'string', 'max:255'],
            'about_story' => ['nullable', 'string', 'max:3000'],
            'about_mission' => ['nullable', 'string', 'max:1200'],
            'about_feature_1' => ['nullable', 'string', 'max:190'],
            'about_feature_2' => ['nullable', 'string', 'max:190'],
            'about_feature_3' => ['nullable', 'string', 'max:190'],
            'wellness_title' => ['nullable', 'string', 'max:190'],
            'wellness_subtitle' => ['nullable', 'string', 'max:255'],
            'wellness_story' => ['nullable', 'string', 'max:2500'],
            'wellness_ritual_1' => ['nullable', 'string', 'max:190'],
            'wellness_ritual_2' => ['nullable', 'string', 'max:190'],
            'wellness_ritual_3' => ['nullable', 'string', 'max:190'],
            'contact_location' => ['nullable', 'string', 'max:190'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['nullable', 'string', 'max:60'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'opening_hours' => ['nullable', 'string', 'max:190'],
            'footer_about' => ['nullable', 'string', 'max:500'],
            'newsletter_heading' => ['nullable', 'string', 'max:190'],
            'newsletter_text' => ['nullable', 'string', 'max:255'],
            'hero_banner' => ['nullable', 'string', 'max:2048'],
            'site_logo' => ['nullable', 'string', 'max:2048'],
            'site_favicon' => ['nullable', 'string', 'max:2048'],
            'about_image' => ['nullable', 'string', 'max:2048'],
            'wellness_image' => ['nullable', 'string', 'max:2048'],
            'hero_banner_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'site_logo_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,avif,svg', 'max:2048'],
            'site_favicon_upload' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,webp,svg', 'max:1024'],
            'about_image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'wellness_image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ]);
        $imageSettings = ['hero_banner', 'site_logo', 'site_favicon', 'about_image', 'wellness_image'];
        foreach ($fields as $field) {
            if (in_array($field, $imageSettings, true)) continue;
            SiteSetting::updateOrCreate(['key' => $field], ['value' => $data[$field] ?? null]);
        }
        foreach (['hero_banner' => 'site', 'site_logo' => 'site', 'site_favicon' => 'site', 'about_image' => 'site', 'wellness_image' => 'site'] as $field => $directory) {
            $setting = SiteSetting::firstOrCreate(['key' => $field]);
            $value = storage_relative($data[$field] ?? null);
            if ($upload = $request->file($field.'_upload')) {
                $old = $setting->getRawOriginal('value');
                if ($old && ! Str::startsWith($old, ['http://', 'https://'])) Storage::disk('public')->delete($old);
                $value = $upload->store($directory, 'public');
            }
            $setting->update(['value' => $value]);
        }
        return back()->with('saved', 'Settings updated.');
    }
    private function settingDefaults(): array
    {
        return [
            'site_name' => 'Skinoveda',
            'site_subtitle' => 'Aesthetic Care',
            'site_tagline' => 'NATURAL BEAUTY · MODERN SCIENCE · HOLISTIC WELLNESS',
            'site_description' => 'Expert aesthetic care, holistic wellness and premium skincare at Skinoveda.',
            'about_title' => 'Where aesthetic science meets mindful wellness.',
            'about_subtitle' => 'Skinoveda was created as a calm, premium care space for advanced skin treatments, holistic wellness, and curated skincare rituals.',
            'about_story' => 'Our approach blends modern dermatology, aesthetic treatments, premium skincare, and Ayurvedic wellness into one thoughtful experience. Every consultation starts with listening, understanding your skin goals, and designing care that feels personal, safe, and beautiful.',
            'about_mission' => 'To help every client feel confident in their skin through expert guidance, honest care, refined treatments, and a peaceful clinic experience.',
            'about_feature_1' => 'Expert led aesthetic treatments',
            'about_feature_2' => 'Holistic wellness and Ayurvedic care',
            'about_feature_3' => 'Premium skincare with personal guidance',
            'contact_location' => 'DHAKA, BANGLADESH',
            'contact_address' => "Gulshan Avenue, Dhaka\nBangladesh",
            'contact_phone' => '+880 1700 000000',
            'contact_email' => 'hello@skinoveda.local',
            'opening_hours' => 'Sat-Thu, 10 am-8 pm',
            'footer_about' => 'A thoughtful blend of modern aesthetic science and holistic care, created just for you.',
            'newsletter_heading' => 'A little glow in your inbox',
            'newsletter_text' => 'New arrivals and thoughtful skin advice.',
            'hero_banner' => null,
            'site_logo' => null,
            'site_favicon' => null,
            'about_image' => null,
            'wellness_title' => 'Holistic wellness for calm skin, body, and mind.',
            'wellness_subtitle' => 'A gentle care journey blending Ayurvedic wisdom, restorative rituals, and modern aesthetic support.',
            'wellness_story' => 'Our wellness philosophy focuses on balance. From soothing consultations to calming rituals, we help you build a routine that supports healthy skin from the inside and outside. Each recommendation is shaped around comfort, consistency, and long term glow.',
            'wellness_ritual_1' => 'Ayurvedic inspired care plans',
            'wellness_ritual_2' => 'Stress calming skin rituals',
            'wellness_ritual_3' => 'Personal wellness consultation',
            'wellness_image' => null,
        ];
    }
    private function searchDate(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            try {
                $date = Carbon::createFromFormat('!Y-m-d', $value);
            } catch (\Throwable) {
                return null;
            }

            return $date instanceof Carbon && $date->format('Y-m-d') === $value ? $date : null;
        }
        $value = str_replace('-', '/', $value);
        foreach (['j/n/Y', 'd/m/Y'] as $format) {
            try {
                $date = Carbon::createFromFormat('!'.$format, $value);
            } catch (\Throwable) {
                continue;
            }
            if ($date instanceof Carbon && $date->format($format) === $value) {
                return $date;
            }
        }

        return null;
    }
    private function resource(string $key): array { abort_unless(isset($this->resources[$key]),404); return $this->resources[$key]; }
    private function validated(Request $request,string $resource,array $fields,?Model $row=null): array
    {
        $imageFields=['image','photo','hero_photo','logo','client_photo','before_image','after_image'];
        if (! $request->filled('slug') && ($request->filled('name') || $request->filled('title'))) {
            $request->merge(['slug' => Str::slug($request->input('name') ?: $request->input('title'))]);
        }
        $table = $this->resources[$resource][0]::make()->getTable();
        $rules=[]; foreach($fields as $field){ $rules[$field]=match($field){
            'slug'=>['required','string','max:190','alpha_dash','unique:'.$table.',slug'.($row?','.$row->id:'')],
            'name','client_name','title','designation'=>['required','string','max:190'],
            'brand_id'=>['required','exists:brands,id'],'category_id'=>['required','exists:categories,id'],
            'category'=>['nullable','string','in:'.implode(',', array_keys(Treatment::categories()))],
            'doctor_id'=>['nullable','exists:doctors,id'],'treatment_id'=>['nullable','exists:treatments,id'],'email'=>['nullable','email'],
            'price'=>[$resource === 'products' ? 'required' : 'nullable','numeric','min:0'],'compare_at_price'=>['nullable','numeric','min:0'],
            'subtotal','shipping','total'=>['nullable','numeric','min:0'],'rating'=>['nullable','integer','between:1,5'],
            'stock','review_count','duration_minutes','years_experience','sort_order','capacity'=>['nullable','integer','min:0'],
            'skin_types','credentials','focus_areas'=>['nullable','array'],'is_featured','is_active','is_approved','is_published','is_available'=>['nullable','boolean'],
            'appointment_at'=>['nullable','date'],'status','payment_method'=>['nullable','string','max:40'],'icon'=>['nullable','string','max:100'],
            default=>in_array($field,$imageFields,true)?['nullable','string','max:2048']:['nullable','string','max:12000']
        }; }
        if ($resource === 'service-categories') {
            $rules['focus_areas'] = ['nullable'];
        }
        if ($resource === 'focus-areas') {
            $rules['service_category_id'] = ['required', 'exists:service_categories,id'];
            $rules['name'] = ['required', 'string', 'max:190'];
            $rules['name_bn'] = ['nullable', 'string', 'max:190'];
            $rules['slug'] = ['required', 'string', 'max:190', 'alpha_dash', Rule::unique('focus_areas', 'slug')->where(fn ($query) => $query->where('service_category_id', $request->input('service_category_id')))->ignore($row?->id)];
        }
        if ($resource === 'payment-methods') {
            $rules['name'] = ['required', 'string', 'max:80'];
            $rules['slug'] = ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('payment_methods', 'slug')->ignore($row?->id)];
            $rules['account_number'] = [$request->boolean('is_active') ? 'required' : 'nullable', 'string', 'max:40'];
            $rules['advance_amount'] = ['required', 'numeric', $request->boolean('is_active') ? 'min:1' : 'min:0'];
            $rules['instructions'] = ['nullable', 'string', 'max:500'];
        }
        foreach($imageFields as $field) if(in_array($field,$fields,true)) $rules[$field.'_upload']=['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:5120'];
        $data=$request->validate($rules); foreach(['is_featured','is_active','is_approved','is_published','is_available'] as $flag) if(in_array($flag,$fields,true)) $data[$flag]=$request->boolean($flag);
        foreach($imageFields as $field) unset($data[$field.'_upload']);
        foreach($imageFields as $field) if(array_key_exists($field,$data)) $data[$field]=storage_relative($data[$field]);
        foreach (['skin_types', 'credentials', 'focus_areas'] as $listField) {
            if (array_key_exists($listField, $data)) {
                $data[$listField] = $this->cleanListInput($data[$listField]);
            }
        }
        if(in_array('slug',$fields,true)&&empty($data['slug'])&&!empty($data['name'])) $data['slug']=Str::slug($data['name']);
        return $data;
    }

    private function cleanListInput(mixed $value): array
    {
        $items = is_array($value) ? $value : preg_split('/[\r\n,]+/', (string) $value);

        return collect($items)
            ->flatMap(fn ($item) => preg_split('/[\r\n,]+/', (string) $item) ?: [])
            ->map(fn ($item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function storeImages(Request $request,string $resource,array &$data,?Model $row=null): void
    {
        $fields=match($resource){'products'=>['image'],'brands'=>['logo'],'service-categories'=>['image'],'focus-areas'=>['image'],'treatments'=>['image'],'doctors'=>['photo','hero_photo'],'testimonials'=>['client_photo'],'before-afters','gallery'=>['before_image','after_image'],'blog'=>['image'],default=>[]};
        foreach($fields as $field){
            $upload=$request->file($field.'_upload');
            if(!$upload) continue;
            $old=$row?->getRawOriginal($field);
            if($old && !Str::startsWith($old,['http://','https://'])) Storage::disk('public')->delete($old);
            $data[$field]=$upload->store($resource,'public');
        }
    }
}
