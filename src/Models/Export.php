<?php

namespace Leyton\ClevExport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Export extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => 'integer',
        'criterias' => 'array',
        'exported_at' => 'datetime',
        'reason' => 'array'
    ];
    const CREATED = 1;
    const IN_PROGRESS = 2;
    const MERGING = 4;
    const EXPORTED = 5;
    const FAILED = 6;

    /**
     * Created by
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('clevexport.owner_class'), config('clevexport.owner_id'));
    }

    /**
     * Exports
     *
     * @return HasMany
     */
    public function subExports() : HasMany
    {
        return $this->hasMany(SubExport::class);
    }

    /**
     * Change to status
     *
     * @param $status
     * @param array $data
     * @return Export
     */
    public function toStatus($status, array $data = []): self
    {
        $this->update([
            'status' => $status,
            'reason' => $data
        ]);

        return $this;
    }

    /**
     * Get the path of the export
     *
     * @return string
     */
    public function getPath(): string
    {
        return 'exports/' .$this->id . '-exported.csv';
    }

    public function getLocalPath(): string
    {
        return 'exports/'. $this->id . '/1.csv';
    }

    public function status(): string
    {
        $status = [
            static::CREATED => 'Crée',
            static::IN_PROGRESS => 'En cours',
            static::MERGING => 'En export',
            static::EXPORTED => 'Exporté'
        ];

        return $status[$this->status];
    }

    public function downloadLink(): string
    {
        return route('exports.download', $this->id);
    }

    /**
     * Check if the file is ready to be downloaded
     *
     * @return bool
     */
    public function canBeDownloaded(): bool
    {
        return $this->status === static::EXPORTED;
    }

    /**
     * Check if the salarie can download file
     *
     * @param Authenticatable $salarie
     * @return bool
     */
    public function canBeDownloadedBy(Authenticatable $salarie): bool
    {
        return $this->owner()->is($salarie);
    }
}
