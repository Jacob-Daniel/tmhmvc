<?php
declare(strict_types=1);

if (filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) === -1) {
    unset(
        $_SESSION['srch_fld'],
        $_SESSION['srch_val'],
        $_SESSION['image_item']
    );
    header('Location: /admin/index.php?action=gallery');
    exit;
}

$condition = filter_input(INPUT_GET, 'condition', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$_SESSION['page'] = $page;

$perPage = PER_PAGE;
$itemId  = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * PER_PAGE;

if (!empty($condition) && strlen($condition) >= 2) {
    $searchTerm = '%' . $condition . '%';

    $sql = "SELECT * FROM images
            WHERE title LIKE ? OR alt LIKE ? OR description LIKE ?
            ORDER BY id DESC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $images = $stmt->get_result();

} else {

$images = getList('images', "ORDER BY title ASC LIMIT $offset," . PER_PAGE);

}
$pageinfo = setupPaging('images', PER_PAGE);

render('gallery', [
    'images'   => $images,
    'page'     => $page,
    'pageinfo' => $pageinfo,
    'perPage'  => $perPage,
    'itemId'   => $itemId,
    'condition'   => $condition,
]);