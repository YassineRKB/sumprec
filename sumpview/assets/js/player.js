/**
 * SumpPlayer Class
 *
 * Handles all audio playback, UI updates, and API interactions for the SumpView theme player.
 * It is instantiated in main.js and controlled by event listeners.
 */
class SumpPlayer {
    constructor() {
        // --- State Management ---
        this.playlist = null; // Will hold the currently loaded playlist data from the API
        this.trackIndex = 0;
        this.sound = null;
        this.shuffle = false;
        this.loop = false; // Can be false, 'one', or 'all'
        this.raf = null; // For requestAnimationFrame
        this.isSeeking = false;

        // --- Visualizer State ---
        this.particles = [];

        // --- Static Data ---
        this.icons = {
            play: '<path d="M8 5v14l11-7z"/><path d="M0 0h24v24H0z" fill="none"/>',
            pause: '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/><path d="M0 0h24v24H0z" fill="none"/>'
        };

        // --- DOM Element Caching ---
        this.dom = this._cacheDomElements();
        this.particleCtx = this.dom.particleCanvas.getContext('2d');

        // --- Initial Setup ---
        this._bindEvents();
    }

    /**
     * Fetches playlist data from the WordPress REST API and loads the first track.
     * @param {string|number} releaseId The ID of the release to load.
     */
    async loadPlaylistById(releaseId) {
        // Use the localized data from functions.php
        const apiUrlBase = sumpViewApiSettings.apiUrl; // This is the base, e.g., "http://yoursite.com/wp-json/"
        const nonce = sumpViewApiSettings.nonce;

        if (!apiUrlBase || !releaseId) {
            console.error('API URL or Release ID is missing.');
            return Promise.reject('API URL or Release ID is missing.');
        }

        // Correctly construct the final endpoint URL
        const endpoint = `${apiUrlBase}release/${releaseId}`;

        try {
            const response = await fetch(endpoint, {
                headers: {
                    'X-WP-Nonce': nonce
                }
            });

            if (!response.ok) {
                throw new Error(`API request failed with status ${response.status}`);
            }

            const data = await response.json();
            this.playlist = data;
            this.loadTrack(0, true); // Load the first track and autoplay
            return Promise.resolve(data);

        } catch (error) {
            console.error('Failed to fetch playlist:', error);
            return Promise.reject(error);
        }
    }

    /**
     * Loads a specific track into Howler and prepares it for playback.
     * @param {number} trackIndex The index of the track in the current playlist.
     * @param {boolean} [autoplay=false] Whether to start playing immediately.
     */
    loadTrack(trackIndex, autoplay = false) {
        if (this.sound) {
            this.sound.unload();
        }

        this.trackIndex = trackIndex;
        const track = this.playlist.tracks[this.trackIndex];

        this._updateUI();
        this._renderQueue();

        this.sound = new Howl({
            src: [track.src],
            html5: true, // Essential for cross-domain audio analysis if needed
            volume: this.dom.mainVolumeSlider.value,
            onplay: () => {
                this._setPlayState(true);
                this.raf = requestAnimationFrame(this._step.bind(this));
            },
            onpause: () => {
                this._setPlayState(false);
                cancelAnimationFrame(this.raf);
            },
            onend: () => {
                if (this.loop === 'one') {
                    this.play();
                } else {
                    this.next();
                }
            },
            onload: () => {
                const duration = this.sound.duration();
                this.dom.duration.textContent = this._formatTime(duration);
                this.dom.fsDuration.textContent = this._formatTime(duration);
                if (autoplay) {
                    this.play();
                }
            }
        });
    }


    // --- Core Playback Methods ---
    play() { if (this.sound && !this.sound.playing()) { this.sound.play(); } }
    pause() { if (this.sound && this.sound.playing()) { this.sound.pause(); } }
    togglePlayPause() { if (this.sound && this.sound.playing()) { this.pause(); } else { this.play(); } }

    next() {
        if (!this.playlist) return;
        let nextIndex = this.trackIndex + 1;
        if (nextIndex >= this.playlist.tracks.length) {
            if (this.loop === 'all') {
                nextIndex = 0;
            } else {
                this._setPlayState(false);
                return; // Stop playback
            }
        }
        this.loadTrack(nextIndex, true);
    }

    prev() {
        if (!this.playlist) return;
        let prevIndex = this.trackIndex - 1;
        if (prevIndex < 0) {
            prevIndex = this.playlist.tracks.length - 1;
        }
        this.loadTrack(prevIndex, true);
    }

    // --- UI Update Methods ---
    _updateUI() {
        const track = this.playlist.tracks[this.trackIndex];
        this.dom.albumArt.src = this.playlist.cover;
        this.dom.trackTitle.textContent = track.title;
        this.dom.trackArtist.textContent = this.playlist.artist;

        this.dom.fullscreenCoverArt.src = this.playlist.cover;
        this.dom.fullscreenTrackTitle.textContent = track.title;
        this.dom.fullscreenTrackArtist.textContent = this.playlist.artist;

        this.dom.currentTime.textContent = this._formatTime(0);
        this.dom.fsCurrentTime.textContent = this._formatTime(0);
        this.dom.progress.style.width = '0%';
        this.dom.fsProgress.style.width = '0%';
    }

