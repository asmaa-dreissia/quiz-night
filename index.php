<?php
include 'config.php';

// Récupérer la liste des quiz existants 
$stmt = $conn->prepare("SELECT quizzes.id AS quiz_id, quizzes.title AS quiz_title, quizzes.cover_image, questions.id AS question_id, questions.question_text, answers.id AS answer_id, answers.answer_text, answers.is_correct FROM quizzes JOIN questions ON quizzes.id = questions.quiz_id JOIN answers ON questions.id = answers.question_id");
$stmt->execute();
$quiz_questions_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les données par quiz
$quizzes_data = [];

foreach ($quiz_questions_answers as $qa) {
    $quizzes_data[$qa['quiz_id']]['title'] = $qa['quiz_title'];
    $quizzes_data[$qa['quiz_id']]['cover_image'] = $qa['cover_image'];
    $quizzes_data[$qa['quiz_id']]['questions'][$qa['question_id']]['question_text'] = $qa['question_text'];
    $quizzes_data[$qa['quiz_id']]['questions'][$qa['question_id']]['answers'][] = [
        'answer_text' => $qa['answer_text'],
        'is_correct' => $qa['is_correct']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Index</title>
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
        }

        footer {
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

        .quiz button {
            background-color: #19aeff;
            color: black;
            border: 2px solid #19aeff;
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

        .buttons button::before, .quiz button::before {
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

        .buttons button:hover::before, .quiz button:hover::before {
            opacity: 1;
        }

        .buttons button:hover, .quiz button:hover {
            color: #000;
            background-color: #0ff;
            box-shadow: 0 0 20px #0ff, 0 0 40px #0ff, 0 0 60px #0ff, 0 0 80px #0ff;
            border-color: #0ff;
        }

        h1 {
            text-align: center;
            font-size: 2.5rem;
            color: rgba(0, 0, 0, 0.7);
        }

        .quiz-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            box-sizing: border-box;
            margin-bottom: 50px; /* Ajout de la marge en bas pour espacer les cartes du footer */
        }

        .quiz {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .quiz h3 {
            margin: 0 0 10px 0;
            font-size: 24px; /* Augmenter la taille de la police pour le titre */
            color: white;
        }

        .quiz img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .quiz ul {
            list-style-type: none;
            padding: 0;
            width: 100%;
            color: white;
        }

        .quiz ul li {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px; /* Augmenter la marge entre les questions */
        }

        .quiz ul li ul {
            padding-left: 0;
            margin-top: 10px;
        }

        .quiz ul li ul li {
            display: block;
            width: auto;
            margin-bottom: 10px; /* Augmenter la marge entre les réponses */
        }

        .separator {
            border: 0;
            border-top: 1px solid #fff; /* Couleur blanche */
            margin: 10px auto; /* Espacement vertical autour de la barre et centrage horizontal */
            width: 80%; /* Largeur de la barre */
            opacity: 0.5; /* Opacité pour rendre la barre un peu transparente */
        }

        .correct-answer {
            color: #00FF00; /* Vert néon pour la bonne réponse */
            font-weight: bold;
            background-color: rgba(0, 255, 0, 0.1); /* Légère couleur de fond verte */
            padding: 5px;
            border-radius: 5px;
            text-shadow: 0 0 5px #00FF00, 0 0 10px #00FF00, 0 0 15px #00FF00, 0 0 20px #00FF00, 0 0 25px #00FF00, 0 0 30px #00FF00, 0 0 35px #00FF00;
        }

        /* Effet néon bleu foncé */
        .neon-blue {
            border: 2px solid #227AFF;
            animation: neon-border-blue 1.5s infinite alternate;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Ajout de la transition */
        }

        @keyframes neon-border-blue {
            0% {
                box-shadow: 0 0 10px #227AFF, 0 0 20px #227AFF, 0 0 30px #227AFF, 0 0 40px #227AFF;
            }
            25% {
                box-shadow: 0 0 20px #227AFF, 0 0 30px #227AFF, 0 0 40px #227AFF, 0 0 50px #227AFF;
            }
            50% {
                box-shadow: 0 0 10px #227AFF, 0 0 20px #227AFF, 0 0 30px #227AFF, 0 0 40px #227AFF;
            }
            75% {
                box-shadow: 0 0 20px #227AFF, 0 0 30px #227AFF, 0 0 40px #227AFF, 0 0 50px #227AFF;
            }
            100% {
                box-shadow: 0 0 30px #227AFF, 0 0 40px #227AFF, 0 0 50px #227AFF, 0 0 60px #227AFF;
            }
        }

        .neon-blue:hover {
            transform: scale(1.05); /* Ajout de l'effet d'agrandissement au survol */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Ajout d'un effet d'ombre */
        }

        /* Effet néon rose */
        .neon-pink {
            border: 2px solid #FF00FF;
            animation: neon-border-pink 1.5s infinite alternate;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Ajout de la transition */
        }

        @keyframes neon-border-pink {
            0% {
                box-shadow: 0 0 10px #FF00FF, 0 0 20px #FF00FF, 0 0 30px #FF00FF, 0 0 40px #FF00FF;
            }
            25% {
                box-shadow: 0 0 20px #FF00FF, 0 0 30px #FF00FF, 0 0 40px #FF00FF, 0 0 50px #FF00FF;
            }
            50% {
                box-shadow: 0 0 10px #FF00FF, 0 0 20px #FF00FF, 0 0 30px #FF00FF, 0 0 40px #FF00FF;
            }
            75% {
                box-shadow: 0 0 20px #FF00FF, 0 0 30px #FF00FF, 0 0 40px #FF00FF, 0 0 50px #FF00FF;
            }
            100% {
                box-shadow: 0 0 30px #FF00FF, 0 0 40px #FF00FF, 0 0 50px #FF00FF, 0 0 60px #FF00FF;
            }
        }

        .neon-pink:hover {
            transform: scale(1.05); /* Ajout de l'effet d'agrandissement au survol */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Ajout d'un effet d'ombre */
        }


        /* Media Queries pour rendre le design responsive */
        @media (max-width: 992px) {
            .quiz-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px; /* Ajustement de l'espacement pour les écrans moyens */
            }

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

            .quiz {
                padding: 10px;
            }
        }

        @media (max-width: 600px) {
            .quiz-container {
                grid-template-columns: 1fr;
                gap: 20px; /* Ajustement de l'espacement pour les petits écrans */
            }

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

            .quiz h3 {
                font-size: 20px;
            }

            .quiz ul li {
                font-size: 16px;
            }
        }
    </style>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }

        function showCorrectAnswers(quizId) {
            var correctAnswers = document.querySelectorAll('.quiz[data-quiz-id="' + quizId + '"] .answer');
            correctAnswers.forEach(function(answer) {
                if (answer.dataset.correct === '1') {
                    answer.classList.add('correct-answer');
                }
            });
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
            </div>
        </div>
    </header>
    <section>
        <h1>Quiz Index</h1>
        <div class="quiz-container">
            <?php 
            $counter = 0; 
            foreach ($quizzes_data as $quiz_id => $quiz): 
                $neon_class = ($counter % 2 == 0) ? 'neon-blue' : 'neon-pink';
                $counter++;
            ?>
                <div class="quiz <?php echo $neon_class; ?>" data-quiz-id="<?php echo htmlspecialchars($quiz_id); ?>">
                    <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                    <?php if (!empty($quiz['cover_image'])): ?>
                        <img src="<?php echo htmlspecialchars($quiz['cover_image']); ?>" alt="<?php echo htmlspecialchars($quiz['title']); ?>">
                    <?php endif; ?>
                    <ul>
                        <?php foreach ($quiz['questions'] as $question_id => $question): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($question['question_text']); ?></strong>
                                <ul>
                                    <?php foreach ($question['answers'] as $answer): ?>
                                        <li class="answer" data-correct="<?php echo $answer['is_correct'] ? '1' : '0'; ?>">
                                            <?php echo htmlspecialchars($answer['answer_text']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <hr class="separator"> <!-- Ajouter cette ligne -->
                        <?php endforeach; ?>
                    </ul>
                    <button onclick="showCorrectAnswers(<?php echo htmlspecialchars($quiz_id); ?>)">Afficher les bonnes réponses</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <footer>
        <div class="footer">
            <p>&copy; 2024 Quiz Night. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
