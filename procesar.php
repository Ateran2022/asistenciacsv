<?php
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

// Obtener datos del formulario
$diaColegio = $_POST['diaColegio'];
$fecha = $_POST['fecha'];
$curso = $_POST['curso'];
$estudiantes = implode(", ", $_POST['estudiantes']);
$docente = $_POST['docente'];
$motivo = $_POST['motivo'];

// Consulta SQL
$sql = "INSERT INTO registro_asistencia (dia_colegio, fecha, curso, estudiantes, docente, motivo)
VALUES ('$diaColegio', '$fecha', '$curso', '$estudiantes', '$docente', '$motivo')";

if ($conn->query($sql) === TRUE) {
    // Redirigir a la página de éxito con un parámetro
    header("Location: success.html?form=clear");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
