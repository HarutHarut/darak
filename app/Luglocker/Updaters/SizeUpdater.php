<?php

namespace App\Luglocker\Updaters;

use App\Models\Size;

trait SizeUpdater
{
    /**
     * @param Size $size
     * @param array $data
     * @return Size
     */
    public function sizeUpdate(Size $size, array $data) : Size
    {
        if (isset($data['name'])) {
            $size->name = $data['name'];
        }
        if (isset($data['width'])) {
            $size->width = $data['width'];
        }
        if (isset($data['height'])) {
            $size->height = $data['height'];
        }
        if (isset($data['length'])) {
            $size->length = $data['length'];
        }
        if (isset($data['desc'])) {
            $size->desc = $data['desc'];
        }

        $size->save();

        return $size->fresh();
    }
}
