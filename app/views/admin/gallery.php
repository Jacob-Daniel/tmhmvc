<?php require_once __DIR__ . '/components/imageTable.php';
 ?>
<fieldset class="bg-white p-3 border rounded">
    <legend class="font-semibold">Gallery</legend>

    <div class="border-b mb-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <?php draw_pager('gallery', $pageinfo['pages'], $pageinfo['page'], 1, $itemId ?? 0); ?>
            </div>
            <div class="text-sm text-gray-600">
                Page <?= $pageinfo['page'] ?> of <?= $pageinfo['pages'] ?>
            </div>
        </div>
          
        <div id="message" class="mt-4">
            <pre id="r" class="text-sm text-red-600 whitespace-pre-wrap"></pre>
        </div>
    </div>

    <form action="/admin/api/saveimage" method="post" id="gal" data-ajax enctype="multipart/form-data">
        <input type="hidden" name="item_word" value="Image/s">
        <input type="hidden" name="table" value="images">
        <input type="hidden" name="idfield" value="id">
        <input type="hidden" name="imagepath" value="imagepath">
        <div class="flex flex-wrap items-center gap-3 mb-6 p-4 bg-gray-100 rounded-lg">
            <input 
                type="text" 
                id="psch" 
                value="<?= htmlspecialchars($condition ?? '') ?>"
                placeholder="Search filename, title, alt..." 
                data-table="gallery"
                data-field="imagepath"
                class="flex-1 min-w-[200px] px-3 py-1.5 border rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                
            <label class="flex items-center gap-2 text-sm">
                <input id="select-all" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="font-medium text-gray-700">Select All</span>
            </label>

            <button 
                type="button"
                id="deleteSelected"
                class="btn-delete-selected inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                disabled>
                <i class="fa fa-times mr-1"></i> Delete Selected
            </button>

            <label for="imagepath" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                <input id="imagepath" type="file" name="imagepath[]" multiple class="sr-only">
                <i class="fa fa-upload mr-1"></i> Select Files
            </label>

            <button type="button" id="uploadsubmit" class="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Upload
            </button>


            <button type="button" onclick="loadContent('gallery',{page:-1})" class="px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fa fa-refresh mr-1"></i> Refresh
            </button>
        </div>

        <div id="restab" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <?= buildImageTable($images, true); ?>
        </div>
    </form>
</div>