var replenishment = document.getElementById('myModal');
var button = document.getElementById("openModalButton");
var span = document.getElementById("closeModalButton");

button.onclick = function() {
    replenishment.style.display = "block";
}

span.onclick = function() {
    replenishment.style.display = "none";
}