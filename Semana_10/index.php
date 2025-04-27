<?php
// Carpeta donde están los archivos
$directorio = "archivos/";

// Crear carpeta si no existe
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// Con la siguiente funcion obtenenemos el ícono según la extensión del documento
function obtenerIcono($extension) {
    $iconos = [
        'txt' => '📄',
        'php' => '🐘',
        'html' => '🧩',
        'jpg' => '🖼️',
        'png' => '🎴',
        'pdf' => '📕',
    ];
    return $iconos[strtolower($extension)] ?? '📁'; //Si el archivo ingresado no contiene ninguna extension que tiene el arreglo esta pone uno predeterminado
}

//Verificamos si el usuario presionó el boton de eliminar
if (isset($_GET['eliminar'])) {
    $archivoEliminar = $directorio . basename($_GET['eliminar']); //Tomamos el directorio del archivo que queremos eliminar y lo almacenamos en una variable
    if (file_exists($archivoEliminar)) { //Si el archivo existe lo eliminamos o lo petateamos
        unlink($archivoEliminar);
        echo "<p class='mensaje exito'>Archivo eliminado correctamente.</p>"; //Mandamos un mensaje al usuario sobre informacion de que el archivo se petateó
    } else {
        echo "<p class='mensaje error'>Error: El archivo no existe.</p>"; //Si no existe pues no lo elimina jajsaja
    }
}


//Verficamos otra ve si el usuario envio el mini form y le dio al boton de name renombrar
if (isset($_POST['renombrar'])) {
    $archivoOriginal = $directorio . basename($_POST['archivo_original']);
    $nuevoNombre = trim($_POST['nuevo_nombre']);

    if (!empty($nuevoNombre)) {
        // Validar que termine en .html, .php o .txt
        if (preg_match('/\.(html|php|txt)$/i', $nuevoNombre)) {
            $nuevoNombreCompleto = $directorio . $nuevoNombre;
            if (file_exists($archivoOriginal)) {
                rename($archivoOriginal, $nuevoNombreCompleto);
                echo "<p class='mensaje exito'>Archivo renombrado exitosamente.</p>";
            } else {
                echo "<p class='mensaje error'>Error: El archivo original no existe.</p>";
            }
        } else {
            echo "<p class='mensaje error'>Error: El nuevo nombre debe terminar en .html, .php o .txt.</p>";
        }
    } else {
        echo "<p class='mensaje error'>Error: El nuevo nombre no puede estar vacío.</p>";
    }
}

// Subir archivo
if (isset($_POST['subir']) && isset($_FILES['archivo'])) {
    if (!empty($_FILES['archivo']['name'])) {
        $archivoSubir = $directorio . basename($_FILES['archivo']['name']);
        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $archivoSubir)) {
            echo "<p class='mensaje exito'>Archivo subido exitosamente.</p>";
        } 
    } else {
        echo "<p class='mensaje error'>Error: No seleccionaste ningún archivo.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Archivos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Gestión de Archivos 📁</h1>

    <!-- Formulario de subida de archivo -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Selecciona un archivo:</label>
        <input type="file" name="archivo" required>
        <input type="submit" name="subir" value="Subir Archivo">
    </form>

    <hr>

    <h2>Lista de archivos:</h2>
    <table>
        <tr>
            <th>Ícono</th>
            <th>Nombre</th>
            <th>Peso (MB)</th>
            <th>Acciones</th>
        </tr>

        <?php
        $archivos = array_diff(scandir($directorio), array('.', '..'));

        foreach ($archivos as $archivo) {
            $rutaArchivo = $directorio . $archivo;
            $extension = pathinfo($archivo, PATHINFO_EXTENSION);
            $icono = obtenerIcono($extension);
            $pesoMB = filesize($rutaArchivo) / (1024 * 1024);
            $pesoMB = number_format($pesoMB, 2);

            echo "<tr>";
            echo "<td>$icono</td>";

            if (in_array(strtolower($extension), ['html', 'php', 'txt'])) {
                echo "<td><a href='$rutaArchivo' target='_blank'>$archivo</a></td>";
            } else {
                echo "<td>$archivo</td>";
            }

            echo "<td>$pesoMB MB</td>";

            echo "<td>";
            echo "<a href='?eliminar=" . urlencode($archivo) . "' onclick='return confirm(\"¿Seguro que deseas eliminar este archivo?\");'>Eliminar</a> ";

            if (in_array(strtolower($extension), ['html', 'php', 'txt'])) {
                echo "| <a href='?editar=" . urlencode($archivo) . "'>Renombrar</a>";
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <hr>

    <?php
    if (isset($_GET['editar'])) {
        $archivoEditar = basename($_GET['editar']);
        echo "
        <h2>Renombrar Archivo:</h2>
        <form action='' method='POST'>
            <input type='hidden' name='archivo_original' value='$archivoEditar'>
            <label>Nuevo nombre:</label>
            <input type='text' name='nuevo_nombre' required>
            <input type='submit' name='renombrar' value='Renombrar'>
        </form>
        ";
    }
    ?>

</body>
</html>
