<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryHierarchySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Parent Categories (Level 0)
        $mensFashion = Category::updateOrCreate(
            ['slug' => 'mens-fashion'],
            [
                'parent_id' => null,
                'name' => "Men's Fashion",
                'name_bn' => 'পুরুষদের ফ্যাশন',
                'image' => 'assets/category-men.jpg',
                'icon' => 'fa-solid fa-shirt',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $womensFashion = Category::updateOrCreate(
            ['slug' => 'womens-fashion'],
            [
                'parent_id' => null,
                'name' => "Women's Fashion",
                'name_bn' => 'নারীদের ফ্যাশন',
                'image' => 'assets/category-women.jpg',
                'icon' => 'fa-solid fa-person-dress',
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $others = Category::updateOrCreate(
            ['slug' => 'others-accessories'],
            [
                'parent_id' => null,
                'name' => 'Others & Accessories',
                'name_bn' => 'অন্যান্য ও এক্সেসরিজ',
                'image' => 'assets/category-accessories.jpg',
                'icon' => 'fa-solid fa-gem',
                'sort_order' => 3,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        // 2. Subcategories for Men's Fashion (Level 1)
        $mensPanjabiEthnic = Category::updateOrCreate(
            ['slug' => 'mens-panjabi-ethnic'],
            [
                'parent_id' => $mensFashion->id,
                'name' => 'Panjabi & Ethnic',
                'name_bn' => 'পাঞ্জাবি ও এথনিক',
                'image' => 'assets/category-men.jpg',
                'icon' => 'fa-solid fa-vest',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $mensPants = Category::updateOrCreate(
            ['slug' => 'mens-pants-trousers'],
            [
                'parent_id' => $mensFashion->id,
                'name' => 'Pants & Trousers',
                'name_bn' => 'প্যান্ট ও ট্রাউজার',
                'image' => 'assets/category-men.jpg',
                'icon' => 'fa-solid fa-vest-patches',
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $mensShirts = Category::updateOrCreate(
            ['slug' => 'mens-shirts-tops'],
            [
                'parent_id' => $mensFashion->id,
                'name' => 'Shirts & Tops',
                'name_bn' => 'শার্ট ও টপস',
                'image' => 'assets/category-men.jpg',
                'icon' => 'fa-solid fa-shirt',
                'sort_order' => 3,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        // 3. Child Categories for Men's Fashion (Level 2)
        $festivePanjabi = Category::updateOrCreate(
            ['slug' => 'festive-panjabi'],
            [
                'parent_id' => $mensPanjabiEthnic->id,
                'name' => 'Festive Panjabi',
                'name_bn' => 'উৎসব পাঞ্জাবি',
                'image' => 'assets/category-men.jpg',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $kabliSuits = Category::updateOrCreate(
            ['slug' => 'kabli-suits-sets'],
            [
                'parent_id' => $mensPanjabiEthnic->id,
                'name' => 'Kabli Suits & Sets',
                'name_bn' => 'কাবলি স্যুট সেট',
                'image' => 'assets/category-men.jpg',
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $cargoPants = Category::updateOrCreate(
            ['slug' => 'cargo-trousers'],
            [
                'parent_id' => $mensPants->id,
                'name' => 'Twel Stitch & Cargo Pants',
                'name_bn' => 'টুইল কার্গো ও ট্রাউজার',
                'image' => 'assets/category-men.jpg',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $chinos = Category::updateOrCreate(
            ['slug' => 'chinos-casual-pants'],
            [
                'parent_id' => $mensPants->id,
                'name' => 'Chinos & Casual Pants',
                'name_bn' => 'চিনোস ও ক্যাজুয়াল প্যান্ট',
                'image' => 'assets/category-men.jpg',
                'sort_order' => 2,
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        $casualShirts = Category::updateOrCreate(
            ['slug' => 'casual-formal-shirts'],
            [
                'parent_id' => $mensShirts->id,
                'name' => 'Casual & Formal Shirts',
                'name_bn' => 'ক্যাজুয়াল ও ফর্মাল শার্ট',
                'image' => 'assets/category-men.jpg',
                'sort_order' => 1,
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        $sweaters = Category::updateOrCreate(
            ['slug' => 'sweaters-winterwear'],
            [
                'parent_id' => $mensShirts->id,
                'name' => 'Sweaters & Winterwear',
                'name_bn' => 'সোয়েটার ও শীতের পোশাক',
                'image' => 'assets/category-men.jpg',
                'sort_order' => 2,
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        // 4. Subcategories for Women's Fashion (Level 1)
        $womensEthnic = Category::updateOrCreate(
            ['slug' => 'womens-ethnic'],
            [
                'parent_id' => $womensFashion->id,
                'name' => "Women's Ethnic & Fusion",
                'name_bn' => 'এথনিক ও ফিউশন',
                'image' => 'assets/category-women.jpg',
                'icon' => 'fa-solid fa-wand-magic-sparkles',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $womensSarees = Category::updateOrCreate(
            ['slug' => 'sarees-traditional'],
            [
                'parent_id' => $womensFashion->id,
                'name' => 'Sarees & Traditional',
                'name_bn' => 'শাড়ি ও ট্র্যাডিশনাল',
                'image' => 'assets/category-women.jpg',
                'icon' => 'fa-solid fa-ribbon',
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        // 5. Child Categories for Women's Fashion (Level 2)
        $kurtis = Category::updateOrCreate(
            ['slug' => 'kurtis-tunics'],
            [
                'parent_id' => $womensEthnic->id,
                'name' => 'Kurtis & Tunics',
                'name_bn' => 'কুর্তি ও টিউনিক',
                'image' => 'assets/category-women.jpg',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $threePiece = Category::updateOrCreate(
            ['slug' => 'salwar-kameez-3piece'],
            [
                'parent_id' => $womensEthnic->id,
                'name' => 'Salwar Kameez & 3-Piece',
                'name_bn' => 'সালোয়ার কামিজ ও থ্রি-পিস',
                'image' => 'assets/category-women.jpg',
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $jamdaniSarees = Category::updateOrCreate(
            ['slug' => 'jamdani-silk-sarees'],
            [
                'parent_id' => $womensSarees->id,
                'name' => 'Jamdani & Silk Sarees',
                'name_bn' => 'জামদানি ও সিল্ক শাড়ি',
                'image' => 'assets/category-women.jpg',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $partySarees = Category::updateOrCreate(
            ['slug' => 'party-festive-sarees'],
            [
                'parent_id' => $womensSarees->id,
                'name' => 'Party & Festive Sarees',
                'name_bn' => 'পার্টি ও উৎসব শাড়ি',
                'image' => 'assets/category-women.jpg',
                'sort_order' => 2,
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        // 6. Subcategories & Children for Others & Accessories
        $accessoriesSub = Category::updateOrCreate(
            ['slug' => 'artisanal-accessories'],
            [
                'parent_id' => $others->id,
                'name' => 'Artisanal Accessories',
                'name_bn' => 'কারুশিল্প এক্সেসরিজ',
                'image' => 'assets/category-accessories.jpg',
                'icon' => 'fa-solid fa-gem',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        $wallets = Category::updateOrCreate(
            ['slug' => 'wallets-belts'],
            [
                'parent_id' => $accessoriesSub->id,
                'name' => 'Wallets & Belts',
                'name_bn' => 'ওয়ালেট ও বেল্ট',
                'image' => 'assets/category-accessories.jpg',
                'sort_order' => 1,
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        $bags = Category::updateOrCreate(
            ['slug' => 'bags-essentials'],
            [
                'parent_id' => $accessoriesSub->id,
                'name' => 'Bags & Essentials',
                'name_bn' => 'ব্যাগ ও এসেনশিয়াল',
                'image' => 'assets/category-accessories.jpg',
                'sort_order' => 2,
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        // 7. Remap existing products to appropriate categories
        // Saree -> Jamdani & Silk Sarees
        Product::where('title', 'like', '%Jamdani%')
            ->orWhere('title', 'like', '%Saree%')
            ->update(['category_id' => $jamdaniSarees->id]);

        // Panjabi -> Festive Panjabi
        Product::where('title', 'like', '%Panjabi%')
            ->update(['category_id' => $festivePanjabi->id]);

        // Kurti -> Kurtis & Tunics
        Product::where('title', 'like', '%Kurti%')
            ->update(['category_id' => $kurtis->id]);

        // Kabli -> Kabli Suits & Sets
        Product::where('title', 'like', '%Kabli%')
            ->update(['category_id' => $kabliSuits->id]);

        // Cargo -> Twel Stitch & Cargo Pants
        Product::where('title', 'like', '%Cargo%')
            ->update(['category_id' => $cargoPants->id]);

        // Sweater -> Sweaters & Winterwear
        Product::where('title', 'like', '%Sweater%')
            ->update(['category_id' => $sweaters->id]);
    }
}
