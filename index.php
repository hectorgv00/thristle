<?php
session_start();

// Hardcoded credentials
define('USERNAME', 'hectorgv00');
define('PASSWORD', 'hectorgv00');

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle login
if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] === USERNAME && $_POST['password'] === PASSWORD) {
        $_SESSION['logged_in'] = true;
    } else {
        $login_error = "Usuario o contraseña inválidos.";
    }
}

/**
 * Get the subfolders inside the "imagenes" folder
 *
 * @param string $directory
 * @return array
 */
function getSubfolders($directory)
{
    $subfolders = [];
    if (is_dir($directory)) {
        $dirs = scandir($directory);
        foreach ($dirs as $dir) {
            if ($dir !== '.' && $dir !== '..') {
                $fullPath = $directory . DIRECTORY_SEPARATOR . $dir;
                if (is_dir($fullPath)) {
                    $subfolders[] = $dir;
                }
            }
        }
    }
    return $subfolders;
}

/**
 * Get image files from a specific subfolder
 *
 * @param string $directory
 * @return array
 */
function getImages($directory)
{
    $images = [];
    if (is_dir($directory)) {
        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                // You can add more extensions if needed
                if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                    $images[] = $file;
                }
            }
        }
    }
    return $images;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>thristle</title>
    <link rel="icon" href="./favicon.ico" />
    <style>
        <?php include './styles/style.css'; ?>
    </style>
    <script>
        <?php include './scripts/script.js'; ?>
    </script>
</head>

<body>

    <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
        <!-- LOGIN FORM -->
        <div class="login-container-container">

            <div class="login-container">
                <h2>Login in thristle</h2>
                <?php if (isset($login_error)): ?>
                    <div class="login-error"><?php echo $login_error; ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <input type="text" name="username" placeholder="Usuario" required />
                    <input type="password" name="password" placeholder="Contraseña" required />
                    <input type="submit" value="Entrar" />
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- HEADER -->


        <!-- NAVIGATION -->
        <nav>
            <h1>
                thristle
            </h1>
            <span>Usuario: <?php echo USERNAME; ?></span>
            <a href="?action=logout">Cerrar Sesión</a>
        </nav>

        <!-- MAIN CONTENT: TWO-PANE LAYOUT -->
        <div class="main-container">
            <!-- LEFT PANE: List of subfolders -->
            <div class="left-pane">
                <h3>VirtualHosts</h3>
                <ul class="folder-list">
                    <?php
                    $subfolders = getSubfolders('imagenes');
                    $currentSubfolder = isset($_GET['subfolder']) ? $_GET['subfolder'] : '';
                    foreach ($subfolders as $sf):
                        $selectedClass = ($sf === $currentSubfolder) ? 'selected' : '';
                        // Build the URL with the subfolder param
                        $url = $_SERVER['PHP_SELF'] . '?subfolder=' . urlencode($sf);
                        echo "<li><a class=\"$selectedClass\" href=\"$url\">$sf</a></li>";
                    endforeach;
                    ?>
                </ul>
            </div>

            <!-- RIGHT PANE: Images -->
            <div class="right-pane">
                <?php if ($currentSubfolder): ?>
                    <?php
                    $images = getImages('imagenes' . DIRECTORY_SEPARATOR . $currentSubfolder);
                    if (count($images) > 0):
                    ?>
                        <div class="grid-container">
                            <?php foreach ($images as $img): ?>
                                <?php $imgPath = 'imagenes/' . $currentSubfolder . '/' . $img; ?>
                                <img
                                    src="<?php echo $imgPath; ?>"
                                    alt="<?php echo $img; ?>"
                                    onclick="openModal('<?php echo $imgPath; ?>')" />
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No se encontraron imágenes en la carpeta <strong><?php echo htmlspecialchars($currentSubfolder); ?></strong>.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Por favor, selecciona un VirtualHost de la lista para ver sus imágenes.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- MODAL OVERLAY -->
        <div class="modal-overlay" id="modalOverlay" onclick="closeModal()">
            <div class="modal-content" onclick="event.stopPropagation()">
                <button class="close-btn" onclick="closeModal()">×</button>
                <img id="modalImage" src="" alt="Imagen grande" />
            </div>
        </div>

    <?php endif; ?>

</body>

</html>