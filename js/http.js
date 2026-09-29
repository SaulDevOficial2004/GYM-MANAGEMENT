function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}

async function apiFetch(url, options) {
    options = options || {};
    var headers = Object.assign({
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-Token': getCsrfToken()
    }, options.headers || {});

    if (options.body instanceof FormData) {
        delete headers['Content-Type'];
    }

    var config = Object.assign({}, options, {
        headers: headers,
        credentials: 'same-origin'
    });

    var response = await fetch(url, config);
    var data = null;
    try { data = await response.json(); } catch(e) { /* non-JSON */ }

    if (response.status === 401 && data && data.force_logout) {
        window.location.href = 'index.php?session=disabled';
        return null;
    }

    if (response.status === 419) {
        Toastify({
            text: 'Sesión expirada. Recarga la página.',
            duration: 5000,
            gravity: 'top',
            position: 'right',
            style: {
                background: 'linear-gradient(135deg, #d93a62, #ff6b81)',
                borderRadius: '10px',
                fontFamily: 'Poppins, sans-serif',
                fontSize: '12px'
            }
        }).showToast();
        return null;
    }

    return { response: response, data: data };
}
