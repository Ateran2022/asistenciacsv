<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['perfil'])) {
    header("Location: login.php");
    exit();
}

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $diaColegio = $_POST['diaColegio'];
    $fecha = $_POST['fecha'];
    $curso = $_POST['curso'];
    $estudiantes = $_POST['estudiantes'];
    $docente = $_POST['docente'];
    $motivo = $_POST['motivo'];

    $sql = "UPDATE registro_asistencia SET dia_colegio = ?, fecha = ?, curso = ?, estudiantes = ?, docente = ?, motivo = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssi", $diaColegio, $fecha, $curso, $estudiantes, $docente, $motivo, $id);
    $stmt->execute();
    header("Location: ver_registros.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM registro_asistencia WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$registro = $result->fetch_assoc();

// Obtener cursos para el select
$cursos = $conn->query("SELECT DISTINCT curso FROM registro_asistencia");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Registro</h1>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">

            <label for="diaColegio">Día del Colegio:</label>
            <input type="text" id="diaColegio" name="diaColegio" value="<?php echo $registro['dia_colegio']; ?>" required>

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $registro['fecha']; ?>" required>

            <label for="curso">Curso:</label>
            <select id="curso" name="curso" required>
                <?php while ($row = $cursos->fetch_assoc()): ?>
                    <option value="<?php echo $row['curso']; ?>" <?php echo $row['curso'] == $registro['curso'] ? 'selected' : ''; ?>>
                        <?php echo $row['curso']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="estudiantes">Estudiantes:</label>
            <input type="text" id="estudiantes" name="estudiantes" value="<?php echo $registro['estudiantes']; ?>" required>

            <label for="docente">Docente:</label>
            <input type="text" id="docente" name="docente" value="<?php echo $registro['docente']; ?>" required>

            <label for="motivo">Motivo:</label>
            <input type="text" id="motivo" name="motivo" value="<?php echo $registro['motivo']; ?>" required>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
