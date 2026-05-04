
<form action="/admin/api/saveform" method="post" enctype="multipart/form-data" data-ajax id="navform">
    <input type="hidden" name="edit" value="<?= $id; ?>" />
    <input type="hidden" name="table" value="navigation" />
    <input type="hidden" name="idfield" value="id" />
    <input type="hidden" name="item_word" value="Navigation Item" />
    <input type="hidden" name="has_active_field" value="1">

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border p-4 rounded">
        <legend class="font-semibold text-gray-700">
            <?= htmlspecialchars($title ?? 'Navigation Item', ENT_QUOTES) ?>
        </legend>

        <div class="md:col-span-8 space-y-4 bg-white">

            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-y-1">
                <label for="label" class="w-full text-sm font-medium text-gray-700">Label</label>
                <input name="label" id="label" value="<?= htmlspecialchars($label ?? '', ENT_QUOTES) ?>"
                       class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="slug" class="w-full text-sm font-medium text-gray-700">
                    Slug: SEO-friendly URL (example: /about-us)
                </label>
                <input name="slug" id="slug" value="<?= htmlspecialchars($slug ?? '', ENT_QUOTES) ?>"
                       class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="page_id" class="w-full text-sm font-medium text-gray-700">Linked Page</label>
                <select name="page_id" id="page_id"
                        class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
                    <option value="">-- Select Page --</option>
                    <?php foreach ($pages as $page): ?>
                        <option value="<?= $page['id'] ?>" <?= ($page_id ?? '') == $page['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($page['title'], ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="parent_id" class="w-full text-sm font-medium text-gray-700">Parent Menu Item</label>
                <select name="parent_id" id="parent_id"
                        class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
                    <option value="">-- No Parent --</option>
                    <?php foreach ($navItems as $nav): ?>
                        <option value="<?= $nav['id'] ?>" <?= ($parent_id ?? '') == $nav['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nav['label'], ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="menu_group" class="w-full text-sm font-medium text-gray-700">Menu Group</label>
                <input name="menu_group" id="menu_group" value="<?= htmlspecialchars($menu_group ?? 'main', ENT_QUOTES) ?>"
                       class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="sequence" class="w-full text-sm font-medium text-gray-700">Sequence / Order</label>
                <input type="number" name="sequence" id="sequence" value="<?= htmlspecialchars($sequence ?? 1, ENT_QUOTES) ?>"
                       class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-y-1">
                <label for="target" class="w-full text-sm font-medium text-gray-700">Target</label>
                <select name="target" id="target"
                        class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200">
                    <option value="_self" <?= ($target ?? '') == '_self' ? 'selected' : '' ?>>Same Window (_self)</option>
                    <option value="_blank" <?= ($target ?? '') == '_blank' ? 'selected' : '' ?>>New Window (_blank)</option>
                </select>
            </div>

        </div>

        <div class="md:col-span-4 flex flex-col gap-y-3 min-w-0">
            <div class="flex flex-col space-y-2 min-w-0">
<?php
            actionButtons([
                'module' => 'navigation',
                'id' => $id,
                'targets' => [
                    'save'    => 'navform',
                    'back'    => 'navlist',
                    'new'     => 'navform',
                    'refresh' => 'navform',
                    'delete'  => 'navlist',
                ]
            ]);
?>
            </div>

            <div class="flex gap-y-1 items-center gap-x-2 justify-start border p-2 rounded-sm">
                <label for="active">Active</label>
                <input type="checkbox" id="active" name="active" value="1" <?= !empty($active) ? 'checked' : '' ?>>
            </div>

        </div>
    </fieldset>
</form>