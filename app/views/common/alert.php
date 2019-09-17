<?php foreach( app\components\Session::getMessages() as $message ) : ?>
    <?php if ( $message[ 'tag' ] === 'alert' ) : ?>
        <div class="text-left alert alert-<?= $message[ 'type' ] ?> alert-dismissible fade show" role="alert">
            <?= $message[ 'message' ] ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif ?>
<?php endforeach ?>
