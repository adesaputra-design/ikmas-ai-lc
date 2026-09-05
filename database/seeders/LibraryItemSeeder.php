<?php

namespace Database\Seeders;

use App\Models\LibraryItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LibraryItemSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $member = User::where('role', 'member')->first() ?? $admin;

        $items = [
            [
                'title' => 'Co-Intelligence: Living and Working with AI',
                'slug' => 'co-intelligence-living-and-working-with-ai',
                'type' => 'book',
                'category' => 'Fundamental AI',
                'author_name' => 'Prof. Ethan Mollick (Wharton School)',
                'reading_time' => '10 mnt baca',
                'summary_preview' => 'Panduan komprehensif tentang bagaimana berkolaborasi dengan kecerdasan buatan sebagai rekan kerja (co-worker), bukan sekadar alat otomatisasi biasa.',
                'content' => "## 🌟 Poin Kunci & Intisari Buku\n\nDalam buku *Co-Intelligence*, Prof. Ethan Mollick menggarisbawahi empat aturan emas dalam berinteraksi dengan AI:\n\n1. **Selalu undang AI ke dalam setiap meja diskusi**: Jadikan AI sebagai mitra brainstorming pertama Anda, bukan yang terakhir.\n2. **Jadilah manusia di dalam lingkaran kendali (Human-in-the-loop)**: AI menghasilkan draf dan ide dengan kecepatan luar biasa, tetapi verifikasi fakta dan sentuhan empati tetap menjadi tanggung jawab manusia.\n3. **Perlakukan AI seperti manusia pintar yang aneh**: AI bukanlah mesin pencari database; ia bekerja berdasarkan pola bahasa dan penalaran probabilistik.\n4. **Asumsikan ini adalah versi AI terlemah yang pernah Anda gunakan**: Kemampuan model AI terus meningkat secara eksponensial setiap tahun.\n\n### 💡 Implementasi Praktis untuk Alumni IKMAS\n- Gunakan AI untuk memecah masalah besar menjadi tugas-tugas mikro.\n- Manfaatkan teknik *role prompting* untuk mendapatkan sudut pandang ahli sebelum mengambil keputusan bisnis atau edukasi.",
                'is_featured' => true,
                'status' => 'approved',
            ],
            [
                'title' => 'The Coming Wave: Technology, Power, and the Greatest Dilemma',
                'slug' => 'the-coming-wave-technology-power-dilemma',
                'type' => 'book',
                'category' => 'Etika & Masa Depan AI',
                'author_name' => 'Mustafa Suleyman (Co-founder DeepMind & CEO Microsoft AI)',
                'reading_time' => '12 mnt baca',
                'summary_preview' => 'Eksplorasi mendalam mengenai gelombang ganda AI dan bioteknologi sintetis, serta tantangan penahanan (containment problem) bagi peradaban modern.',
                'content' => "## 🌊 Mengapa Gelombang Teknologi Ini Berbeda?\n\nMustafa Suleyman membedah mengapa AI generasi baru memiliki sifat unik:\n\n1. **Aksesibilitas Ekstrem**: Perangkat lunak AI sumber terbuka (open-source) dapat diunduh dan dijalankan di perangkat lokal oleh siapa saja di seluruh dunia.\n2. **Dampak Asimetris**: Individu atau tim kecil kini memiliki daya ungkit produktivitas setara korporasi besar berkat orkestrasi agen otonom.\n3. **Kebutuhan Regulasi Tangkas**: Penahanan risiko tidak bisa dilakukan dengan larangan total, melainkan tata kelola transparansi model dan pengujian keamanan siber berlapis.\n\n### 📌 Peluang bagi Komunitas\nMemahami etika dan keamanan data AI menjadi diferensiasi penting bagi para profesional alumni Assalaam yang berkarier di industri digital.",
                'is_featured' => true,
                'status' => 'approved',
            ],
            [
                'title' => 'Episode #420: Masa Depan Model Penalaran & Agentic AI',
                'slug' => 'episode-420-masa-depan-model-penalaran-agentic-ai',
                'type' => 'podcast',
                'category' => 'LLM & Prompting',
                'podcast_source' => 'Lex Fridman Podcast',
                'duration' => '1 jam 45 mnt (Resume 5 mnt)',
                'media_embed_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'summary_preview' => 'Diskusi mendalam seputar evolusi arsitektur model penalaran, penguatan memori jangka panjang agen, dan masa depan kolaborasi manusia-mesin.',
                'content' => "## 🎙️ Intisari Diskusi Episode\n\nNarasumber membahas pergeseran paradigma dari sekadar *next-token prediction* menuju *test-time compute reasoning*:\n\n- **Agen Otonom**: Model masa depan tidak hanya menjawab pertanyaan, melainkan menyusun rencana multi-langkah dan mengeksekusi aksi nyata di browser atau sistem operasi.\n- **Evaluasi Diri (Self-Correction)**: Model yang mampu mengkritisi hasil pekerjaannya sendiri sebelum memberikan output akhir menghasilkan tingkat akurasi 3x lebih tinggi pada tugas kompleks.\n- **Kecakapan Rekayasa Konteks**: Panjang konteks hingga jutaan token memungkinkan pemrosesan seluruh basis kode atau tumpukan berkas dalam sekali sesi.",
                'is_featured' => true,
                'status' => 'approved',
            ],
            [
                'title' => 'The AI Breakdown: Strategi Mengintegrasikan AI ke Alur Kerja Harian',
                'slug' => 'the-ai-breakdown-strategi-integrasi-ai',
                'type' => 'podcast',
                'category' => 'Bisnis & Startup AI',
                'podcast_source' => 'The AI Breakdown Daily',
                'duration' => '25 mnt (Resume 4 mnt)',
                'media_embed_url' => 'https://open.spotify.com/episode/sample123',
                'summary_preview' => 'Analisis ringkas dan taktis tentang cara para profesional dan agensi mengadopsi AI tanpa mengorbankan kualitas maupun privasi klien.',
                'content' => "## ⚡ 5 Langkah Praktis Integrasi AI\n\n1. **Audit Tugas Berulang**: Petakan 20% aktivitas harian yang menghabiskan 80% energi administratif.\n2. **Bangun Template Prompt Kustom**: Simpan instruksi standar tim untuk menjaga konsistensi gaya bahasa dan format keluaran.\n3. **Gunakan Tool Sesuai Karakter Tugas**: Claude untuk penalaran dokumen panjang, ChatGPT untuk drafting ide, dan Cursor untuk coding terintegrasi.\n4. **Kerahasiaan Data**: Hindari memasukkan data rahasia tanpa perjanjian keamanan enterprise.\n5. **Iterasi Mingguan**: Luangkan 1 jam setiap Jumat untuk mengevaluasi tool AI baru yang relevan.",
                'is_featured' => false,
                'status' => 'approved',
            ],
            [
                'title' => 'Rancang Bangun Sistem Temu Kembali Informasi Fikih Berbasis RAG dan LLM',
                'slug' => 'rancang-bangun-sistem-temu-kembali-fikih-rag-llm',
                'type' => 'academic',
                'category' => 'LLM & Prompting',
                'academic_degree' => 'tesis',
                'institution' => 'Institut Teknologi Bandung (ITB)',
                'publication_year' => 2024,
                'co_authors' => 'Prof. Dr. Ir. Pembimbing, M.T.',
                'external_url' => 'https://doi.org/10.1016/j.sample.2024.01',
                'user_id' => $member->id,
                'summary_preview' => 'Penelitian tesis S2 tentang penerapan Retrieval-Augmented Generation (RAG) untuk meminimalisir halusinasi pada konsultasi rujukan teks keislaman klasik.',
                'content' => "## 🎓 Abstrak Penelitian\n\nPenerapan Large Language Models (LLM) pada domain keilmuan spesifik kerap menghadapi kendala halusinasi fakta dan ketiadaan rujukan dalil yang presisi. Riset tesis ini mengusulkan arsitektur *Hybrid Retrieval-Augmented Generation (RAG)* yang menggabungkan pencarian semantik vektor (*dense retrieval*) dengan pencarian kata kunci leksikal (*BM25 sparse retrieval*) pada korpus teks fikih klasik berbahasa Arab dan terjemahan Indonesia.\n\n### 🔬 Metodologi & Temuan Utama\n1. Evaluasi pada 500 kueri pertanyaan fikih menunjukkan peningkatan akurasi sitasi sebesar 42.8% dibandingkan pemodelan LLM dasar tanpa RAG.\n2. Waktu latensi inferensi berhasil ditekan hingga di bawah 1.5 detik per respons dengan teknik kuantisasi model 4-bit.\n3. Hasil penelitian ini menjadi acuan pengembangan asisten AI terpercaya dalam ranah studi Islam dan alumni pesantren.",
                'is_featured' => true,
                'status' => 'approved',
            ],
        ];

        foreach ($items as $data) {
            LibraryItem::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
