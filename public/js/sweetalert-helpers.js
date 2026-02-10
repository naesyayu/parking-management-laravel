/**
 * ========================================
 * SWEETALERT2 CONFIRMATION HELPERS (SAFE)
 * ========================================
 * - Defensive coding
 * - No UI lock
 * - Error handling
 * - Safe redirect & form submit
 * ========================================
 */

/**
 * Internal helper: safe redirect
 */
function safeRedirect(url) {
    if (!url) {
        console.error('Redirect URL tidak valid:', url);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'URL tidak ditemukan. Silakan refresh halaman.'
        });
        return;
    }

    Swal.close();
    setTimeout(() => {
        window.location.href = url;
    }, 150);
}

/**
 * Internal helper: safe form submit
 */
function safeSubmit(formId) {
    const form = document.getElementById(formId);

    if (!form) {
        console.error('Form tidak ditemukan:', formId);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Form tidak ditemukan. Silakan refresh halaman.'
        });
        return;
    }

    Swal.close();
    setTimeout(() => {
        form.submit();
    }, 150);
}

/**
 * Confirm Create
 */
function confirmCreate(entityName, createUrl) {
    Swal.fire({
        title: `Tambah ${entityName}?`,
        text: `Apakah Anda yakin ingin menambahkan ${entityName.toLowerCase()} baru?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tambah',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            safeRedirect(createUrl);
        }
    });
}

/**
 * Confirm Edit
 */
function confirmEdit(entityName, dataName, editUrl) {
    Swal.fire({
        title: `Edit ${entityName}?`,
        html: `Apakah Anda yakin ingin mengedit ${entityName.toLowerCase()}<br>
               <strong class="text-primary">"${dataName}"</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Edit',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            safeRedirect(editUrl);
        }
    });
}

/**
 * Confirm Delete (Soft Delete)
 */
function confirmDelete(entityName, dataName, formId) {
    Swal.fire({
        title: `Hapus ${entityName}?`,
        html: `Data <strong class="text-danger">"${dataName}"</strong> akan dipindahkan ke backup.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            safeSubmit(formId);
        }
    });
}

/**
 * Confirm Restore
 */
function confirmRestore(entityName, dataName, formId) {
    Swal.fire({
        title: `Pulihkan ${entityName}?`,
        html: `Data <strong class="text-success">"${dataName}"</strong> akan dikembalikan.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Pulihkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            safeSubmit(formId);
        }
    });
}
