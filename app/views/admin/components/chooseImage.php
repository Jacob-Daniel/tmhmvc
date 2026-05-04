<?php
function renderChooseImage(array $opts = []): void
{
    $fieldId      = $opts['fieldId']      ?? 'imagepath';
    $boxId        = $opts['boxId']        ?? 'main-img-box';
    $imgId        = $opts['imgId']        ?? 'main-img';
    $type         = $opts['type']         ?? 'single';
    $content      = $opts['content']      ?? 'pageform';
    $existingPath = $opts['existingPath'] ?? '';
    $label        = $opts['label']        ?? 'Select Image';

    $src = '';
    if ($existingPath) {
        $src = file_exists(PUBLIC_UPLOADS_PATH . '/thumbs/200/' . $existingPath)
            ? BASE_URL_IMG_DIR . '/thumbs/200/' . $existingPath
            : BASE_URL_IMG_DIR .'/'. $existingPath;
    }
    ?>
    <div class="choose-image flex flex-col w-full gap-y-2">
        <button type="button"
                data-open-images
                data-content="<?= htmlspecialchars($content) ?>"
                data-type="<?= htmlspecialchars($type) ?>"
                data-box-id="<?= htmlspecialchars($boxId) ?>"
                data-img-id="<?= htmlspecialchars($imgId) ?>"
                data-field-id="<?= htmlspecialchars($fieldId) ?>"
                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition w-full">
            <?= htmlspecialchars($label) ?>
        </button>

        <div id="<?= htmlspecialchars($boxId) ?>">
            <?php if ($src): ?>
                <img id="<?= htmlspecialchars($imgId) ?>"
                     src="<?= htmlspecialchars($src) ?>"
                     class="rounded border max-w-[200px]"
                     alt="<?= htmlspecialchars($label) ?>">
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>