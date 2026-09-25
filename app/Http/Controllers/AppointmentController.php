<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\FocusArea;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->merge(['focus_area_id' => $request->input('focus_area_id') ?: null]);
        $data = $request->validate([
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'treatment_id' => ['nullable', 'exists:treatments,id'],
            'category' => ['nullable', 'string', 'max:80'],
            'focus_area_id' => ['nullable', 'integer', 'exists:focus_areas,id'],
            'client_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'appointment_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        if (! empty($data['focus_area_id']) && ! empty($data['category'])) {
            $categoryId = ServiceCategory::where('slug', $data['category'])->value('id');
            $belongs = $categoryId && FocusArea::whereKey($data['focus_area_id'])->where('service_category_id', $categoryId)->where('is_active', true)->exists();
            if (! $belongs) {
                return back()->withErrors(['focus_area_id' => t('Please choose a focus area from this service.')])->withInput();
            }
        }
        Appointment::create($data);

        return back()->with('appointment_submitted', t('Booking successful. A representative from our team will contact you very soon.'));
    }
}
