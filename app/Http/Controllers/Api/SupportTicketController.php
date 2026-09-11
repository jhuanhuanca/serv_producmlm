<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportTicketReplyRequest;
use App\Http\Requests\StoreSupportTicketRequest;
use App\Http\Requests\UpdateSupportTicketRequest;
use App\Http\Resources\SupportTicketResource;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupportTicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = SupportTicket::query()->orderByDesc('id');

        $status = $request->string('status')->toString();
        if (in_array($status, SupportTicket::STATUSES, true)) {
            $query->where('status', $status);
        }

        $source = $request->string('source')->toString();
        if (in_array($source, SupportTicket::SOURCES, true)) {
            $query->where('source', $source);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('email')) {
            $query->where('email', $request->string('email')->toString());
        }

        $perPage = min(50, max(1, $request->integer('per_page', 20)));

        return SupportTicketResource::collection($query->paginate($perPage));
    }

    public function store(StoreSupportTicketRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['status'] = SupportTicket::STATUS_OPEN;

        $ticket = SupportTicket::query()->create($payload);

        return (new SupportTicketResource($ticket->load('replies')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): SupportTicketResource
    {
        $ticket = SupportTicket::query()->with('replies')->findOrFail($id);

        return new SupportTicketResource($ticket);
    }

    public function update(UpdateSupportTicketRequest $request, int $id): SupportTicketResource
    {
        $ticket = SupportTicket::query()->with('replies')->findOrFail($id);
        $ticket->update($request->validated());

        return new SupportTicketResource($ticket->fresh('replies'));
    }

    public function reply(StoreSupportTicketReplyRequest $request, int $id): SupportTicketResource
    {
        $ticket = SupportTicket::query()->findOrFail($id);
        $ticket->replies()->create($request->validated());

        if ($ticket->status === SupportTicket::STATUS_OPEN) {
            $ticket->update(['status' => SupportTicket::STATUS_IN_PROGRESS]);
        }

        return new SupportTicketResource($ticket->fresh('replies'));
    }
}
