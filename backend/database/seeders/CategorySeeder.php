<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'skill' => [
                ['name' => 'Formwork', 'name_he' => 'טפסנות', 'name_ar' => 'قوالب', 'slug' => 'formwork'],
                ['name' => 'Rebar', 'name_he' => 'ברזל/זיון', 'name_ar' => 'حديد تسليح', 'slug' => 'rebar'],
                ['name' => 'General', 'name_he' => 'כללי', 'name_ar' => 'عام', 'slug' => 'general'],
                ['name' => 'Finishing', 'name_he' => 'גמר', 'name_ar' => 'تشطيب', 'slug' => 'finishing'],
                ['name' => 'Skeleton', 'name_he' => 'שלד', 'name_ar' => 'هيكل', 'slug' => 'skeleton'],
                ['name' => 'Electrical', 'name_he' => 'חשמל', 'name_ar' => 'كهرباء', 'slug' => 'electrical'],
                ['name' => 'Plumbing', 'name_he' => 'אינסטלציה', 'name_ar' => 'سباكة', 'slug' => 'plumbing'],
                ['name' => 'Painting', 'name_he' => 'צביעה', 'name_ar' => 'دهان', 'slug' => 'painting'],
                ['name' => 'Waterproofing', 'name_he' => 'איטום', 'name_ar' => 'عزل مائي', 'slug' => 'waterproofing'],
            ],
            'work_area' => [
                ['name' => 'North', 'name_he' => 'צפון', 'name_ar' => 'شمال', 'slug' => 'north'],
                ['name' => 'Center', 'name_he' => 'מרכז', 'name_ar' => 'وسط', 'slug' => 'center'],
                ['name' => 'South', 'name_he' => 'דרום', 'name_ar' => 'جنوب', 'slug' => 'south'],
            ],
            'project_type' => [
                ['name' => 'Residential', 'name_he' => 'מגורים', 'name_ar' => 'سكني', 'slug' => 'residential'],
                ['name' => 'Commercial', 'name_he' => 'מסחרי', 'name_ar' => 'تجاري', 'slug' => 'commercial'],
                ['name' => 'Infrastructure', 'name_he' => 'תשתיות', 'name_ar' => 'بنية تحتية', 'slug' => 'infrastructure'],
                ['name' => 'Transportation', 'name_he' => 'תחבורה', 'name_ar' => 'نقل', 'slug' => 'transportation'],
                ['name' => 'Public', 'name_he' => 'ציבורי', 'name_ar' => 'عام', 'slug' => 'public'],
            ],
            'region' => [
                ['name' => 'North', 'name_he' => 'צפון', 'name_ar' => 'شمال', 'slug' => 'north'],
                ['name' => 'Center', 'name_he' => 'מרכז', 'name_ar' => 'وسط', 'slug' => 'center'],
                ['name' => 'South', 'name_he' => 'דרום', 'name_ar' => 'جنوب', 'slug' => 'south'],
            ],
        ];

        $sortOrder = 0;
        foreach ($categories as $type => $items) {
            foreach ($items as $item) {
                Category::updateOrCreate(
                    ['type' => $type, 'slug' => $item['slug']],
                    array_merge($item, [
                        'type' => $type,
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
