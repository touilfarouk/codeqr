<?php
include('sqlConnect.php');
// Fetch data from the database
$query = $bdd->prepare("SELECT * FROM documents ");
$query->execute();
$rows = $query->fetchAll(); // Fetch all rows
$f = "visit.php";
if (!file_exists($f)) {
    touch($f);
    $handle = fopen($f, "w");
    fwrite($handle, 0);
    fclose($handle);
}
include('libs/phpqrcode/qrlib.php');

// Fonction pour générer un mot de passe aléatoire
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
$ref_document = '';
$distinataire = '';
$objet = '';
$errorobjet = '';

if (isset($_POST['submit'])) {
    // set error correction level L
    $err_correction = 'L';
    $pixel_size = 8;
    $frame_size = 1; // Change the frame size to 8px
    $tempDir = 'images/';

    // Check folder permissions
    $folderPermission = substr(sprintf('%o', fileperms($tempDir)), -4);
    if ($folderPermission != '0777') {
        $errorobjet = 'You do not have permissions to generate a file to a directory - ' . $tempDir . '. Please change the folder permission to 777.';
    } else {
        // Retrieve form data
        $ref_doc_img  = uniqid();
        $ref_document = $_POST['ref_document'];
        $date = $_POST['date'];
        $distinataire = $_POST['distinataire'];
        $ref_document = $_POST['ref_document'];
        $objet = $_POST['objet'];

        // Générer le contenu du code QR
        $randomPassword = generateRandomPassword(); // Générer un mot de passe aléatoire
        $codeContents = 'Ref document: ' . $ref_document . PHP_EOL . 'Date document: ' . $date . PHP_EOL . 'Distinataire: ' . $distinataire . PHP_EOL . 'Objet: ' . $objet . PHP_EOL . 'Password: ' . $randomPassword;

        // Générer le code QR avec la bibliothèque PHP QR Code
        QRcode::png($codeContents, $tempDir . '' . $ref_doc_img . '.png', $err_correction, $pixel_size, $frame_size);

        // Load the QR code image
        $qrImagePath = $tempDir . $ref_doc_img . '.png';
        list($qrImageWidth, $qrImageHeight) = getimagesize($qrImagePath);

        // Resize the QR code image to make it larger
        $newWidth = $qrImageWidth * 2;
        $newHeight = $qrImageHeight * 2;

        // Create a new image with the new dimensions
        $newQrImage = imagecreatetruecolor($newWidth, $newHeight);
        $qrImage = imagecreatefrompng($qrImagePath);

        // Resize the image
        imagecopyresampled($newQrImage, $qrImage, 0, 0, 0, 0, $newWidth, $newHeight, $qrImageWidth, $qrImageHeight);

        // Load the logo image and resize it
        $logoImagePath = './images/logo.jpg';
        $logoImage = imagecreatefromjpeg($logoImagePath);
        list($logoWidth, $logoHeight) = getimagesize($logoImagePath);
        $newLogoWidth = 100; // Fixed width
        $newLogoHeight = 100; // Fixed height

        // Calculate position for centering the logo
        $x = ($newWidth - $newLogoWidth) / 2;
        $y = ($newHeight - $newLogoHeight) / 2;

        // Overlay the logo on the QR code image
        imagecopyresampled($newQrImage, $logoImage, $x, $y, 0, 0, $newLogoWidth, $newLogoHeight, $logoWidth, $logoHeight);

        // Save the modified QR code image
        imagepng($newQrImage, $tempDir . '' . $ref_doc_img . '_resized_logo.png');

        // Free up memory
        imagedestroy($qrImage);
        imagedestroy($newQrImage);
        imagedestroy($logoImage);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>QR Code Generator</title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
       <!-- Bootstrap JS CDN -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.5/dist/sweetalert2.all.min.js"></script>

</head>

<body>

    









<!-- Button to trigger modal -->


<!-- Modal -->
<div class="modal fade" id="recordsModal" tabindex="-1" role="dialog" aria-labelledby="recordsModalLabel" aria-hidden="true">
<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="recordsModalLabel">Liste des enregistrements</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<style>
        /* Custom styles for DataTable */
        table.dataTable thead th, table.dataTable thead td {
            color: white;
            background-color: #006400; /* Darker green for header */
        }
        table.dataTable tbody th, table.dataTable tbody td {
            color: white;
            background-color: #008000; /* Green background for table body */
        }
        /* Optional: Adjust the container's style */
        .container {
            margin-top: 20px;
            background-color: #333; /* Dark background */
            padding: 20px;
            border-radius: 10px;
            background-color:#013b01;
        }
        h2 {
            color: white;
        }

        .modal-content {top:120px;}
    </style>


    <h2>Liste des enregistrements</h2>
    <table id="example" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ref Document</th>
                <th>Date</th>
                <th>Distinataire</th>
                <th>Objet</th>
                <th>Code</th>
                <th>Qrcode</th>
            </tr>
        </thead>
        <tbody>
                    <?php foreach ($rows as $row): ?> <!-- Loop through each row -->
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['ref_document']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['distinataire'], 0, 10)); ?></td> <!-- Display first 10 characters of distinataire -->
                            <td><?php echo htmlspecialchars(substr($row['objet'], 0, 10)); ?></td> <!-- Display first 10 characters of objet -->
                            <td><?php echo htmlspecialchars($row['password']); ?></td>
                            <td><img src="<?php echo './images/' . htmlspecialchars($row['ref_doc_img']); ?>" alt="QR Code" style="width:30px;height:30px;"></td> <!-- Display QR code images -->
                        </tr>
                        <?php endforeach; ?>

                    </tbody>
            </table>
        </div>
    </div>




