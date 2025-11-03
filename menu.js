document.addEventListener("DOMContentLoaded", () => {
  const menuContainer = document.querySelector(".menu");

  fetch("menu.json")
    .then(response => {
      if (!response.ok) throw new Error("No se pudo cargar menu.json");
      return response.json();
    })
    .then(data => {
      data.menu.forEach(item => {
        const li = document.createElement("li");
        if (item.submenu) {
          li.classList.add("submenu");
          li.innerHTML = `<a href="#" class="main-link">${item.icono ? item.icono + " " : ""}${item.titulo} <span class="caret">▾</span></a>`;

          const ulSub = document.createElement("ul");
          ulSub.classList.add("dropdown");

          item.submenu.forEach(sub => {
            const subLi = document.createElement("li");
            if (sub.enlace)
              subLi.innerHTML = `<a href="${sub.enlace}">${sub.titulo}</a>`;
            else if (sub.accion)
              subLi.innerHTML = `<a href="#" onclick="${sub.accion}">${sub.titulo}</a>`;
            else
              subLi.innerHTML = `<a href="#">${sub.titulo}</a>`;
            ulSub.appendChild(subLi);
          });

          li.appendChild(ulSub);
        } else {
          li.innerHTML = `<a href="${item.enlace || "#"}" class="main-link">${item.icono ? item.icono + " " : ""}${item.titulo}</a>`;
        }

        menuContainer.appendChild(li);
      });
    })
    .catch(err => {
      console.error("Error cargando el menú:", err);
      const menuContainer = document.querySelector(".menu");
      menuContainer.innerHTML = "<li>Error cargando el menú</li>";
    });
});
