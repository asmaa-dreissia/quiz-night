<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur'; // Valeur par défaut si la clé n'est pas définie

// Fonction pour vérifier si l'utilisateur est propriétaire du quiz
function is_owner($quiz_id, $user_id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = :quiz_id AND user_id = :user_id");
    $stmt->bindParam(':quiz_id', $quiz_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

// Traitement du formulaire pour ajouter un quiz
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == 'add_quiz') {
    $title = $_POST['title'];
    $cover_image = $_POST['cover_image']; // Récupérer l'URL de l'image de couverture

    try {
        $stmt = $conn->prepare("INSERT INTO quizzes (user_id, title, cover_image) VALUES (:user_id, :title, :cover_image)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':cover_image', $cover_image); // Lier le paramètre de l'image de couverture
        $stmt->execute();
        echo "Quiz ajouté avec succès !";
    } catch (Exception $e) {
        echo "Une erreur s'est produite lors de l'ajout du quiz : " . $e->getMessage();
    }
}

// Traitement du formulaire pour mettre à jour l'image de couverture
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == 'update_cover_image') {
    $quiz_id = $_POST['quiz_id'];
    $cover_image = $_POST['cover_image'];

    if (is_owner($quiz_id, $user_id, $conn)) {
        try {
            $stmt = $conn->prepare("UPDATE quizzes SET cover_image = :cover_image WHERE id = :quiz_id AND user_id = :user_id");
            $stmt->bindParam(':cover_image', $cover_image);
            $stmt->bindParam(':quiz_id', $quiz_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            echo "Image de couverture mise à jour avec succès !";
        } catch (Exception $e) {
            echo "Une erreur s'est produite lors de la mise à jour de l'image de couverture : " . $e->getMessage();
        }
    } else {
        echo "Vous n'êtes pas autorisé à modifier ce quiz.";
    }
}

// Traitement du formulaire pour ajouter une question
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == 'add_question') {
    $question_text = $_POST['question'];
    $quiz_id = $_POST['quiz_id']; // ID du quiz auquel la question appartient
    $answers = $_POST['answers']; // Tableau des réponses
    $correct_answer_index = $_POST['correct_answer']; // Index de la réponse correcte dans le tableau des réponses

    if (is_owner($quiz_id, $user_id, $conn)) {
        try {
            $conn->beginTransaction();

            // Insérer la question dans la table 'questions'
            $stmt = $conn->prepare("INSERT INTO questions (quiz_id, user_id, question_text) VALUES (:quiz_id, :user_id, :question_text)");
            $stmt->bindParam(':quiz_id', $quiz_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':question_text', $question_text);
            $stmt->execute();
            $question_id = $conn->lastInsertId();

            // Insérer les réponses dans la table 'answers'
            $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (:question_id, :answer_text, :is_correct)");

            foreach ($answers as $index => $answer) {
                $is_correct = ($index == $correct_answer_index - 1) ? 1 : 0; // Vérifie si l'index de la réponse correspond à la réponse correcte
                $stmt->bindParam(':question_id', $question_id);
                $stmt->bindParam(':answer_text', $answer);
                $stmt->bindParam(':is_correct', $is_correct);
                $stmt->execute();
            }

            $conn->commit();
            echo "Question ajoutée avec succès !";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Une erreur s'est produite lors de l'ajout de la question : " . $e->getMessage();
        }
    } else {
        echo "Vous n'êtes pas autorisé à ajouter des questions à ce quiz.";
    }
}

// Traitement du formulaire pour supprimer un quiz
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == 'delete_quiz') {
    $quiz_id = $_POST['quiz_id'];

    if (is_owner($quiz_id, $user_id, $conn)) {
        try {
            $conn->beginTransaction();

            // Supprimer les réponses associées
            $stmt = $conn->prepare("DELETE FROM answers WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = :quiz_id)");
            $stmt->bindParam(':quiz_id', $quiz_id);
            $stmt->execute();

            // Supprimer les questions associées
            $stmt = $conn->prepare("DELETE FROM questions WHERE quiz_id = :quiz_id");
            $stmt->bindParam(':quiz_id', $quiz_id);
            $stmt->execute();

            // Supprimer le quiz
            $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = :quiz_id AND user_id = :user_id");
            $stmt->bindParam(':quiz_id', $quiz_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $conn->commit();
            echo "Quiz supprimé avec succès !";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Une erreur s'est produite lors de la suppression du quiz : " . $e->getMessage();
        }
    } else {
        echo "Vous n'êtes pas autorisé à supprimer ce quiz.";
    }
}

// Traitement du formulaire pour modifier un quiz
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == 'edit_quiz') {
    $quiz_id = $_POST['quiz_id'];
    $title = $_POST['title'];
    $cover_image = $_POST['cover_image'];

    if (is_owner($quiz_id, $user_id, $conn)) {
        try {
            $stmt = $conn->prepare("UPDATE quizzes SET title = :title, cover_image = :cover_image WHERE id = :quiz_id AND user_id = :user_id");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':cover_image', $cover_image);
            $stmt->bindParam(':quiz_id', $quiz_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            echo "Quiz mis à jour avec succès !";
        } catch (Exception $e) {
            echo "Une erreur s'est produite lors de la mise à jour du quiz : " . $e->getMessage();
        }
    } else {
        echo "Vous n'êtes pas autorisé à modifier ce quiz.";
    }
}