<script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#example').DataTable({
            "pagingType": "full_numbers" // Enables full pagination
        });
    });
</script>





      </div>
    
    </div>
  </div>
</div>
</div>



















<div class="container">



        <div class="row">
            <!-- Formulaire -->
            <div class="col-md-6">
                <form method="POST">
                    <?php echo @$errorobjet; ?>
                    <br />
                    <div class="container-card">
                        <div id="logo"></div>
                    </div>
                    <br />
                    <div class="form-group">
                        <label for="ref_document">Ref document</label>
                        <input type="text" class="form-control" placeholder="Ref document" id="ref_document" name="ref_document" value="<?php echo @$ref_document; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date document</label>
                        <input type="date" class="form-control" placeholder="Date" id="date" name="date" value="<?php echo @$date; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="distinataire">Distinataire</label>
                        <input type="text" class="form-control" placeholder="Distinataire" id="distinataire" name="distinataire" value="<?php echo @$distinataire; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="objet">Objet</label>
                        <textarea class="form-control" name="objet" rows="3" placeholder="Entrez votre objet" id="objet"><?php echo @$objet; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" name="submit">Générer le code QR</button>
                </form>

            </div>
            <div class="col-md-6">
 <button style="float:right" type="button" class="btn btn-primary mt-3 right" data-toggle="modal" data-target="#recordsModal">Liste des enregistrements</button>
 </div>
            <!-- QR Code et Boutons -->
            <?php if (isset($ref_doc_img)) : ?>
                <div class="col-md-6">
                    <div class="qr-image">
                        <img src="images/<?php echo $ref_doc_img; ?>_resized_logo.png" class="img-fluid" style="width:400px; height:400px;">
                        <p style="color:#fff"><b>Code</b>: <?php echo @$randomPassword; ?></p>
                        <input type="text" hidden   id="password"  value="<?php echo @$randomPassword; ?>" >
                        <a class="btn btn-warning mt-3" href="download.php?file=<?php echo $ref_doc_img; ?>_resized_logo.png&code=<?php echo $row['password'] ?>" role="button"><i class="fas fa-download"></i> Télécharger </a>

<input type="text" id="ref_doc_img" value="<?php echo $ref_doc_img; ?>_resized_logo.png" hidden />
                        <button type="button" class="btn btn-danger mt-3" id="saveToDatabase"><i class="fas fa-save"></i> Enregistrer </button>
                      
                        <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#recordsModal">Afficher la liste</button>
                    </div>

                </div>
            <?php endif; ?>
        </div>
    </div>

 
    <script>
$(document).ready(function() {
    $('#saveToDatabase').click(function(event) {
        event.preventDefault();

        // Retrieve form data
        var ref_document = $('#ref_document').val();
        var date = $('#date').val();
        var distinataire = $('#distinataire').val();
        var objet = $('#objet').val();
        var password = $('#password').val();
        // Corrected the selector here by adding the missing #
        var ref_doc_img = $('#ref_doc_img').val();

        console.log("ref_document:", ref_document, "date:", date, "distinataire:", distinataire, "objet:", objet, "password:", password, "ref_doc_img:", ref_doc_img);

        // Send data via AJAX in JSON format
        $.ajax({
            type: 'POST',
            url: './saveToDatabase.php', // Path to your PHP script
            contentType: "application/json", // Set Content-Type to application/json
            data: JSON.stringify({ // Convert the data object to a JSON string
                ref_document: ref_document,
                date: date,
                distinataire: distinataire,
                objet: objet,
                password: password,
                ref_doc_img: ref_doc_img
            }),
            success: function(response) {
                // Display response using SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message, // Assuming your backend sends a message in the response
                });
            },
            error: function(xhr, status, error) {
                // Handle errors
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseText, // Display error response from the server
                });
            }
        });
    });
});


    </script>

</body>

</html>
