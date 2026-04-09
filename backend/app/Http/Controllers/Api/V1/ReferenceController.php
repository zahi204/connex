<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DocumentType;
use App\Enums\Skill;
use App\Enums\WorkArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ReferenceController extends BaseApiController
{
    private static array $supported = ['skills', 'work_areas', 'languages', 'document_types'];

    public function index(string $type): JsonResponse
    {
        if (! in_array($type, self::$supported, true)) {
            return $this->error("Unknown reference type '{$type}'.", 404);
        }

        $data = Cache::remember("reference.{$type}", 86400, fn () => $this->build($type));

        return $this->success($data);
    }

    private function build(string $type): array
    {
        return match ($type) {
            'skills' => array_map(
                fn (Skill $s) => ['value' => $s->value, 'label' => $s->label()],
                Skill::cases()
            ),
            'work_areas' => array_map(
                fn (WorkArea $w) => ['value' => $w->value, 'label' => $w->label()],
                WorkArea::cases()
            ),
            'languages' => [
                ['value' => 'hebrew', 'label' => 'Hebrew'],
                ['value' => 'arabic', 'label' => 'Arabic'],
                ['value' => 'english', 'label' => 'English'],
                ['value' => 'russian', 'label' => 'Russian'],
                ['value' => 'amharic', 'label' => 'Amharic'],
                ['value' => 'tigrinya', 'label' => 'Tigrinya'],
                ['value' => 'french', 'label' => 'French'],
                ['value' => 'romanian', 'label' => 'Romanian'],
                ['value' => 'other', 'label' => 'Other'],
            ],
            'document_types' => array_map(
                fn (DocumentType $d) => ['value' => $d->value, 'label' => $d->label()],
                DocumentType::cases()
            ),
            default => [],
        };
    }
}
