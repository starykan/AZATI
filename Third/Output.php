<?php

$conn = new PDO('mysql:host=localhost;dbname=Azati', 'root', '123');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = $conn->prepare('SELECT id, speciality FROM specialities');
$query->execute();
$specialityList = $query->fetchAll(\PDO::FETCH_ASSOC);
?>
<html>
<head>
    <title>HTML-форма изменения новых записей</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>table1</title>
</head>
<body>
<div class="container">
    <h1>Анкета</h1>
    <div class="alert alert-success collapse" role="alert" id="success-alert"></div>
    <div class="alert alert-danger collapse" role="alert" id="error-alert"></div>
    <div class="row">
        <div class="col-6">
            <form action="/api/MakePerson.php" method="post" enctype="multipart/form-data" id="person_form">
                <div id="messages" hidden></div>
                <input type="hidden" id="Id" name="Id">
                <div class="row">
                    <div class="col-6">
                        <label for="Name">Имя</label>
                        <input class="form-control" id="Name" name="Name" placeholder="Имя">
                    </div>
                    <div class="col-6">
                        <label for="Family">Фамилия</label>
                        <input class="form-control" id="Family" name="Family" placeholder="Фамилия">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="Patronymic">Отчество</label>
                    <input class="form-control" id="Patronymic" name="Patronymic" placeholder="Отчество">
                </div>
                <div class="mb-3">
                    <label for="Speciality">Специальность</label>
                    <select class="form-control" id="Speciality" name="Speciality">
                        <?php foreach ($specialityList as $speciality) { ?>
                            <option value="<?php echo $speciality['id']; ?>">
                                <?php echo $speciality['speciality']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="Skills">Навыки</label>
                    <textarea class="form-control" id="Skills" rows="10" cols="30" name="Skills"></textarea>
                </div>
                <div class="mb-3">
                    <label for="inputfile">Загрузка фото</label>
                    <input class="form-control" type="file" id="inputfile" name="inputfile">
                </div>
                <div class="mb-3">
                    <input class="form-control btn-success" id="submit-button" type="submit" value="Создание">
                </div>
            </form>
        </div>
        <div class="col-6">
            <div id="displayBlock" hidden="hidden">
                <img class="img-thumbnail" id="Picture" style="max-height: 300px">
                <div class="mb-3"><b>Имя: </b><span id="display-name"></span></div>
                <div class="mb-3"><b>Фамилия: </b><span id="display-family"></span></div>
                <div class="mb-3"><b>Отчество: </b><span id="display-patronymic"></span></div>
                <div class="mb-3"><b>Специальность: </b><span id="display-speciality"></span></div>
                <div class="mb-3"><b>Навыки: </b> <span id="display-skills"></span></div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
var form = document.getElementById('person_form');
form.onsubmit = async (e) => {
    e.preventDefault();
    const form = e.currentTarget;
    const url = form.action;
    const method = form.method;
    const formData = new FormData(form);
    fetch(url, {
        method: method,
        body: formData
    })
    .then(response => {
        console.log(response);
        if (response.status !== 200) {
            return response.json().then(data => {
                errorAlert(data.message);
            });
        }
        return response.json().then(data => {

            var submitButton = document.getElementById("submit-button");
            submitButton.setAttribute("hidden", "hidden");
            form.action = "/api/PutPerson.php";
            document.getElementById("Name").onchange = () => submitButton.click();
            document.getElementById("Family").onchange = () => submitButton.click();
            document.getElementById("Patronymic").onchange = () => submitButton.click();
            document.getElementById("Speciality").onchange = () => submitButton.click();
            document.getElementById("Skills").onchange = () => submitButton.click();
            document.getElementById("inputfile").onchange = () => submitButton.click();
            document.getElementById("Id").value = data.id;
            document.getElementById("displayBlock").removeAttribute("hidden");

            successAlert("Успешно сохранено");

            document.getElementById("Name").value = data.name;
            document.getElementById("Family").value = data.family;
            document.getElementById("Patronymic").value = data.patronymic;
            document.getElementById("Speciality").value = data.speciality.id;
            document.getElementById("Skills").value = data.skills;
            document.getElementById("Picture").src = data.imageUrl;
            document.getElementById("inputfile").value = null;
            document.getElementById("display-name").textContent = data.name;
            document.getElementById("display-family").textContent = data.family;
            document.getElementById("display-patronymic").textContent = data.patronymic;
            document.getElementById("display-speciality").textContent = data.speciality.name;
            document.getElementById("display-skills").textContent = data.skills;
        });
    });
}
function successAlert(text) {
    var alert = $("#success-alert");
    alert.html(text);
    alert.show();
    setTimeout(function() {
        alert.hide();
    }, 2000);
}
function errorAlert(text) {
    var alert = $("#error-alert");
    alert.html(text);
    alert.show();
    setTimeout(function() {
        alert.hide();
    }, 2000);
}
</script>
</body>
</html>

