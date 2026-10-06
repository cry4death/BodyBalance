<?php

namespace App\View\Components\Seo;

use App\Domain\Seo\SeoData;
use App\Domain\Seo\SeoResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

/**
 * <head> tags for the current page: <x-seo.meta :seo="$seo" />.
 * Without :seo it resolves SEO of the current route from the admin panel / config('seo.pages').
 */
class Meta extends Component
{
    public SeoData $seo;

    public string $canonical;

    public function __construct(SeoResolver $resolver, ?SeoData $seo = null)
    {
        $this->seo = $seo ?? $resolver->forRoute(Route::currentRouteName());
        $this->canonical = $this->seo->canonical ?? url()->current();
    }

    public function render(): View
    {
        return view('components.seo.meta');
    }
}
