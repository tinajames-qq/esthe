<?php 

session_start();

function getDB(): PDO
{
	try 
	{
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

$db = getDB();

try
{
	$random_number = rand(100000, 999999);
	$stmt = $db->prepare("INSERT INTO utilisateurs (nom, email, nationality, password, photo, sexe, diplome, code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->execute([$_POST['name'], $_POST['email'], $_POST['nationality'], $_POST['password'], $_POST['photo'], $_POST['sexe'], $_POST['diploma'], $random_number]);

	$user_id = $db->lastInsertId();
    $_SESSION['pending_user_id'] = $user_id;

} catch (PDOException $e) {
	die("Erreur SQL : $e");
}


header("Location: auth-confirm.php");
exit();