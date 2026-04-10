    $(function () {

    const urlBase = "index.php";

    function cargarSolicitudes() {
        $.get(urlBase, { option: "solicitudes_json" }, function (data) {

            let solicitudes = data; 
            let html = "";

            if (solicitudes.length === 0) {
                html = "<tr><td colspan='6'>No hay solicitudes</td></tr>";
            } else {
                solicitudes.forEach(s => {
                    html += `
                        <tr>
                            <td>${s.id}</td>
                            <td>${s.taller}</td>
                            <td>${s.username}</td>
                            <td>${s.usuario_id}</td>
                            <td>${s.fecha_solicitud}</td>
                            <td>
                                <button class="aprobar" data-id="${s.id}">✔</button>
                                <button class="rechazar" data-id="${s.id}">✖</button>
                            </td>
                        </tr>
                    `;
                });
            }

            $("#solicitudes-body").html(html);

        }, "json"); 
    }

    
    $(document).on("click", ".aprobar", function () {
        let id = $(this).data("id");

        $.post(urlBase, { option: "aprobar", id_solicitud: id }, function (res) {
            let data = JSON.parse(res); 
            alert(data.success ? "Aprobado" : data.error);
            cargarSolicitudes();
        });
    });

    
    $(document).on("click", ".rechazar", function () {
        let id = $(this).data("id");

        $.post(urlBase, { option: "rechazar", id_solicitud: id }, function (res) {
            let data = JSON.parse(res);
            alert(data.success ? "Rechazado" : data.error);
            cargarSolicitudes();
        });
    });

    
    $("#btnLogout").click(function () {
        $.post(urlBase, { option: "logout" }, function () {
            window.location = "index.php?page=login";
        });
    });

  
    cargarSolicitudes();
});