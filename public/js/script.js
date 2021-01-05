function toggleNewComment() {
    var x = document.getElementById("newComment");
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  }

  function clickAvatar(){
    document.querySelector('#submit').click();
  }

  function displayImage(image){
    if(image.files[0]){ // si une image existe
      var reader = new FileReader();
      reader.onload = function(image) {
        document.querySelector('#avatar').setAttribute('src', image.target.result);
      }
      reader.readAsDataURL(image.files[0]);
    }
  }