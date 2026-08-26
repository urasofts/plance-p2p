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

--
-- Volcado de datos para la tabla `dispersiones`
--

INSERT INTO `dispersiones` (`id`, `request_id`, `destino`, `descripcion`, `precio_total`, `precio_base`, `impuesto`, `moneda`, `usuario_id`, `estado`, `created_at`) VALUES
(1, '3835621', 'Buenos Aires, Argentina', 'Tiquete a Buenos Aires, Argentina', 920000.00, 800000.00, 120000.00, 'COP', 'mjairstiven@gmail.com', 'aprobada', '2026-08-09 03:17:19'),
(2, '3835622', 'Buenos Aires, Argentina', 'Tiquete a Buenos Aires, Argentina', 920000.00, 800000.00, 120000.00, 'COP', 'mjairstiven@gmail.com', 'aprobada', '2026-08-09 03:18:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gateway_ordenes`
--

CREATE TABLE `gateway_ordenes` (
  `id` int(11) NOT NULL,
  `producto` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `tipo_doc` varchar(10) NOT NULL,
  `num_doc` varchar(20) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `request_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gateway_ordenes`
--

INSERT INTO `gateway_ordenes` (`id`, `producto`, `precio`, `nombre`, `correo`, `telefono`, `tipo_doc`, `num_doc`, `estado`, `request_id`, `created_at`) VALUES
(1, '660 UC Points', 39900.00, 'Jair', 'velixidepg3d@gmail.com', '30139198414', 'TI', '12334567', 'aprobada', '1599853215', '2026-08-06 22:54:07'),
(2, '325 UC Points', 21900.00, 'jair', 'jeoestiven@gmail.com', '121314567', 'TI', '12345678', 'aprobada', '1599853217', '2026-08-06 22:56:22'),
(3, '360 Gold', 19900.00, 'John', 'mjairstiven@gmail.com', '3031111111', 'CC', '123456789', 'aprobada', '1599853219', '2026-08-06 22:59:14'),
(4, '325 UC Points', 21900.00, 'jair', 'jeoestiven@gmail.com', '310101391313', 'TI', '1245778', 'aprobada', '1599853221', '2026-08-06 23:00:14'),
(5, '60 UC Points', 4900.00, 'Jair!', 'mjairstiven@gmail.com', '30111111111', 'TI', '123456789', 'aprobada', '1599865025', '2026-08-11 16:10:15'),
(6, '325 UC Points', 21900.00, 'Jair!', 'mjairstiven@gmail.com', '301111111111', 'TI', '123456789', 'pendiente', 'GW-BS-C818D2AF', '2026-08-11 21:28:03'),
(7, '360 Gold', 19900.00, 'una bna mmda we t aliviana goe', 'mjairstiven@gmail.com', '301111111', 'CC', '123456789', 'pendiente', 'GW-BS-1AA39F75', '2026-08-11 21:29:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gateway_suscripciones`
--

CREATE TABLE `gateway_suscripciones` (
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
  `token` varchar(255) NOT NULL,
  `request_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gateway_suscripciones`
--

INSERT INTO `gateway_suscripciones` (`id`, `servicio`, `plan`, `precio`, `nombre`, `correo`, `telefono`, `tipo_doc`, `num_doc`, `estado`, `token`, `request_id`, `created_at`) VALUES
(1, 'Netflix', 'Estándar', 26900.00, 'Mosquerito', 'jairmosquera2019@gmail.com', '30011111', 'CC', '12345678', 'aprobada', '', '1599863469', '2026-08-08 21:29:18'),
(2, 'Netflix', 'Estándar', 26900.00, 'Jair', 'mjairstiven@gmail.com', '300911111', 'CC', '123456789', 'pendiente', '', '1599865390', '2026-08-11 20:23:38');

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

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id`, `request_id`, `producto`, `precio`, `jugador_id`, `correo`, `estado`, `monto_pagado`, `created_at`) VALUES
(1, 3834129, '88 CP', 7000.00, '12345678', 'jairmosquera2019@gmail.com', 'rechazada', NULL, '2026-08-06 20:25:23'),
(2, 3834132, '310 Diamantes', 11900.00, '123456', 'jairmosquera2019@gmail.com', 'rechazada', NULL, '2026-08-06 20:28:12'),
(3, 3834149, '460 CP', 12927.00, '1111111', NULL, 'pendiente', NULL, '2026-08-06 20:37:26'),
(4, 3834150, '460 CP', 12927.00, '12143145', NULL, 'pendiente', NULL, '2026-08-06 20:38:29'),
(5, 3834159, '460 CP', 12927.00, '123456789', NULL, 'pendiente', NULL, '2026-08-06 20:46:04'),
(6, 3834166, '120 Monedas', 5000.00, 'ADAS-111-222-333', 'jairmosquera2019@gmail.com', 'aprobada', NULL, '2026-08-06 21:05:23'),
(7, 3834202, '460 CP', 12927.00, '1234567', NULL, 'pendiente', NULL, '2026-08-06 21:22:09'),
(8, 3834222, '60 Platinum', 4900.00, '12345678', NULL, 'aprobada', 4900.00, '2026-08-06 21:46:51'),
(9, 3834257, '88 CP', 7000.00, '1234565678', NULL, 'aprobada', NULL, '2026-08-06 22:18:36'),
(10, 3834263, '1100 FC', 25500.00, '121314141', NULL, 'aprobada', NULL, '2026-08-06 22:24:32'),
(11, 3834264, '460 CP', 12927.00, '123456677', NULL, 'aprobada', NULL, '2026-08-06 22:28:23'),
(12, 3834265, '120 Monedas', 5000.00, 'ADADA-122-333-444', NULL, 'aprobada', NULL, '2026-08-06 22:30:36'),
(13, 3834281, 'Pase Elite', 22000.00, '123456789', NULL, 'aprobada', NULL, '2026-08-06 22:46:48'),
(14, 3834284, '310 Diamantes', 11900.00, '12345678', NULL, 'aprobada', NULL, '2026-08-06 22:49:16'),
(15, 3834290, 'Star Pass', 18500.00, '12132456', NULL, 'aprobada', NULL, '2026-08-06 22:51:03'),
(16, 3834291, '310 Diamantes', 11900.00, '1234567890', NULL, 'aprobada', NULL, '2026-08-06 22:53:05'),
(17, 3835603, '460 CP', 12927.00, '1223456', NULL, 'aprobada', NULL, '2026-08-08 16:20:51'),
(18, 3835617, '88 CP', 7000.00, '123456789', NULL, 'aprobada', NULL, '2026-08-08 20:53:21'),
(19, 3835618, '10560 Monedas', 240000.00, '1321567', NULL, 'aprobada', NULL, '2026-08-08 20:54:15'),
(20, 3835619, '100 Diamantes', 4500.00, '457788', NULL, 'aprobada', NULL, '2026-08-08 21:28:36'),
(21, 3836802, '60 Platinum', 4900.00, 'Y6U-33462B', NULL, 'aprobada', 4900.00, '2026-08-11 16:09:04');

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

--
-- Volcado de datos para la tabla `recurrencias`
--

INSERT INTO `recurrencias` (`id`, `servicio`, `plan`, `precio`, `usuario_id`, `estado`, `request_id`, `periodicidad`, `next_payment`, `created_at`, `fecha_fin`) VALUES
(1, 'YouTube Premium', 'Individual', 19900.00, 'mjairstiven@gmail.com', 'aprobada', '3835624', 'M', '2026-09-09', '2026-08-09 03:29:38', '2027-08-09');

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

--
-- Volcado de datos para la tabla `reservaciones`
--

INSERT INTO `reservaciones` (`id`, `request_id`, `habitacion`, `descripcion`, `precio`, `moneda`, `usuario_id`, `estado`, `created_at`, `session_id`) VALUES
(1, 'PRE-9567D550', 'Habitación Ejecutiva', 'Habitación Ejecutiva (checkin: 2026-08-15 al 2026-08-31)', 6240000.00, 'COP', 'jairmosquera2019@gmail.com', 'pendiente', '2026-08-08 16:09:48', ''),
(2, 'PRE-3237F204', 'Habitación Doble', 'Habitación Doble (checkin: 2026-08-15 al 2026-08-31)', 3520000.00, 'COP', 'jairmosquera2019@gmail.com', 'pendiente', '2026-08-08 16:19:47', ''),
(3, 'PRE-E5EFECE4', 'Habitación Estándar', 'Habitación Estándar (checkin: 2026-08-08 al 2026-08-31)', 3450000.00, 'COP', 'jairmosquera2019@gmail.com', 'pendiente', '2026-08-08 16:28:03', ''),
(4, 'PRE-043C6490', 'Habitación Doble', 'Habitación Doble (checkin: 2026-08-08 al 2026-09-30)', 11660000.00, 'COP', 'jairmosquera2019@gmail.com', 'aprobada', '2026-08-08 19:55:21', '3835615'),
(5, 'PRE-55F8F255', 'Habitación Doble', 'Habitación Doble (checkin: 2026-08-12 al 2026-08-31)', 4180000.00, 'COP', 'mjairstiven@gmail.com', 'aprobada', '2026-08-09 03:22:02', '3835623');

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

--
-- Volcado de datos para la tabla `suscription_rec`
--

INSERT INTO `suscription_rec` (`id`, `servicio`, `plan`, `precio`, `usuario_id`, `estado`, `request_id`, `periodicidad`, `next_payment`, `fecha_fin`, `created_at`) VALUES
(1, 'Claude', 'Max', 109000.00, 'mjairstiven@gmail.com', 'aprobada', '3835620', 'M', '2026-09-09', '2027-08-09', '2026-08-09 01:34:35');

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
-- Indices de la tabla `gateway_suscripciones`
--
ALTER TABLE `gateway_suscripciones`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `gateway_ordenes`
--
ALTER TABLE `gateway_ordenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `gateway_suscripciones`
--
ALTER TABLE `gateway_suscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `payment_link`
--
ALTER TABLE `payment_link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `recurrencias`
--
ALTER TABLE `recurrencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reservaciones`
--
ALTER TABLE `reservaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
