<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    public const SOURCE_LANDING = 'landing';

    public const SOURCE_PLATFORM = 'platform';

    public const STATUS_OPEN = 'open';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_CLOSED = 'closed';

    public const SOURCES = [
        self::SOURCE_LANDING,
        self::SOURCE_PLATFORM,
    ];

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_IN_PROGRESS,
        self::STATUS_CLOSED,
    ];

    protected $fillable = [
        'source',
        'status',
        'name',
        'email',
        'subject',
        'message',
        'user_id',
        'catalog_company_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'catalog_company_id' => 'integer',
        ];
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class)->orderBy('id');
    }
}
