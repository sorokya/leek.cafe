<?php

declare(strict_types=1);

namespace App\Services;

use App\ImageRole;
use App\Models\Content;
use App\Models\Image;
use Illuminate\Support\Facades\DB;

final class EmbedImageSyncer
{
    public function sync(Content $content, ?string $embeds): void
    {
        $hashes = $this->parseEmbeds($embeds);

        DB::transaction(function () use ($content, $hashes): void {
            $imageIds = $this->resolveImageIds($hashes);

            $existing = $content->embedImages()
                ->pluck('images.id')
                ->all();

            $toDetach = array_diff($existing, $imageIds);
            $toAttach = array_diff($imageIds, $existing);

            if ($toDetach !== []) {
                $content->images()
                    ->wherePivot('role', ImageRole::EMBED->value)
                    ->detach($toDetach);
            }

            if ($toAttach !== []) {
                $content->embedImages()->attach(
                    array_fill_keys(
                        $toAttach,
                        ['role' => ImageRole::EMBED->value],
                    ),
                );
            }
        });
    }

    /**
     * @return array<int, string>
     */
    private function parseEmbeds(?string $embeds): array
    {
        if (! is_string($embeds) || trim($embeds) === '') {
            return [];
        }

        $parts = array_map(trim(...), explode(',', $embeds));
        $parts = array_filter($parts, static fn (string $h): bool => $h !== '');

        return array_values(array_unique($parts));
    }

    /**
     * @param array<int, string> $hashPrefixes
     *
     * @return array<int, int>
     */
    private function resolveImageIds(array $hashPrefixes): array
    {
        if ($hashPrefixes === []) {
            return [];
        }

        /** @var array<int, int> $ids */
        $ids = Image::query()
            ->where(function ($q) use ($hashPrefixes): void {
                foreach ($hashPrefixes as $prefix) {
                    $q->orWhere('hash', 'like', $prefix . '%');
                }
            })
            ->pluck('id')
            ->all();

        return $ids;
    }
}
