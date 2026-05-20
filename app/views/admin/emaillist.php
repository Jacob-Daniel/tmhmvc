<fieldset class="mb-4 border rounded">
    <legend class="font-semibold ms-3">Emails</legend>
    <div id="filter-bar" class="flex items-center justify-end space-x-2 mb-2 p-3">
        <input
            type="text"
            id="psch"
            data-table="emails"
            data-field="em_name"
            data-target="restable"
            placeholder="Search"
            class="flex-1 min-w-[200px] px-3 py-1.5 border rounded-md text-sm focus:ring-2"
        />     
    <?php
        actionButtons([
            'module' => 'emails',
            'targets' => [
                'new' => 'emailform',
            ]
        ]);
    ?>
    </div>
      <div id="restable" class="overflow-x-auto">
        <?= buildTable($emails, $config); ?>
    </div>
</fieldset>