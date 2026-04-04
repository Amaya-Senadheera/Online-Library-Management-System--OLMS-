/* ==========================================================================
   OLMS Global JavaScript (Vintage Theme Updated)
   UI Lead: Member 3 - Rakash MRM_244166J
   
   Note: Put all custom frontend interactivity here.
   ========================================================================== */

// Wait for DOM to fully load
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // 1. Auto-hide alerts after 5 seconds
    // ============================================
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) alert.remove();
            }, 500);
        }, 5000);
    });
    
    // ============================================
    // 2. Book card hover effect
    // ============================================
    const bookCards = document.querySelectorAll('.hover-card');
    bookCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
    });
    
    // ============================================
    // 3. Search form - clear button functionality
    // ============================================
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && searchInput.value) {
        const formGroup = searchInput.closest('.row');
        if (formGroup && !document.querySelector('.clear-search-btn')) {
            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'btn btn-outline-secondary clear-search-btn';
            clearBtn.innerHTML = '✕ Clear';
            clearBtn.style.marginLeft = '10px';
            clearBtn.onclick = () => {
                searchInput.value = '';
                window.location.href = window.location.pathname;
            };
            searchInput.parentNode.appendChild(clearBtn);
        }
    }
    
    // ============================================
    // 4. Star rating interactive preview (for review modal)
    // ============================================
    const ratingStars = document.querySelectorAll('.star-rating label');
    ratingStars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const stars = this.parentElement.querySelectorAll('label');
            let found = false;
            stars.forEach(s => {
                if (s === this) found = true;
                if (found) {
                    // 🟢 CHANGED: Replaced bright yellow with Vintage Ochre
                    s.style.color = '#B08A5B';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
        
        star.addEventListener('mouseout', function() {
            const checked = this.parentElement.querySelector('input:checked');
            const stars = this.parentElement.querySelectorAll('label');
            if (checked) {
                const checkedValue = checked.value;
                stars.forEach((s, idx) => {
                    const starValue = stars.length - idx;
                    if (starValue <= checkedValue) {
                        // 🟢 CHANGED: Replaced bright yellow with Vintage Ochre
                        s.style.color = '#B08A5B';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            } else {
                stars.forEach(s => s.style.color = '#ddd');
            }
        });
    });
    
    // ============================================
    // 5. Copy ISBN to clipboard (if any ISBN elements exist)
    // ============================================
    const isbnElements = document.querySelectorAll('.copy-isbn');
    isbnElements.forEach(el => {
        el.style.cursor = 'pointer';
        el.addEventListener('click', function() {
            const isbn = this.textContent.trim();
            navigator.clipboard.writeText(isbn).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '✓ Copied!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 1500);
            });
        });
    });
    
    // ============================================
    // 6. Confirm delete for admin actions
    // ============================================
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // ============================================
    // 7. Lazy load images (improves performance)
    // ============================================
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // ============================================
    // 8. Back to top button (for long pages)
    // ============================================
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '↑';
    backToTop.className = 'back-to-top-btn shadow';
    backToTop.setAttribute('aria-label', 'Back to top');
    
    // 🟢 CHANGED: Replaced Bootstrap Blue with Vintage Red-Brown (#8C3A35)
    backToTop.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #8C3A35; 
        color: white;
        border: none;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        font-size: 24px;
        box-shadow: 0 4px 15px rgba(28, 17, 10, 0.3);
    `;
    
    // Add hover effect via JS since it's an inline style
    backToTop.addEventListener('mouseenter', () => {
        backToTop.style.background = '#6E2D29'; // Darker red-brown on hover
        backToTop.style.transform = 'translateY(-3px)';
    });
    backToTop.addEventListener('mouseleave', () => {
        backToTop.style.background = '#8C3A35'; // Back to normal
        backToTop.style.transform = 'translateY(0)';
    });

    document.body.appendChild(backToTop);
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTop.style.opacity = '1';
            backToTop.style.visibility = 'visible';
        } else {
            backToTop.style.opacity = '0';
            backToTop.style.visibility = 'hidden';
        }
    });
    
    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    // ============================================
    // 9. Mobile menu auto-close on link click
    // ============================================
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        });
    });
    
    console.log('OLMS - Catalog & UI Loaded ✅');
});

// ============================================
// Utility function: Format date for display
// ============================================
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

// ============================================
// Utility function: Show toast notification
// ============================================
function showToast(message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container') || (() => {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1100';
        document.body.appendChild(container);
        return container;
    })();
    
    const toastId = 'toast-' + Date.now();
    // Note: bg-info uses the Vintage Ochre defined in CSS. Success/Danger remain default.
    const bgColor = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');
    
    const toastHTML = `
        <div id="${toastId}" class="toast border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="toast-header ${bgColor} text-white border-0">
                <strong class="me-auto">OLMS Notification</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body bg-white text-dark">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}