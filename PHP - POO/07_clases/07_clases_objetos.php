<?php
    require '07_course.php';
    $course = new Course(
        'Curso programación básica ',
        'PHP Básico',
        'Este es el curso de programación',
        ['PHP', 'JavaScript', 'HTML'],
    );
    $course->agregarLenguaje('Java');
    $course->agregarLenguaje('Python');
    $course->actualizarTitulo('Curso programación avanzada');
        
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $course->obtenerTitulo() ?></title>
</head>
<body>
    <h1><?= $course->obtenertitulo() ?></h1>
    <h2><?= $course->obtenersubtitulo() ?></h2>
    <p>
        <?= $course->obtenercontenido() ?>
    </p>
    <p>
        <strong> lenguajes </strong>
        <ul>
            <?php foreach ($course->obtenerLenguaje() as $lenguaje):?>
                <li><?=$lenguaje ?></li>
            <?php endforeach;?>               
        </ul>             
    </p>
</body>
</html>