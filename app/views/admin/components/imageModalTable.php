<?php
function buildImageModalTable(mysqli_result $images): string
{
    ob_start();

    if (!$images || $images->num_rows === 0) {
        echo '<div class="col-span-full text-center py-12 text-gray-500">No images found</div>';
        return ob_get_clean();
    }

    while ($image = $images->fetch_object()) {
        $imageId   = (string)$image->id;
        $imagefile = htmlspecialchars($image->imagepath ?? '', ENT_QUOTES);
        $alt       = htmlspecialchars($image->alt ?? '', ENT_QUOTES);
        $title     = htmlspecialchars($image->title ?? '', ENT_QUOTES);

        $thumbPath = PUBLIC_UPLOADS_PATH . '/thumbs/200/' . $image->imagepath;
        $src = file_exists($thumbPath)
            ? BASE_URL_IMG_DIR . '/thumbs/200/' . $image->imagepath
            : BASE_URL_IMG_DIR . '/' . $image->imagepath;
        $safeSrc = htmlspecialchars($src, ENT_QUOTES);
        ?>

        <div class="relative group bg-white rounded overflow-hidden hover:shadow-lg transition-shadow duration-200"
             data-path="<?= $imagefile ?>">
            <div class="aspect-square bg-gray-100 relative">
                <img src="<?= $safeSrc ?>"
                     alt="<?= $alt ?: 'Image thumbnail' ?>"
                     title="<?= $title ?>"
                     class="w-full h-full object-cover">

                <div class="absolute top-2 right-2">
                    <label class="relative inline-flex items-center justify-center w-8 h-8 bg-white rounded-md shadow-sm cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio"
                               name="modal-selected"
                               class="select-images w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                               value="<?= $imagefile ?>"
                               data-id="<?= $imageId ?>"
                               data-filename="<?= $imagefile ?>">
                    </label>
                </div>
            </div>
            <div class="p-3 space-y-1">
                <?php if ($title): ?>
                    <h3 class="text-sm font-medium text-gray-900 truncate" title="<?= $title ?>">
                        <?= $title ?>
                    </h3>
                <?php endif; ?>
                
                <p class="text-xs text-gray-600 truncate font-mono" title="<?= $imagefile ?>">
                    <?= $imagefile ?>
                </p>
            </div>
        </div>

    <?php
    }

    return ob_get_clean();
}