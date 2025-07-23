<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSV Upload</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6 font-sans">

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-md p-6">
        <h1 class="text-2xl font-semibold mb-4">CSV Upload</h1>

        <!-- Upload Form with Drag-and-Drop -->
        <form id="uploadForm" method="POST" action="/upload" enctype="multipart/form-data">
            @csrf
            <input type="file" id="csvFileInput" name="csv_file" class="hidden" required>

            <div id="dropzone"
                class="border-2 border-dashed border-gray-300 rounded-lg px-6 py-8 text-center text-gray-500 cursor-pointer hover:border-blue-400 transition flex flex-col items-center gap-4"
                onclick="document.getElementById('csvFileInput').click();"
                ondragover="event.preventDefault(); this.classList.add('border-blue-400', 'bg-blue-50');"
                ondragleave="this.classList.remove('border-blue-400', 'bg-blue-50');" ondrop="handleDrop(event)">

                <p class="text-sm">Select file or drag & drop your CSV here</p>

                <div id="fileName" class="text-sm text-gray-700 hidden"></div>

                <button type="submit" id="uploadBtn"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm hidden"
                    onclick="event.stopPropagation();">
                    Upload File
                </button>
            </div>
        </form>

        <!-- Upload Table -->
        <div class="mt-8">
            <h2 class="text-lg font-medium mb-3">Upload History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded text-sm">
                    <thead class="bg-gray-200 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort('uploaded_at')">
                                Time <span id="sortIconTime">⬍</span>
                            </th>
                            <th class="px-4 py-2 text-left cursor-pointer" onclick="toggleSort('filename')">
                                File Name <span id="sortIconFilename">⬍</span>
                            </th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody id="uploadTable" class="divide-y divide-gray-200 bg-white">
                        <!-- Rows injected by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="/products"
                class="text-sm bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                View Product List
            </a>
            <a href="/horizon" class="text-sm bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900 transition">
                Queue Monitor (Horizon)
            </a>
        </div>
    </div>

    <script>
        let currentSortField = null;
        let currentSortDirection = null; // null = unsorted, 'asc', or 'desc'

        const fileInput = document.getElementById('csvFileInput');
        const dropzone = document.getElementById('dropzone');
        const fileNameDisplay = document.getElementById('fileName');
        const uploadBtn = document.getElementById('uploadBtn');

        function handleDrop(e) {
            e.preventDefault();
            dropzone.classList.remove('border-blue-400', 'bg-blue-50');

            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                showSelectedFile();
            }
        }

        function showSelectedFile() {
            const file = fileInput.files[0];
            if (file) {
                fileNameDisplay.textContent = `Selected: ${file.name}`;
                fileNameDisplay.classList.remove('hidden');
                uploadBtn.classList.remove('hidden');
            }
        }

        fileInput.addEventListener('change', showSelectedFile);

        function formatTime(datetime) {
            const dt = new Date(datetime);
            const day = dt.getDate();
            const month = dt.getMonth() + 1;
            const year = dt.getFullYear();
            let hours = dt.getHours();
            const minutes = dt.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;

            return `${day}/${month}/${year} ${hours}:${minutes}${ampm}`;
        }

        function humanizeMinutesAgo(diffMin) {
            if (diffMin < 1) return 'Just now';
            if (diffMin === 1) return '1 minute ago';
            if (diffMin < 60) return `${diffMin} minutes ago`;
            if (diffMin < 1440) {
                const hours = Math.floor(diffMin / 60);
                return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            }
            if (diffMin < 2880) return 'Yesterday';
            const days = Math.floor(diffMin / 1440);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        }

        function fetchUploads() {
            axios.get('/uploads')
                .then(res => {
                    let uploads = res.data;

                    // Sort based on current field and direction
                    if (currentSortField && currentSortDirection) {
                        uploads.sort((a, b) => {
                            const fieldA = a[currentSortField]?.toLowerCase?.() ?? a[currentSortField];
                            const fieldB = b[currentSortField]?.toLowerCase?.() ?? b[currentSortField];

                            if (fieldA < fieldB) return currentSortDirection === 'asc' ? -1 : 1;
                            if (fieldA > fieldB) return currentSortDirection === 'asc' ? 1 : -1;
                            return 0;
                        });
                    }

                    // Update icon
                    document.getElementById('sortIconTime').textContent = currentSortField === 'uploaded_at' ?
                        (currentSortDirection === 'asc' ? '↑' : '↓') :
                        '⬍';
                    document.getElementById('sortIconFilename').textContent = currentSortField === 'filename' ?
                        (currentSortDirection === 'asc' ? '↑' : '↓') :
                        '⬍';

                    const table = document.getElementById('uploadTable');
                    table.innerHTML = '';

                    if (uploads.length === 0) {
                        table.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center text-gray-500 py-4">
                                    No uploads yet. Upload a CSV to get started.
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    uploads.forEach(upload => {
                        const uploadedAt = new Date(upload.uploaded_at);
                        const now = new Date();
                        const diffMin = Math.floor((now - uploadedAt) / 60000);
                        const formattedTime = formatTime(upload.uploaded_at);
                        const humanTime = humanizeMinutesAgo(diffMin);

                        table.innerHTML += `
                    <tr>
                        <td class="px-4 py-2 align-top">
                            ${formattedTime}<br>
                            <span class="text-xs text-gray-500">(${humanTime})</span>
                        </td>
                        <td class="px-4 py-2">${upload.filename}</td>
                        <td class="px-4 py-2 capitalize">
                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold ${
                                upload.status === 'completed' ? 'bg-green-100 text-green-800' :
                                upload.status === 'processing' ? 'bg-yellow-100 text-yellow-800' :
                                upload.status === 'failed' ? 'bg-red-100 text-red-800' :
                                'bg-gray-100 text-gray-800'
                            }">
                                ${upload.status}
                            </span>
                        </td>
                    </tr>
                `;
                    });
                });
        }

        function toggleSort(field) {
            if (currentSortField !== field) {
                currentSortField = field;
                currentSortDirection = 'asc';
            } else if (currentSortDirection === 'asc') {
                currentSortDirection = 'desc';
            } else if (currentSortDirection === 'desc') {
                currentSortField = null;
                currentSortDirection = null;
            } else {
                currentSortDirection = 'asc';
            }
            fetchUploads();
        }

        fetchUploads();
        setInterval(fetchUploads, 3000); // polling every 3 sec

        const iconMap = {
            null: '⬍',
            asc: '↑',
            desc: '↓'
        };

        document.getElementById('sortIconTime').textContent =
            currentSortField === 'uploaded_at' ? iconMap[currentSortDirection] : iconMap.null;

        document.getElementById('sortIconFilename').textContent =
            currentSortField === 'filename' ? iconMap[currentSortDirection] : iconMap.null;
    </script>

</body>

</html>