    _renderQueue() {
        this.dom.queueList.innerHTML = '';
        this.playlist.tracks.forEach((track, index) => {
            const item = document.createElement('li');
            item.className = 'queue-item';
            if (index === this.trackIndex) {
                item.classList.add('now-playing');
            }
            item.innerHTML = `<img src="${this.playlist.cover}" alt="${this.playlist.album}"><div class="details"><span class="title">${track.title}</span><span class="artist">${this.playlist.artist}</span></div>`;
            item.addEventListener('click', () => {
                this.loadTrack(index, true);
            });
            this.dom.queueList.appendChild(item);
        });
    }

    _setPlayState(isPlaying) {
        const icon = isPlaying ? this.icons.pause : this.icons.play;
        const title = isPlaying ? 'Pause' : 'Play';
        this.dom.mainPlayPauseIcon.innerHTML = icon;
        this.dom.fsPlayPauseIconPaths.innerHTML = icon;
        this.dom.mainPlayPauseBtn.setAttribute('title', title);
        this.dom.fsPlayPauseWrapper.setAttribute('title', title);
    }

    // --- Control Toggles & Setters ---
    toggleShuffle() {
        this.shuffle = !this.shuffle;
        this.dom.mainShuffleBtn.classList.toggle('active', this.shuffle);
        this.dom.fsShuffleWrapper.classList.toggle('active', this.shuffle);
    }

    toggleLoop() {
        if (this.loop === false) { this.loop = 'all'; }
        else if (this.loop === 'all') { this.loop = 'one'; }
        else { this.loop = false; }
        const isActive = this.loop !== false;
        this.dom.mainLoopBtn.classList.toggle('active', isActive);
        this.dom.fsLoopWrapper.classList.toggle('active', isActive);
    }

    setVolume(value) {
        if (this.sound) this.sound.volume(parseFloat(value));
        this.dom.mainVolumeSlider.value = value;
        this.dom.fsVolumeSlider.value = value;
    }

    toggleFullscreen() {
        this.dom.fullscreenOverlay.classList.toggle('active');
        if (this.dom.fullscreenOverlay.classList.contains('active')) {
            this._initParticles();
        }
    }

    // --- Seek & Progress Bar Logic ---
    _handleSeek(event, progressBarElement) {
        if (!this.sound || this.sound.state() !== 'loaded') return;
        const bounds = progressBarElement.getBoundingClientRect();
        const percent = Math.min(Math.max(0, (event.clientX - bounds.left) / bounds.width), 1);
        this.sound.seek(this.sound.duration() * percent);
        this._step(); // Update UI immediately
    }

    _step() {
        if (this.sound && this.sound.playing() && !this.isSeeking) {
            const seek = this.sound.seek() || 0;
            const duration = this.sound.duration();
            const percent = `${((seek / duration) * 100) || 0}%`;
            this.dom.currentTime.textContent = this._formatTime(seek);
            this.dom.fsCurrentTime.textContent = this._formatTime(seek);
            this.dom.progress.style.width = percent;
            this.dom.fsProgress.style.width = percent;
            this._drawParticles(seek);
        }
        if (this.sound && this.sound.playing()) {
            this.raf = requestAnimationFrame(this._step.bind(this));
        }
    }


    // --- Visualizer Logic ---
    _initParticles() {
        const canvas = this.dom.particleCanvas;
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        this.particles = [];
        const particleCount = 100;
        for (let i = 0; i < particleCount; i++) {
            this.particles.push(new Particle(canvas));
        }
    }

