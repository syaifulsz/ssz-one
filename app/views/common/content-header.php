<div class="mb-3">
    <div class="h5">
        <?= $title ?>
    </div>
    <?php if ( $sub ?? false ) : ?>
        <div class="small" style="opacity: 0.35">
            <?= implode( ' / ', explode( '/', $sub ) ) ?>
        </div>
    <?php endif ?>
</div>
