




<?php
/*
Tout le code doit se faire dans ce fichier PHP

Réalisez un formulaire HTML contenant :
- firstname
- lastname
- email
- pwd
- pwdConfirm

Créer une table "user" dans la base de données, regardez le .env à la racine et faites un build de docker
si vous n'arrivez pas à les récupérer pour qu'il les prenne en compte

Lors de la validation du formulaire vous devez :
- Nettoyer les valeurs, exemple trim sur l'email et lowercase (5 points)
- Attention au mot de passe (3 points)
- Attention à l'unicité de l'email (4 points)
- Vérifier les champs sachant que le prénom et le nom sont facultatifs
- Insérer en BDD avec PDO et des requêtes préparées si tout est OK (4 points)
- Sinon afficher les erreurs et remettre les valeurs pertinantes dans les inputs (4 points)

Le design je m'en fiche mais pas la sécurité

Bonus de 3 points si vous arrivez à envoyer un mail via un compte SMTP de votre choix
pour valider l'adresse email en bdd

Pour le : 22 Octobre 2025 - 8h
M'envoyer un lien par mail de votre repo sur y.skrzypczyk@gmail.com
Objet du mail : TP1 - 2IW3 - Nom Prénom
Si vous ne savez pas mettre votre code sur un repo envoyez moi une archive
*/

try {          //le try catch me permet à me connecter
   $host= 'db';
   $dbname = 'postgres';
   $user = 'devuser'; 
   $password = 'devpass'; 

   $dsn = "pgsql:host=$host;port=5432;dbname=$dbname;"; //La variable dsn me permet de savoir où savoir est ma bdd
   $pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
   ]); //Le PDO me permet d'etre sur que la connexion marche
   }catch (PDOException $exception) {
    echo "Connexion échoué :". $exception->getMessage();//Message d'erreur si la connexion echoue
    exit; 
   }




//Je recupere les données de mon formulaire grâce post
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim(htmlspecialchars($_POST['firstname']));
    $lastname = trim(htmlspecialchars ($_POST['lastname']));
    $email = strtolower (trim(htmlspecialchars($_POST['email'])));
    $pwd = trim(htmlspecialchars($_POST['pwd']));
    $pwdConfirm = trim(htmlspecialchars($_POST['pwdConfirm']));

    // Maintenant je vérifie si les deux mdp se correspondent avec un if
    if ($pwd !== $pwdConfirm) {
        echo "Les mots de passe sont différents";
        exit;
    }

    // Apres avoir verfier les deux mdp, je verifie si l'email n'existe deja pas dans la bdd
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->bindParam(':email',$email);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    //ici je stipule que si le nombre d'email deja inscript est superieur à 0 alors l'email tapé existe déjà
    if ($count > 0) {
        echo "Email déjà utiliser";
        exit;
    }


    //Je censure le mdp avec un hash pour ne pas qu'il apparaisse en clair
    $passwordHashed = password_hash($pwd, PASSWORD_BCRYPT);
    $requete = $pdo->prepare("INSERT INTO users VALUES (:firstname, :lastname, :pwd, :email)");
    $requete->bindParam(':firstname', $firstname);
    $requete->bindParam(':lastname', $lastname);
    $requete->bindParam(':pwd', $passwordHashed);
    $requete->bindParam(':email', $email);
    $resultat = $requete->execute();
    echo "compte crée";

}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Insciption Utilisateur </title>
</head>
<body>
    <h1> Formulaire Inscription </h1>

    <form method="POST" action="">

        <label for="firstname">Prénom :</label>
            <input type="text" id="firstname" name="firstname" placeholder="Prénom">

        <label for="lastname"> Nom : </label>
            <input type="text" id="lastname" name="lastname" placeholder="Nom" >

        <label for="email">Email : </label>
            <input type="text" id="email" name="email" placeholder="email" required>

        <label for="pwd">Mot de passe  : </label>
            <input type="password" id="pwd" name="pwd" placeholder="pwd" required>

        <label for="pwdConfirm">Verfifier Mot de Passe  : </label>
            <input type="password" id="pwdConfirm" name="pwdConfirm" placeholder="Verifpwd" required>
                <button type="submit" name="send_new">Envoyer</button>
            </form>
        </body>
        </html>
<?php 

    try {

        $mail = new PHPMailer(true)

         // Ici je fais le parametrage de PHPMailer pour envoyer un mail automatique
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'Tp1PHP@gmail.com';
        $mail->Password = 'llxpspneydlofsxe';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Envoie de l'email
        $mail->send();
?>
