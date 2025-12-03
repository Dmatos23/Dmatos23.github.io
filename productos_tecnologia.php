<?php
require_once "conexion.php";

// Variables
$mensaje = "";

// INSERTAR / ACTUALIZAR
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion      = $_POST["accion"] ?? "";
    $id          = $_POST["IDproducto"] ?? "";
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio      = $_POST["precio"] ?? 0;
    $unidades    = $_POST["unidades_disponible"] ?? 0;
    $categoria   = $_POST["categoria"] ?? "";

    if ($accion === "insertar") {
        $sql = "INSERT INTO productos_tecnologia (descripcion, precio, unidades_disponible, categoria)
                VALUES (:descripcion, :precio, :unidades, :categoria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":descripcion" => $descripcion,
            ":precio"      => $precio,
            ":unidades"    => $unidades,
            ":categoria"   => $categoria
        ]);
        $mensaje = "Producto registrado correctamente.";
    } elseif ($accion === "modificar" && !empty($id)) {
        $sql = "UPDATE productos_tecnologia
                SET descripcion = :descripcion,
                    precio = :precio,
                    unidades_disponible = :unidades,
                    categoria = :categoria
                WHERE IDproducto = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":descripcion" => $descripcion,
            ":precio"      => $precio,
            ":unidades"    => $unidades,
            ":categoria"   => $categoria,
            ":id"          => $id
        ]);
        $mensaje = "Producto modificado correctamente.";
    }
}

// CARGAR PARA EDICIÓN
$id          = "";
$descripcion = "";
$precio      = "";
$unidades    = "";
$categoria   = "";

if (isset($_GET["editar"])) {
    $editarId = (int)$_GET["editar"];
    $sql = "SELECT * FROM productos_tecnologia WHERE IDproducto = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $editarId]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($producto) {
        $id          = $producto["IDproducto"];
        $descripcion = $producto["descripcion"];
        $precio      = $producto["precio"];
        $unidades    = $producto["unidades_disponible"];
        $categoria   = $producto["categoria"];
    }
}

// ELIMINAR
if (isset($_GET["eliminar"])) {
    $eliminarId = (int)$_GET["eliminar"];
    $sql = "DELETE FROM productos_tecnologia WHERE IDproducto = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $eliminarId]);
    $mensaje = "Producto eliminado correctamente.";
}

// CONSULTAR / BÚSQUEDA
$busqueda = $_GET["buscar"] ?? "";
$sqlLista = "SELECT * FROM productos_tecnologia
             WHERE descripcion LIKE :b OR categoria LIKE :b
             ORDER BY IDproducto DESC";
$stmtLista = $pdo->prepare($sqlLista);
$stmtLista->execute([":b" => "%$busqueda%"]);
$productos = $stmtLista->fetchAll(PDO::FETCH_ASSOC);

