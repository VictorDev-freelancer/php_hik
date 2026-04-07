<?php

require '08_course.php';

require '08_Author.php';

$author = new Author(

    name: 'Juan Pérez',

    email: 'juanperez@example.com',

    bio: 'Desarrollador backend con 10 años de experiencia en PHP y Laravel.'

);

$course = new Course(

    title: 'Curso Profesional de PHP y Laravel',

    subtitle: 'Aprende PHP y LARAVEL desde cero',

    description: 'Lorem ipsum dolor sit amet consectetur adipisicing elit...',

    tags: ['Curso de HTML', 'Curso de PYTHON', 'Curso de LARAVEL', 'Curso de GITHUB'],

    author: $author

);

$author->updateName('Juan P. Ramírez');

$course->addTags('Frameworks');

$course->addTags('Desarrollo de Software');

$course->addTags('');

$course->addTags('PHP');

$course->updateTitle('Nuevo Curso Avanzado de PHP');

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $course->title ?></title>

    <style>

        body {

            margin: 0;

            height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            background: linear-gradient(135deg, #ece9e6, #ffffff);

            font-family: Arial, Helvetica, sans-serif;

        }

        .container1 {

            width: 650px;

            min-height: 520px;

            background: #fff;

            padding: 30px;

            border-radius: 12px;

            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);

            text-align: center;

        }

        h1 {

            margin-top: 0;

            font-size: 28px;

            color: #333;

        }

        .sub-title {

            font-size: 20px;

            color: #0077cc;

            margin-bottom: 20px;

        }

        p {

            color: #444;

            line-height: 1.6;

        }

        .etiquetass {

            text-align: left;

            margin-top: 20px;

        }

        .etiquetass ul {

            list-style: none;

            padding-left: 0;

        }

        .etiquetass li {

            background: #f0f8ff;

            margin-bottom: 8px;

            padding: 8px 12px;

            border-radius: 6px;

            border-left: 4px solid #0077cc;

            font-weight: bold;

        }

    </style>

</head>

<body>

    <!-- Forma personalizada -->

    <!-- Forma profesional -->

    <div class="container1">

        <h1>Bienvenido al <?= $course->title ?></h1>

        <h2 class="sub-title"><?= $course->subtitle ?></h2>

        <p><?= $course->description ?></p>

        <div class="etiquetass">

            <strong>Etiquetas:</strong>

            <ul>

                <?php foreach ($course->tags as $tag): ?>

                    <li><?= $tag ?></li>

                <?php endforeach; ?>

            </ul>

        </div>

        <!-- Ejercicio -->

        <div class="autor">

            <?= $course->author ?>

        </div>

    </div>

    <!-- Forma Rapida -->

    <!--<div class="container1">  

        <?= $course ?>

    </div>-->

</body>

</html>