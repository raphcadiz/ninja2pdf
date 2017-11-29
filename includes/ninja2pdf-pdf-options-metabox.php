<table class="form-table">
    <tbody>
    <?php foreach ($pdf_options as $key => $pdf_option): ?>
        <?= $pdf_option_instance->generate_input($pdf_option); ?>
    <?php endforeach; ?>
    </tbody>
</table>