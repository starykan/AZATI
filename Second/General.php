<?php

$conn = new PDO('mysql:host=localhost;dbname=Azati', 'root', '123');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = $conn->prepare('SELECT id, speciality FROM specialty');
$query->execute();
$specialityList = $query->fetchAll(\PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // TODO: validate fields length
        // TODO: rename tables and columns

        $errorMessage = null;
        if (empty($_POST['Name']) || empty($_POST['Family']) || empty($_POST['Patronymic']) || empty($_POST['Specialty']) || empty($_POST['Skills'])) {
            $errorMessage = "Не все данные введены.";
        }
        if ($_FILES['inputfile']['error'] != UPLOAD_ERR_OK || $_FILES['inputfile']['type'] != 'image/jpeg') {
            $errorMessage = "Загрузите файл .jpeg.";
        }
        $rawSkillList = $_POST['Skills'];
        $skillListRaw = explode(",", $rawSkillList);
        $skillList = [];
        foreach ($skillListRaw as $skill) {
            $skill = trim($skill);
            if (!empty($skill)) {
                $skillList[] = $skill;
            }
        }
        if (empty($skillList)) {
            $errorMessage = 'введите навыки через запятую';
        }
        if (null === $errorMessage) {
            $filename = uniqid() . '.jpg';
            $targetPath = dirname(__FILE__) . '/uploads/' . $filename;
            if (!move_uploaded_file($_FILES['inputfile']['tmp_name'], $targetPath)) {
                throw new \Exception('file not uploaded');
            }

            $stmt = $conn->prepare("INSERT INTO people (name, family, patronymic, id_specialty, path) VALUE (:name, :family, :patronymic, :id_specialty, :path)");

            $name = $_POST['Name'];
            $family = $_POST['Family'];
            $patronymic = $_POST['Patronymic'];
            $idSpecialty = $_POST['Specialty'];

            $stmt->execute([
                'name' => $name,
                'family' => $family,
                'patronymic' => $patronymic,
                'path' => $filename,
                'id_specialty' => $idSpecialty,
            ]);
            $peopleId = $conn->lastInsertId();

            $skillIds = [];
            $wordInsertStmt = $conn->prepare('INSERT INTO skill (skill) VALUE (:skill)');
            $selectQuery = $conn->prepare('SELECT id FROM skill WHERE skill = :skill');
            foreach ($skillList as $skill) {
                $selectQuery->execute(['skill' => $skill]);
                if ($row = $selectQuery->fetch()) {
                    $skillIds[] = $row['id'];
                    continue;
                }
                $wordInsertStmt->execute(['skill' => $skill]);
                $skillIds[] = $conn->lastInsertId();
            }
            foreach ($skillIds as $skillId) {
                $conn
                    ->prepare('INSERT INTO people_skill (id_people, id_skill) VALUES (:people, :skill)')
                    ->execute(['skill' => $skillId, 'people' => $peopleId]);
            }
        }
    } catch (Exception $e) {
        echo "Ошибка: " . $e->getMessage();
    }
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
<form action = "General.php" method="post" enctype="multipart/form-data">
    <?php
    if ($errorMessage !== null){
        echo $errorMessage;
    }
    ?>
    <table>
        <tr>
            <td><label for="Name">Имя</label></td>
            <td>
                <input id="Name" name="Name" placeholder="Имя">
            </td>
        <tr><td><label for="Family">Фамилия</label></td>
            <td>
                <input id="Family" name="Family" placeholder="Фамилия">
            </td>
        <tr><td><label for="Patronymic">Отчество</label></td>
            <td>
                <input id="Patronymic" name="Patronymic" placeholder="Отчество">
            </td></tr>
        <tr><td><label for="Specialty">Специальность</label></td>
            <td>
                <select id="Specialty" name="Specialty">
                    <?php foreach ($specialityList as $speciality) { ?>
                        <option value="<?php echo $speciality['id']; ?>"><?php echo $speciality['speciality']; ?></option>
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
</body>
</html>