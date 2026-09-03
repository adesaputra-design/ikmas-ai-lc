<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;

class AdminTentangController extends Controller
{
    /**
     * Tampilkan form edit konten halaman /tentang.
     */
    public function index()
    {
        $content = PageContent::getPage('tentang');

        return view('admin.tentang.edit', compact('content'));
    }

    /**
     * Simpan perubahan konten ke database.
     */
    public function update(Request $request)
    {
        $request->validate([
            'content' => ['required', 'array'],
            'content.*' => ['required', 'string'],
        ]);

        foreach ($request->input('content') as $key => $value) {
            PageContent::updateOrCreate(
                ['page' => 'tentang', 'key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.tentang.index')
            ->with('success', 'Konten halaman Tentang berhasil diperbarui.');
    }
}
