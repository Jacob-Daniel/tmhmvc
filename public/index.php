<?php
require_once __DIR__ . '/../app/bootstrap/app.php';
require_once __DIR__ . '/../app/front_core/head.php';

// $meta = resolveMetaRecord($pageContext);
// if ($meta['page'] === null) {
//     http_response_code(404);
//     require __DIR__ . '/../app/views/front/partials/header.php';
//     require __DIR__ . '/../app/controllers/front/pages/404.php';
//     // require __DIR__ . '/../app/views/front/partials/footer.php';
//     exit;
// }

// $page = $meta['page']->slug ?? null;
// $subcategory = $pageContext['subcategory'] ?? null;


// if ($page === 'home') {
// require __DIR__ . '/../app/views/front/partials/header-home.php';
//     require __DIR__ . '/../app/controllers/front/pages/home.php';
// } elseif ($subcategory) {
//     require_once __DIR__ . '/../app/views/front/partials/header.php';
//     require __DIR__ . '/../app/controllers/front/pages/subcategory.php';
// } elseif ($page && file_exists(__DIR__ . "/../app/controllers/front/pages/{$page}.php")) {
//     require_once __DIR__ . '/../app/views/front/partials/header.php';
//     require __DIR__ . "/../app/controllers/front/pages/{$page}.php";
// } else {
//     http_response_code(404);
//     require_once __DIR__ . '/../app/views/front/partials/header.php';
//     require __DIR__ . '/../app/controllers/front/pages/404.php';
// }

// // require __DIR__ . '/../app/views/front/partials/footer.php';