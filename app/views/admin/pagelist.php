<fieldset class="mb-4 border rounded">
    <?php if (!empty($deleteMessage)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($deleteMessage) ?>
        </div>
    <?php endif; ?>

    <legend class="font-semibold ms-3">Pages</legend>
    <?php draw_pager('pagelist', $pageinfo['pages'], $pageinfo['page'], 1, $item ?? 0); ?>

    <div id="filter-bar" class="flex items-center space-x-2 mb-2 p-3">
        <input
            type="text"
            id="psch"
            data-table="pages"
            data-field="title"
            data-target="restable"
            placeholder="Search"
            class="flex-1 min-w-[200px] px-3 py-1.5 border rounded-md text-sm focus:ring-2"
        /> 

    <?php
        actionButtons([
            'module' => 'pages',
            'targets' => [
                'new' => 'pageform',
            ]
        ]);
    ?>
    </div>
    <div id="restable" class="overflow-x-auto">
        <?= buildTable($pages, $config); ?>
    </div>
</fieldset>