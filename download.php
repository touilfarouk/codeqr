<?php
if (!empty($_GET['file']) && !empty($_GET['code'])) {
    $fileName = basename($_GET['file']);
    $code = $_GET['code']; // Retrieve the dynamic code from the URL
    $filePath = 'images/'.$fileName;
    if (file_exists($filePath)) {
        // Load the original QR code image
        $src = imagecreatefrompng($filePath);

        // Define new dimensions (width and height) for the QR code image
        $newWidth = 150;
        $newHeight = 150;

        // Create a new true color image with extra space for text
        $dst = imagecreatetruecolor($newWidth, $newHeight + 20); // +20 pixels for text

        // Fill the new image with white background
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        // Copy and resize the original image into the new image
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($src), imagesy($src));

        // Prepare to add text
        $black = imagecolorallocate($dst, 0, 0, 0); // Black color for the text
        $text = "" . $code; // Dynamic code
        $font = './arial.ttf'; // Specify the path to your TrueType font file

        // Check if the font file exists to avoid errors
        if (!file_exists($font)) {
            die("Font file not found: " . $font);
        }

        // Add text to the image
        $fontSize = 10; // Size of the font
        imagettftext($dst, $fontSize, 0, 10, $newHeight + 15, $black, $font, $text);
        
        // Output the final image
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Content-Type: image/png");
        imagepng($dst);

        // Clean up
        imagedestroy($dst);
        imagedestroy($src);

        exit;
    } else {
        echo 'The file does not exist.';
    }
} else {
    echo 'Missing parameters.';
}
?>
