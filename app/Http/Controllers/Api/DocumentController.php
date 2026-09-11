<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Company;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentController extends Controller
{
    public function index(Request $request, int $companyId): AnonymousResourceCollection
    {
        Company::query()->findOrFail($companyId);

        $query = Document::query()
            ->where('company_id', $companyId)
            ->orderBy('sort_order')
            ->orderByDesc('id');

        $type = $this->fileType($request);

        if ($type !== null) {
            $query->where('file_type', $type);
        } else {
            $query->where('file_type', '!=', Document::TYPE_VOUCHER);
        }

        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        $perPage = min(100, max(1, $request->integer('per_page', 20)));

        return DocumentResource::collection($query->paginate($perPage));
    }

    public function store(StoreDocumentRequest $request, int $companyId): JsonResponse
    {
        Company::query()->findOrFail($companyId);

        $payload = $request->validated();
        $payload['company_id'] = $companyId;
        $payload['is_active'] = array_key_exists('is_active', $payload) ? (bool) $payload['is_active'] : true;
        $payload['sort_order'] = (int) ($payload['sort_order'] ?? 0);

        $document = Document::query()->create($payload);

        return (new DocumentResource($document))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateDocumentRequest $request, int $id): DocumentResource
    {
        $document = Document::query()->findOrFail($id);
        $document->update($request->validated());

        return new DocumentResource($document->fresh());
    }

    public function destroy(int $id): JsonResponse
    {
        Document::query()->findOrFail($id)->delete();

        return response()->json(['message' => 'Documento eliminado']);
    }

    private function fileType(Request $request): ?string
    {
        $type = $request->string('file_type')->toString();

        if ($type === 'flyer') {
            return Document::TYPE_IMAGE;
        }

        if (in_array($type, Document::TYPES, true)) {
            return $type;
        }

        $kind = $request->string('kind')->toString();

        return match ($kind) {
            'flyer' => Document::TYPE_IMAGE,
            'pdf' => Document::TYPE_PDF,
            'video' => Document::TYPE_VIDEO,
            'audio' => Document::TYPE_AUDIO,
            'voucher' => Document::TYPE_VOUCHER,
            default => null,
        };
    }
}
