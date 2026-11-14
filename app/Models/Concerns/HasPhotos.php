<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * 写真を持つモデル（神社・参拝記録・お店）用。
 *
 * photos はポリモーフィック関連で外部キーを張れないので、
 * 親を消したら写真（とファイル）もここで消す。
 * 外部キーの restrict で親の削除が失敗したときに写真だけ消えないよう、deleting ではなく deleted で消す。
 */
trait HasPhotos
{
    public static function bootHasPhotos(): void
    {
        static::deleted(function (self $model) {
            $model->photos()->get()->each->delete();
        });
    }

    /**
     * @return MorphMany<Photo, $this>
     */
    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable')->ordered();
    }
}
