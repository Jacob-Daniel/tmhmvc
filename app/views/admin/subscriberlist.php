<fieldset class="mb-4 border rounded">
    <legend class="font-semibold ms-3">Subscribers</legend>
    <?php if (!empty($deleteMessage)): ?>
        <div class="<?= $deleteMessageType === 'success'
            ? 'bg-green-100 border border-green-400 text-green-700'
            : 'bg-red-100 border border-red-400 text-red-700' ?> px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($deleteMessage) ?>
        </div>
    <?php endif; ?>
    <div id="filter-bar" class="flex items-center space-x-2 mb-2 p-3">
        <div>
            <?php draw_pager('subscriberlist', $pageinfo['pages'], $pageinfo['page']); ?>
        </div>
        <select
            data-filter="group_id"
            class="px-3 py-1.5 border rounded-md text-sm"
        >
                <option value="">All groups</option>
            <?php foreach ($groups as $g): ?>
                <option value="<?= $g['id'] ?>" <?= ($_GET['group_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g['group_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select data-filter="unsub" class="px-3 py-1.5 border rounded-md text-sm">
            <option value="">All</option>
            <option value="0">Active</option>
            <option value="1">Unsubscribed</option>
        </select>    
        <input
            type="text"
            id="psch"
            data-table="subscribers"
            data-field="email"
            data-target="restable"
            placeholder="Search"
            class="flex-1 min-w-[200px] px-3 py-1.5 border rounded-md text-sm focus:ring-2"
        /> 

        <?php actionButtons([
            'class' => 'flex space-y-2',
            'module'  => 'subscribers',
            'id'      => 0,
            'targets' => [
                'new'  => 'subscriberform',
            ]
        ]); ?>
    </div>

    <div id="restable" class="overflow-x-auto">
        <?= buildTable($subscribers, $config); ?>
    </div>
</fieldset>