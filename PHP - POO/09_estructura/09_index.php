<?php

require 'Course.php';

$course = new Course(

    title: 'Curso Profesional de PHP y Laravel',

    subtitle: 'Aprende PHP y LARAVEL desde cero',

    description: 'Lorem ipsum dolor sit amet consectetur adipisicing elit...',

    tags: ['Curso de HTML', 'Curso de PYTHON', 'Curso de LARAVEL', 'Curso de GITHUB'],

    type: CourseType::FREE

);

?>

<!DOCTYPE html>

<html lang="es">

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

        background: linear-gradient(135deg, #dfe9f3, #ffffff);

        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

    }

    .container1 {

        width: 700px;

        background: #ffffff;

        padding: 40px;

        border-radius: 16px;

        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);

        text-align: left;

        animation: fadeIn 0.6s ease-out;

    }

    @keyframes fadeIn {

        from {

            opacity: 0;

            transform: translateY(20px);

        }

        to {

            opacity: 1;

            transform: translateY(0);

        }

    }

    h1 {

        margin-top: 0;

        font-size: 30px;

        color: #222;

        font-weight: 700;

    }

    h2 {

        font-size: 22px;

        color: #0077cc;

        margin-bottom: 15px;

        font-weight: 600;

    }

    p {

        color: #444;

        line-height: 1.7;

        font-size: 16px;

        margin-bottom: 25px;

    }

    .etiquetass {

        margin-top: 25px;

    }

    .etiquetass strong {

        font-size: 18px;

        color: #333;

    }

    .etiquetass ul {

        list-style: none;

        padding-left: 0;

        margin-top: 10px;

    }

    .etiquetass li {

        background: #eef7ff;

        margin-bottom: 10px;

        padding: 10px 14px;

        border-radius: 8px;

        border-left: 5px solid #0077cc;

        font-weight: 600;

        color: #005fa3;

        transition: transform 0.2s ease, background 0.2s ease;

    }

    .etiquetass li:hover {

        transform: translateX(6px);

        background: #dff0ff;

    }

    </style>

</head>

<body>

    <div class="container1">

        <?= $course ?>

    </div>

</body>

</html>