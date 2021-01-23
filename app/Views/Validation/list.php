<?php if (! empty($errors)) : ?>
    <?php foreach ($errors as $error) : ?>
        <div class="error"><?= esc($error) ?></div>
    <?php endforeach ?>
<?php endif ?>
