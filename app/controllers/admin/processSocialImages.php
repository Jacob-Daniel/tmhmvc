<?php
require_once __DIR__ . '/../../bootstrap/app.php';

// Define paths
$upload_path = 'images/';
$facebook_path = $upload_path . 'facebook/';
$twitter_path = $upload_path . 'twitter/';

// Ensure social media directories exist
if (!file_exists($facebook_path)) mkdir($facebook_path, 0755, true);
if (!file_exists($twitter_path)) mkdir($twitter_path, 0755, true);

// Define social media image sizes
$social_media_sizes = array(
    'facebook' => array('width' => 1200, 'height' => 630),
    'twitter' => array('width' => 1200, 'height' => 675)
);

// Get the table name from command line arguments or default to 'events'
$table = isset($argv[1]) ? $argv[1] : 'events';

// Query to get all images used in events
function getCurrentImages($table){
	global $db;
	$sql = "SELECT imagepath FROM images WHERE id IN (SELECT i.id FROM images i, $table t WHERE i.imagepath = t.imagepath AND t.active = 1)";
	if(!$res = $db->query($sql)) die($db->error . '--' . $sql);
	return $res;
}

$images = getCurrentImages($table);
while($image = $images->fetch_object()) {
    if(isset($image)) {
        print_r($image->imagepath);
    } else {
        exit('error');
    }

    $image_path = $upload_path . $image->imagepath;

    if (file_exists($image_path)) {
        // Get original image dimensions
        list($original_width, $original_height) = getimagesize($image_path);
        
        foreach ($social_media_sizes as $platform => $size) {
            $output_path = ($platform == 'facebook' ? $facebook_path : $twitter_path) . basename($image->imagepath);
            echo "Creating social media image for $platform: $output_path\n";
            
            // Determine if the image is portrait or landscape
            $aspect_ratio = $original_width / $original_height;

            if ($aspect_ratio > 1) {
                // Landscape
                $resize_width = $size['width'];
                $resize_height = round($size['width'] / $aspect_ratio);
            } else {
                // Portrait or square
                $resize_height = $size['height'];
                $resize_width = round($size['height'] * $aspect_ratio);
            }

            // Create the image with the calculated dimensions and extend the canvas to the target size
            $cmd = "convert $image_path -resize {$resize_width}x{$resize_height} -background white -gravity center -extent {$size['width']}x{$size['height']} $output_path";
            exec($cmd);
            
            // Check for success
            if (!file_exists($output_path)) {
                error_log("Error creating $platform image: $output_path does not exist.");
            } else {
                echo "Social media image created for $platform: $output_path\n";
            }
        }
    } else {
        echo "Image does not exist: $image_path\n";
    }
}
?>

