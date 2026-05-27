<fieldset class="mb-4 border rounded">
    <?php if (!empty($deleteMessage)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($deleteMessage) ?>
        </div>
    <?php endif; ?>

    <legend class="font-semibold ms-3">Banner Items</legend>

    <div id="filter-bar" class="flex items-center space-x-2 mb-2 p-3">
        <input
            type="text"
            id="psch"
            data-table="banners"
            data-field="slug"
            data-target="restable"
            placeholder="Search"
            class="flex-1 min-w-[200px] px-3 py-1.5 border rounded-md text-sm focus:ring-2"
        /> 
        <button 
          type="button"
          onclick="loadContent('bannerform')"
          id="newBannerBtn" 
          class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 ml-auto">
            New
        </button>
    </div>
    <div id="restable" class="overflow-x-auto">
        <?= buildTable($banners, $config); ?>
    </div>
</fieldset>