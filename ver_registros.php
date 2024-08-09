<?php
session_start();

// Verifica si el usuario está logueado y qué tipo de perfil tiene
if (!isset($_SESSION['perfil'])) {
    header("Location: login.php"); // Redirige al login si no está autenticado
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$perfil = $_SESSION['perfil'];
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

// Obtener cursos para los select
$cursos = $conn->query("SELECT DISTINCT curso FROM registro_asistencia");

// Verifica si el usuario es administrador y si se ha solicitado generar el PDF
if ($perfil === 'administrador' && isset($_GET['generate_pdf'])) {
    require('fpdf/fpdf.php');
    
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
    $pdf->Cell(30, 10, 'Día del Colegio', 1);
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
    exit();
}







// Manejar eliminación y edición de registros
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $id = $_POST['id'];

        if ($_POST['action'] == 'delete') {
            $sql = "DELETE FROM registro_asistencia WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
        } elseif ($_POST['action'] == 'update') {
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
        }
    }
}

$cursoSeleccionado = isset($_GET['curso']) ? $_GET['curso'] : '';

if ($perfil === 'coordinador') {
    $sql = "SELECT * FROM registro_asistencia" . ($cursoSeleccionado ? " WHERE curso = '$cursoSeleccionado'" : "");
} else {
    $sql = "SELECT * FROM registro_asistencia";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Asistencia</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
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
        select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .actions button {
            margin-right: 5px;
        }
        .logout {
            text-align: right;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <form method="POST">
                <button type="submit" name="logout">Cerrar Sesión</button>
            </form>
        </div>

        <h1>Registros de Asistencia</h1>

        <?php if ($perfil === 'coordinador'): ?>
            <form method="GET">
                <label for="curso">Selecciona un curso:</label>
                <select id="curso" name="curso" onchange="this.form.submit()">
                    <option value="">Todos los cursos</option>
                    <?php while ($row = $cursos->fetch_assoc()): ?>
                        <option value="<?php echo $row['curso']; ?>" <?php echo $row['curso'] == $cursoSeleccionado ? 'selected' : ''; ?>>
                            <?php echo $row['curso']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Día del Colegio</th>
                    <th>Fecha</th>
                    <th>Curso</th>
                    <th>Estudiantes</th>
                    <th>Docente</th>
                    <th>Motivo</th>
                    <?php if ($perfil === 'coordinador'): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['dia_colegio']; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo $row['curso']; ?></td>
                        <td><?php echo $row['estudiantes']; ?></td>
                        <td><?php echo $row['docente']; ?></td>
                        <td><?php echo $row['motivo']; ?></td>
                        <?php if ($perfil === 'coordinador'): ?>
                            <td class="actions">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="delete">Eliminar</button>
                        </form>
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="edit">Editar</button>
                        </form>
                    </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

       <?php if ($perfil === 'administrador'): ?>
    <form method="GET">
        <label for="curso">Selecciona un curso para imprimir:</label>
        <select id="curso" name="curso" onchange="this.form.submit()">
            <option value="">Todos los cursos</option>
            <?php $cursos->data_seek(0); // Reiniciar el puntero del resultado ?>
            <?php while ($row = $cursos->fetch_assoc()): ?>
                <option value="<?php echo $row['curso']; ?>" <?php echo $row['curso'] == $cursoSeleccionado ? 'selected' : ''; ?>>
                    <?php echo $row['curso']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="hidden" name="generate_pdf" value="1">
    </form>
<?php endif; ?>

        <script>
            function editRecord(id) {
                window.location.href = 'edit_record.php?id=' + id;
            }
        </script>
    </div>
</body>
</html>
