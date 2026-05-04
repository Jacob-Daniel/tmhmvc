import tinymce from "tinymce/tinymce";
import "tinymce/icons/default/icons.min.js";
import "tinymce/themes/silver/theme.min.js";
import "tinymce/models/dom/model.min.js";
import "tinymce/skins/ui/oxide/skin.js";
import "tinymce/skins/ui/oxide/content.js";
import "tinymce/skins/content/default/content.js";
import "tinymce/plugins/advlist";
import "tinymce/plugins/autolink";
import "tinymce/plugins/lists";
import "tinymce/plugins/link";
import "tinymce/plugins/image";
import "tinymce/plugins/charmap";
import "tinymce/plugins/preview";
import "tinymce/plugins/anchor";
import "tinymce/plugins/searchreplace";
import "tinymce/plugins/wordcount";
import "tinymce/plugins/code";
import "tinymce/plugins/fullscreen";
import "tinymce/plugins/insertdatetime";
import "tinymce/plugins/media";
import "tinymce/plugins/table";
import "tinymce/plugins/emoticons";
import "tinymce/plugins/emoticons/js/emojis";

export function initTinyMCE() {
	if (!window.tinymce) {
		setTimeout(initTinyMCE, 200);
		console.log("tinymce not ready, retrying...");
		return;
	}

	// Remove all existing editors first (SPA safe)
	tinymce.remove();

	/* ==========================
       FULL EDITOR
    ========================== */
	if (document.querySelector("textarea.mce-full")) {
		tinymce.init({
			selector: "textarea.mce-full",
			height: 480,
			license_key: "gpl",
			automatic_uploads: true,
			image_title: true,
			paste_data_images: true,

			plugins:
				"advlist autolink lists link image charmap preview anchor searchreplace wordcount code fullscreen insertdatetime media table emoticons",

			toolbar:
				"undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code fullscreen",

			images_upload_url: "/admin/api/tinymceUpload",

			images_upload_handler: async (blobInfo) => {
				const formData = new FormData();
				formData.append("file", blobInfo.blob(), blobInfo.filename());

				const response = await fetch("/admin/api/tinymceUpload", {
					method: "POST",
					body: formData,
				});

				const json = await response.json();
				if (!response.ok) throw new Error(json.error || "Upload failed");
				return json.location;
			},
		});
	}

	/* ==========================
       BASIC EDITOR (SUMMARY)
    ========================== */
	if (document.querySelector("textarea.mce-basic")) {
		tinymce.init({
			selector: "textarea.mce-basic",
			height: 180,
			menubar: false,
			statusbar: false,
			branding: false,

			plugins: "lists link",

			toolbar: "bold italic | bullist numlist | link | undo redo",

			paste_as_text: true,
		});
	}
}
