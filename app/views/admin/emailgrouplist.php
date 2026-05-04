<fieldset class="mb-4 border rounded">
    <legend class="font-semibold ms-3">Email Groups</legend>
    <div class="flex items-center justify-end space-x-2 mb-2 p-3">
        <?php actionButtons([
            'module' => 'emailgroups',
            'id'     => 0,
            'targets' => [
                'new' => 'emailgroupform',
            ]
        ]); ?>
    </div>
    <table class="min-w-full text-sm text-left border-collapse text-zinc-900">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3">Group Name</th>
                <th class="px-4 py-3 text-center">Members</th>
                <th class="px-4 py-3 text-center">Edit</th>
                <th class="px-4 py-3 text-center">Delete</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php if ($groups->num_rows): ?>
            <?php while ($row = $groups->fetch_object()): ?>
            <tr class="hover:bg-gray-50 transition text-zinc-900">
                <td class="px-4 py-3">
                    <?= htmlspecialchars($row->group_name, ENT_QUOTES) ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php actionButtons([
                        'module'  => 'emailgroups',
                        'id'      => $row->id,
                        'targets' => [
                            'edit' => 'emailgroupmembers',
                        ]
                    ]); ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php actionButtons([
                        'module'  => 'emailgroups',
                        'id'      => $row->id,
                        'targets' => [
                            'edit' => 'emailgroupform',
                        ]
                    ]); ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php actionButtons([
                        'module'  => 'emailgroups',
                        'id'      => $row->id,
                        'targets' => [
                            'delete' => 'emailgrouplist',
                        ]
                    ]); ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                    No email groups found.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</fieldset>