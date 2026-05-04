<form action="/admin/api/saveimage" method="post" enctype="multipart/form-data" data-ajax id="imageform" class="max-w-7xl mx-auto p-6">
    <input type="hidden" name="edit" value="<?= $rec?->id ?? '' ?>">
    <input type="hidden" name="table" value="images">
    <input type="hidden" name="idfield" value="id">
    <input type="hidden" name="item_word" value="Image">
    <input type="hidden" id="imagepath" name="imagepath" value="<?= htmlspecialchars($filename, ENT_QUOTES) ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <section class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900">Image Details</h2>
                
                <div id="message" class="message mb-4">
                    <pre id="r" class="text-sm text-red-600 whitespace-pre-wrap"></pre>
                </div>

                <div class="space-y-4 mb-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" id="title" value="<?= htmlspecialchars($rec?->title ?? '', ENT_QUOTES) ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($rec?->description ?? '', ENT_QUOTES) ?></textarea>
                    </div>

                    <div>
                        <label for="alt" class="block text-sm font-medium text-gray-700 mb-1">
                            Alt Text
                            <?php if (!isset($rec?->alt) || empty($rec->alt)): ?>
                                <span class="text-red-600 text-xs ml-2">* Required: Accessibility</span>
                            <?php endif; ?>
                        </label>
                        <input type="text" name="alt" id="alt" value="<?= htmlspecialchars($rec?->alt ?? '', ENT_QUOTES) ?>" class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <?php if ($rec?->id && $filename): ?>
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900">Image Preview</h3>
                        <?php
                        $filepath = PUBLIC_UPLOADS_PATH . '/' . $filename;
                        if (file_exists($filepath)):
                            $isize = getimagesize($filepath);
                            $fsize = filesize($filepath);
                            $src = BASE_URL_IMG_DIR . '/' . $filename;
                        ?>
                            <div class="mb-6">
                                <img src="<?= htmlspecialchars($src, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($rec->alt ?? '', ENT_QUOTES) ?>" class="max-w-full h-auto border rounded-lg shadow-sm">
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                                <div class="grid grid-cols-[120px_1fr] gap-2">
                                    <span class="font-semibold text-gray-700">Title:</span>
                                    <span class="text-gray-900"><?= htmlspecialchars($rec->title ?? 'N/A', ENT_QUOTES) ?></span>
                                </div>
                                <div class="grid grid-cols-[120px_1fr] gap-2">
                                    <span class="font-semibold text-gray-700">Image Size:</span>
                                    <span class="text-gray-900"><?= $isize[3] ?></span>
                                </div>
                                <div class="grid grid-cols-[120px_1fr] gap-2">
                                    <span class="font-semibold text-gray-700">File Size:</span>
                                    <span class="text-gray-900"><?= formatSizeUnits($fsize) ?></span>
                                </div>
                                <div class="grid grid-cols-[120px_1fr] gap-2">
                                    <span class="font-semibold text-gray-700">Mime Type:</span>
                                    <span class="text-gray-900"><?= htmlspecialchars($isize['mime'], ENT_QUOTES) ?></span>
                                </div>
                                <div class="grid grid-cols-[120px_1fr] gap-2">
                                    <span class="font-semibold text-gray-700">Filename:</span>
                                    <span class="text-gray-900 break-all"><?= htmlspecialchars($filename, ENT_QUOTES) ?></span>
                                </div>
                                <div class="grid grid-cols-[120px_1fr] gap-2">
                                    <span class="font-semibold text-gray-700">Description:</span>
                                    <span class="text-gray-900"><?= htmlspecialchars($rec->description ?? 'N/A', ENT_QUOTES) ?></span>
                                </div>
                                <div class="grid grid-cols-[120px_1fr] gap-2">
                                    <span class="font-semibold text-gray-700">Alt Text:</span>
                                    <span class="text-gray-900"><?= htmlspecialchars($rec->alt ?? 'N/A', ENT_QUOTES) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- Sidebar - Right Column -->
        <div class="lg:col-span-1">
            <aside class="bg-white rounded-lg shadow-sm border p-6 sticky top-6">
                <div class="flex flex-col gap-2">
                    <button type="button" onclick="loadContent('gallery')" class="w-full px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Back
                    </button>

                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save
                    </button>

                    <?php if ($rec?->id): ?>
                        <button type="button" onclick="loadContent('imageform',<?= $rec->id ?>)" class="w-full px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Refresh
                        </button>

                        <button type="button" onclick="loadContent('gallery',0,'<?= htmlspecialchars($filename, ENT_QUOTES) ?>',<?= $rec->id ?>,'imagesform')" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</form>