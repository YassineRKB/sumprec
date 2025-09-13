/**
 * Main JavaScript file for the SumpView theme.
 *
 * Handles theme-wide interactions, player initialization, and animations.
 */

document.addEventListener('DOMContentLoaded', () => {

    // --- Player Initialization ---
    const sumpPlayer = new SumpPlayer();

    document.body.addEventListener('click', function(event) {
        const playButton = event.target.closest('.play-release-btn');
        if (playButton) {
            const releaseId = playButton.dataset.releaseId;
            if (releaseId && typeof sumpPlayer !== 'undefined') {
                sumpPlayer.loadPlaylistById(releaseId);
            }
        }
    });

    // --- Fullscreen Menu Toggle Logic ---
    const menuToggle = document.getElementById('menu-toggle');
    const menuClose = document.getElementById('fullscreen-menu-close');
    const menuOverlay = document.getElementById('fullscreen-menu-overlay');

    if (menuToggle && menuOverlay) {
        menuToggle.addEventListener('click', () => {
            document.body.classList.add('menu-open');
            menuOverlay.classList.add('active');
            menuToggle.setAttribute('aria-expanded', 'true');
        });
    }

    if (menuClose && menuOverlay) {
        menuClose.addEventListener('click', () => {
            document.body.classList.remove('menu-open');
            menuOverlay.classList.remove('active');
            if (menuToggle) {
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // --- GSAP Animations ---
    // This function will animate elements as they enter the viewport
    function initAnimations() {
        // Animate grid items (like releases and artists)
        const gridItems = document.querySelectorAll('.sump-release-item, .sump-artist-item');
        if (gridItems.length > 0) {
            gsap.from(gridItems, {
                duration: 0.8,
                opacity: 0,
                y: 50, // Start 50px down
                stagger: 0.2, // Animate one after the other
                ease: 'power3.out'
            });
        }
        
        // Animate single page titles
        const singleTitle = document.querySelector('.sump-single-release .entry-title, .sump-single-artist .entry-title');
         if (singleTitle) {
            gsap.from(singleTitle, {
                duration: 1,
                opacity: 0,
                y: 30,
                ease: 'power3.out',
                delay: 0.2
            });
        }
    }

    // Run the animations
    initAnimations();

});

