<div class="wrap wrap-<?= $_GET['tab'] ?>">

    <h2>Completed Merges</h2>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Time</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (array_reverse($ninja2pdf_completed_merges) as $key => $merge) {
                $details = json_decode($merge);
                $user_info = get_userdata($details->user);
                $name = $user_info->data->display_name;
                ?>
                <tr>
                    <td><?= $name ?></td>
                    <td><?= $details->time_submitted ?></td>
                    <td><a href="<?= get_site_url() . '?download-completed-pdf=' . $details->file ?>"><?= basename($details->file) ?></a></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>