    _drawParticles(time) {
        const canvas = this.dom.particleCanvas;
        const ctx = this.particleCtx;
        if (canvas.width !== canvas.offsetWidth || canvas.height !== canvas.offsetHeight) {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const color = this._getParticleColor(time);
        this.particles.forEach(p => {
            p.update();
            p.draw(ctx, color);
        });
    }

    _getParticleColor(time) {
        const fraction = (Math.sin(time * 0.5) + 1) / 2;
        const r_low = 58, g_low = 123, b_low = 213; // Blue
        const r_high = 199, g_high = 0, b_high = 0; // Red
        const r = r_low + (r_high - r_low) * fraction;
        const g = g_low + (g_high - g_low) * fraction;
        const b = b_low + (b_high - b_low) * fraction;
        return { r: Math.floor(r), g: Math.floor(g), b: Math.floor(b) };
    }


    // --- Utility Methods ---
    _formatTime(secs) {
        const minutes = Math.floor(secs / 60) || 0;
        const seconds = Math.floor(secs - minutes * 60) || 0;
        return `${minutes}:${(seconds < 10 ? '0' : '')}${seconds}`;
    }

    _cacheDomElements() {
        return {
            albumArt: document.getElementById('player-album-art'), trackTitle: document.getElementById('player-track-title'), trackArtist: document.getElementById('player-track-artist'),
            mainPlayPauseBtn: document.getElementById('main-play-pause-btn'), mainPlayPauseIcon: document.getElementById('main-play-pause-icon'), mainPrevBtn: document.getElementById('main-prev-btn'), mainNextBtn: document.getElementById('main-next-btn'), mainShuffleBtn: document.getElementById('main-shuffle-btn'), mainLoopBtn: document.getElementById('main-loop-btn'),
            currentTime: document.getElementById('player-current-time'), duration: document.getElementById('player-duration'), progressBar: document.getElementById('progress-bar'), progress: document.getElementById('player-progress'),
            mainVolumeSlider: document.getElementById('main-volume-slider'),
            fullscreenBtn: document.getElementById('fullscreen-btn'), fullscreenOverlay: document.getElementById('fullscreen-overlay'), fullscreenCloseBtn: document.getElementById('fullscreen-close-btn'),
            fullscreenCoverArt: document.getElementById('fullscreen-cover-art'), fullscreenTrackTitle: document.getElementById('fullscreen-track-title'), fullscreenTrackArtist: document.getElementById('fullscreen-track-artist'),
            fsPlayPauseWrapper: document.getElementById('fs-play-pause-wrapper'), fsPlayPauseIconPaths: document.getElementById('fs-play-pause-icon-paths'), fsPrevWrapper: document.getElementById('fs-prev-wrapper'), fsNextWrapper: document.getElementById('fs-next-wrapper'),
            fsShuffleWrapper: document.getElementById('fs-shuffle-wrapper'), fsLoopWrapper: document.getElementById('fs-loop-wrapper'),
            fsVolumeSlider: document.getElementById('fs-volume-slider'),
            fsCurrentTime: document.getElementById('fullscreen-current-time'), fsDuration: document.getElementById('fullscreen-duration'), fsProgressBar: document.getElementById('fullscreen-progress-bar'), fsProgress: document.getElementById('fullscreen-player-progress'),
            queueList: document.getElementById('queue-list'),
            particleCanvas: document.getElementById('particle-visualizer'),
        };
    }

    _bindEvents() {
        this.dom.mainPlayPauseBtn.addEventListener('click', this.togglePlayPause.bind(this));
        this.dom.fsPlayPauseWrapper.addEventListener('click', this.togglePlayPause.bind(this));
        this.dom.mainNextBtn.addEventListener('click', this.next.bind(this));
        this.dom.fsNextWrapper.addEventListener('click', this.next.bind(this));
        this.dom.mainPrevBtn.addEventListener('click', this.prev.bind(this));
        this.dom.fsPrevWrapper.addEventListener('click', this.prev.bind(this));
        this.dom.mainShuffleBtn.addEventListener('click', this.toggleShuffle.bind(this));
        this.dom.fsShuffleWrapper.addEventListener('click', this.toggleShuffle.bind(this));
        this.dom.mainLoopBtn.addEventListener('click', this.toggleLoop.bind(this));
        this.dom.fsLoopWrapper.addEventListener('click', this.toggleLoop.bind(this));
        this.dom.mainVolumeSlider.addEventListener('input', (e) => this.setVolume(e.target.value));
        this.dom.fsVolumeSlider.addEventListener('input', (e) => this.setVolume(e.target.value));
        this.dom.fullscreenBtn.addEventListener('click', this.toggleFullscreen.bind(this));
        this.dom.fullscreenCloseBtn.addEventListener('click', this.toggleFullscreen.bind(this));
        [this.dom.progressBar, this.dom.fsProgressBar].forEach(bar => {
            bar.addEventListener('mousedown', (e) => {
                this.isSeeking = true;
                this._handleSeek(e, bar);
                const onMouseMove = (moveEvent) => {
                    if (this.isSeeking) { this._handleSeek(moveEvent, bar); }
                };
                const onMouseUp = () => {
                    this.isSeeking = false;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                };
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        });
    }
}

/**
 * Particle Class for the background visualizer.
 */
class Particle {
    constructor(canvas) {
        this.canvas = canvas;
        this.x = 0;
        this.y = Math.random() * this.canvas.height;
        this.speed = Math.random() * 2 + 1;
        this.size = Math.random() * 1.5 + 1.5;
        this.opacity = Math.random() * 0.5 + 0.2;
    }
    update() {
        this.x += this.speed;
        if (this.x > this.canvas.width) {
            this.x = 0;
            this.y = Math.random() * this.canvas.height;
            this.speed = Math.random() * 2 + 1;
        }
    }
    draw(ctx, color) {
        ctx.fillStyle = `rgba(${color.r}, ${color.g}, ${color.b}, ${this.opacity})`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}

