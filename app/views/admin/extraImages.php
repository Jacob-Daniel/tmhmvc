			<div class="extra-images-wrap">
				<input id="callImageGallery" class="select-img-but" type="button" onclick="loadImages('<?= $page; ?>','pageform');document.getElementById('img-gal').setAttribute('data-type','multi');document.getElementById('restab').setAttribute('class','row extra');document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'" value="Select Slide Images">
				<div class="overflow-scroll">
					<div id="save-ord-msg"></div>
					<table id="eximg" class="extra-images-order">
<?php
						if ($item_images?->num_rows && $item_images->num_rows > 0) {

						$iii = 0;
						while ($item_image = $item_images->fetch_object()) {
						    $imagepath   = $item_image->imagepath   ?? null;
						    if (!$imagepath) {
						        continue;
						    }

						    $alt         = $item_image->alt         ?? '';
						    $description = $item_image->description ?? '';
						    $title       = $item_image->title       ?? '';

						    $thumbPath = PUBLIC_UPLOADS_PATH . '/thumbs/150/' . $imagepath;

						    $src = file_exists($thumbPath)
						        ? BASE_URL_IMG_DIR . '/thumbs/150/' . $imagepath
						        : BASE_URL_IMG_DIR . '/' .$imagepath;

						    $safeImage  = htmlspecialchars($imagepath, ENT_QUOTES);
						    $safeAlt    = htmlspecialchars($alt, ENT_QUOTES);
						    $safeDesc   = htmlspecialchars($description, ENT_QUOTES);
						    $safeTitle  = htmlspecialchars($title, ENT_QUOTES);
						    $safeSrc    = htmlspecialchars($src, ENT_QUOTES);
?>
							<tr  class="thumbs" data-img-id="<?= $safeImage ?>" draggable="true" ondragover="dragover(event)" ondragstart="dragstart(event)" ondragend="dragend(event)" >
							    <td class="grabbable">
							        <i class="fa fa-arrows-alt"></i>
							        <input class="select-images" data-content="prodform" data-pid="<?= (int) $item ?>" data-ifv="<?= $safeImage ?>" id="<?= $safeImage ?>" type="checkbox" value="<?= $safeImage ?>" >

							        <input name="cur_imagepaths[<?= $iii ?>][]" type="hidden" value="<?= $safeImage ?>">
							        <input name="cur_imagepaths[<?= $iii ?>][]" type="hidden" value="<?= $safeDesc ?>">
							        <input name="cur_imagepaths[<?= $iii ?>][]" type="hidden" value="<?= $safeAlt ?>">
							        <input name="cur_imagepaths[<?= $iii ?>][]" type="hidden" value="<?= $safeTitle ?>">
							    </td>
							    <td>
							        <img class="nodrag" draggable="false" ondragstart="return false;" id="<?= $safeImage ?>" src="<?= $safeSrc ?>" alt="<?= $safeAlt ?>">
							    </td>
							</tr>
<?php
						    $iii++;
						}
					}
?>
					</table>
				</div>
				<span class="grab-text">Grab and drag box handle to change image order</span>
				<div>
					Remove selection <a id="extra-images" class="pgal delete fa fa-times"></a>
				</div>
	        </div>