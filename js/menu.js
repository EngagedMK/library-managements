let shrinkBtnNewAlt = document.querySelector(".shrink-btn");

shrinkBtnNewAlt.addEventListener("click", () => {
  document.body.classList.toggle("shrink");
  setTimeout(moveActiveTab, 400);

  shrinkBtnNewAlt.classList.add("hovered");

  setTimeout(() => {
    shrinkBtnNewAlt.classList.remove("hovered");
  }, 500);
});
