<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $fillable = ['page', 'key', 'value'];

    /**
     * Scope untuk filter berdasarkan halaman.
     */
    public function scopeForPage($query, string $page)
    {
        return $query->where('page', $page);
    }

    /**
     * Helper statik: ambil satu nilai konten.
     * Kembalikan $default jika tidak ditemukan.
     */
    public static function getValue(string $page, string $key, string $default = ''): string
    {
        return static::where('page', $page)
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    /**
     * Ambil semua konten satu halaman sebagai array asosiatif [key => value].
     */
    public static function getPage(string $page): array
    {
        return static::where('page', $page)
            ->pluck('value', 'key')
            ->toArray();
    }
}
