<div class="wrap wrap-<?= $_GET['tab'] ?>">

    <h2>Ninja 2 PDF Settings</h2>

    <h2 class="nav-tab-wrapper">

        <a href="<?= admin_url( 'edit.php?post_type=ninja_merge&page=ninja2pdf-settings' ); ?>" class="nav-tab <?= ($_GET['page'] == 'ninja2pdf-settings' && !isset($_GET['tab'])) ? 'nav-tab-active' : '' ?>"><?php _e( 'Settings', 'ninja-pdf' ) ?></a>

        <a href="<?= admin_url( 'edit.php?post_type=ninja_merge&page=ninja2pdf-settings&tab=licenses' ); ?>" class="nav-tab <?= ($_GET['page'] == 'ninja2pdf-settings' && $_GET['tab'] == 'licenses') ? 'nav-tab-active' : '' ?>"><?php _e( 'Licenses', 'ninja-pdf' ) ?></a>

        <a href="<?= admin_url( 'edit.php?post_type=ninja_merge&page=ninja2pdf-settings&tab=addons' ); ?>" class="nav-tab <?= ($_GET['page'] == 'ninja2pdf-settings' && $_GET['tab'] == 'addons') ? 'nav-tab-active' : '' ?>"><?php _e( 'Add-ons', 'ninja-pdf' ) ?></a>

        <a href="<?= admin_url( 'edit.php?post_type=ninja_merge&page=ninja2pdf-settings&tab=system-check' ); ?>" class="nav-tab <?= ($_GET['page'] == 'ninja2pdf-settings' && $_GET['tab'] == 'system-check') ? 'nav-tab-active' : '' ?>"><?php _e( 'System Check', 'ninja-pdf' ) ?></a>

    </h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'ninja2pdf_settings' ); ?>
        <?php do_settings_sections( 'ninja2pdf_settings' ); ?> 
        <?php
            if (isset($_GET['tab']) && $_GET['tab'] == 'licenses') {
                include_once(N2PDF_PATH_INCLUDES . '/ninja2pdf-licenses.php');
            }
        ?>
        <?php submit_button(); ?>
    </form>

</div>