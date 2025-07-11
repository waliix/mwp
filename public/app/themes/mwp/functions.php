// MWP Register scripts and styles.
add_action('wp_enqueue_scripts', function () {
    $manifestPath = get_theme_file_path('assets/.vite/manifest.json');

    if (
        wp_get_environment_type() === 'local' &&
        is_array(wp_remote_get('http://localhost:5173/')) // is Vite.js running
    ) {
        wp_enqueue_script('vite', 'http://localhost:5173/@vite/client');
        wp_enqueue_script('mwp', 'http://localhost:5173/assets/js/index.js');
    } elseif (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        wp_enqueue_script('mwp', get_theme_file_uri('assets/' . $manifest['assets/js/index.js']['file']));
        wp_enqueue_style('mwp', get_theme_file_uri('assets/' . $manifest['assets/js/index.js']['css'][0]));
    }
});
