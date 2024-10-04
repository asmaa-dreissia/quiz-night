<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hacher le mot de passe

    // Vérifier si le nom d'utilisateur existe déjà
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "<p class='error-message'>Le nom d'utilisateur existe déjà. Veuillez en choisir un autre.</p>";
    } else {
        // Insérer le nouvel utilisateur
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            // Redirection vers la page de login après inscription réussie
            header("Location: login.php");
            exit(); // Assurez-vous d'arrêter l'exécution du script après la redirection
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Réinitialisation des marges et paddings par défaut */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-x: hidden; /* Empêche le défilement horizontal */
            background: none;
        }

        /* Style global */
        @font-face {
            font-family: 'Neon';
            src: url('font/Neon.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Neon', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: black;
        }

        header, footer {
            width: 100%;
            background: none;
        }

        .header {
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box; /* Assure que padding est inclus dans la taille totale */
            height: 125px;
        }

        .footer {
            padding: 10px 20px;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box; /* Assure que padding est inclus dans la taille totale */
            background: none;
        }

        footer {
            width: 100%;
            text-align: center;
            padding: 10px;
            margin-top: auto; /* Permet au footer de se coller au bas de la page */
            background: none;
            color: white;
        }

        .logo {
            width: 115px;
            height: 115px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        .logo2 {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            margin-bottom: 20px;
        }

        .logo2 img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        a {
            text-decoration: none;
        }

        .buttons button {
            background-color: white;
            color: black;
            border: 2px solid #fff;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 0;
            font-family: 'Neon', sans-serif;
        }

        .buttons button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.2);
            filter: blur(10px);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .buttons button:hover::before {
            opacity: 1;
        }

        .buttons button:hover {
            color: #000;
            background-color: #0ff;
            box-shadow: 0 0 20px #0ff, 0 0 40px #0ff, 0 0 60px #0ff, 0 0 80px #0ff;
            border-color: #0ff;
        }

        .container {
            text-align: center;
            background-color: #060A19;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 75%;
            max-width: 600px;
            margin-top: 50px;
            margin-bottom: auto;
            box-sizing: border-box; /* Assure que padding est inclus dans la taille totale */
            border: 2px solid #FFA500;
            animation: neon-border 1.5s infinite alternate;
        }

        @keyframes neon-border {
            0% {
                box-shadow: 0 0 15px #FFFF00, 0 0 30px #FFFF00, 0 0 45px #FFFF00, 0 0 60px #FFA500;
            }
            25% {
                box-shadow: 0 0 30px #FFFF00, 0 0 45px #FFFF00, 0 0 60px #FFA500, 0 0 75px #FFA500;
            }
            50% {
                box-shadow: 0 0 15px #FFA500, 0 0 30px #FFA500, 0 0 45px #FFFF00, 0 0 60px #FFFF00;
            }
            75% {
                box-shadow: 0 0 30px #FFA500, 0 0 45px #FFA500, 0 0 60px #FFFF00, 0 0 75px #FFFF00;
            }
            100% {
                box-shadow: 0 0 45px #FFFF00, 0 0 60px #FFFF00, 0 0 75px #FFA500, 0 0 90px #FFA500;
            }
        }

        h2 {
            color: white;
            margin-bottom: 20px;
        }

        p {
            color: white;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="text"], input[type="password"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-family: 'Neon', sans-serif;
            color: black;
        }

        input[type="submit"] {
            background-color: #FF00FF;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            color: black;
            width: 80%;
            max-width: 300px;
            margin-top: 60px;
            font-family: 'Neon', sans-serif;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        input[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 255, 255, 0.2);
            filter: blur(10px);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
            border-radius: 5px;
        }

        input[type="submit"]:hover::before {
            opacity: 1;
        }

        input[type="submit"]:hover {
            background-color: #00FFFF;
            box-shadow: 0 0 20px #00FFFF, 0 0 40px #00FFFF, 0 0 60px #00FFFF, 0 0 80px #00FFFF; /* Ombres cyan pour l'effet néon */
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            width: 80%;
            margin-bottom: 20px;
        }

        .remember-forgot label {
            font-size: 14px;
        }

        .remember-forgot a {
            color: #00FF87;
            font-size: 14px;
            text-decoration: none;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .success-message {
            color: white;
        }

        /* Media Queries pour rendre le design responsive */
        @media (max-width: 1024px) {
            .container {
                width: 90%;
                margin-bottom: 20px; /* Ajout de la marge en bas */
            }

            input[type="text"], input[type="password"], input[type="submit"] {
                width: 90%;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                height: auto;
            }

            .buttons {
                margin-top: 10px;
            }

            .logo {
                width: 90px;
                height: 90px;
            }

            .container {
                width: 90%;
                margin-bottom: 20px; /* Ajout de la marge en bas */
            }

            input[type="text"], input[type="password"], input[type="submit"] {
                width: 90%;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 10px;
            }

            .logo {
                width: 70px;
                height: 70px;
            }

            .buttons button {
                padding: 5px 10px;
                font-size: 14px;
            }

            input[type="text"], input[type="password"], input[type="submit"] {
                width: 95%;
            }

            .container {
                margin-bottom: 20px; /* Ajout de la marge en bas */
            }
        }

        #app {
            overflow: hidden;
            touch-action: pan-up;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            text-align: center;
            text-shadow: 0 5px #ffffff, 0 20px #000, 0 30px #000;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: -1;
        }

        #app h1 {
            --fontSize: 60px;
            --lineHeight: 80px;
            width: auto;
            height: calc(2 * var(--lineHeight));
            line-height: var(--lineHeight);
            font-size: var(--fontSize);
            text-transform: uppercase;
        }

        #app a {
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
            color: #fff;
        }

        #app canvas {
            display: block;
            position: fixed;
            z-index: -1;
            top: 0;
        }
    </style>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</head>
<body>
<div id="app">
        <div id="hero">
        </div>
    </div>
    <header>
        <div class="header">
            <div class="logo">
                <a href="index.php">
                    <img src="images/quiz-night.webp" alt="Quiz Night">
                </a>
            </div>
            <div class="buttons">
                <button onclick="redirectTo('login.php')">LOGIN</button>
                <button onclick="redirectTo('register.php')">SIGN UP</button>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="logo2">
            <img src="images/quiz-night.webp" alt="Quiz Night">
        </div>
        <h2>S'enregistrer</h2>
        <p>S'enregistrer et crée des nouveaux quizzes</p>

        <form method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
            <input type="password" name="password" placeholder="Mot de passe" required><br>
            <input type="submit" value="S'enregistrer">
        </form>
    </div>
    <footer>
        <div class="footer">
            <p>&copy; 2024 Quiz Night. Tous droits réservés.</p>
        </div>
    </footer>

    <script type="module" src="script.js"></script>
</body>
</html>