// Traitement du formulaire pour modifier une question et ses réponses
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == 'edit_question') {
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $answers = $_POST['answers'];
    $correct_answer_index = $_POST['correct_answer'];

    // Vérifier si l'utilisateur est propriétaire de la question
    $stmt = $conn->prepare("SELECT quiz_id FROM questions WHERE id = :question_id AND user_id = :user_id");
    $stmt->bindParam(':question_id', $question_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $quiz_id = $result['quiz_id'] ?? null;

    if ($quiz_id && is_owner($quiz_id, $user_id, $conn)) {
        try {
            $conn->beginTransaction();

            // Mettre à jour la question
            $stmt = $conn->prepare("UPDATE questions SET question_text = :question_text WHERE id = :question_id AND user_id = :user_id");
            $stmt->bindParam(':question_text', $question_text);
            $stmt->bindParam(':question_id', $question_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            // Mettre à jour les réponses
            $stmt = $conn->prepare("UPDATE answers SET answer_text = :answer_text, is_correct = :is_correct WHERE id = :answer_id");

            foreach ($answers as $index => $answer) {
                $is_correct = ($index == $correct_answer_index - 1) ? 1 : 0;
                $stmt->bindParam(':answer_text', $answer['text']);
                $stmt->bindParam(':is_correct', $is_correct);
                $stmt->bindParam(':answer_id', $answer['id']);
                $stmt->execute();
            }

            $conn->commit();
            echo "Question et réponses mises à jour avec succès !";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Une erreur s'est produite lors de la mise à jour de la question : " . $e->getMessage();
        }
    } else {
        echo "Vous n'êtes pas autorisé à modifier cette question.";
    }
}

// Récupérer tous les quiz existants
$stmt = $conn->prepare("SELECT quizzes.*, users.username FROM quizzes INNER JOIN users ON quizzes.user_id = users.id");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les quiz de l'utilisateur connecté
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user_quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_quiz_id = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;

// Récupérer les questions et les réponses en relation avec le quiz sélectionné
if ($selected_quiz_id) {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id");
    $stmt->bindParam(':quiz_id', $selected_quiz_id);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM answers WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = :quiz_id)");
    $stmt->bindParam(':quiz_id', $selected_quiz_id);
    $stmt->execute();
    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $questions = [];
    $answers = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Manager</title>
    <style>
        /* Réinitialisation des marges et paddings par défaut */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-x: hidden; /* Empêche le défilement horizontal */
        }

        /* Style global */
        @font-face {
            font-family: 'Neon';
            src: url('font/Neon.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            background: linear-gradient(45deg, #FF00FF, #00FFFF);
            font-family: 'Neon', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        header, footer {
            width: 100%;
        }

        .header {
            background: #060A19;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box; /* Assure que padding est inclus dans la taille totale */
            height: 125px;
        }

        .footer {
            background: #060A19;
            padding: 10px 20px;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box; /* Assure que padding est inclus dans la taille totale */
            width: 100%;
            text-align: center;
            padding: 10px;
            background-color: #060A19;
            color: white;
            margin-top: auto; /* Permet au footer de se coller au bas de la page */
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

        a {
            text-decoration: none;
        }

        p {
            color: white;
        }

        h2{
            font-size: 2.5rem;
            color: rgba(0, 0, 0, 0.7);
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

        .logout-button {
            background-color: red;
            color: #fff;
            border: 2px solid red;
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

        .logout-button::before {
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

        .logout-button:hover::before {
            opacity: 1;
        }

        .logout-button:hover {
            color: #000;
            background-color: #f00;
            box-shadow: 0 0 20px #f00, 0 0 40px #f00, 0 0 60px #f00, 0 0 80px #f00;
            border-color: #f00;
        }

        .quiz-container {
            display: flex;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            margin-bottom: 200px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            border: 2px solid #00FF00;
            animation: neon-border-green 1.5s infinite alternate;
        }

        @keyframes neon-border-green {
            0% {
                box-shadow: 0 0 10px #00FF00, 0 0 20px #00FF00, 0 0 30px #00FF00, 0 0 40px #00FF00;
            }
            25% {
                box-shadow: 0 0 20px #00FF00, 0 0 30px #00FF00, 0 0 40px #00FF00, 0 0 50px #00FF00;
            }
            50% {
                box-shadow: 0 0 10px #00FF00, 0 0 20px #00FF00, 0 0 30px #00FF00, 0 0 40px #00FF00;
            }
            75% {
                box-shadow: 0 0 20px #00FF00, 0 0 30px #00FF00, 0 0 40px #00FF00, 0 0 50px #00FF00;
            }
            100% {
                box-shadow: 0 0 30px #00FF00, 0 0 40px #00FF00, 0 0 50px #00FF00, 0 0 60px #00FF00;
            }
        }

        .column {
            flex: 1;
            padding: 10px;
            color: white;
        }

        .column h2 {
            margin-top: 0;
            font-size: 24px;
            border-bottom: 2px solid #fff;
            padding-bottom: 10px;
            text-align: center; /* Centre le titre */
        }

        .all-quizzes {
            border-right: 1px solid #ccc;
        }

        .your-quizzes {
            border-right: 1px solid #ccc; /* Ajouter une bordure droite */
            text-align: center; /* Centre tout le contenu de la colonne */
        }

        .your-quizzes form,
        .your-quizzes ul {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        ul {
            list-style: none;
            padding: 0;
            text-align: center; /* Centre les éléments de la liste */
        }

        ul li {
            padding: 10px;
            border-bottom: 1px solid #fff;
        }

        ul li:last-child {
            border-bottom: none;
        }

        ul li a {
            color: #00FFFF;
            text-decoration: none;
        }

        ul li a:hover {
            text-decoration: underline;
        }

        form {
            margin-bottom: 20px;
            text-align: center; /* Centrer le contenu du formulaire */
        }

        form label {
            display: block; /* Faire en sorte que les labels s'affichent sur une nouvelle ligne */
            margin: 10px 0 5px;
            font-size: 14px; /* Réduire la taille de la police des labels */
        }

        form input[type="text"], form textarea, form select {
            width: 80%; /* Réduire la largeur à 80% */
            padding: 8px; /* Réduire le padding */
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-family: 'Neon', sans-serif;
            font-size: 14px; /* Réduire la taille de la police */
            display: inline-block; /* Pour centrer avec text-align: center */
        }

        form input[type="submit"] {
            background-color: #FF00FF;
            border: none;
            padding: 8px 16px; /* Réduire le padding */
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            color: black;
            font-size: 14px; /* Réduire la taille de la police */
            width: auto; /* Ajuster la largeur automatiquement */
            margin-top: 10px;
            font-family: 'Neon', sans-serif;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        form input[type="submit"]::before {
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

        form input[type="submit"]:hover::before {
            opacity: 1;
        }

        form input[type="submit"]:hover {
            background-color: #00FFFF;
            box-shadow: 0 0 20px #00FFFF, 0 0 40px #00FFFF, 0 0 60px #00FFFF, 0 0 80px #00FFFF; /* Ombres cyan pour l'effet néon */
        }

        .delete-button {
            background-color: #ff0000; /* Rouge */
            color: white;
            border: 2px solid #ff0000;
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

        .delete-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 0, 0, 0.2);
            filter: blur(10px);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .delete-button:hover::before {
            opacity: 1;
        }

        .delete-button:hover {
            color: #000;
            background-color: #ff4444; /* Rouge clair */
            box-shadow: 0 0 20px #ff4444, 0 0 40px #ff4444, 0 0 60px #ff4444, 0 0 80px #ff4444;
            border-color: #ff4444;
        }

        form input[type="submit"].edit-button {
            background-color: #00FF00; /* Vert */
            color: black;
            border: 2px solid #00FF00;
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

        form input[type="submit"].edit-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 255, 0, 0.2);
            filter: blur(10px);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        form input[type="submit"].edit-button:hover::before {
            opacity: 1;
        }

        form input[type="submit"].edit-button:hover {
            color: #000;
            background-color: #00FF00; /* Vert clair */
            box-shadow: 0 0 20px #00FF00, 0 0 40px #00FF00, 0 0 60px #00FF00, 0 0 80px #00FF00;
            border-color: #00FF00;
        }

        .questions-answers ul {
            list-style: none;
            padding: 0;
        }

        .questions-answers ul li {
            margin-bottom: 20px;
        }

        .questions-answers ul li strong {
            display: block;
            margin-bottom: 5px;
        }

        .questions-answers ul li ul {
            padding-left: 20px;
        }

        .questions-answers ul li ul li {
            margin-bottom: 5px;
        }

        ul li img {
            width: 50px;
            height: auto;
            margin-left: 10px;
            vertical-align: middle;
        }

        .correct-answer {
            color: #00FF00; /* Vert néon pour la bonne réponse */
            font-weight: bold;
            background-color: rgba(0, 255, 0, 0.1); /* Légère couleur de fond verte */
            padding: 5px;
            border-radius: 5px;
        }

    </style>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</head>
<body>

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
            <a href="index.php" class="logout-button">LOGOUT</a> <!-- Bouton pour se déconnecter -->
        </div>
    </div>
</header>

<h2>Bonjour <?php echo htmlspecialchars($username); ?>, bienvenue sur votre dashboard</h2>

<div class="quiz-container">
    <div class="column all-quizzes">
        <h2>Tous les Quizz</h2>
        <ul>
        <?php foreach ($quizzes as $quiz): ?>
            <li>
                <a href="?quiz_id=<?php echo $quiz['id']; ?>">
                    <?php echo htmlspecialchars($quiz['title']); ?>
                    <?php if (!empty($quiz['cover_image'])): ?>
                        <img src="<?php echo htmlspecialchars($quiz['cover_image']); ?>" alt="<?php echo htmlspecialchars($quiz['title']); ?>">
                    <?php endif; ?>
                </a>
                (Créé par <?php echo htmlspecialchars($quiz['username']); ?>)
            </li>
        <?php endforeach; ?>
        </ul>
    </div>

    <div class="column your-quizzes">
        <h2>Vos Quizz</h2>
        <form method="post">
            <input type="hidden" name="form_type" value="add_quiz">
            <label for="title">Titre:</label>
            <input type="text" id="title" name="title" required><br>
            <label for="cover_image">Image de couverture (URL):</label>
            <input type="text" id="cover_image" name="cover_image"><br>
            <input type="submit" value="Ajouter un Quizz">
        </form>

        <ul>
            <?php foreach ($user_quizzes as $quiz): ?>
                <li>
                    <div>
                        <strong><?php echo htmlspecialchars($quiz['title']); ?></strong>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="form_type" value="update_cover_image">
                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                            <label for="cover_image_<?php echo $quiz['id']; ?>">Image de couverture (URL):</label>
                            <input type="text" id="cover_image_<?php echo $quiz['id']; ?>" name="cover_image">
                            <input type="submit" value="Mettre à jour">
                        </form>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="form_type" value="delete_quiz">
                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                            <input type="submit" value="Supprimer le quizz" class="delete-button">
                        </form>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="form_type" value="edit_quiz">
                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                            <label for="title_<?php echo $quiz['id']; ?>">Titre:</label>
                            <input type="text" id="title_<?php echo $quiz['id']; ?>" name="title" value="<?php echo htmlspecialchars($quiz['title']); ?>" required>
                            <label for="cover_image_<?php echo $quiz['id']; ?>">Image de couverture (URL):</label>
                            <input type="text" id="cover_image_<?php echo $quiz['id']; ?>" name="cover_image" value="<?php echo htmlspecialchars($quiz['cover_image']); ?>">
                            <input type="submit" value="Modifier le titre" class="edit-button">
                        </form>
                        <?php if (!empty($quiz['cover_image'])): ?>
                            <img src="<?php echo htmlspecialchars($quiz['cover_image']); ?>" alt="<?php echo htmlspecialchars($quiz['title']); ?>">
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($selected_quiz_id && is_owner($selected_quiz_id, $user_id, $conn)): ?>
            <h2>Ajouter une Question à <?php echo htmlspecialchars($user_quizzes[array_search($selected_quiz_id, array_column($user_quizzes, 'id'))]['title'] ?? ''); ?></h2>
            <form method="post">
                <input type="hidden" name="form_type" value="add_question">
                <input type="hidden" name="quiz_id" value="<?php echo $selected_quiz_id; ?>">
                <label for="question">Question:</label><br>
                <textarea id="question" name="question" rows="4" cols="50" required></textarea><br>
                
                <label for="answer1">Réponse 1:</label><br>
                <input type="text" id="answer1" name="answers[]" required><br>
                
                <label for="answer2">Réponse 2:</label><br>
                <input type="text" id="answer2" name="answers[]" required><br>
                
                <label for="answer3">Réponse 3:</label><br>
                <input type="text" id="answer3" name="answers[]" required><br>
                
                <label for="answer4">Réponse 4:</label><br>
                <input type="text" id="answer4" name="answers[]" required><br>
                
                <label for="correct_answer">Réponse Correcte:</label><br>
                <select id="correct_answer" name="correct_answer" required>
                    <option value="1">Réponse 1</option>
                    <option value="2">Réponse 2</option>
                    <option value="3">Réponse 3</option>
                    <option value="4">Réponse 4</option>
                </select><br>
                
                <input type="submit" value="Ajouter la Question">
            </form>
        <?php endif; ?>
    </div>

    <div class="column questions-answers">
        <?php if ($selected_quiz_id): ?>
            <h2>Questions et Réponses pour 
            <?php 
                $quizIndex = array_search($selected_quiz_id, array_column($quizzes, 'id'));
                echo htmlspecialchars($quizIndex !== false ? $quizzes[$quizIndex]['title'] : '');
            ?></h2>
            <ul>
                <?php foreach ($questions as $question): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($question['question_text']); ?></strong>
                        <ul>
                            <?php 
                            $question_answers = array_filter($answers, function($answer) use ($question) {
                                return $answer['question_id'] == $question['id'];
                            });
                            ?>

                            <?php foreach ($question_answers as $answer): ?>
                                <li class="<?php echo $answer['is_correct'] ? 'correct-answer' : ''; ?>">
                                    <?php echo htmlspecialchars($answer['answer_text']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if ($question['user_id'] == $user_id): ?>
                            <form method="post">
                                <input type="hidden" name="form_type" value="edit_question">
                                <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                <label for="question_text_<?php echo $question['id']; ?>">Question:</label>
                                <textarea id="question_text_<?php echo $question['id']; ?>" name="question_text" rows="4" cols="50" required><?php echo htmlspecialchars($question['question_text']); ?></textarea><br>

                                <?php foreach ($question_answers as $index => $answer): ?>
                                    <label for="answer_<?php echo $answer['id']; ?>">Réponse <?php echo $index + 1; ?>:</label>
                                    <input type="text" id="answer_<?php echo $answer['id']; ?>" name="answers[<?php echo $index; ?>][text]" value="<?php echo htmlspecialchars($answer['answer_text']); ?>" required><br>
                                    <input type="hidden" name="answers[<?php echo $index; ?>][id]" value="<?php echo $answer['id']; ?>">
                                <?php endforeach; ?>

                                <label for="correct_answer_<?php echo $question['id']; ?>">Réponse Correcte:</label>
                                <select id="correct_answer_<?php echo $question['id']; ?>" name="correct_answer" required>
                                    <?php foreach ($question_answers as $index => $answer): ?>
                                        <option value="<?php echo $index + 1; ?>" <?php echo $answer['is_correct'] ? 'selected' : ''; ?>>Réponse <?php echo $index + 1; ?></option>
                                    <?php endforeach; ?>
                                </select><br>

                                <input type="submit" value="Modifier">
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<footer>
    <div class="footer">
        <p>&copy; 2024 Quiz Night. Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>
