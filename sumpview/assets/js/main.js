/**
 * Main JavaScript file for the SumpView theme.
 *
 * Handles theme-wide interactions.
 */

document.addEventListener('DOMContentLoaded', () => {

    // Initialize the global player object.
    // The player class is defined in player.js
    const sumpPlayer = new SumpPlayer();

    // Find all buttons with the class 'play-release-btn'
    const playButtons = document.querySelectorAll('.play-release-btn');

    // Add a click event listener to each button
    playButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Get the release ID from the button's data attribute
            const releaseId = button.dataset.releaseId;
            
            if (releaseId && typeof sumpPlayer !== 'undefined') {
                // Call the player's method to load and play the playlist
                sumpPlayer.loadPlaylistById(releaseId);
            }
        });
    });

    // You can add other theme-wide JavaScript logic here in the future.
    // For example, handling the opening/closing of the main menu.

});

