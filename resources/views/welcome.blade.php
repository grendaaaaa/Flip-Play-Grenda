<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlipPlay — Media & Collection API Studio</title>
    <!-- Fonts from src -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN configured with FlipPlay Theme -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'fp-bg': '#0C0C0C',
                        'fp-surface': '#141414',
                        'fp-surface2': '#1C1C1E',
                        'fp-border': '#2A2A2A',
                        'fp-text': '#F5F0E8',
                        'fp-muted': '#8A8A8A',
                        'fp-subtle': '#555555',
                        'fp-gold': '#D4AF37',
                        'fp-navy': '#0F1C3F',
                        'fp-red': '#C0392B',
                    },
                    fontFamily: {
                        sans: ['"DM Sans"', 'system-ui', 'sans-serif'],
                        display: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0C0C0C;
            color: #F5F0E8;
            font-family: 'DM Sans', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .font-display {
            font-family: 'Cormorant Garamond', Georgia, serif;
        }

        /* Nav link underline */
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 1px;
            background: #D4AF37;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }

        /* Gold CTA Button */
        .btn-gold {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.5);
            color: #D4AF37;
            background: transparent;
            transition: color 0.35s ease;
        }
        .btn-gold::before {
            content: '';
            position: absolute;
            inset: 0;
            background: #D4AF37;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 0;
        }
        .btn-gold:hover {
            color: #0C0C0C;
        }
        .btn-gold:hover::before {
            transform: scaleX(1);
        }
        .btn-gold span {
            position: relative;
            z-index: 1;
        }

        /* Outline button */
        .btn-ghost {
            border: 1px solid #2A2A2A;
            color: #8A8A8A;
            transition: all 0.25s ease;
        }
        .btn-ghost:hover {
            border-color: #D4AF37;
            color: #F5F0E8;
        }

        /* Underline inputs from FlipPlay */
        .fp-input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid #2A2A2A;
            padding: 8px 0;
            color: #F5F0E8;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .fp-input:focus {
            border-bottom-color: #D4AF37;
        }
        .fp-input::placeholder {
            color: #555555;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
        }
        ::-webkit-scrollbar-track {
            background: #0C0C0C;
        }
        ::-webkit-scrollbar-thumb {
            background: #2A2A2A;
        }
    </style>
