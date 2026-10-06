<?php

namespace App\Console\Commands;

use App\Domain\Seo\Contracts\InSitemap;
use App\Domain\Seo\Contracts\Seoable;
use App\Domain\Seo\SeoResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'seo:sitemap';

    protected $description = 'Generate sitemap.xml from fixed pages and sitemap models (pages marked noindex are skipped)';

    public function handle(SeoResolver $resolver): int
    {
        $sitemap = Sitemap::create();

        foreach (config('seo.sitemap.routes') as $routeName) {
            if (Route::has($routeName) && ! $resolver->forRoute($routeName)->noindex) {
                $sitemap->add(Url::create(route($routeName)));
            }
        }

        /** @var class-string<InSitemap> $modelClass */
        foreach (config('seo.sitemap.models') as $modelClass) {
            foreach ($modelClass::sitemapQuery()->lazy() as $model) {
                if ($model instanceof Seoable && $resolver->forModel($model)->noindex) {
                    continue;
                }

                $sitemap->add($model);
            }
        }

        $path = config('seo.sitemap.path');
        $sitemap->writeToFile($path);

        $this->info(count($sitemap->getTags())." URLs written to {$path}");

        return self::SUCCESS;
    }
}
