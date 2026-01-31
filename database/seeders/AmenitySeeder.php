<?php

namespace Database\Seeders;

use App\Models\AmenityCategory;
use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        // Create Categories First
        $categories = [
            ['name' => 'Sports & Recreation', 'slug' => 'sports-recreation', 'icon' => 'bi-trophy', 'sort_order' => 1],
            ['name' => 'Wellness & Fitness', 'slug' => 'wellness-fitness', 'icon' => 'bi-heart-pulse', 'sort_order' => 2],
            ['name' => 'Clubhouse & Social', 'slug' => 'clubhouse-social', 'icon' => 'bi-people', 'sort_order' => 3],
            ['name' => 'Security & Safety', 'slug' => 'security-safety', 'icon' => 'bi-shield-check', 'sort_order' => 4],
            ['name' => 'Children & Family', 'slug' => 'children-family', 'icon' => 'bi-emoji-smile', 'sort_order' => 5],
            ['name' => 'Convenience & Services', 'slug' => 'convenience-services', 'icon' => 'bi-shop', 'sort_order' => 6],
            ['name' => 'Green & Environment', 'slug' => 'green-environment', 'icon' => 'bi-tree', 'sort_order' => 7],
            ['name' => 'Luxury & Premium', 'slug' => 'luxury-premium', 'icon' => 'bi-gem', 'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            AmenityCategory::create($cat);
        }

        // Now create amenities
        $amenities = [
            // Sports & Recreation
            ['category' => 'Sports & Recreation', 'name' => 'Swimming Pool', 'icon' => 'bi-water', 'is_premium' => true],
            ['category' => 'Sports & Recreation', 'name' => 'Gymnasium', 'icon' => 'bi-heart-pulse', 'is_premium' => false],
            ['category' => 'Sports & Recreation', 'name' => 'Indoor Games Room', 'icon' => 'bi-dice-5', 'is_premium' => false],
            ['category' => 'Sports & Recreation', 'name' => 'Tennis Court', 'icon' => 'bi-circle', 'is_premium' => true],
            ['category' => 'Sports & Recreation', 'name' => 'Badminton Court', 'icon' => 'bi-circle', 'is_premium' => false],
            ['category' => 'Sports & Recreation', 'name' => 'Basketball Court', 'icon' => 'bi-circle', 'is_premium' => true],
            ['category' => 'Sports & Recreation', 'name' => 'Cricket Practice Net', 'icon' => 'bi-circle', 'is_premium' => false],
            ['category' => 'Sports & Recreation', 'name' => 'Squash Court', 'icon' => 'bi-square', 'is_premium' => true],
            ['category' => 'Sports & Recreation', 'name' => 'Jogging Track', 'icon' => 'bi-circle', 'is_premium' => false],
            ['category' => 'Sports & Recreation', 'name' => 'Cycling Track', 'icon' => 'bi-bicycle', 'is_premium' => false],
            ['category' => 'Sports & Recreation', 'name' => 'Table Tennis', 'icon' => 'bi-square', 'is_premium' => false],
            ['category' => 'Sports & Recreation', 'name' => 'Amphitheater', 'icon' => 'bi-info-circle', 'is_premium' => true],
            ['category' => 'Sports & Recreation', 'name' => 'Mini Golf', 'icon' => 'bi-circle', 'is_premium' => true],
            
            // Wellness & Fitness
            ['category' => 'Wellness & Fitness', 'name' => 'Yoga/Meditation Room', 'icon' => 'bi-circle', 'is_premium' => false],
            ['category' => 'Wellness & Fitness', 'name' => 'Spa', 'icon' => 'bi-heart', 'is_premium' => true],
            ['category' => 'Wellness & Fitness', 'name' => 'Sauna', 'icon' => 'bi-droplet', 'is_premium' => true],
            ['category' => 'Wellness & Fitness', 'name' => 'Steam Room', 'icon' => 'bi-moisture', 'is_premium' => true],
            ['category' => 'Wellness & Fitness', 'name' => 'Jacuzzi', 'icon' => 'bi-water', 'is_premium' => true],
            ['category' => 'Wellness & Fitness', 'name' => 'Aerobics Room', 'icon' => 'bi-activity', 'is_premium' => false],
            ['category' => 'Wellness & Fitness', 'name' => 'Zumba Studio', 'icon' => 'bi-music-note', 'is_premium' => false],
            
            // Clubhouse & Social
            ['category' => 'Clubhouse & Social', 'name' => 'Clubhouse', 'icon' => 'bi-building', 'is_premium' => false],
            ['category' => 'Clubhouse & Social', 'name' => 'Party Hall', 'icon' => 'bi-calendar-event', 'is_premium' => false],
            ['category' => 'Clubhouse & Social', 'name' => 'Banquet Hall', 'icon' => 'bi-building', 'is_premium' => true],
            ['category' => 'Clubhouse & Social', 'name' => 'Community Hall', 'icon' => 'bi-people', 'is_premium' => false],
            ['category' => 'Clubhouse & Social', 'name' => 'Library', 'icon' => 'bi-book', 'is_premium' => false],
            ['category' => 'Clubhouse & Social', 'name' => 'Reading Room', 'icon' => 'bi-book', 'is_premium' => false],
            ['category' => 'Clubhouse & Social', 'name' => 'Business Center', 'icon' => 'bi-briefcase', 'is_premium' => true],
            ['category' => 'Clubhouse & Social', 'name' => 'Conference Room', 'icon' => 'bi-display', 'is_premium' => true],
            ['category' => 'Clubhouse & Social', 'name' => 'Multipurpose Hall', 'icon' => 'bi-building', 'is_premium' => false],
            ['category' => 'Clubhouse & Social', 'name' => 'Mini Theater', 'icon' => 'bi-film', 'is_premium' => true],
            ['category' => 'Clubhouse & Social', 'name' => 'Cafeteria', 'icon' => 'bi-cup-hot', 'is_premium' => false],
            
            // Security & Safety
            ['category' => 'Security & Safety', 'name' => '24x7 Security', 'icon' => 'bi-shield-check', 'is_premium' => false],
            ['category' => 'Security & Safety', 'name' => 'CCTV Surveillance', 'icon' => 'bi-camera-video', 'is_premium' => false],
            ['category' => 'Security & Safety', 'name' => 'Gated Community', 'icon' => 'bi-shield-lock', 'is_premium' => false],
            ['category' => 'Security & Safety', 'name' => 'Video Door Phone', 'icon' => 'bi-phone', 'is_premium' => false],
            ['category' => 'Security & Safety', 'name' => 'Fire Safety', 'icon' => 'bi-fire', 'is_premium' => false],
            ['category' => 'Security & Safety', 'name' => 'Intercom Facility', 'icon' => 'bi-telephone', 'is_premium' => false],
            ['category' => 'Security & Safety', 'name' => 'Earthquake Resistant', 'icon' => 'bi-shield', 'is_premium' => true],
            ['category' => 'Security & Safety', 'name' => 'Security Cabin', 'icon' => 'bi-house', 'is_premium' => false],
            
            // Children & Family
            ['category' => 'Children & Family', 'name' => 'Kids Play Area', 'icon' => 'bi-balloon', 'is_premium' => false],
            ['category' => 'Children & Family', 'name' => 'Toddler Pool', 'icon' => 'bi-droplet', 'is_premium' => false],
            ['category' => 'Children & Family', 'name' => 'Creche/Day Care', 'icon' => 'bi-emoji-smile', 'is_premium' => false],
            ['category' => 'Children & Family', 'name' => 'Kids Activity Room', 'icon' => 'bi-palette', 'is_premium' => false],
            ['category' => 'Children & Family', 'name' => 'Sand Pit', 'icon' => 'bi-circle', 'is_premium' => false],
            ['category' => 'Children & Family', 'name' => 'Senior Citizen Park', 'icon' => 'bi-tree', 'is_premium' => false],
            ['category' => 'Children & Family', 'name' => 'Pet Park', 'icon' => 'bi-heart', 'is_premium' => false],
            
            // Convenience & Services
            ['category' => 'Convenience & Services', 'name' => 'Lift', 'icon' => 'bi-arrow-up-square', 'is_premium' => false],
            ['category' => 'Convenience & Services', 'name' => 'Power Backup', 'icon' => 'bi-lightning', 'is_premium' => false],
            ['category' => 'Convenience & Services', 'name' => 'Covered Parking', 'icon' => 'bi-car-front', 'is_premium' => false],
            ['category' => 'Convenience & Services', 'name' => 'Visitor Parking', 'icon' => 'bi-p-circle', 'is_premium' => false],
            ['category' => 'Convenience & Services', 'name' => 'Shopping Center', 'icon' => 'bi-shop', 'is_premium' => true],
            ['category' => 'Convenience & Services', 'name' => 'ATM', 'icon' => 'bi-cash-stack', 'is_premium' => false],
            ['category' => 'Convenience & Services', 'name' => 'Laundry Service', 'icon' => 'bi-droplet', 'is_premium' => true],
            ['category' => 'Convenience & Services', 'name' => 'Housekeeping', 'icon' => 'bi-house', 'is_premium' => true],
            ['category' => 'Convenience & Services', 'name' => 'Grocery Store', 'icon' => 'bi-basket', 'is_premium' => false],
            ['category' => 'Convenience & Services', 'name' => 'Medical Center', 'icon' => 'bi-hospital', 'is_premium' => false],
            ['category' => 'Convenience & Services', 'name' => 'Pharmacy', 'icon' => 'bi-prescription', 'is_premium' => false],
            
            // Green & Environment
            ['category' => 'Green & Environment', 'name' => 'Landscaped Garden', 'icon' => 'bi-flower1', 'is_premium' => false],
            ['category' => 'Green & Environment', 'name' => 'Rain Water Harvesting', 'icon' => 'bi-moisture', 'is_premium' => false],
            ['category' => 'Green & Environment', 'name' => 'Solar Panels', 'icon' => 'bi-sun', 'is_premium' => true],
            ['category' => 'Green & Environment', 'name' => 'Organic Waste Converter', 'icon' => 'bi-recycle', 'is_premium' => false],
            ['category' => 'Green & Environment', 'name' => 'Sewage Treatment Plant', 'icon' => 'bi-droplet', 'is_premium' => false],
            ['category' => 'Green & Environment', 'name' => 'Water Softener', 'icon' => 'bi-water', 'is_premium' => false],
            ['category' => 'Green & Environment', 'name' => 'Terrace Garden', 'icon' => 'bi-tree', 'is_premium' => false],
            ['category' => 'Green & Environment', 'name' => 'Flower Garden', 'icon' => 'bi-flower2', 'is_premium' => false],
            ['category' => 'Green & Environment', 'name' => 'Herbal Garden', 'icon' => 'bi-flower3', 'is_premium' => false],
            
            // Luxury & Premium
            ['category' => 'Luxury & Premium', 'name' => 'Concierge Service', 'icon' => 'bi-person-badge', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Valet Parking', 'icon' => 'bi-key', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Golf Course', 'icon' => 'bi-circle', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Helipad', 'icon' => 'bi-circle', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Private Lift Lobby', 'icon' => 'bi-arrow-up', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Sky Lounge', 'icon' => 'bi-cloud', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Infinity Pool', 'icon' => 'bi-water', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Wine Cellar', 'icon' => 'bi-cup', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Cigar Lounge', 'icon' => 'bi-square', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Private Cinema', 'icon' => 'bi-film', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Observatory Deck', 'icon' => 'bi-binoculars', 'is_premium' => true],
            ['category' => 'Luxury & Premium', 'name' => 'Art Gallery', 'icon' => 'bi-palette', 'is_premium' => true],
        ];

        foreach ($amenities as $amenity) {
            $category = AmenityCategory::where('name', $amenity['category'])->first();
            if ($category) {
                Amenity::create([
                    'category_id' => $category->id,
                    'name' => $amenity['name'],
                    'slug' => \Str::slug($amenity['name']),
                    'icon' => $amenity['icon'],
                    'is_premium' => $amenity['is_premium'],
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('✅ Amenity seeder completed: 8 categories + ' . count($amenities) . ' amenities created');
    }
}
