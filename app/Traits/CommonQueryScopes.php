<?php

namespace App\Traits;

trait CommonQueryScopes {
    public function scopeFilterByDate($query, $date) {
        if (!$date) return $query;
        return $query->whereDate('date', $date);
    }

    public function scopeSearchByTitle($query, $term) {
        if (!$term) return $query;
        return $query->where('title', 'like', "%{$term}%");
    }
}
