let navbar = document.querySelector('.header .flex .navbar');
let profile = document.querySelector('.header .flex .profile');

if (document.querySelector('#menu-btn')) {
    document.querySelector('#menu-btn').onclick = () => {
        navbar.classList.toggle('active');
        profile.classList.remove('active');
    }
}

if (document.querySelector('#user-btn')) {
    document.querySelector('#user-btn').onclick = () => {
        profile.classList.toggle('active');
        navbar.classList.remove('active');
    }
}

window.onscroll = () => {
    if (navbar) navbar.classList.remove('active');
    if (profile) profile.classList.remove('active');
}

// Dark Mode Toggle Logic
const initTheme = () => {
    const darkToggle = document.querySelector('#dark-mode-toggle');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme) {
        document.body.classList.add(currentTheme);
        if (darkToggle && currentTheme === 'dark-mode') {
            darkToggle.checked = true;
        }
    }

    if (darkToggle) {
        darkToggle.addEventListener('change', function () {
            if (this.checked) {
                document.body.classList.remove('light-mode');
                document.body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
                document.body.classList.add('light-mode');
                localStorage.setItem('theme', 'light-mode');
            }
        });
    }
};

// Execute theme init immediately and on DOM content loaded
initTheme();
document.addEventListener('DOMContentLoaded', initTheme);

/* --- LIVE SEARCH LOGIC --- */

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.luxury-search-box .box');
    const resultsContainer = document.querySelector('.search-results-target');
    let debounceTimer;

    if (searchInput && resultsContainer) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();

            clearTimeout(debounceTimer);

            if (query.length > 0) {
                resultsContainer.classList.add('loading');
                debounceTimer = setTimeout(() => {
                    fetch(`live_search.php?search_box=${encodeURIComponent(query)}`)
                        .then(response => response.text())
                        .then(data => {
                            resultsContainer.innerHTML = data;
                            resultsContainer.classList.remove('loading');
                        })
                        .catch(err => {
                            console.error('Search failed:', err);
                            resultsContainer.classList.remove('loading');
                        });
                }, 300);
            } else {
                resultsContainer.innerHTML = '';
            }
        });

        // Prevent default form submission to keep it live
        const searchForm = document.querySelector('.luxury-search-box');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
            });
        }
    }
});

// Cart & Quick View Quantity Selector
document.querySelectorAll('.qty-selector').forEach(selector => {
    const qtyInput = selector.querySelector('.qty');
    const plusBtn = selector.querySelector('.plus');
    const minusBtn = selector.querySelector('.minus');

    if (plusBtn && minusBtn && qtyInput) {
        plusBtn.onclick = (e) => {
            if (qtyInput.value < 99) {
                qtyInput.value = parseInt(qtyInput.value) + 1;
            }
        };

        minusBtn.onclick = (e) => {
            if (qtyInput.value > 1) {
                qtyInput.value = parseInt(qtyInput.value) - 1;
            }
        };
    }
});

// Maison Reveal - Creative Password Toggle
document.addEventListener('DOMContentLoaded', () => {
    const revealBtn = document.querySelector('#maison-reveal-btn');
    const passFields = document.querySelectorAll('.password-suite .box');

    if (revealBtn && passFields.length > 0) {
        let isRevealed = false;

        revealBtn.addEventListener('click', function () {
            isRevealed = !isRevealed;
            const type = isRevealed ? 'text' : 'password';
            const icon = revealBtn.querySelector('i');
            const label = revealBtn.querySelector('span');

            passFields.forEach(field => {
                field.type = type;
            });

            if (isRevealed) {
                icon.className = 'fas fa-eye-slash';
                label.textContent = 'Shield Security';
            } else {
                icon.className = 'fas fa-eye';
                label.textContent = 'Reveal Security';
            }
        });
    }
});
