<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@yanasfashion.com'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('admin123'),
            ]
        );

        // Store Settings
        $settings = [
            'site_name' => "Yanas Fashion",
            'tagline' => 'Bangladeshi Luxury & Contemporary Ethnic Wear',
            'hotline' => '01713580400',
            'whatsapp_number' => '8801713580400',
            'email' => 'support@yanasfashion.com',
            'address' => 'House 42, Road 11, Block D, Banani, Dhaka-1213, Bangladesh',
            'announcement_bar' => '✨ Free Express Delivery in Dhaka on Orders Over ৳3,000 | 🚚 Nationwide COD Across All 64 Districts | 💳 bKash, Nagad Available',
            'inside_dhaka_charge' => '70',
            'suburbs_charge' => '100',
            'outside_dhaka_charge' => '130',
            'free_shipping_threshold' => '3000',
            'currency_symbol' => '৳',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'tiktok_url' => 'https://tiktok.com',
        ];

        foreach ($settings as $k => $v) {
            Setting::updateOrCreate(['key' => $k], ['value' => $v]);
        }

        // Coupons
        Coupon::updateOrCreate(['code' => 'YANA10'], [
            'type' => 'percent',
            'value' => 10,
            'min_order' => 1500,
            'is_active' => true,
        ]);

        Coupon::updateOrCreate(['code' => 'EID200'], [
            'type' => 'fixed',
            'value' => 200,
            'min_order' => 2500,
            'is_active' => true,
        ]);

        // Categories
        $catMen = Category::updateOrCreate(['slug' => 'mens-fashion'], [
            'name' => "Men's Collection",
            'name_bn' => 'মেনস ফ্যাশন',
            'image' => '/assets/category-men.jpg',
            'icon' => 'fa-solid fa-shirt',
            'description' => 'Festive Panjabis, Tailored Kablis, Twel-Stitch Cargo Trousers & PK Polos',
            'sort_order' => 1,
            'is_featured' => true,
        ]);

        $catWomen = Category::updateOrCreate(['slug' => 'womens-ethnic'], [
            'name' => "Women's Ethnic & Fusion",
            'name_bn' => 'ওমেন্স এথনিক ও ফিউশন',
            'image' => '/assets/category-women.jpg',
            'icon' => 'fa-solid fa-person-dress',
            'description' => 'Dhakai Jamdani Sarees, Pure Silk Kurtis & Designer 3-Piece Sets',
            'sort_order' => 2,
            'is_featured' => true,
        ]);

        $catPanjabi = Category::updateOrCreate(['slug' => 'festive-panjabi'], [
            'name' => 'Festive Panjabi',
            'name_bn' => 'ফেস্টিভ পাঞ্জাবি',
            'image' => '/assets/product-embroidered-panjabi.jpg',
            'icon' => 'fa-solid fa-vest',
            'description' => 'Embroidered Cotton, Silk, and Jacquard Panjabis for Eid and Occasions',
            'sort_order' => 3,
            'is_featured' => true,
        ]);

        $catTrousers = Category::updateOrCreate(['slug' => 'cargo-trousers'], [
            'name' => 'Twel Stitch & Cargo Pants',
            'name_bn' => 'কার্গো ও ট্রাউজার',
            'image' => '/assets/product-trousers.jpg',
            'icon' => 'fa-solid fa-socks',
            'description' => 'Heavy Twill Stretch Cargo Pants, Baggy Trousers & Casual Chinos',
            'sort_order' => 4,
            'is_featured' => true,
        ]);

        $catAccessories = Category::updateOrCreate(['slug' => 'artisanal-accessories'], [
            'name' => 'Artisanal Accessories',
            'name_bn' => 'হ্যান্ডক্রাফটেড অ্যাক্সেসরিজ',
            'image' => '/assets/category-accessories.jpg',
            'icon' => 'fa-solid fa-gem',
            'description' => 'Handcrafted Clutches, Embroidered Mojaris & Silver Filigree Jewelry',
            'sort_order' => 5,
            'is_featured' => true,
        ]);

        // Products
        $products = [
            [
                'category_id' => $catWomen->id,
                'title' => 'Heritage Dhakai Jamdani Saree',
                'title_bn' => 'ঐতিহ্যবাহী ঢাকাই জামদানি শাড়ি',
                'slug' => 'heritage-dhakai-jamdani-saree',
                'sku' => 'YF-JMD-01',
                'regular_price' => 14500,
                'sale_price' => 12500,
                'stock_qty' => 15,
                'thumbnail' => '/assets/product-jamdani-saree.jpg',
                'gallery' => ['/assets/product-jamdani-saree.jpg', '/assets/hero.jpg', '/assets/feature-sustainable.jpg'],
                'sizes' => ['Regular (12 Haat)'],
                'colors' => ['Midnight Plum & Zari Gold', 'Crimson Red', 'Emerald Green'],
                'short_desc' => 'Handcrafted on wooden handlooms in Narayanganj using finest 84-count Egyptian combed cotton yarn.',
                'description' => '<p>An extraordinary collector masterpiece embodying the pinnacle of Bangladeshi handloom heritage. Each floral jaal motif is meticulously hand-inserted thread by thread by generational weavers.</p><ul><li><strong>Fabric:</strong> 84-count Pure Cotton Handloom</li><li><strong>Weave Technique:</strong> Authentic Dhakai Supplementary Weft</li><li><strong>Occasion:</strong> Weddings, Pohela Boishakh, Receptions</li><li><strong>Care:</strong> Dry Clean Only</li></ul>',
                'size_chart_html' => '<table class="size-table"><thead><tr><th>Measurement</th><th>Standard Size</th></tr></thead><tbody><tr><td>Length</td><td>12 Haat (approx. 5.5 meters)</td></tr><tr><td>Width</td><td>46 inches</td></tr><tr><td>Blouse Piece</td><td>Included (80 cm unstitched)</td></tr></tbody></table>',
                'badge' => 'Handloom Masterpiece',
                'rating' => 5.0,
                'reviews_count' => 38,
                'is_featured' => true,
                'is_trending' => true,
            ],
            [
                'category_id' => $catPanjabi->id,
                'title' => 'Royal Embroidered Cotton Panjabi',
                'title_bn' => 'রয়্যাল এমব্রয়ডারি কটন পাঞ্জাবি',
                'slug' => 'royal-embroidered-cotton-panjabi',
                'sku' => 'YF-PNJ-02',
                'regular_price' => 4500,
                'sale_price' => 3850,
                'stock_qty' => 30,
                'thumbnail' => '/assets/product-embroidered-panjabi.jpg',
                'gallery' => ['/assets/product-embroidered-panjabi.jpg', '/assets/product-tailored-kabli.jpg'],
                'sizes' => ['M (38)', 'L (40)', 'XL (42)', 'XXL (44)'],
                'colors' => ['Deep Burgundy', 'Navy Blue', 'Off White'],
                'short_desc' => 'Premium combed cotton with subtle metallic thread embroidery on placket, mandarin collar, and cuffs.',
                'description' => '<p>Crafted for distinguished festive and Eid celebrations. Breathable ultra-soft fabric tailored with modern slim-fit comfort.</p><ul><li><strong>Material:</strong> 100% Fine Combed Cotton</li><li><strong>Buttons:</strong> Engraved Metallic Snap Buttons</li><li><strong>Fit:</strong> Semi-Slim Contemporary Fit</li></ul>',
                'size_chart_html' => '<table class="size-table"><thead><tr><th>Size</th><th>Chest</th><th>Length</th><th>Sleeve</th></tr></thead><tbody><tr><td>M (38)</td><td>40"</td><td>40"</td><td>24.5"</td></tr><tr><td>L (40)</td><td>42"</td><td>42"</td><td>25"</td></tr><tr><td>XL (42)</td><td>44"</td><td>44"</td><td>25.5"</td></tr><tr><td>XXL (44)</td><td>46"</td><td>45"</td><td>26"</td></tr></tbody></table>',
                'badge' => 'Festive Offer - ৳650 OFF',
                'rating' => 4.9,
                'reviews_count' => 54,
                'is_featured' => true,
                'is_trending' => true,
            ],
            [
                'category_id' => $catWomen->id,
                'title' => 'Silk Fusion Kurti & Trouser Set',
                'title_bn' => 'সিল্ক ফিউশন কুর্তি ও ট্রাউজার সেট',
                'slug' => 'silk-fusion-kurti-trouser-set',
                'sku' => 'YF-KRT-03',
                'regular_price' => 6200,
                'sale_price' => 5400,
                'stock_qty' => 20,
                'thumbnail' => '/assets/product-silk-kurti.jpg',
                'gallery' => ['/assets/product-silk-kurti.jpg', '/assets/product-silk-dress.jpg'],
                'sizes' => ['36 (S)', '38 (M)', '40 (L)', '42 (XL)'],
                'colors' => ['Dusty Rose', 'Champagne Gold', 'Sage Green'],
                'short_desc' => 'Two-piece pure Rajshahi silk kurti with pearl-work neckline paired with tapered cigarette trousers.',
                'description' => '<p>Contemporary silhouette merging timeless Bangladeshi silk weaving with modern cutwork trousers.</p>',
                'size_chart_html' => '<table class="size-table"><thead><tr><th>Size</th><th>Bust</th><th>Kurti Length</th><th>Waist (Elastic)</th></tr></thead><tbody><tr><td>36 (S)</td><td>36"</td><td>42"</td><td>28"-32"</td></tr><tr><td>38 (M)</td><td>38"</td><td>43"</td><td>30"-34"</td></tr><tr><td>40 (L)</td><td>40"</td><td>44"</td><td>32"-36"</td></tr><tr><td>42 (XL)</td><td>42"</td><td>44"</td><td>34"-38"</td></tr></tbody></table>',
                'badge' => 'New In',
                'rating' => 4.8,
                'reviews_count' => 22,
                'is_featured' => true,
                'is_trending' => false,
            ],
            [
                'category_id' => $catMen->id,
                'title' => 'Tailored Semi-Fitted Kabli Suit',
                'title_bn' => 'টেইলর্ড সেমি-ফিটেড কাবলি স্যুট',
                'slug' => 'tailored-semi-fitted-kabli-suit',
                'sku' => 'YF-KBL-04',
                'regular_price' => 5800,
                'sale_price' => 4950,
                'stock_qty' => 25,
                'thumbnail' => '/assets/product-tailored-kabli.jpg',
                'gallery' => ['/assets/product-tailored-kabli.jpg', '/assets/product-embroidered-panjabi.jpg'],
                'sizes' => ['M (38)', 'L (40)', 'XL (42)', 'XXL (44)'],
                'colors' => ['Midnight Charcoal', 'Desert Tan', 'Jet Black'],
                'short_desc' => 'Complete 2-piece Kabli kurti with matching loose pajama tailored in structured wrinkle-resistant cotton blend.',
                'description' => '<p>The iconic Kabli suit reinvented with tailored modern shoulders, structured collar, and functional flap pockets.</p>',
                'size_chart_html' => '<table class="size-table"><thead><tr><th>Size</th><th>Chest</th><th>Length</th><th>Pajama Length</th></tr></thead><tbody><tr><td>M (38)</td><td>41"</td><td>41"</td><td>39"</td></tr><tr><td>L (40)</td><td>43"</td><td>43"</td><td>40"</td></tr><tr><td>XL (42)</td><td>45"</td><td>45"</td><td>41"</td></tr><tr><td>XXL (44)</td><td>47"</td><td>46"</td><td>42"</td></tr></tbody></table>',
                'badge' => 'Exclusive Drop',
                'rating' => 5.0,
                'reviews_count' => 47,
                'is_featured' => true,
                'is_trending' => true,
            ],
            [
                'category_id' => $catTrousers->id,
                'title' => 'Twel Stitch 6-Pocket Tactical Cargo Pant',
                'title_bn' => 'টুয়েল স্টিচ ৬-পকেট কার্গো প্যান্ট',
                'slug' => 'twel-stitch-6-pocket-cargo-pant',
                'sku' => 'YF-CRG-05',
                'regular_price' => 2250,
                'sale_price' => 1750,
                'stock_qty' => 40,
                'thumbnail' => '/assets/product-trousers.jpg',
                'gallery' => ['/assets/product-trousers.jpg'],
                'sizes' => ['30', '32', '34', '36', '38'],
                'colors' => ['Olive Green', 'Jet Black', 'Khaki Tan', 'Ash Grey'],
                'short_desc' => 'Heavy twill stretch fabric with reinforced double stitching, multi-cargo utility pockets, and elastic jogger cuff.',
                'description' => '<p>Inspired by contemporary streetwear. Extremely durable, comfortable with 3% spandex stretch for maximum agility.</p>',
                'size_chart_html' => '<table class="size-table"><thead><tr><th>Waist</th><th>Hip</th><th>Length</th><th>Thigh</th></tr></thead><tbody><tr><td>30</td><td>38"</td><td>39"</td><td>23"</td></tr><tr><td>32</td><td>40"</td><td>40"</td><td>24"</td></tr><tr><td>34</td><td>42"</td><td>40.5"</td><td>25"</td></tr><tr><td>36</td><td>44"</td><td>41"</td><td>26"</td></tr><tr><td>38</td><td>46"</td><td>41.5"</td><td>27"</td></tr></tbody></table>',
                'badge' => 'Best Seller - 22% OFF',
                'rating' => 4.9,
                'reviews_count' => 86,
                'is_featured' => true,
                'is_trending' => true,
            ],
            [
                'category_id' => $catMen->id,
                'title' => 'Fine Knit Cashmere-Touch Sweater',
                'title_bn' => 'ফাইন নিট ক্যাশমিয়ার-টাচ সোয়েটার',
                'slug' => 'fine-knit-cashmere-touch-sweater',
                'sku' => 'YF-SWT-06',
                'regular_price' => 3200,
                'sale_price' => 2650,
                'stock_qty' => 18,
                'thumbnail' => '/assets/product-knit-sweater.jpg',
                'gallery' => ['/assets/product-knit-sweater.jpg', '/assets/product-wool-coat.jpg'],
                'sizes' => ['M', 'L', 'XL'],
                'colors' => ['Camel Heather', 'Oatmeal Beige', 'Charcoal'],
                'short_desc' => 'Lightweight insulating knit sweater with ribbed mock collar for winter warmth and sophisticated layering.',
                'description' => '<p>Ultra-fine gauge knit engineered for the Bangladeshi winter season. Non-scratchy, breathable, and luxurious to the touch.</p>',
                'size_chart_html' => '<table class="size-table"><thead><tr><th>Size</th><th>Chest</th><th>Length</th></tr></thead><tbody><tr><td>M</td><td>39"</td><td>27"</td></tr><tr><td>L</td><td>41"</td><td>28"</td></tr><tr><td>XL</td><td>43"</td><td>29"</td></tr></tbody></table>',
                'badge' => 'Winter Special',
                'rating' => 4.7,
                'reviews_count' => 19,
                'is_featured' => false,
                'is_trending' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Sample Completed / Active Orders for Admin
        $order1 = Order::updateOrCreate(['order_number' => 'YF-91024'], [
            'customer_name' => 'Tanvir Ahmed',
            'customer_phone' => '01711223344',
            'customer_address' => 'House 14, Road 5, Dhanmondi R/A, Dhaka-1205',
            'customer_note' => 'Please deliver before 5 PM',
            'delivery_zone' => 'inside_dhaka',
            'delivery_charge' => 70.00,
            'subtotal' => 3850.00,
            'discount' => 200.00,
            'total_amount' => 3720.00,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'processing',
        ]);

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'product_name' => 'Royal Embroidered Cotton Panjabi'],
            [
                'product_id' => Product::where('slug', 'royal-embroidered-cotton-panjabi')->value('id'),
                'product_thumbnail' => '/assets/product-embroidered-panjabi.jpg',
                'size' => 'L (40)',
                'unit_price' => 3850.00,
                'quantity' => 1,
                'total_price' => 3850.00,
            ]
        );

        $order2 = Order::updateOrCreate(['order_number' => 'YF-91025'], [
            'customer_name' => 'Nusrat Jahan',
            'customer_phone' => '01819876543',
            'customer_address' => 'Nasirabad Housing Society, GEC Circle, Chattogram',
            'customer_note' => 'Call before arrival',
            'delivery_zone' => 'outside_dhaka',
            'delivery_charge' => 130.00,
            'subtotal' => 12500.00,
            'discount' => 0.00,
            'total_amount' => 12630.00,
            'payment_method' => 'bkash',
            'payment_status' => 'paid',
            'order_status' => 'shipped',
        ]);

        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'product_name' => 'Heritage Dhakai Jamdani Saree'],
            [
                'product_id' => Product::where('slug', 'heritage-dhakai-jamdani-saree')->value('id'),
                'product_thumbnail' => '/assets/product-jamdani-saree.jpg',
                'size' => 'Regular (12 Haat)',
                'unit_price' => 12500.00,
                'quantity' => 1,
                'total_price' => 12500.00,
            ]
        );
    }
}

