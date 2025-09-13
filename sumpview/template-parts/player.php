<?php
/**
 * The template part for displaying the persistent audio player.
 *
 * @package SumpView
 */

?>
<div class="player-container">
    <div class="track-info">
        <img id="player-album-art" src="https://placehold.co/60x60/14182b/ffffff?text=Sump" alt="Album Art">
        <div class="details">
            <span id="player-track-title" class="title">Select a track</span>
            <span id="player-track-artist" class="artist"></span>
        </div>
    </div>
    <div class="player-controls">
        <div class="buttons">
            <svg title="Shuffle" class="control-btn" id="main-shuffle-btn" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"/><path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z"/></svg>
            <svg title="Previous" class="control-btn" id="main-prev-btn" xmlns="http://www.w3.org/2000/svg" height="32" width="32" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
            <div title="Play" class="play-pause-btn" id="main-play-pause-btn">
                <svg id="main-play-pause-icon" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
            </div>
            <svg title="Next" class="control-btn" id="main-next-btn" xmlns="http://www.w3.org/2000/svg" height="32" width="32" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
            <svg title="Loop" class="control-btn" id="main-loop-btn" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/></svg>
        </div>
        <div class="progress-bar-wrapper">
            <span class="time" id="player-current-time">0:00</span>
            <div class="progress-bar" id="progress-bar"><div class="progress-bar-inner" id="player-progress"></div></div>
            <span class="time" id="player-duration">0:00</span>
        </div>
    </div>
    <div class="player-extras">
        <svg title="Fullscreen" class="control-btn" id="fullscreen-btn" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
        <div class="volume-control">
            <svg id="main-volume-icon" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 48 48"><path d="M6 31V17h8l11-11v32L14 31Zm21 2.9V14.1L15.15 22H9v4h6.15Zm-6.5-6.9Z"/></svg>
            <input type="range" class="volume-slider" id="main-volume-slider" min="0" max="1" step="0.01" value="0.75">
        </div>
    </div>
</div>

<!-- Fullscreen Overlay Template Part -->
<div class="player-fullscreen-overlay" id="fullscreen-overlay">
    <div class="fullscreen-queue">
        <div class="tabs"> <button class="tab active">Queue</button> </div>
        <ul class="queue-list" id="queue-list"></ul>
        <div class="sidebar-controls">
             <div class="control-btn-wrapper" id="fs-shuffle-wrapper">
                <svg title="Shuffle" class="control-btn" id="fs-shuffle-btn" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"/><path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z"/></svg>
             </div>
             <div class="control-btn-wrapper" id="fs-loop-wrapper">
                <svg title="Loop" class="control-btn" id="fs-loop-btn" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/></svg>
             </div>
             <div class="volume-control">
                <svg id="fs-volume-icon" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 48 48"><path d="M6 31V17h8l11-11v32L14 31Zm21 2.9V14.1L15.15 22H9v4h6.15Zm-6.5-6.9Z"/></svg>
                <input type="range" class="volume-slider" id="fs-volume-slider" min="0" max="1" step="0.01" value="0.75">
             </div>
        </div>
    </div>

    <div class="fullscreen-main-content">
        <canvas id="particle-visualizer"></canvas>
        <svg class="minimize-btn" id="fullscreen-close-btn" xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 0 24 24" width="32"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 13H5v-2h14v2z"/></svg>
        <div class="content-wrapper">
            <img src="" alt="Album Art" class="cover-art" id="fullscreen-cover-art">
            <div class="track-details">
                <h2 class="track-title" id="fullscreen-track-title"></h2>
                <p class="track-artist" id="fullscreen-track-artist"></p>
            </div>
            <div class="fullscreen-controls-wrapper">
                 <div class="progress-bar-wrapper">
                    <span class="time" id="fullscreen-current-time">0:00</span>
                    <div class="progress-bar" id="fullscreen-progress-bar">
                        <div class="progress-bar-inner" id="fullscreen-player-progress"></div>
                    </div>
                    <span class="time" id="fullscreen-duration">0:00</span>
                </div>
                <div class="fullscreen-main-controls">
                    <div class="fs-btn-wrapper" id="fs-prev-wrapper">
                        <svg title="Previous" class="control-btn" xmlns="http://www.w3.org/2000/svg" height="32" width="32" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                    </div>
                    <div class="fs-btn-wrapper" id="fs-play-pause-wrapper">
                       <svg title="Play" class="control-btn" xmlns="http://www.w3.org/2000/svg" height="32" width="32" viewBox="0 0 24 24" id="fs-play-pause-svg">
                            <g id="fs-play-pause-icon-paths">
                                <path d="M8 5v14l11-7z"/><path d="M0 0h24v24H0z" fill="none"/>
                            </g>
                       </svg>
                    </div>
                    <div class="fs-btn-wrapper" id="fs-next-wrapper">
                        <svg title="Next" class="control-btn" xmlns="http://www.w3.org/2000/svg" height="32" width="32" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
