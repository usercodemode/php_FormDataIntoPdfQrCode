<?php
//require_once('vendor/autoload.php'); // Assuming FPDF is installed using Composer
include 'fpdf/fpdf.php';
include 'phpqrcode/qrlib.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $contact_number = $_POST['contact_number'];
  $roll_number = $_POST['roll_number'];
  $address = $_POST['address'];
  $country = $_POST['country'];
  $city = $_POST['city'];

  $data = ["name" => $name,"email" => $email, "contact_number" => $contact_number, "roll_number" => $roll_number,"address" => $address,"country" => $country,"city" => $city];

  // Image handling
  $image = $_FILES['image']; // Get uploaded image details
  $imageName = $image['name'];
  $imageTmpName = $image['tmp_name'];
  $imageError = $image['error'];

  // Validate image (optional)
  if ($imageError !== 0) {
    echo "Error uploading image: " . $imageError;
    exit;
  }

  // Extract extension (even if missing in filename)
  $ext = pathinfo($imageName, PATHINFO_EXTENSION);

  // Construct the expected image path with extension, using realpath()
  $imagePath = realpath($imageTmpName) . '.' . $ext;

  // Verify file existence
  // if (!file_exists($imagePath)) {
  //     echo "Error: Temporary image file not found.";
  //     exit;
  // }

  // QRcode Generator
  $qrText = json_encode($data);
  $qrMargin = 2;
  $qrSize = 5; // Adjust QR code size as needed
  $qrPNGName = 'qr_code.png'; // Temporary QR code image name

  QRcode::png($qrText, $qrPNGName, $ecc = 'L', $pixelSize = $qrSize, $frameSize = $qrMargin);


  // Create a new FPDF instance
  $pdf = new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial', 'B', 16);
  $pdf->Cell(0, 10, 'Form Data', 0, 1, 'C');

  $pdf->SetFont('Arial', '', 12);

  // User information
  $pdf->Cell(40, 6, 'Name:', 0);
  $pdf->Cell(0, 6, $name, 0, 1);
  $pdf->Cell(40, 6, 'Email:', 0);
  $pdf->Cell(0, 6, $email, 0, 1);
  $pdf->Cell(40, 6, 'Contact Number:', 0);
  $pdf->Cell(0, 6, $contact_number, 0, 1);
  $pdf->Cell(40, 6, 'Roll Number:', 0);
  $pdf->Cell(0, 6, $roll_number, 0, 1);

  // Table for additional data
  $pdf->Cell(100, 6, 'Address', 1, 0, 'C');
  $pdf->Cell(40, 6, 'Country', 1, 0, 'C');
  $pdf->Cell(40, 6, 'City', 1, 1, 'C');
  $pdf->Cell(100, 6, $address, 1, 0);
  $pdf->Cell(40, 6, $country, 1, 0);
  $pdf->Cell(40, 6, $city, 1, 1);
  
  $pdf->Ln(10);
  
  

  // Image (if uploaded successfully)
  if ($imageError === 0) {
    move_uploaded_file($imageTmpName, $imagePath);
    $pdf->Cell(170, 10, 'Photograph', 0, 10, 'R');

    $pdf->Image($imagePath, 145, 80, 45, 45); // Adjust position and size as needed
  } else {
    $pdf->Cell(0, 10, "Error uploading image", 0, 1);
  }

  $pdf->Cell(170, 160, 'QR Code', 0, 10, 'R');

  // Include QR code image (adjust position and size as needed)
  $pdf->Image($qrPNGName, 155, 160, 30, 30);

  // Output the PDF
  $pdf->Output('form_data.pdf', 'D'); // 'D' for download
  unlink($qrPNGName); // Delete temporary QR

}
