<?php

namespace Leyton\ClevExport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubExport extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'integer',
    ];

    const CREATED = 1;
    const IN_PROGRESS = 2;
    const EXPORTED = 3;
    const MERGING = 4;

    /**
     * Get the export parent
     *
     * @return BelongsTo
     */
    public function export(): BelongsTo
    {
        return $this->belongsTo(Export::class);
    }

    /**
     * Check if having the status
     *
     * @param $status
     * @return bool
     */
    public function isHavingStatus($status): bool
    {
        return $this->status === $status;
    }

    /**
     * Change to status
     *
     * @param $status
     */
    public function toStatus($status)
    {
        $this->update(['status' => $status]);
    }

    /**
     * filter by export_id
     *
     * @param $query
     * @param $export_id
     * @return mixed
     */
    public function scopeFor($query, $export_id)
    {
        return $query->where('export_id', $export_id);
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getPath(): string
    {
        return 'exports/' .$this->export_id . '/'. $this->pagination . '.csv';
    }
}
