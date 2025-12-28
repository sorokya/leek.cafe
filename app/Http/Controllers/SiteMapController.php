<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Visibility;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SiteMapController extends Controller
{
    public function __invoke(): Response
    {
        return Cache::remember('sitemap.xml', now()->addHours(6), function () {
            $sitemap = Sitemap::create();

            $sitemap->add(Url::create('/'));
            $sitemap->add(Url::create('/posts'));
            $sitemap->add(Url::create('/login'));

            Content::query()
                ->whereHas('post')
                ->where('visibility', Visibility::PUBLIC->value)
                ->chunk(100, function ($contents) use ($sitemap) {
                    foreach ($contents as $content) {
                        $lastMod = $content->updated_at ?? $content->created_at;
                        if ($lastMod === null) {
                            continue;
                        }

                        $sitemap->add(
                            Url::create("/posts/{$content->slug}")
                                ->setLastModificationDate($lastMod)
                        );
                    }
                });

            Content::query()
                ->whereHas('project')
                ->where('visibility', Visibility::PUBLIC->value)
                ->chunk(100, function ($contents) use ($sitemap) {
                    foreach ($contents as $content) {
                        $lastMod = $content->updated_at ?? $content->created_at;
                        if ($lastMod === null) {
                            continue;
                        }

                        $sitemap->add(
                            Url::create("/projects/{$content->slug}")
                                ->setLastModificationDate($lastMod)
                        );
                    }
                });


            return $sitemap->toResponse(request());
        });
    }
}
