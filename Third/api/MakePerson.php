<?php

$conn = new PDO('mysql:host=localhost;dbname=Azati', 'root', '123');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['message' => '405 Method Not Allowed']);
    exit;
}

$errorMessage = null;
if (empty($_POST['Name']) || empty($_POST['Family']) || empty($_POST['Patronymic']) || empty($_POST['Speciality']) || empty($_POST['Skills'])) {
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
if (!empty($errorMessage)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['message' => $errorMessage], JSON_UNESCAPED_UNICODE);
    exit;
}
$filename = uniqid() . '.jpg';
$targetPath = dirname(dirname(__FILE__)) . '/uploads/' . $filename;
if (!move_uploaded_file($_FILES['inputfile']['tmp_name'], $targetPath)) {
    throw new \Exception('file not uploaded');
}

$stmt = $conn->prepare("INSERT INTO people (name, family, patronymic, id_speciality, path) VALUE (:name, :family, :patronymic, :id_speciality, :path)");

$name = $_POST['Name'];
$family = $_POST['Family'];
$patronymic = $_POST['Patronymic'];
$idSpeciality = $_POST['Speciality'];

$stmt->execute([
    'name' => $name,
    'family' => $family,
    'patronymic' => $patronymic,
    'path' => $filename,
    'id_speciality' => $idSpeciality,
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


$query = $conn->prepare('SELECT people.*, specialities.speciality, skill.skill
    FROM people
        JOIN specialities ON specialities.id = people.id_speciality
        join people_skill on people_skill.id_people = people.id
        join skill on skill.id = people_skill.id_skill
    
    WHERE
    people.id = :id'
);
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
], JSON_UNESCAPED_UNICODE);

