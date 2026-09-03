@extends('layouts.admin')

@section('title', 'Edit Halaman Tentang — IKMAS AI LC')

@section('content')
<div style="max-width: 860px;">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem;">Edit Halaman Tentang</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Perubahan langsung tampil di <a href="{{ url('/tentang') }}" target="_blank" style="color: var(--primary);">/tentang ↗</a></p>
    </div>


    <form action="{{ route('admin.tentang.update') }}" method="POST">
        @csrf

        {{-- ── INTRO ────────────────────────────────────────────────────── --}}
        <div class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                Intro Halaman
            </h2>

            <div class="form-group">
                <label class="form-label">Deskripsi Intro</label>
                <textarea name="content[intro_desc]" class="form-control @error('content.intro_desc') is-invalid @enderror" rows="4">{{ old('content.intro_desc', $content['intro_desc'] ?? '') }}</textarea>
                @error('content.intro_desc')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- ── STRUKTUR ORGANISASI ───────────────────────────────────────── --}}
        <div class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                Struktur Organisasi
            </h2>

            @php
                $roles = [
                    'community_lead'       => 'Community Lead',
                    'program_coordinator'  => 'Program Coordinator',
                    'content_coordinator'  => 'Content Coordinator',
                    'community_moderator'  => 'Community Moderator',
                    'technical_support'    => 'Technical / AI Support',
                ];
            @endphp

            @foreach($roles as $prefix => $label)
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--primary); margin-bottom: 1rem;">{{ $label }}</h3>

                    <div class="form-group">
                        <label class="form-label">Tagline Singkat</label>
                        <input type="text" name="content[{{ $prefix }}_tagline]"
                            class="form-control" value="{{ old("content.{$prefix}_tagline", $content["{$prefix}_tagline"] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi (1 paragraf — tampil untuk semua)</label>
                        <textarea name="content[{{ $prefix }}_description]" class="form-control" rows="3">{{ old("content.{$prefix}_description", $content["{$prefix}_description"] ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggung Jawab Detail (tampil khusus member, gunakan • untuk poin)</label>
                        <textarea name="content[{{ $prefix }}_responsibilities]" class="form-control" rows="6">{{ old("content.{$prefix}_responsibilities", $content["{$prefix}_responsibilities"] ?? '') }}</textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Kapan Didelegasikan / Catatan</label>
                        <textarea name="content[{{ $prefix }}_{{ $prefix === 'community_lead' ? 'note' : 'delegate' }}]" class="form-control" rows="2">{{ old("content.{$prefix}_" . ($prefix === 'community_lead' ? 'note' : 'delegate'), $content[$prefix . '_' . ($prefix === 'community_lead' ? 'note' : 'delegate')] ?? '') }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── RENCANA AKSI ─────────────────────────────────────────────── --}}
        <div class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
            <h2 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                Rencana Aksi
            </h2>

            <div class="form-group">
                <label class="form-label">Why Statement (kutipan)</label>
                <textarea name="content[why_statement]" class="form-control" rows="3">{{ old('content.why_statement', $content['why_statement'] ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Atribusi Why Statement</label>
                <input type="text" name="content[why_attribution]" class="form-control"
                    value="{{ old('content.why_attribution', $content['why_attribution'] ?? '') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Intro Minggu Pertama</label>
                <textarea name="content[week1_intro]" class="form-control" rows="3">{{ old('content.week1_intro', $content['week1_intro'] ?? '') }}</textarea>
            </div>

            @php
                $weekNodes = [
                    'week1_h7'         => 'H-7: Kunci Topik & Tanggal',
                    'week1_h6h5_materi'=> 'H-6 s.d. H-5: Susun Materi',
                    'week1_h5_umumkan' => 'H-5: Umumkan ke Grup WA',
                    'week1_h4h1_momentum' => 'H-4 s.d. H-1: Jaga Momentum',
                    'week1_h1_reminder'=> 'H-1 & Pagi Hari-H: Reminder Personal',
                    'week1_hari_h'     => 'Hari-H: Bawakan Sesi',
                    'week1_followup'   => '24 Jam Setelah Sesi: Follow-up',
                ];
            @endphp

            @foreach($weekNodes as $key => $nodeLabel)
                <div class="form-group">
                    <label class="form-label">{{ $nodeLabel }}</label>
                    <textarea name="content[{{ $key }}]" class="form-control" rows="2">{{ old("content.$key", $content[$key] ?? '') }}</textarea>
                </div>
            @endforeach

            <div class="form-group">
                <label class="form-label">Intro 3 Bulan Pertama</label>
                <textarea name="content[month_intro]" class="form-control" rows="2">{{ old('content.month_intro', $content['month_intro'] ?? '') }}</textarea>
            </div>

            @foreach(['month1' => 'Bulan 1', 'month2' => 'Bulan 2', 'month3' => 'Bulan 3'] as $key => $label)
                <div class="form-group">
                    <label class="form-label">{{ $label }} — Judul Fase</label>
                    <input type="text" name="content[{{ $key }}_title]" class="form-control"
                        value="{{ old("content.{$key}_title", $content["{$key}_title"] ?? '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $label }} — Detail per Minggu (tampil khusus member)</label>
                    <textarea name="content[{{ $key }}_detail]" class="form-control" rows="5">{{ old("content.{$key}_detail", $content["{$key}_detail"] ?? '') }}</textarea>
                </div>
            @endforeach

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Kutipan Penutup</label>
                <textarea name="content[closing_quote]" class="form-control" rows="2">{{ old('content.closing_quote', $content['closing_quote'] ?? '') }}</textarea>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; align-items: center;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ url('/tentang') }}" target="_blank" class="btn btn-secondary">Preview Halaman ↗</a>
        </div>
    </form>
</div>
@endsection
