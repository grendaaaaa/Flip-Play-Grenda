<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlipPlay - Media & Collection Manager</title>
    <!-- Tailwind CSS CDN for instant styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <!-- Navbar -->
    <nav class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-rose-500 to-indigo-600 flex items-center justify-center font-black text-xl shadow-lg shadow-rose-500/20">
                    FP
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">FlipPlay <span class="text-xs px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-semibold border border-rose-500/20">REST API UI</span></h1>
                    <p class="text-xs text-slate-400">Media Library & Personal Collection Management</p>
                </div>
            </div>

            <!-- User Selector & Actions -->
            <div class="flex items-center gap-3">
                <label class="text-xs font-medium text-slate-400">Aktif Sebagai Pengguna:</label>
                <select id="activeUserSelect" onchange="loadUserCollection()" class="bg-slate-800 border border-slate-700 text-sm rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-200">
                    <option value="1">Budi Santoso (ID: 1)</option>
                    <option value="2">Siti Rahma (ID: 2)</option>
                    <option value="3">Demo User (ID: 3)</option>
                </select>
                <button onclick="openModal('addMediaModal')" class="bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm px-4 py-1.5 rounded-lg transition-all shadow-md shadow-rose-600/30 flex items-center gap-1.5">
                    <span>+</span> Tambah Media
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto p-6 space-y-8">
        <!-- Toast Notification -->
        <div id="toast" class="hidden fixed bottom-6 right-6 z-50 bg-slate-900 border border-slate-700 text-sm px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all"></div>

        <!-- Section 1: User's Collection -->
        <section class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <span class="text-rose-400">⭐</span> Koleksi Pengguna: <span id="collectionUserName" class="text-rose-400">...</span>
                    </h2>
                    <p class="text-xs text-slate-400">Endpoint: <code class="bg-slate-800 px-1.5 py-0.5 rounded text-rose-300">GET /api/users/{userId}/collection</code> (Custom Action)</p>
                </div>
                <div class="text-xs bg-slate-800 px-3 py-1 rounded-full text-slate-300 border border-slate-700">
                    Total: <span id="collectionCount" class="font-bold text-white">0</span> Media
                </div>
            </div>

            <!-- Collection Grid -->
            <div id="collectionGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Dynamically rendered -->
            </div>
        </section>

        <!-- Section 2: Explore Catalog (CRUD Media) -->
        <section class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 shadow-xl space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <span class="text-indigo-400">🎬</span> Katalog Media FlipPlay
                    </h2>
                    <p class="text-xs text-slate-400">Endpoint: <code class="bg-slate-800 px-1.5 py-0.5 rounded text-indigo-300">GET /api/media</code> (CRUD Standar + Search Filter)</p>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <input type="text" id="searchInput" placeholder="Cari judul..." oninput="debouncedFetchMedia()" class="bg-slate-800 border border-slate-700 text-sm rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-48 text-slate-200">
                    
                    <select id="typeFilter" onchange="fetchMedia()" class="bg-slate-800 border border-slate-700 text-sm rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-200">
                        <option value="">Semua Tipe</option>
                        <option value="Movie">Movie</option>
                        <option value="Series">Series</option>
                        <option value="Anime">Anime</option>
                        <option value="Podcast">Podcast</option>
                        <option value="Music">Music</option>
                    </select>

                    <button onclick="fetchMedia()" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm px-3 py-1.5 rounded-lg border border-slate-700">
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Media Catalog Cards -->
            <div id="mediaGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Dynamically rendered -->
            </div>
        </section>
    </main>

    <!-- Modal: Add Media to Catalog -->
    <div id="addMediaModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-base text-white">Tambah Media Baru ke Katalog</h3>
                <button onclick="closeModal('addMediaModal')" class="text-slate-400 hover:text-white font-bold">&times;</button>
            </div>
            <form id="addMediaForm" onsubmit="submitNewMedia(event)" class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Judul Media *</label>
                    <input type="text" id="new_title" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Tipe *</label>
                        <select id="new_type" required class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                            <option value="Movie">Movie</option>
                            <option value="Series">Series</option>
                            <option value="Anime">Anime</option>
                            <option value="Podcast">Podcast</option>
                            <option value="Music">Music</option>
                            <option value="Book">Book</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Genre *</label>
                        <input type="text" id="new_genre" required placeholder="e.g. Action / Sci-Fi" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Tahun Rilis</label>
                        <input type="number" id="new_release_year" placeholder="2024" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Durasi (Menit)</label>
                        <input type="number" id="new_duration" placeholder="120" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Rating (0-10)</label>
                        <input type="number" step="0.1" min="0" max="10" id="new_rating" placeholder="8.5" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Deskripsi Singkat</label>
                    <textarea id="new_description" rows="2" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                    <button type="button" onclick="closeModal('addMediaModal')" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="bg-rose-600 hover:bg-rose-500 px-4 py-2 rounded-lg text-xs font-semibold text-white">Simpan Media</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Add / Edit In Collection -->
    <div id="collectModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-base text-white">Simpan ke Koleksi Pengguna</h3>
                <button onclick="closeModal('collectModal')" class="text-slate-400 hover:text-white font-bold">&times;</button>
            </div>
            <form id="collectForm" onsubmit="submitAddToCollection(event)" class="space-y-3">
                <input type="hidden" id="collect_media_id">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Media</label>
                    <p id="collect_media_title" class="font-bold text-rose-400 text-sm"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Status Nonton / Nikmati</label>
                    <select id="collect_status" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="watching">Watching (Sedang Ditonton)</option>
                        <option value="completed">Completed (Selesai)</option>
                        <option value="plan_to_watch">Plan to Watch (Akan Datang)</option>
                        <option value="on_hold">On Hold (Ditunda)</option>
                        <option value="dropped">Dropped (Berhenti)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Rating Pribadi Anda (1 - 10)</label>
                    <input type="number" min="1" max="10" id="collect_rating" placeholder="Contoh: 9" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Catatan / Review Singkat</label>
                    <textarea id="collect_notes" rows="2" placeholder="Tulis review atau momen favorit..." class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                    <button type="button" onclick="closeModal('collectModal')" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 px-4 py-2 rounded-lg text-xs font-semibold text-white">Simpan ke Koleksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Frontend Script interacting directly with Laravel 12 API -->
    <script>
        let searchTimer = null;

        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            toast.className = `fixed bottom-6 right-6 z-50 border text-sm px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all ${isError ? 'bg-rose-950/90 border-rose-700 text-rose-200' : 'bg-emerald-950/90 border-emerald-700 text-emerald-200'}`;
            toast.innerHTML = `<span>${isError ? '⚠️' : '✅'}</span><span>${msg}</span>`;
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

        // 1. Fetch Media List from GET /api/media
        async function fetchMedia() {
            const search = document.getElementById('searchInput').value;
            const type = document.getElementById('typeFilter').value;
            const grid = document.getElementById('mediaGrid');

            grid.innerHTML = '<div class="col-span-full py-8 text-center text-slate-500 text-sm">Memuat data media...</div>';

            try {
                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (type) params.append('type', type);

                const res = await fetch(`/api/media?${params.toString()}`);
                const data = await res.json();

                if (!data.data || data.data.length === 0) {
                    grid.innerHTML = '<div class="col-span-full py-8 text-center text-slate-500 text-sm">Tidak ada media ditemukan.</div>';
                    return;
                }

                grid.innerHTML = data.data.map(m => `
                    <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-4 flex flex-col justify-between hover:border-slate-600 transition-all">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${getTypeBadge(m.type)}">${m.type}</span>
                                <span class="text-xs font-bold text-amber-400">★ ${m.rating}</span>
                            </div>
                            <h3 class="font-bold text-sm text-white line-clamp-1">${m.title}</h3>
                            <p class="text-xs text-slate-400">${m.genre} ${m.release_year ? '• ' + m.release_year : ''}</p>
                            <p class="text-[11px] text-slate-400 line-clamp-2">${m.description || 'Tidak ada deskripsi.'}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-700/60 flex items-center justify-between gap-2">
                            <button onclick="promptCollect(${m.id}, '${escapeQuote(m.title)}')" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs py-1.5 rounded-lg font-semibold transition-all">
                                + Koleksi
                            </button>
                            <button onclick="deleteMedia(${m.id}, '${escapeQuote(m.title)}')" class="px-2 py-1.5 text-xs text-rose-400 hover:bg-rose-500/10 rounded-lg transition-all" title="Hapus Media">
                                🗑️
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                grid.innerHTML = '<div class="col-span-full py-8 text-center text-rose-400 text-sm">Gagal memuat media: ' + err.message + '</div>';
            }
        }

        // 2. Fetch User Collection from GET /api/users/{userId}/collection
        async function loadUserCollection() {
            const userId = document.getElementById('activeUserSelect').value;
            const grid = document.getElementById('collectionGrid');
            grid.innerHTML = '<div class="col-span-full py-6 text-center text-slate-500 text-sm">Memuat koleksi...</div>';

            try {
                const res = await fetch(`/api/users/${userId}/collection`);
                const json = await res.json();

                if (!res.ok) throw new Error(json.message || 'Gagal memuat');

                document.getElementById('collectionUserName').textContent = json.data.user.name;
                document.getElementById('collectionCount').textContent = json.data.collection_count;

                if (json.data.collection.length === 0) {
                    grid.innerHTML = '<div class="col-span-full py-6 text-center text-slate-500 text-sm">Pengguna ini belum menambahkan media ke koleksi pribadinya.</div>';
                    return;
                }

                grid.innerHTML = json.data.collection.map(item => `
                    <div class="bg-slate-800/90 border border-slate-700/80 rounded-xl p-4 flex flex-col justify-between hover:border-slate-600 transition-all">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${getStatusBadge(item.pivot_data?.status)}">${item.pivot_data?.status || 'saved'}</span>
                                <span class="text-xs font-bold text-amber-400">Score: ${item.pivot_data?.personal_rating || item.rating}/10</span>
                            </div>
                            <h4 class="font-bold text-white text-sm">${item.title}</h4>
                            <p class="text-xs text-slate-400">${item.type} • ${item.genre}</p>
                            ${item.pivot_data?.notes ? `<div class="bg-slate-900/80 p-2 rounded text-xs text-slate-300 italic border border-slate-800">"${item.pivot_data.notes}"</div>` : ''}
                        </div>
                        <div class="mt-3 pt-3 border-t border-slate-700/60 flex items-center justify-end">
                            <button onclick="removeFromCollection(${userId}, ${item.id}, '${escapeQuote(item.title)}')" class="text-xs text-rose-400 hover:text-rose-300 hover:underline">
                                Hapus dari Koleksi
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                grid.innerHTML = '<div class="col-span-full py-6 text-center text-rose-400 text-sm">' + err.message + '</div>';
            }
        }

        // 3. Add to Collection Handler (POST /api/users/{userId}/collection)
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

        // 4. Remove from Collection Handler (DELETE /api/users/{userId}/collection/{mediaId})
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

        // 5. Submit New Media (POST /api/media)
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

        // 6. Delete Media (DELETE /api/media/{id})
        async function deleteMedia(id, title) {
            if (!confirm(`Yakin ingin menghapus '${title}' dari katalog?`)) return;

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

        function getTypeBadge(type) {
            switch(type) {
                case 'Movie': return 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
                case 'Series': return 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20';
                case 'Anime': return 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
                case 'Podcast': return 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                default: return 'bg-slate-700 text-slate-300';
            }
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'completed': return 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                case 'watching': return 'bg-blue-500/20 text-blue-400 border border-blue-500/30';
                case 'plan_to_watch': return 'bg-amber-500/20 text-amber-400 border border-amber-500/30';
                default: return 'bg-slate-700 text-slate-300';
            }
        }

        function escapeQuote(str) {
            return (str || '').replace(/'/g, "\\'");
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            fetchMedia();
            loadUserCollection();
        });
    </script>
</body>
</html>
