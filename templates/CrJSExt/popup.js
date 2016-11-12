document.addEventListener('DOMContentLoaded', function() {
  var checkPageButton = document.getElementById('doitBtn');
  checkPageButton.addEventListener('click', function() {
    var element = document.querySelector("#greeting");
    element.innerText = "Hello, world!";
  }, false);
}, false);