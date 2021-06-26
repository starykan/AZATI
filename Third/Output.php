<?php

$conn = new PDO('mysql:host=localhost;dbname=Azati', 'root', '123');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = $conn->prepare('SELECT id, speciality FROM specialities');
$query->execute();
$specialityList = $query->fetchAll(\PDO::FETCH_ASSOC);


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
    <script defer src="/js/jquery-3.6.0.js"></script>
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
    <?php var_dump($personData);?>
</ul>
</body>
</html>


<form action = "General.php" method="post" enctype="multipart/form-data">
    <?php
//    if ($errorMessage !== null){
//        echo $errorMessage;
//    }
    ?>
    <table>
        <tr>
            <td><label for="Name">Имя</label></td>
            <td>
                <input id="Name" name="Name" placeholder="Имя" value="<?php echo htmlentities($personData[0]['name']); ?>">
            </td>
        <tr><td><label for="Family">Фамилия</label></td>
            <td>
                <input id="Family" name="Family" placeholder="Фамилия" value="<?php echo htmlentities($personData[0]['family']); ?>">
            </td>
        <tr><td><label for="Patronymic">Отчество</label></td>
            <td>
                <input id="Patronymic" name="Patronymic" placeholder="Отчество" value="<?php echo htmlentities($personData[0]['patronymic']); ?>">
            </td></tr>
        <tr><td><label for="Speciality">Специальность</label></td>
            <td>
                <select id="Speciality" name="Speciality">
                    <?php foreach ($specialityList as $speciality) { ?>
                        <option
                                <?php if ($personData[0]['id_speciality']==$speciality['id']) { ?>
                                    selected="selected"
                                <?php } ?>
                                value="<?php echo $speciality['id']; ?>"
                        >
                            <?php echo $speciality['speciality']; ?>
                        </option>
                    <?php } ?>
                </select>
            </td></tr>
        <tr><td><label for="Skills">Навыки</label></td>
            <td>
                <p><textarea id="Skills" rows="10" cols="30" name="Skills"></textarea></p>
            </td></tr>
        <tr><td><label for="inputfile">Загрузка файла</label></td>
            <td>
                <input type="file" id="inputfile" name="inputfile"></br>
            </td></tr>
        <tr><td colspan=2><input type="submit" value="Ввод"></td></tr>
    </table>
</form>