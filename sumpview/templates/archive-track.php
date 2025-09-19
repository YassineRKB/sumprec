<?php
/**
 * The template for displaying the Track custom post type archive.
 *
 * @package SumpView
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="tracks-archive-header">
        <h1>All Tracks</h1>
        <button class="play-all-btn" id="play-all-tracks">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z"/>
            </svg>
            Play All
        </button>
    </div>

    <div class="tracks-grid">
        <?php
        // Query tracks ordered by date (newest first)
        $tracks_query = new WP_Query(array(
            'post_type' => 'track',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if ( $tracks_query->have_posts() ) :
            while ( $tracks_query->have_posts() ) : $tracks_query->the_post();
                $release_id = get_field('associated_release');
                $artists = get_field('associated_artists');
                $audio_file = get_field('audio_file');
                
                // Get release cover art
                $cover_image = '';
                if ($release_id) {
                    $cover_image = get_the_post_thumbnail_url($release_id, 'medium_large');
                }
                if (!$cover_image) {
                    $cover_image = 'https://placehold.co/300x300/14182b/ffffff?text=' . urlencode(get_the_title());
                }
                ?>
                <div class="track-card" data-track-id="<?php echo get_the_ID(); ?>" data-track-src="<?php echo $audio_file ? esc_url($audio_file['url']) : ''; ?>">
                    <div class="track-card-image">
                        <img src="<?php echo esc_url($cover_image); ?>" alt="<?php the_title(); ?>">
                        <div class="track-play-overlay">
                            <button class="track-play-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="track-card-content">
                        <h3 class="track-card-title"><?php the_title(); ?></h3>
                        <?php if ( $artists ) : ?>
                            <div class="track-card-artist">
                                <?php
                                $artist_links = array();
                                foreach ($artists as $artist_id) {
                                    $artist_links[] = '<a href="' . get_permalink($artist_id) . '">' . get_the_title($artist_id) . '</a>';
                                }
                                echo implode(', ', $artist_links);
                                ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( $release_id ) : ?>
                            <div class="track-card-release">
                                from <a href="<?php echo get_permalink($release_id); ?>"><?php echo get_the_title($release_id); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px;"><p>No tracks found.</p></div>';
        endif;
        ?>
    </div>

</main><!-- #main -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle individual track clicks
    document.querySelectorAll('.track-card').forEach(function(card) {
        card.addEventListener('click', function() {
            const trackSrc = this.dataset.trackSrc;
            if (trackSrc && window.sumpPlayer) {
                // Create a simple playlist with just this track
                const trackData = {
                    tracks: [{
                        title: this.querySelector('.track-card-title').textContent,
                        src: trackSrc
                    }],
                    artist: this.querySelector('.track-card-artist') ? this.querySelector('.track-card-artist').textContent : 'Unknown Artist',
                    album: this.querySelector('.track-card-release') ? this.querySelector('.track-card-release').textContent.replace('from ', '') : 'Single',
                    cover: this.querySelector('.track-card-image img').src
                };
                window.sumpPlayer.playlist = trackData;
                window.sumpPlayer.loadTrack(0, true);
            }
        });
    });

    // Handle play all button
    document.getElementById('play-all-tracks').addEventListener('click', function() {
        const allTracks = [];
        document.querySelectorAll('.track-card').forEach(function(card) {
            const trackSrc = card.dataset.trackSrc;
            if (trackSrc) {
                allTracks.push({
                    title: card.querySelector('.track-card-title').textContent,
                    src: trackSrc
                });
            }
        });
        
        if (allTracks.length > 0 && window.sumpPlayer) {
            const playlistData = {
                tracks: allTracks,
                artist: 'Various Artists',
                album: 'All Tracks',
                cover: 'https://placehold.co/300x300/14182b/ffffff?text=All+Tracks'
            };
            window.sumpPlayer.playlist = playlistData;
            window.sumpPlayer.loadTrack(0, true);
        }
    });
});
</script>

<?php
get_footer();