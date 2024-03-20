<?php
$f = "visit.php";
if (!file_exists($f)) {
    touch($f);
    $handle = fopen($f, "w");
    fwrite($handle, 0);
    fclose($handle);
}
include('libs/phpqrcode/qrlib.php');
include('libs/phpqrcode/SimpleImage.php');

// Function to extract username from product
function getUsernameFromproduct($product)
{
    $find = '@';
    $pos = strpos($product, $find);
    $username = substr($product, 0, $pos);
    return $username;
}

// Function to generate a random password
function generateRandomPassword($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

// Initialize variables
$product = '';
$subject = '';
$body = '';
$errorMessage = '';

if (isset($_POST['submit'])) {
    // set error correction level L
    $err_correction = 'L';
    $pixel_size = 10;
    $frame_size = 8; // Change the frame size to 8px
    $tempDir = 'images/';

    // Check folder permissions
    $folderPermission = substr(sprintf('%o', fileperms($tempDir)), -4);
    if ($folderPermission != '777' && $folderPermission != '0777') {
        $errorMessage = 'You do not have permissions to generate a file to a directory - ' . $tempDir . '. Please change the folder permission to 777.';
    } else {
        // Retrieve form data
        $product = $_POST['mail'];
        $subject = $_POST['subject'];
        $filename = getUsernameFromproduct($product);
        $body = $_POST['msg'];

        // Generate a random password
        $randomPassword = generateRandomPassword();

        // Generate QR code content including product, subject, body, and password
        $codeContents = 'Product: ' . $product . PHP_EOL . 'Subject: ' . $subject . PHP_EOL . 'Body: ' . $body . PHP_EOL . 'Password: ' . $randomPassword;

        // Generate QR code with PHP QR Code library
        QRcode::png($codeContents, $tempDir . '' . $filename . '.png', $err_correction, $pixel_size, $frame_size);

        // Load the QR code image
        $qrImage = new \claviska\SimpleImage();
        $qrImage->fromFile($tempDir . '' . $filename . '.png');

        // Resize the QR code image to make it larger
        $qrImage->resize($qrImage->getWidth() * 2, $qrImage->getHeight() * 2); // Doubling the size

        // Load the logo image and resize it
        $logoImage = new \claviska\SimpleImage();
        $logoImage
            ->fromFile('logo.jpg')
            ->bestFit(50, 50); // Resize the logo to fit within 50x50 pixels

        // Calculate position for centering the logo
        $x = ($qrImage->getWidth() - $logoImage->getWidth()) / 2;
        $y = ($qrImage->getHeight() - $logoImage->getHeight()) / 2;

        // Overlay the logo on the QR code image
        $qrImage->overlay($logoImage, $x, $y);

        // Save the modified QR code image
        $qrImage->toFile($tempDir . '' . $filename . '.png');
    }
}

if (isset($filename)) {
    echo '<div class="qr-image">';
    echo '<img src="images/' . $filename . '.png" style="width:400px; height:400px;">'; // Adjusted image size
    echo '<p>Password: ' . $randomPassword . '</p>'; // Display the random password
    echo '<a class="btn btn-primary submitBtn" style="width:210px; margin:5px 0;" href="download.php?file=' . $filename . '.png "><br>Download QR Code</a>';
    echo '</div>';
}
?>
