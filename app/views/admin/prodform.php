<form action="/admin/api/saveform"
      method="post"
      enctype="multipart/form-data"
      data-ajax
      id="prodform">

    <input type="hidden" name="edit" value="<?= $id ?>">
    <input type="hidden" name="table" value="products">
    <input type="hidden" name="idfield" value="id">
    <input type="hidden" name="item_word" value="Work Item">
    <input type="hidden" id="imagepath" name="imagepath" value="<?= $imagepath ?>">
    <input type="hidden" name="has_active_field" value="1">

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">    
        <legend class="font-semibold text-gray-700"><?= htmlspecialchars($title ?: 'New Item', ENT_QUOTES) ?></legend>
        <div class="md:col-span-8 space-y-6 bg-white">
            
            <div id="message" class="hidden w-full mb-5 p-2"></div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium">Title</label>
                <input name="title"
                       value="<?= htmlspecialchars($title, ENT_QUOTES) ?>"
                       class="border rounded px-3 py-2 text-sm focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium">URL Slug</label>
                <input name="slug"
                       value="<?= htmlspecialchars($slug, ENT_QUOTES) ?>"
                       class="border rounded px-3 py-2 text-sm focus:ring focus:ring-blue-200">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium">Summary</label>
                <textarea name="summary"
                          class="mce-basic border rounded px-3 py-2 text-sm"
                          rows="2"><?= htmlspecialchars(stripslashes($summary), ENT_QUOTES) ?></textarea>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium">Content</label>
                <textarea name="content"
                          class="mce-full border rounded px-3 py-2 text-sm"
                          rows="12"><?= htmlspecialchars(stripslashes($content), ENT_QUOTES) ?></textarea>
            </div>
        </div>

        <div class="md:col-span-4 flex flex-col gap-4">

            <div class="flex flex-col space-y-2 min-w-0">
<?php
                actionButtons([
                    'module' => 'products',
                    'id' => $id,
                    'targets' => [
                        'save'    => 'prodform',
                        'back'    => 'prodlist',
                        'new'     => 'prodform',
                        'refresh' => 'prodform',
                        'delete'  => 'prodlist',
                    ]
                ]);
?>
            </div>

            <div class="border p-3 rounded">
                <label class="block text-sm font-medium mb-2">Category</label>
                <select name="cat_id" class="w-full border rounded p-2 text-sm">
                    <option value="">Select...</option>
                    <?php while ($cat = $categories->fetch_object()): ?>
                        <option value="<?= $cat->id ?>"
                            <?= ($rec?->cat_id == $cat->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat->slug) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="border p-3 rounded">
                <?php renderChooseImage([
                    'fieldId'      => 'imagepath',
                    'boxId'        => 'main-img-box',
                    'imgId'        => 'main-img',
                    'type'         => 'single',
                    'content'      => 'prodform',
                    'existingPath' => $imagepath,
                    'label'        => 'Select Main Image',
                ]); ?>
            </div>
            <div class="flex items-center gap-2 border p-3 rounded">
                <label>Active</label>
                <input type="checkbox" id="active" name="active" value="1" <?= !empty($active) ? 'checked' : '' ?>>
            </div>

            <div class="flex flex-col border p-3 rounded">
                <label>Sequenece</label>
                <input name="sequence"
                       value="<?= htmlspecialchars((string)$sequence) ?>"
                       class="border rounded px-2 py-1 text-sm">
            </div>

            <div class="flex flex-col gap-2 border p-3 rounded">
                <label>Iframe Embed URL</label>
                <input name="iframe"
                       value="<?= htmlspecialchars($iframe, ENT_QUOTES) ?>"
                       class="border rounded px-2 py-1 text-sm">
            </div>


            <div class="flex flex-col border p-3 rounded">
                <label>SEO: Meta Keywords</label>
                <textarea name="metak"
                          class="border rounded p-2 text-sm"><?= htmlspecialchars(stripslashes($metak)) ?></textarea>
            </div>

            <div class="flex flex-col border p-3 rounded">
                <label>SEO: Meta Description</label>
                <textarea name="metad"
                          class="border rounded p-2 text-sm"><?= htmlspecialchars(stripslashes($metad)) ?></textarea>
            </div>
        </div>
    </fieldset>
</form>
