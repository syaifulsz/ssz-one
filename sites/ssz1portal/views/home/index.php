<?php $this->block() ?>
<?php $this->blockEnd( app\components\View::BLOCK_HEAD ) ?>

<div class="container">
    <?= $this->staticRender( 'common/content-header', [
        'title' => 'Home',
        'sub' => implode( ' / ', explode( '/', __FILE__ ) )
    ] ) ?>
</div>

<?php $this->block() ?>
<?php $this->blockEnd( app\components\View::BLOCK_BODY_END ) ?>
