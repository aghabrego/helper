<?php

namespace Weirdo\Helper\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * @license MIT
 * @package Weirdo\Helper
 */
trait HelperCollection
{
    /**
     * @param array $values
     * @return Collection
     */
    public function createCollect($values = [])
    {
        return collect($values);
    }

    /**
     * @param Collection|EloquentCollection $collection
     * @param string|null $display
     * @param string|null $value
     * @return Collection
     */
    public function tearOffItems($collection, $display = null, $value = null)
    {
        if (is_null($display) && !is_null($value)) {
            return $collection->pluck($value);
        }
        if (!is_null($display) && is_null($value)) {
            return $collection->pluck($display);
        }
        if (!is_null($display) && !is_null($value)) {
            return $collection->pluck($display, $value);
        }

        return $this->createCollect();
    }

    /**
     * @param Collection|Collection[] $details
     * @return Collection|null
     */
    public function getAverage($details)
    {
        $first = $details->first();
        $max = $first ? $first->count() : 0;
        foreach ($details as $detail) {
            $current = $detail->count();
            if ($max < $current) {
                $first = $detail;
                $max = $current;
            }
        }

        return $first ? $first->first() : null;
    }

    /**
     * @param Collection $collection
     * @param string $value
     * @param string $text
     * @param array[] $description
     * @return Collection
     */
    public function setTransform($collection, $value, $text, $description = [])
    {
        return $collection->transform(function ($item, $key) use ($value, $text, $description) {
            $cItem = collect($item);
            $aItem = !is_array($item) ? $item->toArray() : $item;
            $columns = [];
            if (is_array($description) && count($description) > 0) {
                foreach ($description as $newKey) {
                    array_push($columns, array_get($aItem, $newKey));
                }
            }
            $header = trim(implode(',', $columns), ' \t\n\r\0\x0B,');

            return [
                $value => $cItem->get($value),
                $text => count($columns) > 0 ? "{$cItem->get($text)},{$header}" : $cItem->get($text),
            ];
        });
    }
}
