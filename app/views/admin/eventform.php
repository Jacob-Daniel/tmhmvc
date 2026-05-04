<form action="/admin/api/saveeventform"
      method="post"
      enctype="multipart/form-data"
      data-ajax
      id="eventform">

    <input type="hidden" name="edit"       value="<?= $id ?>">
    <input type="hidden" name="table"      value="events">
    <input type="hidden" name="idfield"    value="id">
    <input type="hidden" name="item_word"  value="Event Item">
    <input type="hidden" id="imagepath" name="imagepath" value="<?= $imagepath ?>">
    <input type="hidden" name="has_active_field" value="1">

    <fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">
        <legend class="font-semibold text-gray-700">
            <?= htmlspecialchars($title ?: 'New Event', ENT_QUOTES) ?>
        </legend>

        <!-- ===== LEFT COL ===== -->
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

        <!-- ===== RIGHT COL ===== -->
        <div class="md:col-span-4 flex flex-col gap-4">

            <!-- Action buttons -->
            <div class="flex flex-col space-y-2 min-w-0">
                <?php actionButtons([
                    'module' => 'events',
                    'id'     => $id,
                    'targets' => [
                        'save'    => 'eventform',
                        'back'    => 'eventlist',
                        'new'     => 'eventform',
                        'refresh' => 'eventform',
                        'delete'  => 'eventlist',
                    ]
                ]); ?>
            </div>

            <!-- Active / Featured -->
            <div class="flex items-center gap-4 border p-3 rounded">
                <label class="text-sm">Active</label>
                <input type="checkbox" name="active" value="1" <?= $active ?>>
                <label class="text-sm">Featured</label>
                <input type="checkbox" name="featured" value="1" <?= $featured ?>>
            </div>

            <!-- Primary Category -->
            <div class="border p-3 rounded">
                <label class="block text-sm font-medium mb-2">Primary Category</label>
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

            <!-- Secondary Categories -->
            <div class="border p-3 rounded">
                <label class="block text-sm font-medium mb-2">Secondary Categories</label>
                <select name="event_cats[]"
                        multiple
                        class="w-full border rounded p-2 text-sm chosen-select"
                        size="4">
                    <?php while ($cat = $categories_secondary->fetch_object()): ?>
                        <option value="<?= $cat->id ?>"
                            <?= in_array((int)$cat->id, $subcats) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat->slug) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Price -->
            <div class="flex flex-col border p-3 rounded">
                <label class="text-sm font-medium mb-1">Price</label>
                <input type="number"
                       name="price"
                       step="any"
                       value="<?= htmlspecialchars((string)$price) ?>"
                       class="border rounded px-2 py-1 text-sm">
            </div>

            <!-- Image -->
            <div class="border p-3 rounded">
                <?php renderChooseImage([
                    'fieldId'      => 'imagepath',
                    'boxId'        => 'main-img-box',
                    'imgId'        => 'main-img',
                    'type'         => 'single',
                    'content'      => 'eventform',
                    'existingPath' => $imagepath,
                    'label'        => 'Select Main Image',
                ]); ?>
            </div>

            <!-- Calendar -->
            <div class="border p-3 rounded space-y-3 relative">
                <h4 class="text-sm font-semibold uppercase border-b pb-1">Calendar</h4>

                <div class="flex flex-col gap-1">
                    <label class="text-sm">Start Date</label>
                    <input name="start_date"
                           class="datepickr border rounded px-2 py-1 text-sm"
                           value="<?= htmlspecialchars($start_date) ?>">
                </div>
                <div class="flex flex-col gap-1 relative">
                    <label class="text-sm">End Date</label>
                    <input name="end_date"
                           class="datepickr border rounded px-2 py-1 text-sm"
                           value="<?= htmlspecialchars($end_date) ?>">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm">Start Time</label>
                    <input name="start_time"
                           class="timepickr border rounded px-2 py-1 text-sm"
                           value="<?= htmlspecialchars($start_time) ?>">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm">End Time</label>
                    <input name="end_time"
                           class="timepickr border rounded px-2 py-1 text-sm"
                           value="<?= htmlspecialchars($end_time) ?>">
                </div>
            </div>

            <!-- Recurring — new events only -->
            <?php if (!$id): ?>
            <div class="border p-3 rounded space-y-3" id="recurring-wrap">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium">Recurring</label>
                    <input type="checkbox"
                           name="is_recurring"
                           id="is_recurring"
                           value="1">
                </div>

                <div id="recurring-fields" class="hidden space-y-3">

                    <div>
                        <label class="text-sm font-medium block mb-1">Calendar Days</label>
                        <div class="flex flex-wrap gap-3">
                            <?php
                            $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                            foreach ($days as $k => $v):
                                $checked = in_array($k, $days_array) ? 'checked' : '';
                            ?>
                                <label class="flex items-center gap-1 text-sm">
                                    <input type="checkbox"
                                           name="calendar_days[]"
                                           value="<?= $k ?>" <?= $checked ?>>
                                    <?= $v ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium block mb-1">Frequency</label>
                        <select name="frequency" class="w-full border rounded p-2 text-sm">
                            <option value="">Select...</option>
                            <?php foreach (['Daily','Weekly','Bi-Weekly','Monthly'] as $f): ?>
                                <option value="<?= $f ?>"
                                    <?= ($frequency === $f) ? 'selected' : '' ?>>
                                    <?= $f ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sequence -->
            <div class="flex flex-col border p-3 rounded">
                <label class="text-sm font-medium mb-1">Sequence</label>
                <input name="sequence"
                       value="<?= htmlspecialchars((string)$sequence) ?>"
                       class="border rounded px-2 py-1 text-sm">
            </div>

            <!-- Iframe -->
            <div class="flex flex-col gap-2 border p-3 rounded">
                <label class="text-sm font-medium">Iframe Embed URL</label>
                <input name="iframe"
                       value="<?= htmlspecialchars($iframe, ENT_QUOTES) ?>"
                       class="border rounded px-2 py-1 text-sm">
            </div>

            <!-- SEO -->
            <div class="flex flex-col border p-3 rounded">
                <label class="text-sm font-medium mb-1">SEO: Meta Keywords</label>
                <textarea name="metak"
                          class="border rounded p-2 text-sm"><?= htmlspecialchars(stripslashes($metak)) ?></textarea>
            </div>
            <div class="flex flex-col border p-3 rounded">
                <label class="text-sm font-medium mb-1">SEO: Meta Description</label>
                <textarea name="metad"
                          class="border rounded p-2 text-sm"><?= htmlspecialchars(stripslashes($metad)) ?></textarea>
            </div>

        </div>
    </fieldset>
</form>