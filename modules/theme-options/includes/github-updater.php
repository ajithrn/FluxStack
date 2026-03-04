<?php
/**
 * GitHub Updater for FluxStack Theme
 *
 * Checks a GitHub repository for new releases and integrates
 * with WordPress's theme update system.
 *
 * Activated only when FLUXSTACK_GITHUB_REPO is defined.
 *
 * Usage: define('FLUXSTACK_GITHUB_REPO', 'username/repo-name');
 *
 * @package FluxStack
 */

if (!defined('ABSPATH')) {
    exit;
}

class FS_GitHub_Updater {
    private $theme_slug;
    private $github_repo;
    private $transient_key;
    private $theme_version;

    /**
     * Initialize the updater
     *
     * @param string $github_repo GitHub repo in 'owner/repo' format.
     */
    public function __construct($github_repo) {
        $this->github_repo = $github_repo;
        $this->theme_slug = get_template() === 'bricks' ? get_stylesheet() : get_template();
        $this->transient_key = 'fluxstack_github_update_' . md5($this->theme_slug);

        $theme = wp_get_theme($this->theme_slug);
        $this->theme_version = $theme->get('Version');

        add_filter('pre_set_site_transient_update_themes', array($this, 'check_update'));
        add_filter('themes_api', array($this, 'theme_info'), 10, 3);

        // Clear transient on theme switch or update
        add_action('switch_theme', array($this, 'clear_transient'));
        add_action('upgrader_process_complete', array($this, 'clear_transient'), 10, 0);
    }

    /**
     * Check for theme updates via GitHub releases
     *
     * @param object $transient The update_themes transient.
     * @return object Modified transient.
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote = $this->get_remote_version();
        if (!$remote) {
            return $transient;
        }

        if (version_compare($this->theme_version, $remote->new_version, '<')) {
            $transient->response[$this->theme_slug] = array(
                'theme'       => $this->theme_slug,
                'new_version' => $remote->new_version,
                'url'         => $remote->url,
                'package'     => $remote->package,
            );
        }

        return $transient;
    }

    /**
     * Provide theme info for the WordPress updates UI
     *
     * @param false|object|array $res    The result object.
     * @param string             $action The API action.
     * @param object             $args   The arguments.
     * @return false|object
     */
    public function theme_info($res, $action, $args) {
        if ('theme_information' !== $action) {
            return $res;
        }

        if (!isset($args->slug) || $args->slug !== $this->theme_slug) {
            return $res;
        }

        $remote = $this->get_remote_version();
        if (!$remote) {
            return $res;
        }

        $theme = wp_get_theme($this->theme_slug);

        $res = new stdClass();
        $res->name          = $theme->get('Name');
        $res->slug          = $this->theme_slug;
        $res->version       = $remote->new_version;
        $res->tested        = $remote->tested;
        $res->requires      = '5.6';
        $res->author        = $theme->get('Author');
        $res->download_link = $remote->package;
        $res->trunk         = $remote->package;
        $res->last_updated  = $remote->last_updated;
        $res->sections      = array(
            'description' => $theme->get('Description'),
            'changelog'   => $remote->changelog,
        );

        return $res;
    }

    /**
     * Get remote version (cached with transient)
     *
     * @return object|false Remote version data or false.
     */
    private function get_remote_version() {
        $remote = get_site_transient($this->transient_key);

        if (false === $remote) {
            $remote = $this->fetch_github_release();
            set_site_transient($this->transient_key, $remote ?: 'none', 12 * HOUR_IN_SECONDS);
        }

        // Return false if we cached a miss
        if ('none' === $remote) {
            return false;
        }

        return $remote;
    }

    /**
     * Fetch the latest release from GitHub API
     *
     * @return object|false Release data or false on failure.
     */
    public function fetch_github_release() {
        $url = "https://api.github.com/repos/{$this->github_repo}/releases/latest";
        $response = wp_remote_get($url, array(
            'timeout' => 10,
            'headers' => array('Accept' => 'application/vnd.github.v3+json'),
        ));

        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response));
        if (!isset($body->tag_name)) {
            return false;
        }

        $version = ltrim($body->tag_name, 'v');

        // Find the theme zip asset (prefer named asset over zipball)
        $package = $body->zipball_url;
        if (!empty($body->assets)) {
            foreach ($body->assets as $asset) {
                if ($asset->name === $this->theme_slug . '.zip') {
                    $package = $asset->browser_download_url;
                    break;
                }
            }
        }

        $obj = new stdClass();
        $obj->new_version  = $version;
        $obj->url          = $body->html_url;
        $obj->package      = $package;
        $obj->changelog    = $this->parse_markdown($body->body ?? '');
        $obj->last_updated = $body->published_at;
        $obj->tested       = '6.7';

        return $obj;
    }

    /**
     * Clear the update transient
     */
    public function clear_transient() {
        delete_site_transient($this->transient_key);
    }

    /**
     * Simple markdown to HTML for changelog display
     *
     * @param string $text Markdown text.
     * @return string HTML content.
     */
    private function parse_markdown($text) {
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $text);
        $text = preg_replace('/^\s*-\s+(.*)/m', '<li>$1</li>', $text);
        $text = preg_replace('/((<li>.*<\/li>\s*)+)/s', '<ul>$1</ul>', $text);
        return nl2br($text);
    }
}
