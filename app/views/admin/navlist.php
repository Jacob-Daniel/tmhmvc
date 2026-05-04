<fieldset class="mb-4 border rounded">
    <?php if (!empty($deleteMessage)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($deleteMessage) ?>
        </div>
    <?php endif; ?>

    <legend class="font-semibold ms-3">Navigation Items</legend>

    <div id="filter-bar" class="flex items-center space-x-2 mb-2 p-3">
        <button 
          type="button"
          onclick="loadContent('navform')"
          id="newNavBtn" 
          class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 ml-auto">
            New Navigation Item
        </button>
    </div>

    <table id="table-1" class="min-w-full text-sm text-left border-collapse text-zinc-900">
      <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
        <tr>
          <th class="px-4 py-3">ID</th>
          <th class="px-4 py-3">Label</th>
          <th class="px-4 py-3 text-center">Sequence</th>
          <th class="px-4 py-3 text-center">Active</th>
          <th class="px-4 py-3 text-center">View/Edit</th>
          <th class="px-4 py-3 text-center">Delete</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-200">
<?php
function renderNavRows($navItems, $depth = 0)
{
    foreach ($navItems as $nav) {
        $indent = 20 * $depth;
        $bg = $depth === 0 ? '' : 'bg-gray-' . (50 + ($depth * 50));
?>
        <tr class="<?= $bg ?> hover:bg-gray-50 transition text-zinc-900">
            <td class="px-4 py-3"><?= $nav->id; ?></td>

            <td class="px-4 py-3" style="padding-left: <?= 16 + $indent ?>px">
                <?= str_repeat('– ', $depth); ?>
                <?php createEditField(
                    'navigation',
                    'label',
                    'pn',
                    $nav->id,
                    stripslashes($nav->label),
                    '200px'
                ); ?>
            </td>

            <td class="px-4 py-3 text-center">
                <?php createEditField(
                    'navigation',
                    'sequence',
                    'pr',
                    $nav->id,
                    $nav->sequence,
                    '40px'
                ); ?>
            </td>

            <td class="px-4 py-3 text-center">
                <span class="cursor-pointer font-medium text-blue-600 hover:text-blue-800"
                      onclick="flipField('navigation','active',<?= $nav->id ?>)"
                      id="active_<?= $nav->id ?>">
                    <?= $nav->active ? 'Y' : 'N'; ?>
                </span>
            </td>

            <td class="px-4 py-3 text-center">
                <?php
                actionButtons([
                    'module' => 'navigation',
                    'id' => $nav->id,
                    'targets' => [
                        'edit' => 'navform',
                    ]
                ]);
                ?>
            </td>

            <td class="px-4 py-3 text-center">
                <?php
                actionButtons([
                    'module' => 'navigation',
                    'id' => $nav->id,
                    'targets' => [
                        'delete' => 'navlist',
                    ]
                ]);
                ?>
            </td>
        </tr>

<?php
        if (!empty($nav->children)) {
            renderNavRows($nav->children, $depth + 1);
        }
    }
}
?>

<?php
if (!empty($navigation)) {
    renderNavRows($navigation);
} else {
    echo '<tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">None found</td></tr>';
}
?>
      </tbody>
    </table>
</fieldset>