<?php

session_start();

// Connexion à la base de données
function getDB(): PDO
{
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=projet_1;charset=utf8mb4", "root", "", [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur SQL : $e");
    }
}

$erreur = null;

// Conserver les valeurs saisies en cas d'erreur
$ancien = [
    'name'        => '',
    'email'       => '',
    'nationality' => '',
    'password'    => '',
    'sexe'        => 'Homme',
    'diploma'     => '',
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ancien['name']        = trim($_POST['name']        ?? '');
    $ancien['email']       = trim($_POST['email']       ?? '');
    $ancien['nationality'] = trim($_POST['nationality'] ?? '');
    $ancien['password']    = trim($_POST['password']    ?? '');
    $ancien['sexe']        = $_POST['sexe']             ?? 'Homme';
    $ancien['diploma']     = trim($_POST['diploma']     ?? '');

    // --- Vérification 1 : tous les champs sont remplis ---
    $champs_vides = (
        $ancien['name']        === '' ||
        $ancien['email']       === '' ||
        $ancien['nationality'] === '' || $ancien['nationality'] === '-- Sélectionner une nationalité --' ||
        $ancien['password']    === '' ||
        $ancien['diploma']     === '' || $ancien['diploma']     === '-- Sélectionner un Dîplome --'
    );

    if ($champs_vides) {
        $erreur = "Il faut remplir tous les champs.";
    } else {
        $db = getDB();

        // --- Vérification 2 : email déjà existant ---
        $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$ancien['email']]);

        if ($stmt->fetch()) {
            $erreur = "Cet email existe déjà.";
        } else {
            // Tout est bon → insertion
            $random_number = rand(100000, 999999);
            $stmt = $db->prepare(
                "INSERT INTO utilisateurs (nom, email, nationality, password, photo, sexe, diplome, code)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $ancien['name'],
                $ancien['email'],
                $ancien['nationality'],
                $ancien['password'],
                $_FILES['photo']['name'] ?? '',
                $ancien['sexe'],
                $ancien['diploma'],
                $random_number,
            ]);

            $_SESSION['pending_user_id'] = $db->lastInsertId();

            header("Location: auth-confirm.php");
            exit();
        }
    }
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Register Page</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE 4 | Register Page" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant"
    />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="css/style.css" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="css/style.css" />
    <!--end::Required Plugin(AdminLTE)-->
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="register-page bg-body-secondary">
    <div class="register-box">
      <div class="register-logo">
        <a href="../index2.html"><b>Formulaire</b> D'inscription</a>
      </div>
      <!-- /.register-logo -->
      <div class="card">
        <div class="card-body register-card-body">
          <p class="register-box-msg">Toutes les informations sont obligatoires</p>

          <?php if ($erreur !== null): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <?= htmlspecialchars($erreur) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
          <?php endif; ?>

          <form action="index.php" method="POST" enctype="multipart/form-data">

            <div class="input-group mb-3">
              <input
                type="text"
                class="form-control <?= ($erreur !== null && $ancien['name'] === '') ? 'is-invalid' : '' ?>"
                name="name"
                placeholder="Nom"
                value="<?= htmlspecialchars($ancien['name']) ?>"
              />
              <div class="input-group-text">
                <span class="bi bi-person"></span>
              </div>
            </div>

            <div class="input-group mb-3">
              <input
                type="email"
                class="form-control <?= ($erreur === "Cet email existe déjà." || ($erreur !== null && $ancien['email'] === '')) ? 'is-invalid' : '' ?>"
                name="email"
                placeholder="Email"
                value="<?= htmlspecialchars($ancien['email']) ?>"
              />
              <div class="input-group-text">
                <span class="bi bi-envelope"></span>
              </div>
            </div>

            <div class="input-group mb-3">
              <select class="form-control" name="nationality">
                <option disabled <?= $ancien['nationality'] === '' ? 'selected' : '' ?>>-- Sélectionner une nationalité --</option>
                <option <?= $ancien['nationality'] === 'Gabonaise' ? 'selected' : '' ?>>Gabonaise</option>
                <option <?= $ancien['nationality'] === 'Togolaise' ? 'selected' : '' ?>>Togolaise</option>
                <option <?= $ancien['nationality'] === 'Malienne'  ? 'selected' : '' ?>>Malienne</option>
              </select>
            </div>

            <div class="input-group mb-3">
              <input
                type="password"
                class="form-control <?= ($erreur !== null && $ancien['password'] === '') ? 'is-invalid' : '' ?>"
                name="password"
                placeholder="Mot de passe"
              />
              <div class="input-group-text">
                <span class="bi bi-lock-fill"></span>
              </div>
            </div>

            <div class="input-group mb-3">
              <input type="file" class="form-control" name="photo" accept="image/*" />
            </div>

            <div class="input-group mb-3">
              <label class="form-label">Sexe</label>
            </div>

            <div class="input-group mb-3 gap-2">
              <div>
                <input type="radio" class="form-check-input" id="Homme" name="sexe" value="Homme"
                  <?= $ancien['sexe'] === 'Homme' ? 'checked' : '' ?> />
                <label for="Homme" class="form-label">Homme</label>
              </div>
              <div>
                <input type="radio" class="form-check-input" id="Femme" name="sexe" value="Femme"
                  <?= $ancien['sexe'] === 'Femme' ? 'checked' : '' ?> />
                <label for="Femme" class="form-label">Femme</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <select class="form-control" name="diploma">
                <option disabled <?= $ancien['diploma'] === '' ? 'selected' : '' ?>>-- Sélectionner un Dîplome --</option>
                <option <?= $ancien['diploma'] === 'CEP'      ? 'selected' : '' ?>>CEP</option>
                <option <?= $ancien['diploma'] === 'BEPC'     ? 'selected' : '' ?>>BEPC</option>
                <option <?= $ancien['diploma'] === 'BAC'      ? 'selected' : '' ?>>BAC</option>
                <option <?= $ancien['diploma'] === 'BTS'      ? 'selected' : '' ?>>BTS</option>
                <option <?= $ancien['diploma'] === 'LICENCE'  ? 'selected' : '' ?>>LICENCE</option>
                <option <?= $ancien['diploma'] === 'MASTER'   ? 'selected' : '' ?>>MASTER</option>
                <option <?= $ancien['diploma'] === 'Doctorat' ? 'selected' : '' ?>>Doctorat</option>
              </select>
            </div>

            <!--begin::Row-->
            <div class="row">
              <div class="col-8">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="accepted-terms" value="" id="flexCheckDefault" />
                  <label class="form-check-label" for="flexCheckDefault">
                    Je suis d'accord avec les <a href="#">termes</a>
                  </label>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-4">
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Valider</button>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </form>

          <div class="social-auth-links text-center mb-3 d-grid gap-2">
            <p>- OU -</p>
            <a href="#" class="btn btn-primary">
              <i class="bi bi-facebook me-2"></i> S'inscrire avec Facebook
            </a>
            <a href="#" class="btn btn-danger">
              <i class="bi bi-google me-2"></i> S'inscrire avec Google+
            </a>
          </div>
          <!-- /.social-auth-links -->

          <p class="mb-0">
            <a href="login.php" class="text-center"> J'ai déjà un compte</a>
          </p>
        </div>
        <!-- /.register-card-body -->
      </div>
    </div>
    <!-- /.register-box -->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->

    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <script src="../js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->

    <!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        const isMobile = window.innerWidth <= 992;
        if (
          sidebarWrapper &&
          OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
          !isMobile
        ) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->
  </body>
  <!--end::Body-->
</html>