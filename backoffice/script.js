document.addEventListener('DOMContentLoaded', () => {
  cargarUsuarios();
  cargarPagos();
  cargarHoras();
  cargarUnidades();
  cargarReportes();
});

function cargarUsuarios() {
  const usuarios = [
    { nombre: "Axel Chiberriaga", email: "axel@gmail.com", estado: "Pendiente" },
    { nombre: "Teo Costa", email: "teo@gmail.com", estado: "Pendiente" },
    { nombre: "Tiziano Glisenti", email: "tizi@gmail.com", estado: "Activo" }
  ];

  const tbody = document.getElementById('usuarios-body');
  usuarios.forEach(u => {
    tbody.innerHTML += `
      <tr>
        <td>${u.nombre}</td>
        <td>${u.email}</td>
        <td>${u.estado}</td>
        <td>
          <button class="btn-aceptar">Aceptar</button>
          <button class="btn-rechazar">Rechazar</button>
        </td>
      </tr>
    `;
  });
}

function cargarPagos() {
  const pagos = [
    { usuario: "Axel Chiberriaga", fecha: "2025-07-20", archivo: "comprobante1.pdf", estado: "Pendiente" },
    { usuario: "Teo Costa", fecha: "2025-07-21", archivo: "comprobante2.pdf", estado: "Pendiente" },
    { usuario: "Tiziano Glisenti", fecha: "2025-07-22", archivo: "comprobante3.pdf", estado: "Aprobado" }
  ];

  const tbody = document.getElementById('pagos-body');
  pagos.forEach(p => {
    tbody.innerHTML += `
      <tr>
        <td>${p.usuario}</td>
        <td>${p.fecha}</td>
        <td><a href="#">${p.archivo}</a></td>
        <td>${p.estado}</td>
        <td>
          <button class="btn-aceptar">Aprobar</button>
          <button class="btn-rechazar">Rechazar</button>
        </td>
      </tr>
    `;
  });
}

function cargarHoras() {
  const horas = [
    { usuario: "Axel Chiberriaga", semana: "27/07/2025", horas: 15, motivo: "Enfermedad" },
    { usuario: "Teo Costa", semana: "27/07/2025", horas: 21, motivo: "-" },
    { usuario: "Tiziano Glisenti", semana: "27/07/2025", horas: 12, motivo: "Vacaciones" }
  ];

  const tbody = document.getElementById('horas-body');
  horas.forEach(h => {
    tbody.innerHTML += `
      <tr>
        <td>${h.usuario}</td>
        <td>${h.semana}</td>
        <td>${h.horas}</td>
        <td>${h.motivo}</td>
      </tr>
    `;
  });
}

function cargarUnidades() {
  const unidades = [
    { usuario: "Axel Chiberriaga", unidad: "A12", estado: "En obra" },
    { usuario: "Teo Costa", unidad: "B05", estado: "Finalizada" },
    { usuario: "Tiziano Glisenti", unidad: "B02", estado: "En obra" }
  ];

  const tbody = document.getElementById('unidades-body');
  unidades.forEach(u => {
    tbody.innerHTML += `
      <tr>
        <td>${u.usuario}</td>
        <td>${u.unidad}</td>
        <td>${u.estado}</td>
        <td>
          <button class="btn-aceptar">Actualizar</button>
        </td>
      </tr>
    `;
  });
}

function cargarReportes() {
  const reportes = [
    { usuario: "Axel Chiberriaga", pagos: "No", horas: "No", estado: "En riesgo" },
    { usuario: "Teo Costa", pagos: "Sí", horas: "Sí", estado: "Activo" },
    { usuario: "Tiziano Glisenti", pagos: "No", horas: "No", estado: "En riesgo" }
  ];

  const tbody = document.getElementById('reportes-body');
  reportes.forEach(r => {
    tbody.innerHTML += `
      <tr>
        <td>${r.usuario}</td>
        <td>${r.pagos}</td>
        <td>${r.horas}</td>
        <td>${r.estado}</td>
      </tr>
    `;
  });
}
