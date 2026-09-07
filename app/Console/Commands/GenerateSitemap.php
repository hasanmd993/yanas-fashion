<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate luxury dynamic sitemap.xml for Yana\'s Fashion Google SEO';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap.xml for Yana\'s Fashion...');

        $sitemap = Sitemap::create();

        // 1. Core Static Pages
        $sitemap->add(
            Url::create(route('home'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        $sitemap->add(
            Url::create(route('shop.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9)
        );

        $sitemap->add(
            Url::create(route('tracking.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.6)
        );

        $sitemap->add(
            Url::create(route('cart.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.4)
        );

        $sitemap->add(
            Url::create(route('checkout.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.4)
        );

        // 2. Dynamic Category Pages
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $sitemap->add(
                Url::create(url('/shop?category=' . $category->slug))
                    ->setLastModificationDate($category->updated_at ?? now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
        }
        $this->info("Added {$categories->count()} categories to sitemap.");

        // 3. Dynamic Product Pages with SEO Images
        $products = Product::where('is_active', true)->get();
        foreach ($products as $product) {
            $productUrl = Url::create(route('product.show', $product->slug))
                ->setLastModificationDate($product->updated_at ?? now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9);

            if ($product->thumbnail) {
                $productUrl->addImage(asset($product->thumbnail), $product->title);
            }

            $sitemap->add($productUrl);
        }
        $this->info("Added {$products->count()} products with images to sitemap.");

        // 4. Save to public/sitemap.xml
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap successfully written to public/sitemap.xml!');

        return Command::SUCCESS;
    }
}

