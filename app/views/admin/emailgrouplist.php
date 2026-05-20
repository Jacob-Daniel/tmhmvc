<fieldset class="mb-4 border rounded">
    <legend class="font-semibold ms-3">Email Groups</legend>
       <div id="filter-bar" class="flex items-center space-x-2 mb-2 p-3">
        <input
            type="text"
            id="psch"
            data-table="email_groups"
            data-field="group_name"
            data-target="restable"
            placeholder="Search"
            class="flex-1 min-w-[200px] px-3 py-1.5 border rounded-md text-sm focus:ring-2"
        /> 
        <?php actionButtons([
            'module' => 'emailgroups',
            'id'     => 0,
            'targets' => [
                'new' => 'emailgroupform',
            ]
        ]); ?>

    </div>

    <div id="restable" class="overflow-x-auto">
        <?= buildTable($emailgroups, $config); ?>
    </div>
</fieldset>