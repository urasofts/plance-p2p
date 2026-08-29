-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-08-2026 a las 00:43:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `place_bsd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dispersiones`
--

CREATE TABLE `dispersiones` (
  `id` int(11) NOT NULL,
  `request_id` varchar(100) NOT NULL DEFAULT '',
  `destino` varchar(150) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `precio_total` decimal(10,2) NOT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `impuesto` decimal(10,2) NOT NULL,
  `moneda` varchar(10) NOT NULL DEFAULT 'COP',
  `usuario_id` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gateway_ordenes`
--

CREATE TABLE `gateway_ordenes` (
  `id` int(11) NOT NULL,
  `producto` varchar(100) NOT NULL,
  `tipo_pago` varchar(10) NOT NULL DEFAULT 'basico',
  `precio` decimal(10,2) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `tipo_doc` varchar(10) NOT NULL,
  `num_doc` varchar(20) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `monto_pagado` decimal(10,2) DEFAULT NULL,
  `request_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gateway_abonos`
-- (ledger de pagos parciales de gateway_ordenes con tipo_pago = 'mixto')
--

CREATE TABLE `gateway_abonos` (
  `id` int(11) NOT NULL,
  `gateway_orden_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `medio_pago` varchar(20) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `request_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gateway_recurrencias`
-- (antes `gateway_suscripciones` — ahora es el flujo de Recurrencia común de
--  API Gateway: cobra el primer periodo con payment.recurring y es PlacetoPay
--  quien programa y ejecuta los cobros siguientes, sin token de por medio)
--

CREATE TABLE `gateway_recurrencias` (
  `id` int(11) NOT NULL,
  `servicio` varchar(50) NOT NULL,
  `plan` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `tipo_doc` varchar(10) NOT NULL,
  `num_doc` varchar(20) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `periodicidad` varchar(5) NOT NULL DEFAULT 'M',
  `next_payment` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `request_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gateway_suscription`
--

CREATE TABLE `gateway_suscription` (
  `id` int(11) NOT NULL,
  `servicio` varchar(50) NOT NULL,
  `plan` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `tipo_doc` varchar(10) NOT NULL,
  `num_doc` varchar(20) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `token` varchar(225) NOT NULL,
  `request_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(11) NOT NULL,
  `migration` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `applied_at`) VALUES
(1, '0001_create_users.sql', '2026-08-06 20:19:14'),
(2, '0002_create_ordenes.sql', '2026-08-06 20:19:14'),
(3, '0003_create_dispersiones.sql', '2026-08-06 20:19:14'),
(4, '0004_create_reservaciones.sql', '2026-08-06 20:19:14'),
(5, '0005_create_recurrencias.sql', '2026-08-06 20:19:14'),
(6, '0006_create_suscripciones.sql', '2026-08-06 20:19:14'),
(7, '0007_create_suscription.sql', '2026-08-06 20:19:14'),
(8, '0008_create_suscription_rec.sql', '2026-08-06 20:19:14'),
(9, '0009_create_payment_link.sql', '2026-08-06 20:19:14'),
(10, '0010_create_gateway_ordenes.sql', '2026-08-06 20:19:14'),
(11, '0011_create_gateway_suscripciones.sql', '2026-08-06 20:19:14'),
(12, '0012_create_gateway_suscription.sql', '2026-08-06 20:19:14'),
(13, '0013_create_user_preferences.sql', '2026-08-06 20:19:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL DEFAULT 0,
  `producto` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `jugador_id` varchar(100) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `monto_pagado` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_link`
--

CREATE TABLE `payment_link` (
  `id` int(11) NOT NULL,
  `producto` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `link_id` varchar(100) DEFAULT NULL,
  `link_url` text DEFAULT NULL,
  `referencia` varchar(100) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `estado` varchar(20) DEFAULT 'activo',
  `pagos_usados` int(11) DEFAULT 0,
  `expiracion` datetime NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recurrencias`
--

CREATE TABLE `recurrencias` (
  `id` int(11) NOT NULL,
  `servicio` varchar(50) NOT NULL,
  `plan` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `request_id` varchar(100) DEFAULT NULL,
  `periodicidad` varchar(5) NOT NULL,
  `next_payment` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservaciones`
--

CREATE TABLE `reservaciones` (
  `id` int(11) NOT NULL,
  `request_id` varchar(100) NOT NULL DEFAULT '',
  `habitacion` varchar(150) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `moneda` varchar(10) NOT NULL DEFAULT 'COP',
  `usuario_id` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `session_id` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripciones`
--

CREATE TABLE `suscripciones` (
  `id` int(11) NOT NULL,
  `request_id` varchar(100) NOT NULL DEFAULT '',
  `token` varchar(255) NOT NULL DEFAULT '',
  `plataforma` varchar(50) NOT NULL,
  `plan` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscription`
--

CREATE TABLE `suscription` (
  `id` int(11) NOT NULL,
  `servicio` varchar(50) NOT NULL,
  `plan` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `request_id` varchar(100) NOT NULL DEFAULT '',
  `token` varchar(225) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscription_rec`
--

CREATE TABLE `suscription_rec` (
  `id` int(11) NOT NULL,
  `servicio` varchar(50) NOT NULL,
  `plan` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `usuario_id` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `request_id` varchar(100) NOT NULL DEFAULT '',
  `periodicidad` varchar(5) NOT NULL,
  `next_payment` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `correo`, `usuario`, `contraseña`, `profile_image`, `bio`, `location`, `created_at`) VALUES
(1, 'Jair Stiven Martinez Palacios', 'mjairstiven@gmail.com', 'Jair!', '$2y$10$DzG5w92RXQxBQXGut6XSM.Zo2C9jIKJp82OpxgmN7Wt4/wY9rrrMi', 'avatar_1_1786051071.jpg', 'Bitch, I\'m Yung Plague, here to spread the flames\r\nMakin\' it fucking rain alligator fangs\r\nLightin\' the flame that ignited', 'Argentina', '2026-08-06 21:13:43'),
(133, 'jair palacios', 'jairmosquera2019@gmail.com', 'jair stiven', '$2y$10$0JbHh/oxULPSSyRG7VrpbubScyfuGgD5XDvkJkErOUkEjFmp8k7aa', 'avatar_133_1786053535.jpeg', 'All the murder are devil\r\nAll the hoes are devil\r\nAll this the xannax are devil\r\nAll this the glock are devil', 'Medellin - Colombia', '2026-08-06 20:21:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `usuario_correo` varchar(255) NOT NULL,
  `tema` varchar(10) NOT NULL DEFAULT 'oscuro',
  `fondo` varchar(20) NOT NULL DEFAULT 'ninguno',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `usuario_correo`, `tema`, `fondo`, `updated_at`) VALUES
(1, 'mjairstiven@gmail.com', 'oscuro', 'ninguno', '2026-08-11 21:28:12'),
(2, 'jairmosquera2019@gmail.com', 'claro', 'ninguno', '2026-08-08 20:53:41');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `dispersiones`
--
ALTER TABLE `dispersiones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gateway_ordenes`
--
ALTER TABLE `gateway_ordenes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gateway_abonos`
--
ALTER TABLE `gateway_abonos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gateway_orden_id` (`gateway_orden_id`);

--
-- Indices de la tabla `gateway_recurrencias`
--
ALTER TABLE `gateway_recurrencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gateway_suscription`
--
ALTER TABLE `gateway_suscription`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `migration` (`migration`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `payment_link`
--
ALTER TABLE `payment_link`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `recurrencias`
--
ALTER TABLE `recurrencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `suscription`
--
ALTER TABLE `suscription`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `suscription_rec`
--
ALTER TABLE `suscription_rec`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_correo` (`usuario_correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `dispersiones`
--
ALTER TABLE `dispersiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gateway_ordenes`
--
ALTER TABLE `gateway_ordenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gateway_abonos`
--
ALTER TABLE `gateway_abonos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gateway_recurrencias`
--
ALTER TABLE `gateway_recurrencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gateway_suscription`
--
ALTER TABLE `gateway_suscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `payment_link`
--
ALTER TABLE `payment_link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recurrencias`
--
ALTER TABLE `recurrencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `suscription`
--
ALTER TABLE `suscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `suscription_rec`
--
ALTER TABLE `suscription_rec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de la tabla `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
