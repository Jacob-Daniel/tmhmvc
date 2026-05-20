<fieldset class="mb-4 border rounded">
    <?php if (!empty($deleteMessage)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($deleteMessage) ?>
        </div>
    <?php endif; ?>

    <legend class="font-semibold ms-3">Categories</legend>

    <div id="filter-bar" class="flex justify-between space-x-2 mb-2 p-3">
        <input 
            type="text" 
            id="psch" 
            placeholder="Search" 
            data-table="categories"
            data-field="slug"
            data-target="restable"
            class="flex-1 w-[8rem] px-3 py-1.5 border rounded-md text-sm"
           >
    <?php
        actionButtons([
            'module' => 'categories',
            'targets' => [
                'new' => 'catform',
            ]
        ]);
    ?>
    </div>
    <div id="restable" class="overflow-x-auto">
        <?= buildTable($categories, $config); ?>
    </div>
</fieldset>
