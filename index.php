<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registro de Productos</title>
</head>
<body>
    <?php
    // Clave de cifrado
    $clave = "test-key";
    $iv = "1234567890123456"; // IV fijo de 16 bytes

    // Conexión a la base de datos
    $mysqli = new mysqli("192.168.1.3", "root", "123", "ropa", 3306);

    // Verificar la conexión
    if ($mysqli->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    // Verificar si se ha enviado el formulario para agregar un producto
    if (isset($_POST["guardar"])) {
        $nombre = $_POST["nombre"];
        $precio = $_POST["precio"];
        $descripcion = $_POST["descripcion"];
        $marca_id = $_POST["marca_id"];
        
        $descripcion_cifrada = openssl_encrypt($descripcion, 'AES-128-CBC', $clave, 0, $iv);
        
        // Insertar el producto en la base de datos
        $consulta = $mysqli->prepare("INSERT INTO productos (nombre, precio, descripcion, MarcaID) VALUES (?, ?, ?, ?)");
        $consulta->bind_param("sdss", $nombre, $precio, $descripcion_cifrada, $marca_id);
        $consulta->execute();
        
        echo "<h2>Producto registrado!</h2>";
    }

    // Obtener los productos de la base de datos
    $resultado = $mysqli->query("SELECT * FROM productos");
    $productos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }

    $mysqli->close();
    ?>

    <form action="" method="post">
        <label>Nombre:</label><input type="text" name="nombre" required><br>
        <label>Precio:</label><input type="text" name="precio" required><br>
        <label>Descripción:</label><input type="text" name="descripcion" required><br>
        <label>Marca ID:</label><input type="text" name="marca_id" required><br>
        <center><input type="submit" name="guardar" value="Guardar"></center>
    </form>

    <h2>Productos Registrados</h2>
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Descripción</th>
            <th>Marca ID</th>
        </tr>
        <?php foreach ($productos as $producto): ?>
        <tr>
            <td><?php echo $producto['nombre']; ?></td>
            <td><?php echo $producto['precio']; ?></td>
            <td><?php echo $producto['descripcion']; ?></td>
            <td><?php echo $producto['MarcaID']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Productos Desencriptados</h2>
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Descripción</th>
            <th>Marca ID</th>
        </tr>
        <?php foreach ($productos as $producto): ?>
        <tr>
            <td><?php echo $producto['nombre']; ?></td>
            <td><?php echo $producto['precio']; ?></td>
            <td>
                <?php 
                $descripcion_descifrada = openssl_decrypt($producto['descripcion'], 'AES-128-CBC', $clave, 0, $iv);
                echo $descripcion_descifrada;
                ?>
            </td>
            <td><?php echo $producto['MarcaID']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
