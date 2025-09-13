jQuery(document).ready(function($) {
    'use strict';

    const container = $('#uploader-container');
    const addReleaseBtn = $('#add-release-section');
    const startUploadBtn = $('#start-upload-btn');
    const statusDiv = $('#upload-status');
    let releaseCounter = 0;

    // --- Template Functions ---

    // Provides the HTML for a new release section.
    function getReleaseSectionHTML(index) {
        return `
            <div class="release-section" data-index="${index}">
                <h3 class="release-header">New Release #${index + 1}</h3>
                <div class="form-field">
                    <label for="release-name-${index}">Release Name</label>
                    <input type="text" id="release-name-${index}" class="release-name" placeholder="e.g., Waterfall EP">
                </div>
                <div class="form-field">
                    <label for="release-image-${index}">Featured Image</label>
                    <input type="file" id="release-image-${index}" class="release-image" accept="image/*">
                </div>
                <div class="form-field">
                    <label>Artist Name (must exist)</label>
                    <input type="text" class="release-artist" placeholder="Enter an existing artist name">
                </div>
                <div class="tracks-container">
                    <h4>Tracks</h4>
                    <div class="track-list"></div>
                    <button type="button" class="button button-secondary add-track-btn">+ Add Track</button>
                </div>
            </div>
        `;
    }

    // Provides the HTML for a new track input row.
    function getTrackItemHTML() {
        return `
            <div class="track-item">
                <input type="text" class="track-name" placeholder="Track Name" style="flex-grow: 1;">
                <input type="file" class="track-file" accept="audio/mpeg,audio/wav,audio/m4a">
                <button type="button" class="button button-link-delete remove-track-btn" title="Remove Track">&times;</button>
            </div>
        `;
    }

    // --- Event Handlers ---

    function handleAddRelease() {
        const newSection = getReleaseSectionHTML(releaseCounter);
        container.append(newSection);
        releaseCounter++;
    }

    function handleAddTrack(e) {
        const trackList = $(e.target).prev('.track-list');
        trackList.append(getTrackItemHTML());
    }

    function handleRemoveTrack(e) {
        $(e.target).closest('.track-item').remove();
    }

    // --- Main Processing Logic ---

    async function handleProcessReleases() {
        startUploadBtn.prop('disabled', true).text('Processing...');
        statusDiv.html('');

        const releaseSections = container.find('.release-section');
        if (releaseSections.length === 0) {
            statusDiv.append('<div class="status-item error"><strong>No releases to process.</strong></div>');
            startUploadBtn.prop('disabled', false).text('Process All Releases');
            return;
        }

        // We process each release one by one to avoid overwhelming the server.
        for (const section of releaseSections) {
            const $section = $(section);
            const releaseName = $section.find('.release-name').val() || 'Untitled Release';
            const statusItem = $(`<div class="status-item">Processing "${releaseName}"...</div>`);
            statusDiv.append(statusItem);

            const formData = new FormData();
            formData.append('action', 'sumpcore_process_release');
            formData.append('nonce', sumpUploader.nonce);
            formData.append('release_name', releaseName);
            formData.append('artist_name', $section.find('.release-artist').val());

            const imageFile = $section.find('.release-image')[0].files[0];
            if (imageFile) {
                formData.append('release_image', imageFile);
            }

            $section.find('.track-item').each(function(i, track) {
                const trackName = $(track).find('.track-name').val();
                const trackFile = $(track).find('.track-file')[0].files[0];
                formData.append(`tracks[${i}][name]`, trackName);
                if (trackFile) {
                    formData.append(`track_file_${i}`, trackFile);
                }
            });

            // This is the AJAX call to the backend handler we will build next.
            try {
                const response = await $.ajax({
                    url: sumpUploader.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                });

                if (response.success) {
                    statusItem.append(` <strong class="success">${response.data.message}</strong>`);
                } else {
                    throw new Error(response.data);
                }
            } catch (error) {
                const errorMessage = error.responseJSON ? error.responseJSON.data : (error.message || 'Unknown error');
                statusItem.append(` <strong class="error">Failed: ${errorMessage}</strong>`);
            }
        }

        startUploadBtn.prop('disabled', false).text('Process All Releases');
        statusDiv.append('<div class="status-item"><strong>All releases processed.</strong></div>');
    }


    // --- Initialization ---

    addReleaseBtn.on('click', handleAddRelease);
    container.on('click', '.add-track-btn', handleAddTrack);
    container.on('click', '.remove-track-btn', handleRemoveTrack);
    startUploadBtn.on('click', handleProcessReleases);

    // Add the first release section by default so the page isn't empty.
    handleAddRelease();
});

