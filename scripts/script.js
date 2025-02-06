function openModal(imgSrc) {
  var modalOverlay = document.getElementById("modalOverlay");
  var modalImage = document.getElementById("modalImage");
  modalImage.src = imgSrc;
  modalOverlay.style.display = "flex";
}

function closeModal() {
  var modalOverlay = document.getElementById("modalOverlay");
  var modalImage = document.getElementById("modalImage");
  modalImage.src = "";
  modalOverlay.style.display = "none";
}
