// public/js/sidebar.js

document.addEventListener('DOMContentLoaded', () => {
    // --- SIDEBAR TOGGLE LOGIC ---
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    // Toggle is now handled inline in app.blade.php to avoid conflicts

    // --- SEAMLESS PAGE TRANSITION LOGIC ---
    const panelContent = document.querySelector('.panel-content');
    
    // 1. Create and inject the blurred loading overlay dynamically
    let transitionOverlay = document.getElementById('page-transition-overlay');
    if (!transitionOverlay) {
        transitionOverlay = document.createElement('div');
        transitionOverlay.id = 'page-transition-overlay';
        transitionOverlay.className = 'page-transition-overlay';
        transitionOverlay.innerHTML = '<div class="transition-spinner"></div>';
        document.body.appendChild(transitionOverlay);
    }

    // 2. Initial Page Entrance Animation (Fade-In / Hide Overlay)
    const completeEntrance = () => {
        if (panelContent) {
            panelContent.classList.add('loaded');
        }
        // Smoothly fade out the blurred overlay
        transitionOverlay.classList.add('hidden');
    };

    if (document.readyState === 'complete') {
        completeEntrance();
    } else {
        window.addEventListener('load', completeEntrance);
        // Fallback in case window load takes too long
        setTimeout(() => {
            if (transitionOverlay && !transitionOverlay.classList.contains('hidden')) {
                completeEntrance();
            }
        }, 1200);
    }

    // 3. Link Click Interception (Page Exit Transition)
    const allLinks = document.querySelectorAll('a');
    allLinks.forEach(link => {
        const href = link.getAttribute('href');
        
        // Only intercept valid internal links
        if (!href) return;
        
        // Skip external links, hashes, logout actions, and CSV/file downloads
        const isInternal = href.startsWith('/') || href.startsWith(window.location.origin);
        const isHash = href.startsWith('#');
        const isJavascript = href.startsWith('javascript:');
        const isLogout = href.includes('logout') || link.closest('form[action*="logout"]');
        const isDownload = href.includes('export') || link.hasAttribute('download');

        if (isInternal && !isHash && !isJavascript && !isLogout && !isDownload) {
            link.addEventListener('click', (e) => {
                // If opening in new tab or holding special keys, let browser handle normally
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;

                e.preventDefault();
                const targetUrl = link.href;

                // Close sidebar on mobile
                if (sidebar) {
                    sidebar.classList.remove('active');
                }

                // Show the blurred overlay loader and fade out the current page content
                transitionOverlay.classList.remove('hidden');
                
                if (panelContent) {
                    panelContent.classList.remove('loaded');
                }

                // Wait for the exit animation (220ms matches CSS transition) then navigate
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 220);
            });
        }
    });
});