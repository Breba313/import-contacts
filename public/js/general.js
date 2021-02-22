jQuery(function() {            
    //  Inicializacion de tablas tipo datatables
    $('.table-datatables').DataTable( {
        "order": [[ 1, "desc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });
});

//  Funcion para eliminar registros
function check_delete(id){
    swal.fire({
        title: 'Está seguro de que desea eliminar este archivo?',
        text: "Esta acción no se puede revertir!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, borralo!'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = document.getElementById("url_delete").value + id;
            console.log(url);
            return fetch(url)
            .then(response => {
            if (!response.success) {
                swal.fire(
                    'Eliminado!',
                    'Su archivo ha sido borrado.',
                    'success'
                ).then((result) => {
                    location.reload();
                });
            }
            return response.json()
            })
            .catch(error => {
            swal.showValidationMessage(
                `Request failed: ${error}`
            )
            }) 
        }
    });
    
}