document.addEventListener("DOMContentLoaded", function () {
    if (typeof Swal === "undefined") {
        return;
    }

    const params = new URLSearchParams(window.location.search);
    const status = params.get("status");
    const form = params.get("form");

    if (!status) {
        return;
    }

    const alerts = {
        session_required: { icon: "warning", title: "Sesion requerida", text: "Por favor, inicia sesion para continuar." },
        user_not_found: { icon: "error", title: "Usuario no encontrado", text: "No existe una cuenta con ese correo." },
        wrong_password: { icon: "error", title: "Contrasena incorrecta", text: "La contrasena ingresada no es valida." },
        empty_fields: { icon: "warning", title: "Campos incompletos", text: "Por favor completa todos los campos." },
        email_exists: { icon: "info", title: "Correo registrado", text: "Ese correo ya esta registrado." },
        user_exists: { icon: "info", title: "Usuario registrado", text: "Ese usuario ya esta registrado." },
        name_min: { icon: "warning", title: "Nombre muy corto", text: "El nombre debe tener al menos 5 caracteres." },
        name_format: { icon: "warning", title: "Nombre invalido", text: "El nombre solo puede contener letras y espacios." },
        full_name_required: { icon: "warning", title: "Nombre incompleto", text: "Debes ingresar nombre y apellido." },
        password_len: { icon: "warning", title: "Contrasena invalida", text: "La contrasena debe tener minimo 8 caracteres." },
        password_upper: { icon: "warning", title: "Contrasena invalida", text: "Debe incluir al menos una letra mayuscula." },
        password_lower: { icon: "warning", title: "Contrasena invalida", text: "Debe incluir al menos una letra minuscula." },
        password_number: { icon: "warning", title: "Contrasena invalida", text: "Debe incluir al menos un numero." },
        password_special: { icon: "warning", title: "Contrasena invalida", text: "Debe incluir al menos un caracter especial." },
        id_invalid: { icon: "warning", title: "Identificacion invalida", text: "La identificacion debe tener entre 6 y 20 caracteres y solo numeros/guiones." },
        register_success: { icon: "success", title: "Registro exitoso", text: "Usuario registrado correctamente." },
        register_error: { icon: "error", title: "Error", text: "No se pudo registrar el usuario. Intenta de nuevo." }
    };

    const current = alerts[status];
    if (!current) {
        return;
    }

    if (form === "register") {
        if (typeof register === "function") {
            register();
        } else {
            const btn = document.getElementById("btn__registrarse");
            if (btn) btn.click();
        }
    } else if (form === "login") {
        if (typeof iniciarSesion === "function") {
            iniciarSesion();
        } else {
            const btn = document.getElementById("btn__iniciar-sesion");
            if (btn) btn.click();
        }
    }

    Swal.fire({
        icon: current.icon,
        title: current.title,
        text: current.text,
        confirmButtonText: "Aceptar",
        confirmButtonColor: "#159650"
    }).then(() => {
        params.delete("status");
        params.delete("form");
        const clean = window.location.pathname + (params.toString() ? "?" + params.toString() : "");
        window.history.replaceState({}, document.title, clean);
    });
});
