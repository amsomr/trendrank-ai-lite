<?php
/**
 * Plugin Name:       TrendRank AI Lite
 * Plugin URI:        https://github.com/your-username/trendrank-ai-lite
 * Description:       A complete, automated AI post and image generator with SEO keyword and Rank Math integration.
 * Version:           5.0.0 (Master Prompt Engine)
 * Author:            Omar Amassine
 * Author URI:        https://triplew.ma
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       trendrank-ai-lite
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// --- ADMIN PAGES & SETTINGS REGISTRATION ---

function trai_lite_register_settings() {
    register_setting( 'trai_lite_options_group', 'trai_lite_settings' );
}
add_action( 'admin_init', 'trai_lite_register_settings' );

function trai_lite_add_admin_pages() {
    add_menu_page( 'TrendRank AI Lite', 'TrendRank AI Lite', 'manage_options', 'trendrank-ai-lite', 'trai_lite_main_page_html', 'dashicons-welcome-write-blog', 25 );
    add_submenu_page( 'trendrank-ai-lite', 'Generator', 'Generator', 'manage_options', 'trendrank-ai-lite', 'trai_lite_main_page_html' );
    add_submenu_page( 'trendrank-ai-lite', 'Settings', 'Settings', 'manage_options', 'trai-lite-settings', 'trai_lite_settings_page_html' );
}
add_action( 'admin_menu', 'trai_lite_add_admin_pages' );

function trai_lite_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    ?>
    <div class="wrap">
        <h1>TrendRank AI Lite - Settings</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'trai_lite_options_group' );
            $options = get_option( 'trai_lite_settings', [] );
            
            $api_key = isset( $options['api_key'] ) ? esc_attr( $options['api_key'] ) : '';
            $website_niche = isset( $options['website_niche'] ) ? esc_textarea( $options['website_niche'] ) : '';
            $language = isset( $options['language'] ) ? $options['language'] : 'English';
            $topics_to_generate = isset( $options['topics_to_generate'] ) ? intval( $options['topics_to_generate'] ) : 3;
            $post_status = isset( $options['post_status'] ) ? $options['post_status'] : 'draft';
            $enable_cron = isset( $options['enable_cron'] ) ? $options['enable_cron'] : '';
            $cron_schedule = isset( $options['cron_schedule'] ) ? $options['cron_schedule'] : 'daily';
            $topic_queue = isset( $options['topic_queue'] ) ? esc_textarea( $options['topic_queue'] ) : '';
            ?>
            <table class="form-table">
                <tr valign="top"><th scope="row"><label for="trai_lite_settings[api_key]">OpenAI API Key</label></th><td><input type="password" id="trai_lite_settings[api_key]" name="trai_lite_settings[api_key]" value="<?php echo $api_key; ?>" class="regular-text" required /></td></tr>
                <tr valign="top"><th scope="row"><label for="trai_lite_settings[website_niche]">Your Website's Niche</label></th><td><textarea id="trai_lite_settings[website_niche]" name="trai_lite_settings[website_niche]" rows="3" class="large-text" required><?php echo $website_niche; ?></textarea><p class="description">Crucial for generating relevant new ideas. e.g., "Home fitness for busy professionals, HIIT workouts, minimalist equipment, and healthy meal prep."</p></td></tr>
                <tr valign="top"><th scope="row"><label for="trai_lite_settings[language]">Article Language</label></th><td><select id="trai_lite_settings[language]" name="trai_lite_settings[language]"><option value="English" <?php selected( $language, 'English' ); ?>>English</option><option value="Spanish" <?php selected( $language, 'Spanish' ); ?>>Spanish</option><option value="French" <?php selected( $language, 'French' ); ?>>French</option><option value="German" <?php selected( $language, 'German' ); ?>>German</option><option value="Arabic" <?php selected( $language, 'Arabic' ); ?>>Arabic</option><option value="Portuguese" <?php selected( $language, 'Portuguese' ); ?>>Portuguese</option><option value="Italian" <?php selected( $language, 'Italian' ); ?>>Italian</option></select><p class="description">The language for all generated topics and posts.</p></td></tr>
                <tr valign="top"><th scope="row"><label for="trai_lite_settings[topics_to_generate]">New Topics per Day</label></th><td><input type="number" id="trai_lite_settings[topics_to_generate]" name="trai_lite_settings[topics_to_generate]" value="<?php echo $topics_to_generate; ?>" min="1" max="10" class="small-text" /><p class="description">How many new topic ideas the AI should generate every 24 hours.</p></td></tr>
                <tr valign="top"><th scope="row"><label for="trai_lite_settings[post_status]">Generated Post Status</label></th><td><select id="trai_lite_settings[post_status]" name="trai_lite_settings[post_status]"><option value="draft" <?php selected( $post_status, 'draft' ); ?>>Save as Draft (Recommended)</option><option value="publish" <?php selected( $post_status, 'publish' ); ?>>Publish Immediately</option></select><p class="description">Choose whether to publish posts immediately or save them as drafts for review.</p></td></tr>
                <tr valign="top"><th scope="row">Automation Engine</th><td><label for="trai_lite_settings[enable_cron]"><input type="checkbox" id="trai_lite_settings[enable_cron]" name="trai_lite_settings[enable_cron]" value="1" <?php checked( 1, $enable_cron ); ?>> Enable Automatic Topic & Post Generation</label></td></tr>
                <tr valign="top"><th scope="row"><label for="trai_lite_settings[cron_schedule]">Post Generation Schedule</label></th><td><select id="trai_lite_settings[cron_schedule]" name="trai_lite_settings[cron_schedule]"><option value="hourly" <?php selected( $cron_schedule, 'hourly' ); ?>>Once every hour</option><option value="every_six_hours" <?php selected( $cron_schedule, 'every_six_hours' ); ?>>Once every 6 hours</option><option value="twicedaily" <?php selected( $cron_schedule, 'twicedaily' ); ?>>Twice a day</option><option value="daily" <?php selected( $cron_schedule, 'daily' ); ?>>Once a day</option></select></td></tr>
                <tr valign="top"><th scope="row"><label for="trai_lite_settings[topic_queue]">Topic Queue</label></th><td><textarea id="trai_lite_settings[topic_queue]" name="trai_lite_settings[topic_queue]" rows="10" class="large-text"><?php echo $topic_queue; ?></textarea><p class="description">The engine works from the top down. Format: `Post Topic | Focus Keyword`</p></td></tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function trai_lite_main_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    ?>
     <div class="wrap">
        <h1>Post Generator</h1>
        <?php $count = get_option( 'trai_lite_post_count', 0 ); ?>
        <div style="padding: 10px 15px; background-color: #f0f6fc; border: 1px solid #c9e2f6; margin: 15px 0; max-width: 700px;">
            <p style="margin: 0; font-size: 1.2em;"><strong>Total Posts Generated: <?php echo intval($count); ?></strong></p>
        </div>
        <p>Use this form to generate a single post on demand.</p>
        <?php if ( isset( $_GET['message'] ) ) {
            if ( $_GET['message'] === 'success' && isset( $_GET['post_id'] ) ) {
                $post_id = intval( $_GET['post_id'] );
                $edit_link = get_edit_post_link( $post_id );
                echo '<div class="notice notice-success is-dismissible"><p>Post created successfully! <a href="' . esc_url( $edit_link ) . '">View the ' . esc_html( get_post_status( $post_id ) ) . '.</a></p></div>';
            } elseif ( $_GET['message'] === 'error' ) {
                echo '<div class="notice notice-error is-dismissible"><p><strong>Failed to create post.</strong><br>';
                if ( isset( $_GET['detail'] ) ) { echo '<strong>Details:</strong> ' . esc_html( urldecode( $_GET['detail'] ) ) . '</p>'; }
                echo '</div>';
            }
        } ?>
        <form id="trai-lite-generator-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="trai_lite_generate_post">
            <?php wp_nonce_field( 'trai_lite_generate_post_nonce' ); ?>
            <table class="form-table">
                <tr><th scope="row"><label for="post_topic">Post Topic / Concept</label></th><td><textarea id="post_topic" name="post_topic" rows="2" class="large-text" required></textarea></td></tr>
                <tr><th scope="row"><label for="focus_keyword">Primary Focus Keyword</label></th><td><input type="text" id="focus_keyword" name="focus_keyword" class="large-text" required></td></tr>
            </table>
            <?php submit_button( 'Generate Post' ); ?>
        </form>
        <div id="trai-lite-progress" style="display:none; margin-top: 20px; padding: 15px; border: 1px solid #ccc; background-color: #fff; max-width: 500px;">
            <h2 style="margin-top:0;">Generating Your Post...</h2>
            <p>This process involves multiple steps and may take over a minute. Please do not navigate away.</p>
            <ul id="progress-steps" style="list-style-type: none; padding-left: 0; margin-left: 0;">
                <li id="step-outline" class="progress-step pending"><span class="spinner"></span> Generating SEO Title, Keywords & Outline...</li>
                <li id="step-content" class="progress-step pending"><span class="spinner"></span> Writing Article Sections (this is the longest step)...</li>
                <li id="step-image" class="progress-step pending"><span class="spinner"></span> Creating Featured Image...</li>
                <li id="step-finalizing" class="progress-step pending"><span class="spinner"></span> Finalizing Post & Updating SEO...</li>
            </ul>
        </div>
        <style>.progress-step { margin-bottom: 10px; display: flex; align-items: center; opacity: 0.5; }.progress-step .spinner { visibility: hidden; float: none; margin: 0 8px 0 0; }.progress-step.active { opacity: 1; font-weight: bold; }.progress-step.active .spinner { visibility: visible; -webkit-animation: rotation 1s infinite linear; animation: rotation 1s infinite linear; }.progress-step.done { opacity: 0.8; text-decoration: line-through; }.progress-step.done .spinner { visibility: hidden; }</style>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('trai-lite-generator-form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const topicArea = document.getElementById('post_topic');
                        if (topicArea.value.trim() === '') { e.preventDefault(); return; }
                        document.querySelector('.button-primary').value = 'Generating... Please Wait';
                        document.querySelector('.button-primary').disabled = true;
                        document.getElementById('trai-lite-progress').style.display = 'block';
                        const step1 = document.getElementById('step-outline'), step2 = document.getElementById('step-content'), step3 = document.getElementById('step-image'), step4 = document.getElementById('step-finalizing');
                        step1.classList.replace('pending', 'active');
                        setTimeout(() => { step1.classList.replace('active', 'done'); step2.classList.replace('pending', 'active'); }, 5000);
                        setTimeout(() => { step2.classList.replace('active', 'done'); step3.classList.replace('pending', 'active'); }, 25000);
                        setTimeout(() => { step3.classList.replace('active', 'done'); step4.classList.replace('pending', 'active'); }, 45000);
                    });
                }
            });
        </script>
    </div>
    <?php
}

function trai_lite_setup_dashboard_widget() {
    wp_add_dashboard_widget( 'trai_lite_dashboard_widget', 'TrendRank AI Status', 'trai_lite_dashboard_widget_content');
}
add_action( 'wp_dashboard_setup', 'trai_lite_setup_dashboard_widget' );

function trai_lite_dashboard_widget_content() {
    $options = get_option('trai_lite_settings', []);
    $count = get_option('trai_lite_post_count', 0);
    $is_enabled = isset($options['enable_cron']) && $options['enable_cron'] == 1;
    $schedule = isset($options['cron_schedule']) ? ucfirst($options['cron_schedule']) : 'Daily';
    $topic_queue_raw = isset($options['topic_queue']) ? trim($options['topic_queue']) : '';
    $topics_in_queue = empty($topic_queue_raw) ? 0 : count(explode("\n", $topic_queue_raw));
    echo '<div class="main">';
    echo '<h4>Generation Stats</h4><p><strong>Total Posts Generated:</strong> ' . intval($count) . '</p><p><strong>Topics in Queue:</strong> ' . $topics_in_queue . '</p>';
    echo '<hr><h4>Automation Status</h4>';
    if ($is_enabled) {
        echo '<p style="color: #227122;"><strong>Automation Engine is ACTIVE.</strong></p><p>Posts are generated: ' . esc_html($schedule) . '</p>';
    } else {
        echo '<p style="color: #d63638;"><strong>Automation Engine is DISABLED.</strong></p>';
    }
    echo '<hr><p class="community-events-footer"><a href="' . esc_url(admin_url('admin.php?page=trendrank-ai-lite')) . '">Go to Generator</a> | <a href="' . esc_url(admin_url('admin.php?page=trai-lite-settings')) . '">Go to Settings</a></p>';
    echo '</div>';
}

add_filter( 'cron_schedules', function($schedules){ if(!isset($schedules["every_six_hours"])){$schedules["every_six_hours"] = array('interval' => 21600, 'display' => __('Every 6 Hours'));} return $schedules; });

add_action( 'update_option_trai_lite_settings', 'trai_lite_handle_cron_schedule_change', 10, 2 );
function trai_lite_handle_cron_schedule_change( $old_value, $new_value ) {
    $post_hook = 'trai_lite_post_cron_hook'; $topic_hook = 'trai_lite_topic_cron_hook';
    $is_enabled = isset( $new_value['enable_cron'] ) && $new_value['enable_cron'] == 1;
    $schedule = isset( $new_value['cron_schedule'] ) ? $new_value['cron_schedule'] : 'daily';
    if ( wp_next_scheduled( $post_hook ) ) { wp_clear_scheduled_hook( $post_hook ); }
    if ( wp_next_scheduled( $topic_hook ) ) { wp_clear_scheduled_hook( $topic_hook ); }
    if ( $is_enabled ) {
        wp_schedule_event( time(), $schedule, $post_hook );
        wp_schedule_event( time() + 3600, 'daily', $topic_hook );
    }
}

add_action( 'trai_lite_post_cron_hook', 'trai_lite_execute_post_job' );
function trai_lite_execute_post_job() {
    $options = get_option('trai_lite_settings', []);
    if (empty($options['enable_cron'])) return;
    $topic_queue = isset($options['topic_queue']) ? trim($options['topic_queue']) : '';
    if (empty($topic_queue)) return;
    $topics = explode("\n", $topic_queue);
    $first_topic_line = trim(array_shift($topics));
    if (empty($first_topic_line)) return;
    $options['topic_queue'] = implode("\n", $topics);
    update_option('trai_lite_settings', $options);
    $parts = explode('|', $first_topic_line);
    $topic = trim($parts[0]);
    $focus_keyword = isset($parts[1]) ? trim($parts[1]) : $topic;
    $language = isset($options['language']) ? $options['language'] : 'English';
    trai_lite_generate_the_post($topic, $focus_keyword, $language);
}

add_action( 'trai_lite_topic_cron_hook', 'trai_lite_execute_topic_generation_job' );
function trai_lite_execute_topic_generation_job() {
    $options = get_option('trai_lite_settings', []);
    if (empty($options['enable_cron'])) return;
    $niche = isset($options['website_niche']) ? trim($options['website_niche']) : '';
    $api_key = isset($options['api_key']) ? $options['api_key'] : '';
    $language = isset($options['language']) ? $options['language'] : 'English';
    $topics_to_generate = isset($options['topics_to_generate']) ? intval($options['topics_to_generate']) : 3;
    if (empty($niche) || empty($api_key)) return;
    $topic_prompt = "You are a creative SEO content strategist. My website's core niche is: \"{$niche}\". Please generate exactly {$topics_to_generate} new, engaging, and SEO-friendly blog post ideas relevant to this niche. The output language for the topics MUST be {$language}. Format your response as {$topics_to_generate} separate lines. Each line must follow this exact format: `Post Title | Focus Keyword`. Do not add numbers, bullet points, or any other text.";
    $new_topics_str = trai_lite_call_openai($topic_prompt, 'You are an expert topic generator for blogs.', 500, 45, $language);
    if ($new_topics_str && !is_wp_error($new_topics_str)) {
        $current_queue = isset($options['topic_queue']) ? trim($options['topic_queue']) : '';
        $new_queue = $current_queue . "\n" . trim($new_topics_str);
        $options['topic_queue'] = trim($new_queue);
        update_option('trai_lite_settings', $options);
    }
}

function trai_lite_call_openai($prompt, $system_prompt, $max_tokens, $timeout = 120, $language = 'English') {
    $options = get_option('trai_lite_settings', []);
    $api_key = isset($options['api_key']) ? $options['api_key'] : '';
    if (empty($api_key)) return new WP_Error('api_error', 'API Key is missing from settings.');
    $api_url = '