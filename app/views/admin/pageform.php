<?php render('components/imageModal', ['images' => $images]); ?>

<form  action="/admin/api/saveform" method="post" enctype="multipart/form-data" data-ajax id="pageform">
	<input type="hidden" name="edit" value="<?= $id; ?>" />
	<input type="hidden" name="table" value="pages" />
	<input type="hidden" name="idfield" value="id" />
	<input type="hidden" name="item_word" value="Page" />
	<input type="hidden" name="has_active_field" value="1">	
	<input type="hidden" id="imagepath" name="imagepath" value="<?= $imagepath ??''?>"/>
		<fieldset class="grid grid-cols-1 md:grid-cols-12 gap-6 border rounded p-3">	
	    <legend class="font-semibold text-gray-700">
	        <?= htmlspecialchars($title, ENT_QUOTES) ?>
	    </legend>
		<div class="md:col-span-8 space-y-4 bg-white">

		    <div id="message" class="hidden w-full mb-5 p-2"></div>

		    <div class="flex flex-col gap-y-1">
		        <label for="title" class="w-full text-sm font-medium text-gray-700">
		            Title
		        </label>
		        <input name="title" id="title" value="<?= htmlspecialchars($title, ENT_QUOTES) ?>" class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
		        >
		    </div>

		    <div class="flex flex-col gap-y-1">
		        <label for="slug" class="w-full text-sm font-medium text-gray-700">
		            Slug: SEO-friendly version of the page title. Used in the page URL (example: /about-us).
		        </label>
		        <input name="slug" id="slug" value="<?= htmlspecialchars($slug, ENT_QUOTES) ?>" class="w-full border rounded px-3 py-1.5 text-sm focus:outline-none focus:ring focus:ring-blue-200"
		        >
		    </div>

		    <div class="flex flex-col gap-y-1">
		        <label for="content" class="w-full text-sm font-medium text-gray-700 pt-2">
		            Page Content
		        </label>
		        <textarea id="content" name="content" class="mce-full w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200" rows="12"><?= trim(htmlspecialchars(stripslashes($content), ENT_QUOTES)) ?>
		        </textarea>
			</div>
		</div>

		<div class="md:col-span-4 flex flex-col gap-y-3 min-w-0">
			<div class="flex flex-col space-y-2 min-w-0">
<?php
				actionButtons([
				    'module' => 'pages',
				    'id' => $id,
				    'targets' => [
				        'save'    => 'pageform',
				        'back'    => 'pagelist',
				        'new'     => 'pageform',
				        'refresh' => 'pageform',
				        'delete'  => 'pagelist',
				    ]
				]);
?>
			</div>
			<div class="flex gap-y-1 items-center gap-x-2 justify-start border p-2 rounded-sm">
			    <label for="active">Active</label>
					<input type="checkbox" id="active" name="active" value="1" <?= !empty($active) ? 'checked' : '' ?>>
			</div>
			<div class="flex gap-y-1 items-center justify-start border p-3 rounded-sm">
			<?php renderChooseImage([
			    'fieldId'      => 'imagepath',
			    'boxId'        => 'main-img-box',
			    'imgId'        => 'main-img',
			    'type'         => 'single',
			    'content'      => 'pageform',
			    'existingPath' => $rec->imagepath ?? '',
			    'label'        => 'Select Main Image',
			]); ?>
			</div>
			<div class="flex flex-col gap-y-1 items-start gap-x-2 justify-start border p-2 rounded-sm">
				<label for="metak">SEO: Meta Keywords</label>
				<textarea id="metak" name="metak" class="border p-2"><?= isset($rec->metak) ? stripslashes(htmlspecialchars($rec->metak)) : '' ?></textarea>
			</div>
			<div class="flex flex-col gap-y-1 items-start gap-x-2 justify-start border p-2 rounded-sm">
				<label for="metad">SEO: Meta Description</label>
				<textarea id="metad" name="metad" class="border p-2"><?= isset($rec->metad) ? stripslashes(htmlspecialchars($rec->metad)) : '' ?></textarea>
			</div>
		</div>
	</fieldset>
</form>