</head>
<body class="bg-fp-bg text-fp-text selection:bg-fp-gold selection:text-fp-bg">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 bg-[#0C0C0C]/90 backdrop-blur-md border-b border-fp-border transition-all">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Brand -->
            <div class="flex items-center gap-4">
                <a href="#" class="font-display text-3xl italic text-fp-text tracking-tight hover:text-fp-gold transition-colors">
                    FlipPlay
                </a>
                <span class="text-[10px] tracking-widest uppercase px-2.5 py-0.5 border border-fp-gold/30 text-fp-gold rounded-full font-light">
                    REST API STUDIO
                </span>
            </div>

            <!-- User Selector & Actions -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-xs text-fp-muted">
                    <span class="font-light tracking-wide">PENGGUNA AKTIF:</span>
                    <select id="activeUserSelect" onchange="loadUserCollection()" class="bg-fp-surface border border-fp-border text-fp-text text-xs rounded px-3 py-2 focus:border-fp-gold focus:outline-none cursor-pointer">
                        <option value="1">Budi Santoso (ID: 1)</option>
                        <option value="2">Siti Rahma (ID: 2)</option>
                        <option value="3">Demo User (ID: 3)</option>
                    </select>
                </div>

                <button onclick="openModal('addMediaModal')" class="btn-gold px-4 py-2 text-xs tracking-wider uppercase font-medium rounded cursor-pointer">
                    <span>+ Tambah Media</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-6 py-10 space-y-12">
        <!-- Toast Notification -->
        <div id="toast" class="hidden fixed bottom-6 right-6 z-50 bg-fp-surface border border-fp-border text-xs px-5 py-3 rounded shadow-2xl flex items-center gap-3 transition-all"></div>

        <!-- Section 1: User's Personal Collection -->
        <section class="border border-fp-border bg-fp-surface p-7 rounded-sm">
            <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-fp-border pb-5 mb-6 gap-3">
                <div>
                    <span class="text-[10px] tracking-widest text-fp-gold uppercase">Koleksi Pribadi</span>
                    <h2 class="font-display text-2xl md:text-3xl italic text-fp-text mt-0.5">
                        Koleksi Media: <span id="collectionUserName" class="text-fp-gold">...</span>
                    </h2>
                    <p class="text-xs text-fp-muted mt-1 font-light">
                        Mengakses endpoint <code class="text-fp-gold/90 font-mono">GET /api/users/{userId}/collection</code> (Relasi Many-to-Many via Eloquent)
                    </p>
                </div>
                <div class="text-xs tracking-widest uppercase text-fp-muted border border-fp-border px-3 py-1.5 self-start md:self-auto">
                    Total: <span id="collectionCount" class="text-fp-gold font-medium">0</span> Tersimpan
                </div>
            </div>

            <!-- Collection List Grid -->
            <div id="collectionGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Loaded Dynamically -->
            </div>
        </section>

        <!-- Section 2: Catalog Explorer -->
        <section class="border border-fp-border bg-fp-surface p-7 rounded-sm space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between border-b border-fp-border pb-5 gap-4">
                <div>
                    <span class="text-[10px] tracking-widest text-fp-gold uppercase">Katalog FlipPlay</span>
                    <h2 class="font-display text-2xl md:text-3xl italic text-fp-text mt-0.5">
                        Daftar Semua Media
                    </h2>
                    <p class="text-xs text-fp-muted mt-1 font-light">
                        Mengakses endpoint <code class="text-fp-gold/90 font-mono">GET /api/media</code> (CRUD Standar + Query Search & Type Filtering)
                    </p>
                </div>

                <!-- Search and Filters -->
                <div class="flex flex-wrap items-center gap-3">
                    <div class="w-56">
                        <input type="text" id="searchInput" placeholder="Cari judul media..." oninput="debouncedFetchMedia()" class="fp-input">
                    </div>
                    
                    <select id="typeFilter" onchange="fetchMedia()" class="bg-fp-bg border border-fp-border text-xs text-fp-text px-3 py-2 rounded focus:border-fp-gold focus:outline-none cursor-pointer">
                        <option value="">Semua Format</option>
                        <option value="Movie">Movie</option>
                        <option value="Series">Series</option>
                        <option value="Anime">Anime</option>
                        <option value="Podcast">Podcast</option>
                        <option value="Music">Music</option>
                        <option value="Book">Book</option>
                    </select>

                    <button onclick="fetchMedia()" class="btn-ghost px-3 py-2 text-xs rounded cursor-pointer">
                        Segarkan
                    </button>
                </div>
            </div>

            <!-- Media Grid -->
            <div id="mediaGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <!-- Loaded Dynamically -->
            </div>
        </section>
    </main>

    <!-- Modal: Tambah Media Baru (POST /api/media) -->
    <div id="addMediaModal" class="hidden fixed inset-0 bg-[#0C0C0C]/85 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-fp-surface border border-fp-border max-w-lg w-full p-8 rounded-sm shadow-2xl space-y-5">
            <div class="flex items-center justify-between border-b border-fp-border pb-3">
                <div>
                    <span class="text-[9px] tracking-widest text-fp-gold uppercase">Form Input</span>
                    <h3 class="font-display text-2xl italic text-fp-text">Tambah Media ke FlipPlay</h3>
                </div>
                <button onclick="closeModal('addMediaModal')" class="text-fp-muted hover:text-fp-gold text-2xl cursor-pointer leading-none">&times;</button>
            </div>

            <form id="addMediaForm" onsubmit="submitNewMedia(event)" class="space-y-4">
                <div>
                    <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Judul Media *</label>
                    <input type="text" id="new_title" required placeholder="Contoh: Spirited Away" class="fp-input">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Format / Tipe *</label>
                        <select id="new_type" required class="bg-fp-bg border border-fp-border text-xs text-fp-text w-full px-2 py-2 rounded focus:border-fp-gold focus:outline-none">
                            <option value="Movie">Movie</option>
                            <option value="Series">Series</option>
                            <option value="Anime">Anime</option>
                            <option value="Podcast">Podcast</option>
                            <option value="Music">Music</option>
                            <option value="Book">Book</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Genre *</label>
                        <input type="text" id="new_genre" required placeholder="Fantasy / Adventure" class="fp-input">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Tahun Rilis</label>
                        <input type="number" id="new_release_year" placeholder="2001" class="fp-input">
                    </div>
                    <div>
                        <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Durasi (Menit)</label>
                        <input type="number" id="new_duration" placeholder="125" class="fp-input">
                    </div>
                    <div>
                        <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Rating (0-10)</label>
                        <input type="number" step="0.1" min="0" max="10" id="new_rating" placeholder="8.6" class="fp-input">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Sinopsis / Deskripsi</label>
                    <textarea id="new_description" rows="2" placeholder="Deskripsi singkat konten..." class="fp-input"></textarea>
                </div>

                <div class="flex justify-end items-center gap-3 pt-4 border-t border-fp-border">
                    <button type="button" onclick="closeModal('addMediaModal')" class="text-xs text-fp-muted hover:text-fp-text px-3 py-2 cursor-pointer">Batal</button>
                    <button type="submit" class="btn-gold px-5 py-2 text-xs tracking-wider uppercase font-medium rounded cursor-pointer">
                        <span>Simpan Media</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Simpan ke Koleksi (POST /api/users/{userId}/collection) -->
    <div id="collectModal" class="hidden fixed inset-0 bg-[#0C0C0C]/85 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-fp-surface border border-fp-border max-w-md w-full p-8 rounded-sm shadow-2xl space-y-5">
            <div class="flex items-center justify-between border-b border-fp-border pb-3">
                <div>
                    <span class="text-[9px] tracking-widest text-fp-gold uppercase">Simpan Koleksi</span>
                    <h3 class="font-display text-2xl italic text-fp-text">Koleksi Pengguna</h3>
                </div>
                <button onclick="closeModal('collectModal')" class="text-fp-muted hover:text-fp-gold text-2xl cursor-pointer leading-none">&times;</button>
            </div>

            <form id="collectForm" onsubmit="submitAddToCollection(event)" class="space-y-4">
                <input type="hidden" id="collect_media_id">
                
                <div class="bg-fp-bg p-3 border border-fp-border rounded">
                    <span class="text-[10px] text-fp-muted uppercase tracking-wider block">Media Terpilih</span>
                    <span id="collect_media_title" class="font-display text-lg italic text-fp-gold block mt-0.5"></span>
                </div>

                <div>
                    <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Status Progres</label>
                    <select id="collect_status" class="bg-fp-bg border border-fp-border text-xs text-fp-text w-full px-3 py-2 rounded focus:border-fp-gold focus:outline-none">
                        <option value="watching">Watching (Sedang Menikmati)</option>
                        <option value="completed">Completed (Selesai)</option>
                        <option value="plan_to_watch">Plan to Watch (Direncanakan)</option>
                        <option value="on_hold">On Hold (Ditunda)</option>
                        <option value="dropped">Dropped (Berhenti)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Skor Rating Pribadi (1 - 10)</label>
                    <input type="number" min="1" max="10" id="collect_rating" placeholder="Contoh: 9" class="fp-input">
                </div>

                <div>
                    <label class="block text-[11px] tracking-widest uppercase text-fp-muted mb-1">Catatan / Review Pengguna</label>
                    <textarea id="collect_notes" rows="2" placeholder="Bagikan impresi atau momen favorit..." class="fp-input"></textarea>
                </div>

                <div class="flex justify-end items-center gap-3 pt-4 border-t border-fp-border">
                    <button type="button" onclick="closeModal('collectModal')" class="text-xs text-fp-muted hover:text-fp-text px-3 py-2 cursor-pointer">Batal</button>
                    <button type="submit" class="btn-gold px-5 py-2 text-xs tracking-wider uppercase font-medium rounded cursor-pointer">
                        <span>Konfirmasi Koleksi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Interactive Script -->
    <script>
        let searchTimer = null;

        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            toast.className = `fixed bottom-6 right-6 z-50 border text-xs px-5 py-3 rounded shadow-2xl flex items-center gap-3 transition-all ${isError ? 'bg-[#1C1212] border-fp-red text-rose-200' : 'bg-fp-surface border-fp-gold text-fp-gold'}`;
            toast.innerHTML = `<span class="text-sm">${isError ? '⚠️' : '✦'}</span><span>${msg}</span>`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function debouncedFetchMedia() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(fetchMedia, 300);
        }

        // 1. Fetch Media List
        async function fetchMedia() {
            const search = document.getElementById('searchInput').value;
            const type = document.getElementById('typeFilter').value;
            const grid = document.getElementById('mediaGrid');

            grid.innerHTML = '<div class="col-span-full py-12 text-center text-fp-muted text-xs font-light tracking-wider">Memuat katalog media...</div>';

            try {
                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (type) params.append('type', type);

                const res = await fetch(`/api/media?${params.toString()}`);
                const data = await res.json();

                if (!data.data || data.data.length === 0) {
                    grid.innerHTML = '<div class="col-span-full py-12 text-center text-fp-subtle text-xs font-light">Tidak ada media yang cocok dengan pencarian.</div>';
                    return;
                }

                grid.innerHTML = data.data.map(m => `
                    <div class="bg-fp-surface2 border border-fp-border p-5 rounded-sm flex flex-col justify-between hover:border-fp-gold/40 transition-all duration-300">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-[9px] tracking-widest uppercase px-2 py-0.5 border border-fp-border text-fp-muted rounded-full">
                                    ${m.type}
                                </span>
                                <span class="text-xs font-medium text-fp-gold">
                                    ★ ${m.rating}
                                </span>
                            </div>
                            <h3 class="font-display text-xl italic text-fp-text line-clamp-1">${m.title}</h3>
                            <p class="text-xs text-fp-muted font-light">${m.genre} ${m.release_year ? '• ' + m.release_year : ''}</p>
                            <p class="text-[11px] text-fp-subtle leading-relaxed line-clamp-2">${m.description || 'Tidak ada deskripsi.'}</p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-fp-border flex items-center justify-between gap-2">
                            <button onclick="promptCollect(${m.id}, '${escapeQuote(m.title)}')" class="btn-ghost flex-1 py-1.5 text-xs tracking-wider uppercase rounded cursor-pointer">
                                + Koleksi
                            </button>
                            <button onclick="deleteMedia(${m.id}, '${escapeQuote(m.title)}')" class="px-2.5 py-1.5 text-xs text-fp-muted hover:text-fp-red transition-colors cursor-pointer" title="Hapus Media">
                                ✕
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                grid.innerHTML = '<div class="col-span-full py-12 text-center text-fp-red text-xs">Gagal memuat: ' + err.message + '</div>';
            }
        }

        // 2. Fetch User Collection
        async function loadUserCollection() {
            const userId = document.getElementById('activeUserSelect').value;
            const grid = document.getElementById('collectionGrid');
            grid.innerHTML = '<div class="col-span-full py-8 text-center text-fp-muted text-xs font-light tracking-wider">Memuat koleksi pengguna...</div>';

            try {
                const res = await fetch(`/api/users/${userId}/collection`);
                const json = await res.json();

                if (!res.ok) throw new Error(json.message || 'Gagal memuat koleksi');

                document.getElementById('collectionUserName').textContent = json.data.user.name;
                document.getElementById('collectionCount').textContent = json.data.collection_count;

                if (json.data.collection.length === 0) {
                    grid.innerHTML = '<div class="col-span-full py-8 text-center text-fp-subtle text-xs font-light">Belum ada media yang dimasukkan ke koleksi ini.</div>';
                    return;
                }

                grid.innerHTML = json.data.collection.map(item => `
                    <div class="bg-fp-surface2 border border-fp-border p-5 rounded-sm flex flex-col justify-between hover:border-fp-gold/40 transition-all duration-300">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-[9px] tracking-widest uppercase px-2 py-0.5 border ${getStatusBorder(item.pivot_data?.status)} text-fp-gold rounded-full">
                                    ${item.pivot_data?.status || 'saved'}
                                </span>
                                <span class="text-xs text-fp-gold">
                                    Skor: <strong class="font-medium">${item.pivot_data?.personal_rating || item.rating}</strong>/10
                                </span>
                            </div>
                            <h4 class="font-display text-xl italic text-fp-text">${item.title}</h4>
                            <p class="text-xs text-fp-muted font-light">${item.type} • ${item.genre}</p>
                            ${item.pivot_data?.notes ? `<div class="bg-fp-bg p-3 border border-fp-border text-xs text-fp-muted italic rounded-sm font-light">"${item.pivot_data.notes}"</div>` : ''}
                        </div>
                        <div class="mt-5 pt-3 border-t border-fp-border flex items-center justify-end">
                            <button onclick="removeFromCollection(${userId}, ${item.id}, '${escapeQuote(item.title)}')" class="text-[11px] tracking-wider uppercase text-fp-muted hover:text-fp-red transition-colors cursor-pointer">
                                Lepas dari Koleksi →
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                grid.innerHTML = '<div class="col-span-full py-8 text-center text-fp-red text-xs">' + err.message + '</div>';
            }
        }

        function promptCollect(mediaId, title) {
            document.getElementById('collect_media_id').value = mediaId;
            document.getElementById('collect_media_title').textContent = title;
            document.getElementById('collect_notes').value = '';
            document.getElementById('collect_rating').value = '';
            openModal('collectModal');
        }

        async function submitAddToCollection(e) {
            e.preventDefault();
            const userId = document.getElementById('activeUserSelect').value;
            const mediaId = document.getElementById('collect_media_id').value;
            const status = document.getElementById('collect_status').value;
            const rating = document.getElementById('collect_rating').value;
            const notes = document.getElementById('collect_notes').value;

            try {
                const res = await fetch(`/api/users/${userId}/collection`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        media_id: parseInt(mediaId),
                        status: status,
                        personal_rating: rating ? parseInt(rating) : null,
                        notes: notes || null
                    })
                });

                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Gagal menyimpan ke koleksi');

                showToast(json.message);
                closeModal('collectModal');
                loadUserCollection();
            } catch (err) {
                showToast(err.message, true);
            }
        }

        async function removeFromCollection(userId, mediaId, title) {
            if (!confirm(`Hapus '${title}' dari koleksi pengguna?`)) return;

            try {
                const res = await fetch(`/api/users/${userId}/collection/${mediaId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Gagal menghapus');

                showToast(json.message);
                loadUserCollection();
            } catch (err) {
                showToast(err.message, true);
            }
        }

        async function submitNewMedia(e) {
            e.preventDefault();
            const payload = {
                title: document.getElementById('new_title').value,
                type: document.getElementById('new_type').value,
                genre: document.getElementById('new_genre').value,
                release_year: document.getElementById('new_release_year').value ? parseInt(document.getElementById('new_release_year').value) : null,
                duration_minutes: document.getElementById('new_duration').value ? parseInt(document.getElementById('new_duration').value) : null,
                rating: document.getElementById('new_rating').value ? parseFloat(document.getElementById('new_rating').value) : 0,
                description: document.getElementById('new_description').value || null
            };

            try {
                const res = await fetch('/api/media', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();

                if (!res.ok) {
                    const errorMsg = json.errors ? Object.values(json.errors).flat().join('<br>') : json.message;
                    throw new Error(errorMsg || 'Gagal menambahkan media');
                }

                showToast(json.message);
                document.getElementById('addMediaForm').reset();
                closeModal('addMediaModal');
                fetchMedia();
            } catch (err) {
                showToast(err.message, true);
            }
        }

        async function deleteMedia(id, title) {
            if (!confirm(`Hapus '${title}' dari katalog sistem FlipPlay?`)) return;

            try {
                const res = await fetch(`/api/media/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Gagal menghapus');

                showToast(json.message);
                fetchMedia();
                loadUserCollection();
            } catch (err) {
                showToast(err.message, true);
            }
        }

        function getStatusBorder(status) {
            switch(status) {
                case 'completed': return 'border-emerald-500/40 text-emerald-400';
                case 'watching': return 'border-fp-gold text-fp-gold';
                default: return 'border-fp-border text-fp-muted';
            }
        }

        function escapeQuote(str) {
            return (str || '').replace(/'/g, "\\'");
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchMedia();
            loadUserCollection();
        });
    </script>
</body>
</html>
