/**
 * Main JavaScript file for the SumpView theme.
 *
 * Handles theme-wide interactions.
 */

document.addEventListener('DOMContentLoaded', () => {

    // --- Player Initialization ---
    // This assumes the SumpPlayer class is globally available from player.js
    const sumpPlayer = new SumpPlayer();

    // Attach event listeners to all play buttons on the page
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
});