<?php

$conn = new PDO('mysql:host=localhost;dbname=Azati', 'root', '123');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (empty($_GET['id'])) {
    exit('no id');
}
$id = $_GET['id'];

$query = $conn->prepare('SELECT people.*, specialities.speciality, skill.skill
FROM people
	JOIN specialities ON specialities.id = people.id_speciality
    join people_skill on people_skill.id_people = people.id
    join skill on skill.id = people_skill.id_skill

WHERE
    people.id = :id');
$query->execute(['id' => $id]);
$personData = $query->fetchAll(\PDO::FETCH_ASSOC);
if (empty($personData)) {
    exit('no person');
}
?>
<html>
<head>
    <title>HTML-форма изменения новых записей</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>table1</title>
</head>
<body>
<img src="/uploads/<?php echo $personData[0]['path']; ?>"><br>
<b>Имя: </b><?php echo $personData[0]['name']; ?><br>
<b>Фамилия: </b><?php echo $personData[0]['family']; ?><br>
<b>Отчество: </b><?php echo $personData[0]['patronymic']; ?><br>
<b>Специальность: </b><?php echo $personData[0]['speciality']; ?><br>
<b>Навыки: </b>
<ul>
<?php foreach ($personData as $skill) {?>
    <li><?php echo $skill['skill'] ?></li>
<?php }; ?>
</ul>
</body>
</html>