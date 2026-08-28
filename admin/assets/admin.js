// ===== ADMIN JS =====
(function() {
    // Sidebar toggle (mobile)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('adminOverlay');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (overlay) overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Auto-hide alerts after 5 sec
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Confirm delete
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const msg = btn.getAttribute('data-confirm');
            if (!confirm(msg)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // Category tabs
    const tabs = document.querySelectorAll('.category-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-target');
            document.querySelectorAll('.category-panel').forEach(p => p.style.display = 'none');
            const panel = document.getElementById(target);
            if (panel) panel.style.display = 'block';
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
        });
    });

    // ===== IMAGE UPLOAD (через делегування подій — працює для динамічно доданих) =====
    document.addEventListener('change', async (e) => {
        const input = e.target;
        if (!input.classList || !input.classList.contains('file-input')) return;
        if (!input.files || !input.files.length) return;

        const files = Array.from(input.files);
        const block = input.closest('.image-upload-block');
        if (!block) return;
        const statusEl = block.querySelector('.upload-status');
        const targetField = input.getAttribute('data-target-field');
        const appendToId = input.getAttribute('data-append-to');
        const oldPath = input.getAttribute('data-old-path') || '';

        const button = input.closest('label');
        if (button) {
            button.style.opacity = '0.6';
            button.style.pointerEvents = 'none';
        }

        if (statusEl) {
            statusEl.textContent = '⏳ Завантаження...';
            statusEl.style.color = 'var(--gold,#c9a96e)';
        }

        const uploadedPaths = [];

        for (const file of files) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('old_path', uploadedPaths.length === 0 ? oldPath : '');

            try {
                const resp = await fetch('../api/upload.php', {
                    method: 'POST',
                    body: formData,
                });
                const result = await resp.json();

                if (result.success) {
                    uploadedPaths.push(result.path);
                } else {
                    if (statusEl) {
                        statusEl.textContent = '❌ ' + (result.message || 'Помилка');
                        statusEl.style.color = '#e5484d';
                    }
                    if (button) {
                        button.style.opacity = '1';
                        button.style.pointerEvents = '';
                    }
                    input.value = '';
                    return;
                }
            } catch (err) {
                console.error('Upload error:', err);
                if (statusEl) {
                    statusEl.textContent = '❌ Помилка мережі';
                    statusEl.style.color = '#e5484d';
                }
                if (button) {
                    button.style.opacity = '1';
                    button.style.pointerEvents = '';
                }
                input.value = '';
                return;
            }
        }

        // Якщо appendToId — додаємо шляхи в textarea
        if (appendToId) {
            const ta = document.getElementById(appendToId);
            if (ta) {
                const current = ta.value.trim();
                const newPaths = uploadedPaths.join('\n');
                ta.value = current ? (current + '\n' + newPaths) : newPaths;
            }
        }

        // Якщо targetField — встановлюємо шлях в інпут
        if (targetField) {
            const target = document.getElementsByName(targetField)[0] || document.getElementById(targetField);
            if (target) {
                target.value = uploadedPaths[0];
            }
        }

        // Оновити прев'ю
        const previewWrapper = block.querySelector('.image-preview-wrapper');
        if (previewWrapper && uploadedPaths.length === 1) {
            // Нормалізуємо шлях — якщо починається не з / чи http — додаємо /
            let displayPath = uploadedPaths[0];
            if (!/^https?:|^\/\//.test(displayPath) && !displayPath.startsWith('/')) {
                displayPath = '/' + displayPath;
            }
            previewWrapper.innerHTML = '<img src="' + displayPath + '?' + Date.now() + '" alt="Прев\'ю" class="image-preview">';
        }

        if (statusEl) {
            statusEl.textContent = '✅ Завантажено: ' + uploadedPaths.length + ' фото';
            statusEl.style.color = '#4ade80';
            setTimeout(() => { if (statusEl) statusEl.textContent = ''; }, 3000);
        }
        if (button) {
            button.style.opacity = '1';
            button.style.pointerEvents = '';
        }

        // Очистити інпут
        input.value = '';
    });
})();
