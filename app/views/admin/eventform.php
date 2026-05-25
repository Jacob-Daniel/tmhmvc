<?php render('components/imageModal', ['images' => $images]); ?>
<?= buildForm($rec, $config, ['categories' => $categories, 'subcats' => $subcats, 'seo'=> $seo]); ?>