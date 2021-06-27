<?php
//TODO: CSS
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
    <title>HTML-форма изменения новых записей</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>table1</title>
</head>
<body>
<img id="Picture" src="/uploads/<?php echo $personData[0]['path']; ?>"><br>
<form action = "/api/PutPerson.php" method="post" enctype="multipart/form-data" id="person_form">
    <div id="messages" hidden></div>
    <input type="hidden" id="Id" name="Id" placeholder="Id" value="<?php echo htmlentities($personData[0]['id']); ?>">
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
                <p><textarea id="Skills" rows="10" cols="30" name="Skills"><?php
                        echo implode(', ', array_column($personData, 'skill'));
                        ?></textarea></p>
            </td></tr>
        <tr><td><label for="inputfile">Загрузка файла</label></td>
            <td>
                <input type="file" id="inputfile" name="inputfile"></br>
            </td></tr>
        <tr><td colspan=2><input type="submit" value="Ввод"></td></tr>
    </table>
</form>
<script>
var form = document.getElementById('person_form');
form.onsubmit = async (e) => {
    e.preventDefault();
    const form = e.currentTarget;
    const url = form.action;
    const method = form.method;
    try {
        const formData = new FormData(form);
        fetch(url, {
            method: method,
            body: formData
        })
        .then(response => {
            console.log(response);
            if (response.status !== 200) {
                return response.json().then(data => {
                    const messages = document.getElementById("messages");
                    messages.removeAttribute("hidden");
                    console.log("message: " + data);
                    messages.textContent = data.message;
                });
            }
            if (response.status === 200) {
                return response.json().then(data => {
                    const messages = document.getElementById("messages");
                    messages.removeAttribute("hidden");
                    // messages.setAttribute("hidden", "hidden");
                    messages.textContent = 'Успешно сохранено!';
                    document.getElementById("Name").value = data.name;
                    document.getElementById("Family").value = data.family;
                    document.getElementById("Patronymic").value = data.patronymic;
                    document.getElementById("Speciality").value = data.speciality.id;
                    document.getElementById("Skills").value = data.skills;
                    document.getElementById("Picture").src = data.imageUrl;
                    document.getElementById("inputfile").value = null;
                });
            }
        });
    } catch (error) {
        console.error(error);
    }
}
</script>
</body>
</html>


