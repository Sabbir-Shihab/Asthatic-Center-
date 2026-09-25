<?php

namespace Database\Seeders;

use App\Models\{BeforeAfter, Brand, Category, Doctor, Product, Testimonial, Treatment};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $brands = collect(['Skinoveda', 'The Ordinary', 'La Roche-Posay', 'CeraVe', 'Simple'])->mapWithKeys(function ($name) { $b = Brand::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_active' => true]); return [$name => $b]; });
        $categoryNames = ['Cleanser', 'Toner', 'Serum', 'Moisturizer', 'Sunscreen', 'Mask', 'Eye Care', 'Body Care'];
        $categories = collect($categoryNames)->mapWithKeys(function ($name, $i) { $c = Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $i, 'is_active' => true]); return [$name => $c]; });
        $items = [
            ['Hydrating Face Cleanser','CeraVe','Cleanser',1850,2050,'Dry Skin'], ['Vitamin C Suspension 23%','The Ordinary','Serum',2400,2900,'All Skin Types'], ['Moisturizing Cream','CeraVe','Moisturizer',2200,null,'Sensitive Skin'], ['Anthelios SPF 50+','La Roche-Posay','Sunscreen',3200,3600,'All Skin Types'], ['Purifying Clay Mask','Skinoveda','Mask',1650,null,'Oily Skin'], ['Caffeine Eye Cream','The Ordinary','Eye Care',1950,null,'All Skin Types'], ['Hydrating Body Lotion','Simple','Body Care',1800,null,'Dry Skin'], ['Night Repair Serum','Skinoveda','Serum',2800,3200,'Combination Skin'], ['Soothing Facial Toner','Simple','Toner',1450,null,'Sensitive Skin'], ['Gentle Micellar Cleanser','Simple','Cleanser',1200,null,'All Skin Types'], ['Niacinamide 10% + Zinc','The Ordinary','Serum',2100,2500,'Oily Skin'], ['Daily Moisturizing Lotion','CeraVe','Moisturizer',1950,null,'Dry Skin'],
        ];
        foreach ($items as $i => [$name,$brand,$category,$price,$was,$skin]) Product::updateOrCreate(['slug' => Str::slug($name)], ['brand_id'=>$brands[$brand]->id, 'category_id'=>$categories[$category]->id, 'name'=>$name, 'description'=>'Thoughtfully selected skincare for healthy, comfortable, radiant skin.', 'price'=>$price, 'compare_at_price'=>$was, 'rating'=>5, 'review_count'=>[128,86,94,63,42,37,28,51,31,74,58,46][$i], 'stock'=>24, 'skin_types'=>[$skin], 'is_featured'=>$i<4, 'is_active'=>true, 'image'=>'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=640&auto=format&fit=crop']);
        foreach ([['Royal Glow Facial','Radiance & Rejuvenation'],['Lumière Skin Therapy','Advanced Skin Rejuvenation'],['Pearl Radiance Facial','Brightening & Glow'],['Veda Wellness Ritual','Holistic Healing']] as $i => [$name,$sub]) Treatment::updateOrCreate(['slug'=>Str::slug($name)], ['name'=>$name,'subtitle'=>$sub,'description'=>'A restorative, expert-led treatment tailored to your skin and wellness goals.','duration_minutes'=>[60,75,50,90][$i],'price'=>[4500,5800,3900,6500][$i],'is_featured'=>true,'is_active'=>true,'image'=>'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=900&auto=format&fit=crop']);
        Doctor::updateOrCreate(['slug'=>'dr-laila-mitu'], ['name'=>'Dr. Laila Mitu','designation'=>'Aesthetic Dermatologist & Wellness Specialist','photo'=>'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=1000&auto=format&fit=crop','credentials'=>['MBBS','MD (Dermatology)','Skincare & Anti-Aging Expert'],'years_experience'=>10,'quote'=>'Healthy skin is not just about beauty, it’s about confidence.','bio'=>'Thoughtful, evidence-led aesthetic care with a holistic approach.','is_featured'=>true,'is_active'=>true]);
        Testimonial::updateOrCreate(['client_name'=>'Tasnim Rahman'], ['client_photo'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=240&auto=format&fit=crop','review'=>'My skin has never felt healthier. The team listened carefully and built a routine that actually works for me.','rating'=>5,'is_featured'=>true,'is_approved'=>true]);
        foreach (['A clearer, calmer complexion','A brighter natural glow','A more even skin tone'] as $title) BeforeAfter::updateOrCreate(['title'=>$title], ['treatment'=>'Signature facial','caption'=>'A personalised treatment plan and expert aftercare.','before_image'=>'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=720&auto=format&fit=crop','after_image'=>'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?w=720&auto=format&fit=crop','is_published'=>true]);
    }
}
