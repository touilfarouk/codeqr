<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT');
header('Content-Type: application/json');

include('sqlConnect.php');

// Decode JSON from the request body
$form = json_decode(file_get_contents("php://input"), true);

// Initialize variables without default null values
$ref_document = '';
$date = '';
$distinataire = '';
$objet = '';
$password = '';
$ref_doc_img = ''; // Initialize the variable for the image path

// Check and assign if keys exist in the decoded JSON
if (isset($form['ref_document'])) $ref_document = $form['ref_document'];
if (isset($form['date'])) $date = $form['date'];
if (isset($form['distinataire'])) $distinataire = $form['distinataire'];
if (isset($form['objet'])) $objet = $form['objet'];
if (isset($form['password'])) $password = $form['password'];
if (isset($form['ref_doc_img'])) $ref_doc_img = $form['ref_doc_img']; // Assign the image path if it exists in the form

if ($ref_document && $date && $distinataire && $objet && $password) {
    try {
        // Modify the INSERT statement to include ref_doc_img
        $query = $bdd->prepare("INSERT INTO documents (ref_document, date, distinataire, objet, password, ref_doc_img) VALUES (?, ?, ?, ?, ?, ?)");
        $query->execute([$ref_document, $date, $distinataire, $objet, $password, $ref_doc_img]);

        if ($query->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Data saved to database successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to save data to database"]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage(), 0);
        echo json_encode(["success" => false, "message" => "Database error occurred. Please try again later."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Form fields cannot be empty"]);
}
?>
