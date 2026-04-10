$(function () {

    const urlBase = "index.php";

    function cargarTalleres() {
        $.get(urlBase, { option: "talleres_json" }, function (data) {

            let html = "";

            if (data.length === 0) {
                html = "<p>No hay talleres disponibles</p>";
            } else {
                data.forEach(t => {

                    let sinCupo = t.cupo_disponible <= 0;

                    html += `
                        <div class="card mb-3 p-3 shadow-sm">
                            <h4>${t.nombre}</h4>
                            <p>${t.descripcion}</p>

                            <p>
                                <strong>Cupos disponibles:</strong> 
                                ${sinCupo 
                                    ? '<span class="text-danger">Sin cupos</span>' 
                                    : t.cupo_disponible}
                            </p>

                            <button 
                                class="btn ${sinCupo ? 'btn-secondary' : 'btn-success'} btnSolicitar"
                                data-id="${t.id}"
                                ${sinCupo ? 'disabled' : ''}
                            >
                                ${sinCupo ? 'Agotado' : 'Solicitar'}
                            </button>
                        </div>
                    `;
                });
            }

            $("#talleres-container").html(html);

        }, "json");
    }


    $(document).on("click", ".btnSolicitar", function () {

        let tallerId = $(this).data("id");

        $.post(urlBase, {
            option: "solicitar",
            taller_id: tallerId
        }, function (data) {

            if (data.response == "00") {
                alert(data.message);
                cargarTalleres();
            } else {
                alert(data.message);
            }

        }, "json"); 

    });

    
    $("#btnLogout").click(function () {
        $.post(urlBase, { option: "logout" }, function () {
            window.location = "index.php?page=login";
        });
    });

    cargarTalleres();
});