// Categorías
$categorias = ["Laptop","Smartphone","Tablet","Accesorio","Audio","Almacenamiento","Monitor","Red","Gaming","Otro"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DMStore - Gestión de Productos Tecnología</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --dm-bg:#050608;
            --dm-card:#13151f;
            --dm-card-alt:#181b26;
            --dm-primary:#f1c40f;
            --dm-primary-soft:rgba(241,196,15,0.12);
            --dm-text:#ecf0f1;
            --dm-muted:#95a5a6;
            --dm-danger:#e74c3c;
            --dm-success:#2ecc71;
        }
        *{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',system-ui,sans-serif}
        body{
            background:radial-gradient(circle at top,#1f2933 0,#050608 60%);
            color:var(--dm-text);min-height:100vh;padding:20px;
        }
        .container{max-width:1200px;margin:0 auto;}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
        .logo-title{display:flex;align-items:center;gap:12px;}
        .logo-circle{
            width:40px;height:40px;border-radius:999px;border:2px solid var(--dm-primary);
            display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:var(--dm-primary);
        }
        .header h1{font-size:22px;font-weight:600;}
        .header span{font-size:12px;color:var(--dm-muted);}
        .tag{
            padding:6px 12px;border-radius:999px;background:var(--dm-primary-soft);
            color:var(--dm-primary);font-size:11px;font-weight:500;
        }
        .grid{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1.6fr);gap:20px;}
        .card{
            background:linear-gradient(145deg,var(--dm-card),var(--dm-card-alt));
            border-radius:20px;padding:18px 20px 20px;
            box-shadow:0 18px 40px rgba(0,0,0,0.5);
            border:1px solid rgba(255,255,255,0.03);
        }
        .card h2{font-size:16px;margin-bottom:10px;}
        .card p.subtitle{font-size:12px;color:var(--dm-muted);margin-bottom:15px;}
        form .row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px 16px;margin-bottom:10px;}
        .form-group{display:flex;flex-direction:column;gap:4px;}
        label{font-size:12px;color:var(--dm-muted);}
        input[type="text"],input[type="number"],select{
            background:#05060a;border-radius:999px;border:1px solid rgba(255,255,255,0.06);
            padding:8px 12px;color:var(--dm-text);font-size:13px;outline:none;
        }
        input:focus,select:focus{
            border-color:var(--dm-primary);box-shadow:0 0 0 1px rgba(241,196,15,0.3);
        }
        .badge-id{
            font-size:12px;color:var(--dm-muted);padding:6px 10px;border-radius:999px;
            border:1px dashed rgba(255,255,255,0.1);display:inline-flex;align-items:center;gap:6px;
        }
        .badge-id span{color:var(--dm-primary);font-weight:500;}
        .acciones{display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;}
        button{
            border:none;cursor:pointer;border-radius:999px;padding:7px 14px;
            font-size:13px;font-weight:500;
        }
        .btn-primary{background:var(--dm-primary);color:#000;}
        .btn-secondary{background:transparent;color:var(--dm-primary);border:1px solid rgba(241,196,15,0.5);}
        .btn-danger{background:var(--dm-danger);color:#fff;}
        .btn-small{padding:4px 8px;font-size:11px;}
        .mensaje{margin-top:10px;font-size:12px;color:var(--dm-success);}
        .search-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;gap:10px;}
        .search-bar form{display:flex;gap:8px;flex:1;}
        .search-bar input[type="text"]{flex:1;}
        .total-registros{font-size:11px;color:var(--dm-muted);}
        table{width:100%;border-collapse:collapse;font-size:12px;}
        th,td{padding:7px 8px;border-bottom:1px solid rgba(255,255,255,0.04);text-align:left;}
        th{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--dm-muted);}
        tbody tr:hover{background:rgba(255,255,255,0.02);}
        .pill-categoria{
            padding:3px 8px;border-radius:999px;background:var(--dm-primary-soft);
            color:var(--dm-primary);font-size:11px;
        }
        .precio{font-weight:600;}
        .chip-stock{
            font-size:11px;padding:3px 8px;border-radius:999px;border:1px solid rgba(255,255,255,0.08);
        }
        .chip-stock.low{border-color:var(--dm-danger);color:var(--dm-danger);}
        .chip-stock.ok{border-color:var(--dm-success);color:var(--dm-success);}
        @media(max-width:900px){.grid{grid-template-columns:1fr;}}
        @media(max-width:600px){
            .header{flex-direction:column;align-items:flex-start;gap:8px;}
            form .row{grid-template-columns:1fr;}
            .search-bar{flex-direction:column;align-items:stretch;}
        }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <div class="logo-title">
            <div class="logo-circle">DM</div>
            <div>
                <h1>Gestión de Productos Tecnológicos</h1>
                
            </div>
        </div>
        
    </header>

    <div class="grid">
        <!-- FORMULARIO -->
        <section class="card">
            <h2><?php echo $id ? "Editar producto #$id" : "Nuevo producto tecnológico"; ?></h2>
            <p class="subtitle">Inserta, modifica o guarda productos directamente en la base de datos.</p>

            <form method="post" autocomplete="off">
                <input type="hidden" name="IDproducto" value="<?php echo htmlspecialchars($id); ?>">
                <div class="row">
                    <div class="form-group">
                        <label>ID Producto</label>
                        <div class="badge-id">
                            <?php if ($id): ?>
                                ID actual: <span>#<?php echo htmlspecialchars($id); ?></span>
                            <?php else: ?>
                                <span>Auto</span> (asignado por la BD)
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción del producto</label>
                        <input type="text" name="descripcion" required
                               value="<?php echo htmlspecialchars($descripcion); ?>"
                               placeholder="Ej: Laptop Dell 14'' Core i7, 16GB RAM, 512GB SSD">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Precio (RD$)</label>
                        <input type="number" step="0.01" min="0" name="precio" required
                               value="<?php echo htmlspecialchars($precio); ?>"
                               placeholder="Ej: 45999.99">
                    </div>
                    <div class="form-group">
                        <label>Unidades disponibles</label>
                        <input type="number" min="0" name="unidades_disponible" required
                               value="<?php echo htmlspecialchars($unidades); ?>"
                               placeholder="Ej: 10">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="categoria" required>
                            <option value="">-- Selecciona una categoría --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo ($categoria===$cat)?"selected":""; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="acciones">
                    <?php if ($id): ?>
                        <button type="submit" name="accion" value="modificar" class="btn-primary">Guardar cambios</button>
                        <a href="productos_tecnologia.php" class="btn-secondary" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">Cancelar</a>
                    <?php else: ?>
                        <button type="submit" name="accion" value="insertar" class="btn-primary">Registrar producto</button>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (!empty($mensaje)): ?>
                <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>
        </section>

        <!-- LISTADO -->
        <section class="card">
            <h2>Listado y consulta de productos</h2>
            <p class="subtitle">Consulta, edita o elimina los registros guardados en la base de datos.</p>

            <div class="search-bar">
                <form method="get">
                    <input type="text" name="buscar" placeholder="Buscar por descripción o categoría..."
                           value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit" class="btn-secondary">Buscar</button>
                </form>
                <div class="total-registros">
                    Registros encontrados: <?php echo count($productos); ?>
                </div>
            </div>

            <div style="overflow-x:auto;max-height:420px;">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($productos) === 0): ?>
                        <tr><td colspan="6">No hay productos registrados o que coincidan con la búsqueda.</td></tr>
                    <?php else: ?>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td>#<?php echo $p["IDproducto"]; ?></td>
                                <td><?php echo htmlspecialchars($p["descripcion"]); ?></td>
                                <td><span class="pill-categoria"><?php echo htmlspecialchars($p["categoria"]); ?></span></td>
                                <td class="precio">RD$ <?php echo number_format($p["precio"],2); ?></td>
                                <td>
                                    <?php
                                    $stockClass = $p["unidades_disponible"] <= 3 ? "low" : "ok";
                                    ?>
                                    <span class="chip-stock <?php echo $stockClass; ?>">
                                        <?php echo $p["unidades_disponible"]; ?> uds
                                    </span>
                                </td>
                                <td>
                                    <a href="productos_tecnologia.php?editar=<?php echo $p["IDproducto"]; ?>"
                                       class="btn-secondary btn-small"
                                       style="text-decoration:none;display:inline-block;text-align:center;">Editar</a>
                                    <a href="productos_tecnologia.php?eliminar=<?php echo $p["IDproducto"]; ?>"
                                       class="btn-danger btn-small"
                                       style="text-decoration:none;display:inline-block;text-align:center;"
                                       onclick="return confirm('¿Seguro que deseas eliminar este producto?');">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
</body>
</html>

