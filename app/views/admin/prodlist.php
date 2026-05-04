<fieldset class="mb-4 border rounded py-4">
    <legend class="font-semibold ms-3 ms-4">Products</legend>

    <?php if (!empty($deleteMessage)): ?>
        <div class="<?= $deleteMessageType === 'success'
            ? 'bg-green-100 border border-green-400 text-green-700'
            : 'bg-red-100 border border-red-400 text-red-700' ?> px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($deleteMessage) ?>
        </div>
    <?php endif; ?>

    <div id="filter-bar" class="flex gap-2 mb-4 px-4 justify-between">
        <div>
            <?php draw_pager('prodlist', $pageinfo['pages'], $pageinfo['page']); ?>
        </div>
            
        <input 
            type="text" 
            id="psch" 
            placeholder="Search" 
            data-table="products"
            data-field="slug"
            class="flex-1 w-[8rem] px-3 py-1.5 border rounded-md text-sm"
           >
  
        <button
            type="button"
            onclick="loadContent('prodform', { id: 0 })"
            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 ml-auto"
        >
            New Item
        </button>
    </div>

    <div id="message" class="mb-2">
        <pre id="r" class="text-sm text-gray-600"></pre>
    </div>

    <div id="restab" class="overflow-x-auto">
        <?= buildProductTable($products); ?>
    </div>
</fieldset>

<div class="mt-4">
    <fieldset class="border-t pt-2">
        <?= draw_pager('prodlist', $pageinfo['pages'], $pageinfo['page']); ?>
        <p class="page-count text-gray-700">
            Page <?= $pageinfo['page'] ?> of <?= $pageinfo['pages'] ?>
        </p>
    </fieldset>
</div>