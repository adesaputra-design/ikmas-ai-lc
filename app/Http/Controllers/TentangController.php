<?php

namespace App\Http\Controllers;

use App\Models\PageContent;

class TentangController extends Controller
{
    public function index()
    {
        $content = PageContent::getPage('tentang');

        return view('tentang', compact('content'));
    }
}
