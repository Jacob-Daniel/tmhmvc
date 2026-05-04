<fieldset class="mb-4 border rounded">
    <legend class="font-semibold ms-3">Emails</legend>
    <div id="filter-bar" class="flex items-center justify-end space-x-2 mb-2 p-3">
    <?php
        actionButtons([
            'module' => 'email',
            'targets' => [
                'new' => 'emailform',
            ]
        ]);
    ?>
    </div>
    <table class="min-w-full text-sm text-left border-collapse text-zinc-900">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3 text-center">Active</th>
                <th class="px-4 py-3 text-center">Edit</th>
                <th class="px-4 py-3 text-center">Delete</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php if ($emails->num_rows): ?>
            <?php while ($row = $emails->fetch_object()): ?>
            <tr class="hover:bg-gray-50 transition text-zinc-900">
                <td class="px-4 py-3"><?= htmlspecialchars($row->em_name, ENT_QUOTES) ?></td>
                <td class="px-4 py-3 text-center">
                    <span class="cursor-pointer font-medium text-blue-600 hover:text-blue-800"
                          onclick="flipField('emails','active',<?= $row->id ?>)"
                          id="active_<?= $row->id ?>">
                        <?= (int)$row->active === 1 ? 'Y' : 'N' ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php actionButtons([
                        'module'  => 'emails',
                        'id'      => $row->id,
                        'targets' => [
                            'edit' => 'emailform',
                        ]
                    ]); ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php actionButtons([
                        'module'  => 'emails',
                        'id'      => $row->id,
                        'targets' => [
                            'delete' => 'emaillist',
                        ]
                    ]); ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="px-4 py-6 text-center text-gray-500">No email templates found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</fieldset>