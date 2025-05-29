document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".open-modal").forEach((button) => {
    button.addEventListener("click", function () {
      const sellOrder = this.dataset.sellorder;

      fetch("getSells.php?sell_order=" + sellOrder)
        .then((res) => res.text()) // o .json() si respondes con JSON
        .then((html) => {
          document.getElementById("ventasTableContainer").innerHTML = html;
        })
        .catch((error) => {
          console.error("Error cargando ventas:", error);
          document.getElementById("ventasTableContainer").innerHTML = "<p class='text-danger'>Error .</p>";
        });
    });
  });
});

