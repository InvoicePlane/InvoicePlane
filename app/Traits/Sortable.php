<?php

namespace IP\Traits;

use Illuminate\Support\Facades\DB;

trait Sortable
{
    public static function link($col, $title = null, $requestMatches = null)
    {
        if ($requestMatches and !request()->is($requestMatches)) {
            return $title;
        }

        if (is_null($title)) {
            $title = str_replace('_', ' ', $col);
            $title = ucfirst($title);
        }

        $indicator = (request('s') == $col ? (request('o') === 'asc' ? '&uarr;' : '&darr;') : null);
        $parameters = array_merge(request()->all(), ['s' => $col, 'o' => (request('o') === 'asc' ? 'desc' : 'asc')]);

        return link_to_route(request()->route()->getName(), "$title $indicator", $parameters);
    }

    public function scopeSortable($query, $defaultSort = [])
    {
        if (request()->has('s') and request()->has('o') and isset($this->sortable) and $this->sortIsAllowed()) {
            if (in_array(request('s'), $this->sortable)) {
                return $query->orderBy(request('s'), request('o'));
            } elseif (array_key_exists(request('s'), $this->sortable)) {
                foreach ($this->sortable[request('s')] as $col) {
                    if (str_contains($col, '(')) {
                        $query->orderBy(DB::raw($col), request('o'));
                    } else {
                        $query->orderBy($col, request('o'));
                    }
                }

                return $query;
            }
        } elseif ($defaultSort) {
            foreach ($defaultSort as $col => $sort) {
                if (str_contains($col, '(')) {
                    $query->orderBy(DB::raw($col), $sort);
                } else {
                    $query->orderBy($col, $sort);
                }
            }

            return $query;
        }

        return $query;
    }

    private function sortIsAllowed()
    {
        // Sortable must be an array.
        if (!is_array($this->sortable)) {
            return false;
        }

        // If it's contained in sortable, it's allowed.
        if (array_key_exists(request('s'), $this->sortable) or in_array(request('s'), $this->sortable)) {
            return true;
        }

        // If sortable contains "custom" and s=custom_*, it's allowed.
        if ((array_key_exists('custom', $this->sortable) or in_array('custom', $this->sortable)) and substr(request('s'), 0, 7) == 'column_') {
            return true;
        }

        return false;
    }
}