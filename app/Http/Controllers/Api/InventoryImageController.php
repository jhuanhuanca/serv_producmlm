<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryImageRequest;
use App\Http\Resources\InventoryImageResource;
use App\Models\InventoryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class InventoryImageController extends Controller
{
    public function store(StoreInventoryImageRequest $request): JsonResponse
    {
        $file = $request->file('file');
        if (! $file instanceof UploadedFile) {
            return response()->json(['message' => 'Adjunta una imagen.'], 422);
        }

        $bytes = file_get_contents($file->getRealPath() ?: $file->getPathname());
        if ($bytes === false || $bytes === '') {
            return response()->json(['message' => 'No se pudo leer el archivo.'], 422);
        }

        $image = InventoryImage::query()->create([
            'uuid' => (string) Str::uuid(),
            'company_id' => $request->integer('company_id') ?: null,
            'owner_key' => $request->filled('owner_key') ? $request->string('owner_key')->toString() : null,
            'kind' => $request->string('kind')->toString(),
            'original_name' => Str::limit($file->getClientOriginalName() ?: 'imagen.jpg', 240, ''),
            'mime' => $file->getMimeType() ?: 'image/jpeg',
            'size' => strlen($bytes),
            'content' => $bytes,
        ]);

        return (new InventoryImageResource($image))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $uuid): Response
    {
        $image = InventoryImage::query()->where('uuid', $uuid)->firstOrFail();

        return response($image->content, 200, [
            'Content-Type' => $image->mime ?: 'application/octet-stream',
            'Content-Length' => (string) $image->size,
            'Content-Disposition' => 'inline; filename="'.addslashes($image->original_name).'"',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
