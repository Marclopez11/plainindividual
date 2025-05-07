<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Convertidor d'Excel a Word</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-light: #818CF8;
            --primary-dark: #4338CA;
            --secondary: #1E293B;
            --tertiary: #F8FAFC;
            --accent: #F43F5E;
            --grey-light: #F1F5F9;
            --grey: #E2E8F0;
            --grey-dark: #94A3B8;
            --dark: #0F172A;
            --success: #10B981;
            --error: #EF4444;
            --warning: #F59E0B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            height: 100vh;
            width: 100%;
            background: linear-gradient(135deg, #4F46E5 0%, #818CF8 100%);
            margin: 0;
            padding: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            color: white;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .brand-logo svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
        }

        .brand-text {
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .template-button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .template-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .template-button svg {
            width: 16px;
            height: 16px;
        }

        .content-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .card {
            width: 100%;
            max-width: 700px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            text-align: center;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .card-header {
            margin-bottom: 2rem;
        }

        .card-title {
            color: var(--secondary);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }

        .card-subtitle {
            color: var(--grey-dark);
            font-size: 1rem;
            line-height: 1.5;
            margin: 0 auto;
            max-width: 500px;
        }

        .upload-container {
            margin-bottom: 1.5rem;
        }

        .upload-zone {
            border: 2px dashed var(--grey);
            border-radius: 12px;
            padding: 2.5rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--tertiary);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .upload-zone:hover {
            border-color: var(--primary);
            background: white;
            transform: translateY(-2px);
        }

        .upload-zone.dragover {
            border-color: var(--primary);
            background: white;
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.15);
        }

        .upload-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            color: var(--primary);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
            100% { transform: translateY(0px); }
        }

        .upload-text {
            color: var(--secondary);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .upload-subtext {
            color: var(--grey-dark);
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .file-types {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .file-type {
            padding: 0.3rem 0.7rem;
            background: white;
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--grey-dark);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid var(--grey);
        }

        .file-type:hover {
            background: var(--grey-light);
            transform: translateY(-2px);
            color: var(--primary);
        }

        .file-input {
            display: none;
        }

        .upload-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
        }

        .upload-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }

        .notice {
            padding: 1rem;
            background: var(--tertiary);
            border-radius: 8px;
            color: var(--grey-dark);
            font-size: 0.9rem;
            line-height: 1.5;
            text-align: center;
            border-left: 3px solid var(--primary);
        }

        .file-info {
            margin-top: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 12px;
            display: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            animation: slideUp 0.5s ease;
            border: 1px solid var(--grey);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .file-info.active {
            display: flex;
            align-items: center;
        }

        .file-icon {
            width: 40px;
            height: 40px;
            background: var(--tertiary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .file-icon svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
        }

        .file-details {
            flex-grow: 1;
        }

        .file-name {
            color: var(--secondary);
            font-weight: 600;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .file-extension {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            background: var(--tertiary);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .file-size {
            color: var(--grey-dark);
            font-size: 0.8rem;
        }

        .file-progress {
            height: 4px;
            background: var(--grey-light);
            border-radius: 4px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .file-progress-bar {
            height: 100%;
            background: var(--primary);
            width: 0;
            transition: width 0.5s ease;
        }

        .button-container {
            display: flex;
            gap: 0.8rem;
            margin-top: 1rem;
        }

        .conversion-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
            display: none;
            flex: 1;
        }

        .change-file-button {
            background: var(--tertiary);
            color: var(--secondary);
            border: 1px solid var(--grey);
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: none;
            flex: 1;
        }

        .change-file-button:hover {
            background: var(--grey-light);
            transform: translateY(-2px);
        }

        .conversion-button.active, .change-file-button.active {
            display: block;
        }

        .conversion-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }

        .conversion-status {
            display: none;
            margin-top: 1rem;
            padding: 1.5rem;
            background: var(--tertiary);
            border-radius: 12px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .conversion-status.active {
            display: block;
        }

        .success-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            color: var(--success);
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon svg {
            width: 30px;
            height: 30px;
        }

        .conversion-status p {
            color: var(--secondary);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .upload-another {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3);
        }

        .upload-another:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }

        .toast {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            padding: 0.8rem 1.2rem;
            background: var(--secondary);
            color: white;
            border-radius: 8px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 1000;
        }

        .toast.active {
            transform: translateY(0);
            opacity: 1;
        }

        .toast svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 768px) {
            body {
                overflow-y: auto;
            }

            .card {
                padding: 1.5rem;
            }

            .upload-zone {
                padding: 1.5rem;
            }

            .upload-icon {
                width: 50px;
                height: 50px;
            }

            .button-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="brand">
            <div class="brand-logo">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="brand-text">Excel a Word</div>
        </div>

        <a href="{{ route('download.template') }}" class="template-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Descarrega plantilla
        </a>
    </header>

    <div class="content-container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Convertidor d'Excel a Word</h1>
                <p class="card-subtitle">Transforma el pla individualitzat en format Excel a Word amb un sol clic</p>
            </div>

            <div class="upload-container">
                <form action="{{ route('upload.file') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="upload-zone" id="dropZone">
                        <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>

                        <div class="upload-text">Arrossega el teu fitxer Excel</div>
                        <div class="upload-subtext">o fes clic per seleccionar un fitxer</div>

                        <div class="file-types">
                            <div class="file-type">.xlsx</div>
                            <div class="file-type">.xls</div>
                        </div>

                        <input type="file" name="excel_file" class="file-input" id="fileInput" accept=".xlsx,.xls">
                        <button type="button" class="upload-button" id="uploadBtn">Seleccionar Fitxer</button>
                    </div>

                    <div class="notice">
                        El procés de conversió respectarà el format de la plantilla oficial i generarà un document Word descarregable.
                    </div>

                    <div class="file-info" id="fileInfo">
                        <div class="file-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="file-details">
                            <div class="file-name" id="fileName">filename.xlsx <span class="file-extension" id="fileExt">XLSX</span></div>
                            <div class="file-size" id="fileSize">0 KB</div>
                            <div class="file-progress">
                                <div class="file-progress-bar" id="fileProgressBar"></div>
                            </div>
                        </div>
                    </div>

                    <div class="button-container">
                        <button type="submit" class="conversion-button" id="convertBtn">Començar Conversió</button>
                        <button type="button" class="change-file-button" id="changeFileBtn">Canviar Fitxer</button>
                    </div>

                    <div class="conversion-status" id="conversionStatus">
                        <div class="success-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p>La conversió ha finalitzat amb èxit!</p>
                        <button type="button" class="upload-another" id="uploadAnotherBtn">Convertir un altre fitxer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span id="toastText">Fitxer pujat correctament!</span>
    </div>

    <script>
        // File Upload Handling
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileExt = document.getElementById('fileExt');
        const fileSize = document.getElementById('fileSize');
        const fileProgressBar = document.getElementById('fileProgressBar');
        const uploadBtn = document.getElementById('uploadBtn');
        const convertBtn = document.getElementById('convertBtn');
        const changeFileBtn = document.getElementById('changeFileBtn');
        const toast = document.getElementById('toast');
        const toastText = document.getElementById('toastText');
        const conversionStatus = document.getElementById('conversionStatus');
        const uploadAnotherBtn = document.getElementById('uploadAnotherBtn');
        const uploadForm = document.getElementById('uploadForm');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        // Handle dropped files
        dropZone.addEventListener('drop', handleDrop, false);
        uploadBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', handleFiles);

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            dropZone.classList.add('dragover');
        }

        function unhighlight(e) {
            dropZone.classList.remove('dragover');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles({ target: { files: files } });
        }

        function handleFiles(e) {
            const file = e.target.files[0];
            if (file) {
                fileInfo.classList.add('active');
                convertBtn.classList.add('active');
                changeFileBtn.classList.add('active');

                // Update file name and extension
                const fullName = file.name;
                const extension = fullName.split('.').pop().toUpperCase();

                fileName.innerHTML = fullName + ' <span class="file-extension" id="fileExt">' + extension + '</span>';
                fileSize.textContent = formatFileSize(file.size);

                // Reset progress bar
                fileProgressBar.style.width = '0%';

                // Show toast message
                showToast('Fitxer seleccionat! Fes clic a "Començar Conversió" per continuar.');

                // Hide drop zone after file is selected
                dropZone.style.display = 'none';
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showToast(text) {
            toastText.textContent = text;
            toast.classList.add('active');

            setTimeout(() => {
                toast.classList.remove('active');
            }, 3000);
        }

        // Change file button action
        changeFileBtn.addEventListener('click', () => {
            // Reset file selection
            fileInfo.classList.remove('active');
            convertBtn.classList.remove('active');
            changeFileBtn.classList.remove('active');

            // Show drop zone again
            dropZone.style.display = 'flex';

            // Clear file input
            fileInput.value = '';

            // Show toast message
            showToast('Pots seleccionar un nou fitxer Excel');
        });

        // Form submission
        convertBtn.addEventListener('click', (e) => {
            if (!fileInput.files.length) {
                showToast('Si us plau, selecciona un fitxer primer!');
                e.preventDefault();
            } else {
                simulateProgress();
                showToast('Convertint el teu fitxer...');
            }
        });

        // Simulate progress
        function simulateProgress() {
            let width = 0;
            fileProgressBar.style.width = '0%';

            const interval = setInterval(() => {
                if (width >= 90) {
                    clearInterval(interval);
                } else {
                    width += 1;
                    fileProgressBar.style.width = width + '%';
                }
            }, 30);
        }

        // Upload another file button
        uploadAnotherBtn.addEventListener('click', () => {
            // Reset the form
            uploadForm.reset();
            fileInfo.classList.remove('active');
            conversionStatus.classList.remove('active');
            fileProgressBar.style.width = '0%';

            // Show the drop zone again
            dropZone.style.display = 'flex';

            showToast('Pots seleccionar un altre fitxer');
        });

        // For form submission
        uploadForm.addEventListener('submit', function(e) {
            // Complete progress bar on actual submission
            fileProgressBar.style.width = '100%';
        });
    </script>
</body>
</html>

