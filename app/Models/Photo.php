<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Database\Factories\PhotoFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * 神社・参拝記録・お店の写真。
 */
#[Fillable(['path', 'caption', 'sort_order'])]
class Photo extends Model
{
    /** @use HasFactory<PhotoFactory> */
    use HasFactory, HasSortOrder;

    protected static function booted(): void
    {
        // レコードを消したらファイルも消す
        static::deleted(function (Photo $photo) {
            self::disk()->delete($photo->path);
        });
    }

    /**
     * 写真を置くディスク。config/admin.php の photos.disk（本番は s3＝Cloudflare R2）。
     */
    public static function disk(): Filesystem
    {
        return Storage::disk(config('admin.photos.disk'));
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function photoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return self::disk()->url($this->path);
    }
}
