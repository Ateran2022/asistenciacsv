<?php
session_start();
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

require('fpdf/fpdf.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "asistencia_colegio";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$cursoSeleccionado = isset($_GET['curso']) ? $_GET['curso'] : '';
$sql = "SELECT * FROM registro_asistencia" . ($cursoSeleccionado ? " WHERE curso = '$cursoSeleccionado'" : "");
$result = $conn->query($sql);

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Registros de Asistencia', 0, 1, 'C');
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(30, 10, 'Dia del Colegio', 1);
$pdf->Cell(30, 10, 'Fecha', 1);
$pdf->Cell(30, 10, 'Curso', 1);
$pdf->Cell(40, 10, 'Estudiantes', 1);
$pdf->Cell(30, 10, 'Docente', 1);
$pdf->Cell(30, 10, 'Motivo', 1);
$pdf->Ln();

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10, 10, $row['id'], 1);
    $pdf->Cell(30, 10, $row['dia_colegio'], 1);
    $pdf->Cell(30, 10, $row['fecha'], 1);
    $pdf->Cell(30, 10, $row['curso'], 1);
    $pdf->Cell(40, 10, $row['estudiantes'], 1);
    $pdf->Cell(30, 10, $row['docente'], 1);
    $pdf->Cell(30, 10, $row['motivo'], 1);
    $pdf->Ln();
}

$pdf->Output();
?>
