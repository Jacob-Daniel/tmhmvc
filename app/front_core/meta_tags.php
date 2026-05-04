<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= $desc ?? '' ?>" />
<meta name="keywords" content="<?= $metak ?? '' ?>" />
<meta property="og:url" content="<?= $fullurl ?? '' ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= $title ?? '' ?>" />
<meta property="og:description" content="<?= $desc ?? '' ?>" />
<meta property="og:image" content="<?= $image_url ?? '' ?>" />
<meta property="og:image:width" content="<?= $width ?? '' ?>" />
<meta property="og:image:height" content="<?= $height ?? '' ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="<?= $comp_name ?? '' ?>" />
<meta name="twitter:url" content="<?= $fullurl ?? '' ?>" />
<meta name="twitter:creator" content="<?= $title ?? '' ?>" />
<meta name="twitter:title" content="<?= $title ?? '' ?>" />
<meta name="twitter:description" content="<?= $desc ?? '' ?>" />
<meta name="twitter:image" content="<?= isset($image_url) ? $image_url . '?' . uniqid() : '' ?>"/>
<meta name="twitter:image:alt" content="<?= $title ?? '' ?>" />
