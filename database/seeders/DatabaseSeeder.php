<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LearningMaterial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Default Admin & Sample Members
        $admin = User::firstOrCreate(
            ['email' => 'admin@ikmas.ai'],
            [
                'name' => 'Admin IKMAS AI',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'admin',
                'alumni_year' => '2010',
                'whatsapp_number' => '081234567800',
            ]
        );
        $admin->update(['role' => 'admin']);

        // 2. Seed Categories
        $catProductivity = Category::firstOrCreate(
            ['slug' => 'produktivitas'],
            ['name' => 'Produktivitas & Administrasi', 'type' => 'learning', 'color' => '#2563eb']
        );

        $catPrompting = Category::firstOrCreate(
            ['slug' => 'prompting-dasar'],
            ['name' => 'Prompt Engineering Dasar', 'type' => 'learning', 'color' => '#0ea5e9']
        );

        $catTools = Category::firstOrCreate(
            ['slug' => 'tools-dan-aplikasi'],
            ['name' => 'Tools & Aplikasi AI', 'type' => 'learning', 'color' => '#10b981']
        );

        $catWorkflow = Category::firstOrCreate(
            ['slug' => 'automasi-workflow'],
            ['name' => 'Automasi & Workflow', 'type' => 'learning', 'color' => '#f59e0b']
        );

        $catOpportunity = Category::firstOrCreate(
            ['slug' => 'peluang-dan-bisnis'],
            ['name' => 'Peluang & Monetisasi', 'type' => 'learning', 'color' => '#8b5cf6']
        );

        // 3. Seed Realistic Learning Materials
        $materials = [
            [
                'category_id' => $catProductivity->id,
                'title' => 'AI Study Group #1: AI untuk Produktivitas Sehari-hari',
                'slug' => 'ai-study-group-1-produktivitas-sehari-hari',
                'level' => 'beginner',
                'pillar' => 'productivity',
                'summary' => 'Materi pembuka Study Group IKMAS AI: memahami apa yang bisa dilakukan AI, cara memberi instruksi praktis, dan langsung mempraktikkannya untuk menghemat 2 jam kerja.',
                'content' => '
                    <h2>Mengapa Kita Mulai dari Sini?</h2>
                    <p>Banyak orang merasa AI adalah teknologi rumit yang hanya dipahami oleh lulusan teknik informatika. Di <strong>IKMAS AI Learning Center</strong>, kita membongkar mitos tersebut. AI pada dasarnya adalah mitra berpikir dan asisten kerja tercepat yang pernah diciptakan.</p>
                    
                    <div style="background: var(--bg-surface-alt); border-left: 4px solid var(--primary); padding: 1rem 1.25rem; margin: 1.5rem 0; border-radius: 0.5rem;">
                        <strong>Prinsip Utama:</strong> Bukan sekadar belajar AI, tetapi bagaimana AI bisa langsung meringankan beban pekerjaan dan kehidupan sehari-harimu mulai hari ini.
                    </div>

                    <h3>3 Hal yang Bisa Langsung Kamu Coba Hari Ini:</h3>
                    <ol style="margin-left: 1.5rem; margin-bottom: 1.5rem;">
                        <li><strong>Meringkas Dokumen Panjang:</strong> Masukkan artikel, laporan PDF, atau notulensi rapat, lalu minta AI membuat ringkasan 5 butir poin penting.</li>
                        <li><strong>Membuat Draf Pesan & Email Profesional:</strong> Jelaskan poin yang ingin kamu sampaikan dalam kalimat santai, biarkan AI merapikannya menjadi bahasa resmi.</li>
                        <li><strong>Brainstorming Ide Cepat:</strong> Dapatkan 10 sudut pandang baru untuk materi presentasi, nama program, atau strategi penjualan.</li>
                    </ol>

                    <h3>Formula Prompt Sederhana (Formula R-T-C)</h3>
                    <p>Untuk mendapatkan hasil yang bagus dari ChatGPT atau Claude, gunakan formula <strong>Role - Task - Context</strong>:</p>
                    <pre style="background: #0f172a; color: #f8fafc; padding: 1.25rem; border-radius: 0.75rem; overflow-x: auto; font-size: 0.9rem; margin: 1.25rem 0;"><code>Bertindaklah sebagai asisten manajer komunikasi alumni Assalaam.
Tolong buatkan draf pesan WhatsApp pengumuman santai (Task)
untuk mengajak rekan angkatan mengikuti sesi ngobrol santai seputar pengenalan AI minggu depan.
Gunakan nada hangat, bersahabat, dan hindari kata-kata yang menggurui (Context).</code></pre>

                    <p>Cobalah menyalin prompt di atas dan masukkan ke ChatGPT sekarang juga. Rasakan perbedaannya dibandingkan hanya mengetik satu baris kalimat pendek!</p>
                ',
                'reading_minutes' => 6,
                'video_url' => 'https://youtube.com',
                'slide_url' => '#',
                'is_published' => true,
            ],
            [
                'category_id' => $catPrompting->id,
                'title' => 'Prompt Engineering Dasar: Seni Memberi Instruksi Efektif',
                'slug' => 'prompt-engineering-dasar-instruksi-efektif',
                'level' => 'beginner',
                'pillar' => 'basics',
                'summary' => 'Pelajari teknik menyusun prompt yang jelas agar AI memberikan jawaban akurat, terstruktur, dan tidak halusinasi.',
                'content' => '
                    <h2>Mengapa Prompt Begitu Menentukan?</h2>
                    <p>Kualitas jawaban yang kamu dapatkan dari AI 100% berbanding lurus dengan kejelasan instruksi yang kamu berikan. Konsep sederhananya: <em>Garbage in, garbage out</em>. Jika instruksi kita samar, jawaban AI akan klise dan umum.</p>

                    <h3>4 Elemen Prompt Berkualitas Tinggi</h3>
                    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem;">
                        <li><strong>1. Persona / Peran:</strong> Beri tahu AI peran apa yang harus dimainkan (misal: "Bertindaklah sebagai akuntan publik", "Bertindaklah sebagai guru bahasa").</li>
                        <li><strong>2. Tujuan Jelas:</strong> Apa output konkret yang kamu inginkan (ringkasan tabel, draf 3 paragraf, kode program, dll.).</li>
                        <li><strong>3. Batasan / Constraints:</strong> Hal-hal apa yang TIDAK boleh dilakukan (misal: "Maksimal 200 kata", "Hindari jargon teknis", "Gunakan Bahasa Indonesia baku").</li>
                        <li><strong>4. Contoh (Few-Shot Prompting):</strong> Berikan 1 contoh gaya bahasa atau format yang kamu sukai.</li>
                    </ul>

                    <h3>Latihan Mandiri</h3>
                    <p>Coba bandingkan hasil dari prompt: <em>"Buatkan saya artikel tentang kopi"</em> dengan: <em>"Bertindaklah sebagai barista berpengalaman. Tuliskan panduan 3 langkah menyeduh kopi manual brew V60 untuk pemula, dengan bahasa santai dan tips praktis."</em> Kamu akan melihat perbedaan kualitas yang sangat mencolok!</p>
                ',
                'reading_minutes' => 7,
                'video_url' => null,
                'slide_url' => '#',
                'is_published' => true,
            ],
            [
                'category_id' => $catTools->id,
                'title' => 'Perbandingan LLM: Kapan Pakai ChatGPT, Claude, atau Gemini?',
                'slug' => 'perbandingan-llm-chatgpt-claude-gemini',
                'level' => 'explorer',
                'pillar' => 'tools',
                'summary' => 'Bedah kelebihan masing-masing model AI populer agar kamu tahu tool terbaik untuk kebutuhan analisismu.',
                'content' => '
                    <h2>Tidak Semua AI Cocok untuk Segala Hal</h2>
                    <p>Sering kali anggota komunitas bertanya: <em>"Tool mana yang paling bagus?"</em> Jawabannya tergantung tugas apa yang sedang kamu selesaikan.</p>

                    <h3>1. Claude (Anthropic)</h3>
                    <p>Sangat unggul dalam: <strong>Gaya penulisan natural, empati bahasa, analisis naskah panjang, dan penulisan kode terstruktur</strong>. Jika kamu butuh menulis artikel mendalam, merevisi tulisan ilmiah, atau membaca berkas PDF 50 halaman, Claude adalah pilihan utama.</p>

                    <h3>2. ChatGPT (OpenAI)</h3>
                    <p>Sangat unggul dalam: <strong>Ekosistem integrasi yang luas, plugin/GPTs kustom, kecepatan respons, dan pengolahan data serbaguna</strong>. Sangat pas untuk asisten harian serba bisa.</p>

                    <h3>3. Gemini (Google)</h3>
                    <p>Sangat unggul dalam: <strong>Integrasi langsung dengan Google Workspace (Docs, Gmail, Drive, YouTube), pencarian informasi real-time, dan pemrosesan multimodal (gambar/video besar)</strong>.</p>
                ',
                'reading_minutes' => 8,
                'video_url' => 'https://youtube.com',
                'slide_url' => '#',
                'is_published' => true,
            ],
            [
                'category_id' => $catWorkflow->id,
                'title' => 'Automasi Alur Kerja: Rangkum Notulen Rapat Jadi Action Plan',
                'slug' => 'automasi-alur-kerja-notulen-rapat-action-plan',
                'level' => 'explorer',
                'pillar' => 'workflow',
                'summary' => 'Cara praktis mengubah rekaman suara rapat atau catatan berantakan menjadi tabel tugas dengan PIC dan tenggat waktu.',
                'content' => '
                    <h2>Masalah Klasik Pasca Rapat</h2>
                    <p>Berapa banyak waktu yang kita habiskan hanya untuk merapikan coretan rapat dan membagikan tugas ke tim? Dengan workflow AI sederhana, pekerjaan 45 menit bisa diselesaikan dalam 3 menit.</p>

                    <h3>Alur Kerja 3 Langkah</h3>
                    <ol style="margin-left: 1.5rem; margin-bottom: 1.5rem;">
                        <li><strong>Langkah 1 (Transkripsi):</strong> Rekam audio rapat menggunakan aplikasi voice notes atau tool seperti Whisper / Otter.ai.</li>
                        <li><strong>Langkah 2 (Ekstraksi & Strukturisasi):</strong> Masukkan teks transkripsi mentah ke LLM dengan prompt ekstraksi tabel tugas.</li>
                        <li><strong>Langkah 3 (Distribusi):</strong> Salin tabel tugas ke grup koordinasi tim atau aplikasi manajemen proyek seperti Trello/Notion.</li>
                    </ol>

                    <pre style="background: #0f172a; color: #f8fafc; padding: 1.25rem; border-radius: 0.75rem; font-size: 0.85rem;"><code>Tolong baca transkrip rapat berikut.
Buatkan tabel rangkuman dengan kolom:
1. Poin Keputusan Utama
2. Tindakan / Action Items
3. Penanggung Jawab (PIC)
4. Perkiraan Tenggat Waktu (jika disebutkan)</code></pre>
                ',
                'reading_minutes' => 6,
                'video_url' => null,
                'slide_url' => '#',
                'is_published' => true,
            ],
            [
                'category_id' => $catOpportunity->id,
                'title' => 'AI for Opportunity: Menghasilkan Nilai Nyata dari Keterampilan AI',
                'slug' => 'ai-for-opportunity-menghasilkan-nilai-nyata',
                'level' => 'practitioner',
                'pillar' => 'opportunity',
                'summary' => 'Kisah inspiratif dan strategi alumni memanfaatkan AI untuk meningkatkan efisiensi bisnis, membuka jasa baru, dan memperluas jaringan.',
                'content' => '
                    <h2>Menghubungkan Belajar dengan Dampak Nyata</h2>
                    <p>Belajar AI terasa paling menyenangkan ketika dampaknya terasa langsung pada hidup kita — baik berupa penghematan waktu, peningkatan kepuasan klien, maupun terbukanya pintu rezeki baru.</p>

                    <div style="background: linear-gradient(135deg, rgba(37,99,235,0.08) 0%, rgba(14,165,233,0.08) 100%); border-radius: 0.75rem; padding: 1.5rem; margin: 1.5rem 0;">
                        <h4 style="margin-bottom: 0.5rem; color: var(--primary);">Studi Kasus Nyata Alumni Assalaam:</h4>
                        <p style="font-size: 0.95rem; margin-bottom: 0;">
                            Seorang alumni yang mengelola usaha konveksi berhasil memangkas waktu pembuatan draf katalog dan deskripsi produk dari 3 hari menjadi 2 jam menggunakan kombinasi prompt copywriting dan automasi Canva Magic. Efisiensi ini memungkinkan mereka melayani pesanan 2x lebih banyak.
                        </p>
                    </div>

                    <h3>3 Peluang yang Bisa Dieksplorasi Alumni:</h3>
                    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem;">
                        <li><strong>Jasa Automasi Konten untuk UMKM:</strong> Membantu pelaku usaha lokal merapikan kalender konten bulanan dengan bantuan AI.</li>
                        <li><strong>Konsultasi Efisiensi Internal:</strong> Membantu instansi sekolah atau yayasan merapikan arsip dokumen dan notulensi menggunakan LLM.</li>
                        <li><strong>Pembuatan Produk Digital & Portofolio:</strong> Menulis panduan, materi pelatihan, atau mini aplikasi bermanfaat.</li>
                    </ul>
                ',
                'reading_minutes' => 9,
                'video_url' => 'https://youtube.com',
                'slide_url' => '#',
                'is_published' => true,
            ],
        ];

        foreach ($materials as $item) {
            LearningMaterial::firstOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

        // 4. Seed Rich Prompt Library
        $prompts = [
            [
                'category_id' => $catProductivity->id,
                'title' => 'Formula Rangkuman Notulensi Rapat Jadi Action Plan',
                'slug' => 'formula-rangkuman-notulensi-rapat-action-plan',
                'target_role' => 'Umum & Produktivitas',
                'target_tool' => 'ChatGPT / Claude',
                'prompt_text' => 'Bertindaklah sebagai sekretaris eksekutif profesional.
Berikut adalah catatan mentah / transkrip rapat tim kami:
[Tempelkan teks notulensi rapat di sini]

Tolong buatkan dokumen ringkasan yang rapi dengan format:
1. Ringkasan Eksekutif (maksimal 3 kalimat)
2. Keputusan Kunci yang Disepakati
3. Tabel Matriks Tindakan Lanjut dengan kolom:
   - Tindakan / Tugas Spesifik
   - Penanggung Jawab (PIC)
   - Tenggat Waktu (Deadline)
   - Indikator Selesai
Gunakan bahasa Indonesia baku yang lugas dan profesional.',
                'instruction' => 'Ganti [Tempelkan teks notulensi rapat di sini] dengan draft coretan rapatmu.',
                'tags' => 'notulensi, rapat, produktivitas, action plan',
                'is_featured' => true,
                'copy_count' => 48,
            ],
            [
                'category_id' => $catOpportunity->id,
                'title' => 'Formula Copywriting Penawaran Produk Berbasis Manfaat',
                'slug' => 'formula-copywriting-penawaran-produk-berbasis-manfaat',
                'target_role' => 'Pebisnis / Marketer',
                'target_tool' => 'ChatGPT / Claude',
                'prompt_text' => 'Bertindaklah sebagai copywriter konversi tinggi dengan pengalaman 10 tahun.
Saya memiliki produk/layanan: [Nama & Jenis Produk]
Target audiens utama saya adalah: [Jelaskan siapa calon pembelimu]
Masalah terbesar yang dialami audiens saya saat ini adalah: [Masalah Utama]

Tolong buatkan teks penawaran bergaya percakapan santai untuk WhatsApp / Landing Page dengan struktur:
1. Hook menarik perhatian yang menyentuh masalah utama audiens
2. Agitasi dampak negatif jika masalah tidak diselesaikan
3. Solusi produk kami (jelaskan 3 fitur diubah menjadi MANFAAT nyata)
4. Social proof / alasan kenapa kami bisa dipercaya
5. Call to Action (CTA) yang jelas dan tanpa rasa tertekan
Gunakan gaya bahasa hangat, tidak klise, dan hindari kata-kata lebay.',
                'instruction' => 'Isi ketiga variabel bertanda kurung siku sesuai spesifikasi bisnismu.',
                'tags' => 'copywriting, penjualan, wa marketing, bisnis',
                'is_featured' => true,
                'copy_count' => 65,
            ],
            [
                'category_id' => $catPrompting->id,
                'title' => 'Draf Email & Pesan Resmi untuk Pihak Eksternal / Klien',
                'slug' => 'draf-email-dan-pesan-resmi-eksternal-klien',
                'target_role' => 'Umum & Produktivitas',
                'target_tool' => 'Claude / ChatGPT',
                'prompt_text' => 'Saya ingin mengirimkan email kepada: [Penerima, misal: Klien / Calon Partner / Pimpinan Yayasan]
Tujuan email ini adalah: [Tujuan, misal: Mengajukan proposal kerjasama / Follow up penawaran / Permohonan audiensi]
Poin-poin penting yang harus tercantum:
- [Poin 1]
- [Poin 2]
- [Poin 3]

Tolong buatkan 2 pilihan draf:
Pilihan A: Sangat formal dan terstruktur.
Pilihan B: Semi-formal, hangat, namun tetap menghormati tata krama.
Lengkapi dengan subjek email yang jelas dan menarik untuk dibuka.',
                'instruction' => 'Sangat cocok untuk komunikasi antar alumni atau korespondensi kantor resmi.',
                'tags' => 'email, komunikasi, surat, administrasi',
                'is_featured' => false,
                'copy_count' => 31,
            ],
            [
                'category_id' => $catTools->id,
                'title' => 'Perancang Kalender Konten Edukasi & Interaksi 30 Hari',
                'slug' => 'perancang-kalender-konten-edukasi-interaksi-30-hari',
                'target_role' => 'Penulis / Content Creator',
                'target_tool' => 'ChatGPT / Claude',
                'prompt_text' => 'Bertindaklah sebagai social media strategist.
Topik atau niche akun saya adalah: [Topik Akun, misal: Edukasi Parenting Islami / Bisnis F&B / Belajar Bahasa Arab]
Platform utama: [Instagram / TikTok / LinkedIn]

Tolong rancang matriks kalender konten untuk 4 minggu (30 hari) dengan pembagian pilar:
- 40% Edukasi praktis (Tips, how-to, tutorial)
- 30% Inspirasi & Storytelling (Kisah nyata, refleksi, why)
- 20% Interaksi & Engagement (Polling, tanya jawab, kuis)
- 10% Promosi produk / ajakan bergabung

Sajikan dalam bentuk tabel dengan kolom: Hari/Minggu, Format (Carousel/Reels/Single Image), Hook Utama, dan Ide Call to Action.',
                'instruction' => 'Ganti niche akun dan platform sesuai saluran media sosial yang kamu kelola.',
                'tags' => 'konten, social media, instagram, content creator',
                'is_featured' => true,
                'copy_count' => 54,
            ],
            [
                'category_id' => $catWorkflow->id,
                'title' => 'Perancang Rencana Pelajaran & Bahan Ajar Interaktif',
                'slug' => 'perancang-rencana-pelajaran-bahan-ajar-interaktif',
                'target_role' => 'Pendidik / Guru',
                'target_tool' => 'ChatGPT / Gemini',
                'prompt_text' => 'Bertindaklah sebagai konsultan kurikulum dan metode pengajaran inovatif.
Mata pelajaran / Topik: [Topik Pelajaran, misal: Sejarah Peradaban Islam / Fisika Dasar Gerak Lurus]
Tingkat peserta didik: [Tingkat, misal: Santri Pesantren Tingkat SMA / Mahasiswa Semester 1]
Durasi sesi: [Durasi, misal: 90 menit]

Tolong buatkan Modul Ajar terstruktur yang mencakup:
1. Tujuan Pembelajaran (Capaian Spesifik)
2. Pembuka / Ice Breaking Pemantik Rasa Ingin Tahu (15 menit)
3. Inti Pembelajaran dengan Studi Kasus Nyata (50 menit)
4. Aktivitas Interaktif Diskusi Kelompok (15 menit)
5. Refleksi & Penutup (10 menit)
6. 3 Pertanyaan Pemantik untuk Diskusi Mendalam',
                'instruction' => 'Sangat membantu para alumni yang berprofesi sebagai ustadz, guru, dosen, atau instruktur pelatihan.',
                'tags' => 'pendidikan, guru, modul ajar, silabus',
                'is_featured' => false,
                'copy_count' => 22,
            ],
            [
                'category_id' => $catTools->id,
                'title' => 'Asisten Review & Optimasi Kode Program untuk Pemula',
                'slug' => 'asisten-review-optimasi-kode-program-pemula',
                'target_role' => 'Developer / IT',
                'target_tool' => 'Cursor / Claude',
                'prompt_text' => 'Bertindaklah sebagai senior software engineer yang sabar dan membimbing junior programmer.
Berikut adalah potongan kode program yang saya tulis:
```[Bahasa, misal: php/javascript/python]
[Tempelkan kode program di sini]
```

Tolong bantu saya:
1. Jelaskan secara ringkas apa yang dilakukan kode ini
2. Apakah ada potensi bug, celah keamanan, atau inefisiensi performa?
3. Tuliskan versi refactoring yang lebih bersih, mematuhi best practice (clean code), dan mudah dibaca
4. Berikan penjelasan mengapa perubahan tersebut lebih baik.',
                'instruction' => 'Tempelkan kode dan tentukan bahasa pemrogramannya.',
                'tags' => 'coding, programmer, php, refactoring',
                'is_featured' => false,
                'copy_count' => 39,
            ],
        ];

        foreach ($prompts as $p) {
            \App\Models\Prompt::firstOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }

        // 5. Seed Events & Study Group Schedule
        $events = [
            [
                'title' => 'AI Study Group Sesi #1: AI untuk Produktivitas Sehari-hari',
                'slug' => 'ai-study-group-sesi-1-ai-untuk-produktivitas-sehari-hari',
                'description' => 'Sesi belajar bersama perdana mengenal dan mempraktikkan AI untuk efisiensi pekerjaan harian: cara memberikan instruksi yang tepat, teknik prompt dasar, dan hands-on langsung.',
                'event_date' => now()->addDays(4)->setTime(19, 30),
                'duration_minutes' => 90,
                'location_url' => 'https://zoom.us/j/sample-ikmas-ai',
                'speaker_name' => 'Tim Fasilitator IKMAS AI',
                'speaker_title' => 'Penggiat AI Alumni Assalaam',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Workshop Daring: Merancang Konten & Desain Visual dengan Canva AI',
                'slug' => 'workshop-daring-merancang-konten-desain-visual-canva-ai',
                'description' => 'Eksplorasi pembuatan materi presentasi, poster kegiatan, dan konten media sosial yang rapi dan memikat dengan bantuan kecerdasan buatan dalam hitungan menit.',
                'event_date' => now()->addDays(11)->setTime(20, 00),
                'duration_minutes' => 90,
                'location_url' => 'https://zoom.us/j/sample-ikmas-ai-2',
                'speaker_name' => 'Alumni Praktisi Kreatif',
                'speaker_title' => 'Graphic Designer & Content Strategist',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Sharing Session: Pengantar & Peluncuran Komunitas IKMAS AI',
                'slug' => 'sharing-session-pengantar-peluncuran-komunitas-ikmas-ai',
                'description' => 'Temu silaturahmi daring dan sosialisasi visi IKMAS AI Learning Center serta perkenalan Agen Garuda kepada rekan-rekan alumni.',
                'event_date' => now()->subDays(7)->setTime(19, 30),
                'duration_minutes' => 60,
                'location_url' => null,
                'speaker_name' => 'Garuda & Inisiator Komunitas',
                'speaker_title' => 'Pengurus IKMAS AI',
                'status' => 'completed',
                'recording_url' => 'https://youtube.com',
                'materials_url' => '#',
            ],
        ];

        foreach ($events as $ev) {
            \App\Models\Event::firstOrCreate(
                ['slug' => $ev['slug']],
                $ev
            );
        }

        // 6. Seed Showcase Projects from Alumni
        $alumni1 = User::firstOrCreate(
            ['email' => 'fajar.alumni@example.com'],
            [
                'name' => 'Fajar Nugraha',
                'password' => Hash::make('password123'),
                'whatsapp_number' => '081234567811',
                'alumni_year' => '2014',
                'role' => 'member',
            ]
        );

        $alumni2 = User::firstOrCreate(
            ['email' => 'anisa.alumni@example.com'],
            [
                'name' => 'Anisa Rahmawati',
                'password' => Hash::make('password123'),
                'whatsapp_number' => '081234567822',
                'alumni_year' => '2018',
                'role' => 'member',
            ]
        );

        $showcases = [
            [
                'user_id' => $alumni1->id,
                'title' => 'Bot WA Layanan Pelanggan Otomatis Toko Batik Alumni',
                'slug' => 'bot-wa-layanan-pelanggan-otomatis-toko-batik-alumni',
                'description' => 'Solusi chatbot cerdas menggunakan integrasi OpenAI API dan WhatsApp Cloud API untuk menjawab pertanyaan seputar stok produk, ukuran kemeja, dan ongkos kirim secara otomatis 24 jam non-stop.',
                'tools_used' => 'ChatGPT API, Node.js, WhatsApp Cloud API',
                'project_url' => 'https://tokoalumni.test',
                'impact_story' => 'Memangkas waktu tunggu respons calon pembeli dari 30 menit menjadi instan 5 detik, serta meningkatkan konversi penjualan online hingga 35% dalam 2 bulan pertama.',
                'status' => 'approved',
            ],
            [
                'user_id' => $alumni2->id,
                'title' => 'E-Book Ilustrasi Cerita Edukasi Santri dengan Midjourney',
                'slug' => 'e-book-ilustrasi-cerita-edukasi-santri-dengan-midjourney',
                'description' => 'Proyek penerbitan digital mandiri yang memadukan cerita adab harian santri dengan ilustrasi visual berkualitas tinggi yang dibuat melalui panduan prompt Midjourney dan diedit di Canva.',
                'tools_used' => 'Midjourney v6, Canva Magic, Claude',
                'project_url' => 'https://karyasantri.test',
                'impact_story' => 'Berhasil memproduksi 40 halaman ilustrasi penuh dalam 1 minggu (yang biasanya memakan waktu 2 bulan) dan telah dibaca oleh lebih dari 500 santri baru.',
                'status' => 'approved',
            ],
        ];

        foreach ($showcases as $sc) {
            \App\Models\Showcase::firstOrCreate(
                ['slug' => $sc['slug']],
                $sc
            );
        }
    }
}
