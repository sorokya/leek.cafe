<?php

declare(strict_types=1);

namespace App\Services;

use App\ImageRole;
use App\Models\Content;
use App\Models\Image;
use Illuminate\Support\Facades\DB;

final class InlineImageSyncer
{
    public function sync(Content $content): void
    {
        $imageHashes = $this->extractImageHashes($content);
        DB::transaction(function () use ($content, $imageHashes): void {
            $imageIds = Image::query()
                ->where(function ($q) use ($imageHashes): void {
                    foreach ($imageHashes as $prefix) {
                        $q->orWhere('hash', 'like', $prefix . '%');
                    }
                })
                ->pluck('id')
                ->all();

            $existing = $content->inlineImages()
                ->pluck('images.id')
                ->all();

            $toDetach = array_diff($existing, $imageIds);
            $toAttach = array_diff($imageIds, $existing);

            if ($toDetach !== []) {
                $content->images()
                    ->wherePivot('role', ImageRole::INLINE->value)
                    ->detach($toDetach);
            }

            if ($toAttach !== []) {
                $content->inlineImages()->attach(
                    array_fill_keys(
                        $toAttach,
                        ['role' => ImageRole::INLINE->value]
                    )
                );
            }
        });
    }

    /** Extract image hashes from markdown content.
     * @return array<int, string>
     */
    private function extractImageHashes(Content $content): array
    {
        if (!$content->body) {
            return [];
        }

        preg_match_all('/@img:([a-f0-9]+)/i', (string) $content->body, $matches);

        return array_unique($matches[1]);
    }
}
