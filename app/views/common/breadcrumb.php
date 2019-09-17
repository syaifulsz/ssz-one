<?php

$breadcrumb = $breadcrumb ?? $this->breadcrumb;

if ( $breadcrumb ) : ?>
    <nav aria-label="breadcrumb" class="container">
        <ol class="breadcrumb p-0 bg-transparent">
            <?php

            $index = 1;
            foreach ( $breadcrumb as $crumb ) : $active = ( $index === count( $breadcrumb ) ) ?>
                <li class="breadcrumb-item <?= $active ? 'active' : '' ?>" <?= $active ? 'aria-current="page"' : '' ?>>
                    <?php if ( $active ) : ?>
                        <?= $crumb[ 'label' ] ?>
                    <?php else : ?>
                        <a href="<?= $crumb[ 'url' ] ?>">
                            <?= $crumb[ 'label' ] ?>
                        </a>
                    <?php endif ?>
                </li>
            <?php $index++; endforeach ?>
        </ol>
    </nav>
<?php endif ?>
