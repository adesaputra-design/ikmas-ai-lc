<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        $contents = [
            // ─── INTRO ───────────────────────────────────────────────────────
            'intro_desc' => 'IKMAS AI Learning Center adalah ruang belajar dan kolaborasi Artificial Intelligence resmi bagi alumni Assalaam. Kami percaya setiap alumni berhak beradaptasi, bertumbuh, dan membuka peluang hidup lebih baik melalui penguasaan teknologi AI — bersama, bukan sendiri-sendiri.',

            // ─── STRUKTUR ORGANISASI ─────────────────────────────────────────

            // Community Lead
            'community_lead_tagline' => 'Penjaga arah dan keberlangsungan komunitas.',
            'community_lead_description' => 'Peran ini berbeda dari empat peran lainnya — bukan soal menjalankan satu fungsi operasional tertentu, tapi soal menjaga arah dan menanggung keberlangsungan komunitas secara keseluruhan.',
            'community_lead_responsibilities' => "• Menjaga Why tetap hidup di setiap keputusan\n• Mengambil keputusan akhir saat ada perbedaan prioritas\n• Menjadi penghubung ke pihak luar (PP IKMAS, alumni network, kolaborator)\n• Merekrut dan mempercayakan peran secara bertahap\n• Menjaga evaluasi rutin: retention, dampak nyata, kesiapan naik level\n• Menjadi wajah dan \"ingatan\" komunitas di fase awal",
            'community_lead_note' => 'Peran ini tidak bisa "didelegasikan" seperti peran lain — ini kepemilikan atas arah komunitas. Suksesi kepemimpinan butuh proses kepercayaan yang jauh lebih dalam.',

            // Program Coordinator
            'program_coordinator_tagline' => 'Penjaga ritme — memastikan aktivitas benar-benar terjadi.',
            'program_coordinator_description' => 'Peran "penjaga ritme" komunitas — memastikan aktivitas benar-benar terjadi secara konsisten, bukan cuma direncanakan lalu terlupakan.',
            'program_coordinator_responsibilities' => "• Menyusun kalender aktivitas (mingguan, bulanan, kuartalan)\n• Menjaga format Study Group tetap konsisten (60–90 menit, 70% practice)\n• Menentukan topik berikutnya dari polling atau progres sesi sebelumnya\n• Koordinasi logistik acara: link meeting, waktu, pengingat\n• Memantau ritme keterlibatan sebelum komunitas jadi pasif",
            'program_coordinator_delegate' => 'Mulai didelegasikan begitu ritme naik ke lebih dari 1x/bulan dan kewalahan mengurus jadwal + logistik sekaligus.',

            // Content Coordinator
            'content_coordinator_tagline' => 'Penjaga isi — memastikan setiap sesi benar-benar bernilai.',
            'content_coordinator_description' => 'Menjaga isi komunitas — memastikan setiap sesi dan postingan benar-benar bernilai buat anggota.',
            'content_coordinator_responsibilities' => "• Mengelola 9 Content Pillar: AI Basics, AI Tools, AI Productivity, AI Workflow, AI Cases, AI Tips, AI Experiment, AI Showcase, AI for Opportunity\n• Menyusun materi Study Group: slide ringkas, contoh kasus, prompt latihan\n• Mendokumentasikan hasil sesi jadi konten yang bisa diposting ulang\n• Mengumpulkan cerita AI for Opportunity dari member\n• Menjaga konten harian/casual tetap jalan",
            'content_coordinator_delegate' => 'Biasanya peran kedua yang dilepas — cocok untuk member yang paling rajin praktik dan suka berbagi hasil eksperimennya.',

            // Community Moderator
            'community_moderator_tagline' => 'Penjaga suasana — memastikan grup aman dan nyaman.',
            'community_moderator_description' => 'Menjaga suasana — memastikan grup tetap terasa aman dan nyaman untuk bertanya, terutama buat pemula.',
            'community_moderator_responsibilities' => "• Menegakkan 10 Community Rules (tidak merendahkan pemula, hindari spam)\n• Menyapa anggota baru dan memastikan mereka kenal aturan dasar\n• Menengahi diskusi yang memanas atau menyimpang\n• Mendeteksi member yang mulai pasif dan menyapa secara personal\n• Menjaga kualitas percakapan harian",
            'community_moderator_delegate' => 'Biasanya peran paling terakhir dilepas — butuh kepercayaan tinggi dan penilaian sosial yang matang.',

            // Technical Support
            'technical_support_tagline' => 'Penjaga kelancaran teknis — hambatan alat tidak jadi penghalang belajar.',
            'technical_support_description' => 'Menjaga sisi teknis tetap lancar — memastikan hambatan alat bantu tidak jadi penghalang belajar.',
            'technical_support_responsibilities' => "• Setup infrastruktur acara: link meeting, akses tepat waktu, backup rencana\n• Membantu member yang kesulitan teknis (daftar akun, error prompt)\n• Menjaga akses ke tools/resources tetap terorganisir\n• Riset tools AI baru untuk pillar AI Tools dan AI Workflow\n• Troubleshooting saat live session",
            'technical_support_delegate' => 'Sering jadi peran pertama yang dilepas — cocok untuk member yang natural sering membantu saat orang lain kebingungan.',

            // ─── RENCANA AKSI ────────────────────────────────────────────────

            'why_statement' => '"Saya percaya bahwa setiap alumni Assalaam seharusnya bisa beradaptasi dengan terus belajar, membuka diri, dan meningkatkan skill — karena tanpa itu, akhirnya mereka tertinggal, tidak adaptif, serta menutup peluang untuk hidup lebih baik."',
            'why_attribution' => '— Ade Machnun S, Litbang PP IKMAS',

            // Minggu Pertama
            'week1_title' => 'Menuju Study Group #1',
            'week1_intro' => 'Topik dikunci dari hasil polling grup: AI untuk Produktivitas & Otomasi, digabung dengan penggunaan ChatGPT/Claude sehari-hari. Format online, target realistis ~5 orang hadir.',
            'week1_h7' => 'Kunci topik & tanggal. Gabungkan dua topik teratas polling jadi satu tema. Tetapkan hari/jam pasti, siapkan link meeting sekarang — jangan tunggu H-1.',
            'week1_h6h5_materi' => 'Susun materi ringan: 60–90 menit (10 opening, 15 konsep, 20 demo, 20 practice, 10 sharing, 5 closing). Cukup 3–5 slide + 2–3 prompt latihan.',
            'week1_h5_umumkan' => 'Umumkan ke grup WA: topik, tanggal, jam, link meeting, satu kalimat pemantik. Minta konfirmasi kehadiran lewat react emoji.',
            'week1_h4h1_momentum' => 'Jaga momentum di grup: lempar pertanyaan ringan tentang kerjaan yang ingin dibantu AI. Jawabannya jadi contoh kasus real di sesi.',
            'week1_h1_reminder' => 'Kirim reminder personal via japri langsung ke yang sudah konfirmasi — bukan cuma broadcast grup. Sertakan ulang link meeting.',
            'week1_hari_h' => 'Bawakan sesi & catat kehadiran. Biarkan peserta coba prompt langsung secara real-time. Catat siapa hadir dan hasil praktiknya.',
            'week1_followup' => '24 jam setelah sesi: ucapan terima kasih, ringkasan singkat, ajak yang hadir share pengalaman. Tanya siapa tertarik jadi facilitator/volunteer berikutnya.',

            // 3 Bulan
            'month_intro' => 'Naik bertahap, bukan melompat: mulai dari bukti konsep, lalu delegasi kecil, lalu bukti dampak pertama. Sesuai prinsip Engagement > Membership.',
            'month1_title' => 'Bulan 1 — Bukti Konsep',
            'month1_detail' => "• Minggu 1: Jalankan Study Group #1 (AI untuk Produktivitas & Otomasi). Fokus: buat formatnya benar-benar jalan, bukan sempurna.\n• Minggu 1–2: Dokumentasikan hasil sesi — posting rekap ringan + hasil praktik peserta. Bibit pertama konten AI Showcase.\n• Sepanjang bulan: Amati siapa yang paling aktif — catat 1–2 nama calon co-facilitator/volunteer pertama.",
            'month2_title' => 'Bulan 2 — Mulai Delegasi',
            'month2_detail' => "• Minggu 5: Jalankan Study Group #2 dari topik polling berikutnya. Ajak satu anggota aktif bantu isi 5–10 menit sesi.\n• Minggu 6–7: Kumpulkan cerita AI for Opportunity pertama — tanya langsung: ada yang mulai terpakai untuk kerjaan/peluang baru?\n• Akhir bulan: Mulai apresiasi ringan — label \"Kontributor Bulan Ini\", bukan lomba, sekadar pengakuan.",
            'month3_title' => 'Bulan 3 — Bukti Dampak',
            'month3_detail' => "• Minggu 9: Jalankan Study Group #3 — naikkan sedikit level: workflow/otomasi sederhana. Peserta lama sudah punya modal dari dua sesi sebelumnya.\n• Minggu 10–11: Mini showcase pertama — ajak member yang sudah praktik untuk share hasil kecil mereka.\n• Akhir bulan: Evaluasi 3 bulan — cek retention, siapa dapat dampak nyata, dan apakah ada calon volunteer siap pegang peran tetap.",

            'closing_quote' => '"Lebih baik memiliki 100 anggota dengan 30 anggota aktif daripada 1.000 anggota tanpa aktivitas."',
        ];

        foreach ($contents as $key => $value) {
            PageContent::updateOrCreate(
                ['page' => 'tentang', 'key' => $key],
                ['value' => $value]
            );
        }
    }
}
