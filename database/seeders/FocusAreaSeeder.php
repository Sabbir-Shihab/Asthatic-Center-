<?php

namespace Database\Seeders;

use App\Models\FocusArea;
use App\Models\ServiceCategory;
use App\Models\Treatment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FocusAreaSeeder extends Seeder
{
    public function run(): void
    {
        $blade = file_get_contents(resource_path('views/treatments/category.blade.php'));
        if (! preg_match('/\$serviceDetails = (\[.*?\]);/s', $blade, $match)) {
            throw new \RuntimeException('Could not read the included focus area lists.');
        }

        $serviceDetails = eval('return '.$match[1].';');
        $dictionary = require lang_path('bn/ui.php');
        $categories = Treatment::categories();

        foreach ($serviceDetails as $slug => $detail) {
            $category = ServiceCategory::where('slug', $slug)->first();
            if (! $category) {
                continue;
            }

            $categoryName = $categories[$slug] ?? $category->name;
            $categoryNameBn = $dictionary[$categoryName] ?? $categoryName;
            $image = $category->getRawOriginal('image') ?: ($detail[3] ?? null);

            foreach (array_values($detail[4] ?? []) as $index => $name) {
                $nameBn = $dictionary[$name] ?? null;
                $labelBn = $nameBn ?: $name;

                FocusArea::firstOrCreate(
                    ['service_category_id' => $category->id, 'slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'name_bn' => $nameBn,
                        'summary' => $name.' at Skinoveda, planned around your concern and comfort.',
                        'summary_bn' => 'স্কিনোভেদায় '.$labelBn.', আপনার সমস্যা ও আরাম মাথায় রেখে সাজানো।',
                        'details' => $name.' is part of '.$categoryName.' at Skinoveda. Care begins with a private consultation. The specialist listens to your concern, checks whether this focus area is suitable, and explains the approach, expected sessions, comfort level, and aftercare before anything begins. The plan stays personal, so the same name can look different from one client to the next.',
                        'details_bn' => $labelBn.' স্কিনোভেদার '.$categoryNameBn.' সেবার অংশ। যত্ন শুরু হয় একটি ব্যক্তিগত পরামর্শ দিয়ে। বিশেষজ্ঞ আপনার কথা শোনেন, এই ফোকাস এরিয়া আপনার জন্য উপযুক্ত কি না দেখেন, এবং শুরুর আগে পদ্ধতি, সম্ভাব্য সেশন, আরামের মাত্রা ও আফটারকেয়ার বুঝিয়ে বলেন। পরিকল্পনা ব্যক্তিগত থাকে, তাই একই সেবা একেকজনের জন্য একেকরকম হতে পারে।',
                        'who_for' => 'People who want focused support for '.$name.', with guidance shaped around their skin, health, or wellness goals.',
                        'who_for_bn' => 'যাঁরা '.$labelBn.' নিয়ে নির্দিষ্ট সহায়তা চান, এবং ত্বক, স্বাস্থ্য বা সুস্থতার লক্ষ্য অনুযায়ী নির্দেশনা পেতে চান।',
                        'what_to_expect' => 'Your visit starts with a consultation for '.$name.'. The expert explains the suitable option, how many sessions may be needed, and what to do before and after.',
                        'what_to_expect_bn' => $labelBn.' এর জন্য ভিজিট শুরু হয় পরামর্শ দিয়ে। বিশেষজ্ঞ উপযুক্ত বিকল্প, কয়টি সেশন লাগতে পারে, এবং আগে ও পরে কী করতে হবে তা ব্যাখ্যা করেন।',
                        'care_note' => 'Share your medical history, current routine, and any sensitivity during consultation so the plan stays safe and accurate.',
                        'care_note_bn' => 'পরিকল্পনা নিরাপদ ও নির্ভুল রাখতে পরামর্শে আপনার চিকিৎসার ইতিহাস, বর্তমান রুটিন এবং যেকোনো সংবেদনশীলতা জানান।',
                        'image' => $image,
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
