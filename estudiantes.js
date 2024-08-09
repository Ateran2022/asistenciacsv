function mostrarEstudiantes(curso) {
    const estudiantes = {
        "9-30": ["Rojas Marin Pedro Andres", "Perez Paz Maria Carolina", "Diaz Marin Juan", "Diaz Lopez Manuel", "Bolivar Diaz Maria", "Paz Marin Juana", "Diaz Martinez Juan Camilo", "Perez Marin Juana", "Tapias Mario"],
        "9-31": ["Carlos", "Mario", "Camila"],
        "9-32": ["Andres", "Pedro", "Camila"],
        "10-33": ["Juan", "Carlos", "Maria"],
        "10-34": ["Mario", "Andres", "Juan"],
        "11-36": ["Camila", "Maria", "Pedro"],
        "11-38": ["Carlos", "Andres", "Mario"]
    };

    const estudiantesSelect = document.getElementById('estudiantes');
    if (estudiantesSelect) {
        estudiantesSelect.innerHTML = "";

        if (curso in estudiantes) {
            estudiantes[curso].forEach(function(estudiante) {
                const option = document.createElement('option');
                option.value = estudiante;
                option.text = estudiante;
                estudiantesSelect.appendChild(option);
            });
        } else {
            estudiantesSelect.innerHTML = "<option>No hay estudiantes disponibles</option>";
        }
    }
}

let estudiantesRegistrados = [];

// Función para registrar estudiantes
function registrar() {
    const estudiantesSelect = document.getElementById('estudiantes');
    const textView = document.getElementById('textView');
    if (estudiantesSelect && textView) {
        const estudiantesSeleccionados = Array.from(estudiantesSelect.selectedOptions)
                                               .map(option => option.text);

        estudiantesSeleccionados.forEach(estudiante => {
            if (!estudiantesRegistrados.includes(estudiante)) {
                estudiantesRegistrados.push(estudiante);
            }
        });

        // Actualizar el TextView con los estudiantes registrados
        textView.value = estudiantesRegistrados.join("\n");

        // Actualizar la lista desplegable de estudiantes registrados
        actualizarListaDesplegable();
    }
}

// Función para actualizar la lista desplegable de estudiantes registrados
function actualizarListaDesplegable() {
    const select = document.getElementById('estudiantesRegistrados');
    if (select) {
        select.innerHTML = ''; // Limpiar la lista desplegable
        estudiantesRegistrados.forEach(estudiante => {
            const option = document.createElement('option');
            option.value = estudiante;
            option.text = estudiante;
            select.add(option);
        });
    }
}

// Función para eliminar el estudiante seleccionado
function eliminar() {
    const select = document.getElementById('estudiantesRegistrados');
    const textView = document.getElementById('textView');
    if (select && textView) {
        const estudianteSeleccionado = select.value;

        if (estudianteSeleccionado) {
            // Eliminar el estudiante de la lista global
            estudiantesRegistrados = estudiantesRegistrados.filter(estudiante => estudiante !== estudianteSeleccionado);

            // Actualizar el TextView con los estudiantes restantes
            textView.value = estudiantesRegistrados.join("\n");

            // Actualizar la lista desplegable de estudiantes registrados
            actualizarListaDesplegable();
        } else {
            alert("Por favor, selecciona un estudiante para eliminar.");
        }
    }
}
