<?php
header("Content-Type: application/json; charset=UTF-8");

$conn = new PDO('mysql:host=localhost;dbname=Azati', 'root', '123');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['message' => '405 Method Not Allowed']);
    exit;
}

if (empty($_POST['Id']) || empty($_POST['Name']) || empty($_POST['Family']) || empty($_POST['Patronymic']) || empty($_POST['Speciality']) || empty($_POST['Skills'])) {
    $errorMessage = "Не все данные введены.";
}
$peopleId = $_POST['Id'];
$name = $_POST['Name'];
$family = $_POST['Family'];
$patronymic = $_POST['Patronymic'];
$idSpeciality = $_POST['Speciality'];

$picUploaded = !empty($_FILES['inputfile']) /* field exists */ && $_FILES['inputfile']['error'] != UPLOAD_ERR_NO_FILE /* file is uploaded */;
if ($picUploaded) {
    if ($_FILES['inputfile']['error'] != UPLOAD_ERR_OK || $_FILES['inputfile']['type'] != 'image/jpeg') {
        $errorMessage = "Загрузите файл .jpeg.";
    }
}

$stmt = $conn->prepare('select * from people where id = :id');
$stmt->execute(['id' => $peopleId]);
$oldData = $stmt->fetch();
if (false === $oldData) {
    $errorMessage = "Неверный ID.";
}

if (!empty($errorMessage)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['message' => $errorMessage]);
    exit;
}
$conn->beginTransaction();

if ($picUploaded) {
    $filename = uniqid() . '.jpg';
    $targetPath = dirname(dirname(__FILE__)) . '/uploads/' . $filename;
    if (!move_uploaded_file($_FILES['inputfile']['tmp_name'], $targetPath)) {
        throw new \Exception('file not uploaded');
    }
    $stmt = $conn->prepare('UPDATE people SET name = :name, family = :family, patronymic = :patronymic, id_speciality = :id_speciality, path = :path where id = :id');
    $stmt->execute([
        'id' => $peopleId,
        'name' => $name,
        'family' => $family,
        'patronymic' => $patronymic,
        'id_speciality' => $idSpeciality,
        'path' => $filename,
    ]);
}
else{
    $stmt = $conn->prepare('UPDATE people SET name = :name, family = :family, patronymic = :patronymic, id_speciality = :id_speciality where id = :id');
    $stmt->execute([
        'id' => $peopleId,
        'name' => $name,
        'family' => $family,
        'patronymic' => $patronymic,
        'id_speciality' => $idSpeciality,
    ]);
}

$query = $conn->prepare('SELECT skill.id FROM people_skill JOIN skill on skill.id = people_skill.id_skill where id_people = :id');
$query->execute(['id' => $peopleId]);
$skillFromDB = $query->fetchAll();
$existingSkills = array_column($skillFromDB, 'id');

$rawSkillList = $_POST['Skills'];
$skillListRaw = explode(",", $rawSkillList);
$newSkills = [];
foreach ($skillListRaw as $skillName) {
    $skillName = trim($skillName);
    if (empty($skillName)) {
        continue;
    }
    $stmt = $conn->prepare('SELECT id FROM skill WHERE skill = :skill');
    $stmt->execute(['skill' => $skillName]);
    if ($row = $stmt->fetch()) {
        $newSkills[] = $row['id'];
        continue;
    }
    $insertStmt = $conn->prepare('INSERT INTO skill (skill) VALUE (:skill)');
    $insertStmt->execute(['skill' => $skillName]);
    $newSkills[] = $conn->lastInsertId();
}

$deletingSkills = array_diff($existingSkills, $newSkills);
$insertingSkills = array_diff($newSkills, $existingSkills);

foreach ($deletingSkills as $skillId) {
    $conn
        ->prepare('DELETE FROM people_skill where id_skill = :skillId and id_people =:peopleId')
        ->execute(['peopleId' => $peopleId,'skillId' => $skillId]);
}
foreach ($insertingSkills as $skillId){
    $conn
        ->prepare('INSERT INTO people_skill (id_people,id_skill) value (:peopleId, :skillId)')
        ->execute(['peopleId' => $peopleId, 'skillId' => $skillId]);
}

$conn->commit();

if (!empty($picUploaded)) {
    unlink(dirname(dirname(__FILE__)) . '/uploads/' . $oldData['path']);
}

$query = $conn->prepare('SELECT people.*, specialities.speciality, skill.skill
FROM people
	JOIN specialities ON specialities.id = people.id_speciality
    join people_skill on people_skill.id_people = people.id
    join skill on skill.id = people_skill.id_skill

WHERE
    people.id = :id');
$query->execute(['id' => $peopleId]);
$personData = $query->fetchAll(\PDO::FETCH_ASSOC);

header("HTTP/1.1 200 OK");
echo json_encode([
    'id' => $personData[0]['id'],
    'name' => $personData[0]['name'],
    'family' => $personData[0]['family'],
    'patronymic' => $personData[0]['patronymic'],
    'speciality' => [
        'id' => $personData[0]['id_speciality'],
        'name' => $personData[0]['speciality'],
    ],
    'skills' => implode(', ', array_column($personData, 'skill')),
    'imageUrl' => '/uploads/' . $personData[0]['path'],
]);
