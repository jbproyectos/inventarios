-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 28-01-2025 a las 22:25:45
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_sistemas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesorio`
--

CREATE TABLE `accesorio` (
  `Id_accesorio` int(11) NOT NULL,
  `foto` varchar(50) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `modelo` varchar(20) NOT NULL,
  `marca` varchar(20) NOT NULL,
  `condicion` varchar(20) NOT NULL,
  `total` int(20) NOT NULL,
  `Id_oficina` int(11) NOT NULL,
  `Id_departamento` int(11) NOT NULL,
  `costo` int(10) NOT NULL,
  `disponibilidad` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `celular`
--

CREATE TABLE `celular` (
  `Id_celular` int(11) NOT NULL,
  `foto` varchar(200) NOT NULL,
  `modelo` varchar(10) DEFAULT NULL,
  `marca` varchar(20) NOT NULL,
  `estado_bateria` int(3) DEFAULT NULL,
  `total` int(20) NOT NULL,
  `Id_oficina` int(11) NOT NULL,
  `Id_departamento` int(11) NOT NULL,
  `costo` int(20) NOT NULL,
  `disponibilidad` varchar(20) NOT NULL,
  `asignado_a` varchar(100) NOT NULL,
  `segundo_dueno` text DEFAULT NULL,
  `contrasena` varchar(10) DEFAULT NULL,
  `IMEI` varchar(20) NOT NULL,
  `telefono_uno` varchar(10) NOT NULL,
  `telefono2` varchar(10) NOT NULL,
  `telefono3` varchar(10) NOT NULL,
  `cuenta_correo` varchar(50) NOT NULL,
  `password_correo` varchar(20) DEFAULT NULL,
  `icloud_dos` varchar(50) DEFAULT NULL,
  `password_ic` varchar(50) DEFAULT NULL,
  `llamadas_num` varchar(12) DEFAULT NULL,
  `whatsapp` varchar(12) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `CEO`
--

CREATE TABLE `CEO` (
  `id` int(10) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `departamento` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `CEO`
--

INSERT INTO `CEO` (`id`, `nombre`, `departamento`) VALUES
(5, 'LUIS ARMANDO RODRIGUEZ SILVA', NULL),
(6, 'JOEL AYARZAGOITA TAMEZ', NULL),
(7, '(ELY) YOLANDA ELIZABETH TAMEZ PEÑA', NULL),
(8, 'ABBY', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `computadora`
--

CREATE TABLE `computadora` (
  `Id_computadora` int(11) NOT NULL,
  `asignado_a` varchar(100) NOT NULL,
  `Id_departamento` int(11) NOT NULL,
  `Id_oficina` int(11) NOT NULL,
  `correo_asociado` varchar(100) NOT NULL,
  `contrasenaGmail1` varchar(100) NOT NULL,
  `contrasenaOutlook1` varchar(100) NOT NULL,
  `correoAsociado2` varchar(100) NOT NULL,
  `contrasenaGmail2` varchar(100) NOT NULL,
  `contrasenaOutlook2` varchar(100) NOT NULL,
  `correoAsociado3` varchar(100) NOT NULL,
  `contrasenaWindow` varchar(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `marca` enum('ACER','ACTECK','ASUS','CUSTOM','DELL','HP','LENOVO','MAC','TOSHIBA','SIN MARCA') NOT NULL,
  `tipoDeDisco` varchar(20) NOT NULL,
  `procesador` varchar(20) NOT NULL,
  `ram` varchar(10) NOT NULL,
  `condicion` varchar(250) NOT NULL,
  `costoEquipoActual` varchar(50) NOT NULL,
  `fechaDeAsignacion` text NOT NULL,
  `anoDeProcesador` varchar(4) NOT NULL,
  `fechaDeLanzamiento` varchar(20) NOT NULL,
  `status` varchar(50) NOT NULL,
  `posibleFechaParaVenta` text NOT NULL,
  `nuevaCompra` varchar(50) NOT NULL,
  `foto` varchar(200) NOT NULL,
  `pcAnterior` varchar(100) NOT NULL,
  `posibleAsignacion` varchar(100) NOT NULL,
  `total` varchar(50) NOT NULL,
  `costoAlComprar` varchar(50) NOT NULL,
  `costoALaVenta` varchar(50) NOT NULL,
  `disponibilidad` varchar(50) NOT NULL,
  `propietario_Destino` varchar(50) NOT NULL,
  `foto2` varchar(200) NOT NULL,
  `fechaDeReasignacion` varchar(20) NOT NULL,
  `revisado` varchar(5) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `computadora`
--

INSERT INTO `computadora` (`Id_computadora`, `asignado_a`, `Id_departamento`, `Id_oficina`, `correo_asociado`, `contrasenaGmail1`, `contrasenaOutlook1`, `correoAsociado2`, `contrasenaGmail2`, `contrasenaOutlook2`, `correoAsociado3`, `contrasenaWindow`, `tipo`, `modelo`, `marca`, `tipoDeDisco`, `procesador`, `ram`, `condicion`, `costoEquipoActual`, `fechaDeAsignacion`, `anoDeProcesador`, `fechaDeLanzamiento`, `status`, `posibleFechaParaVenta`, `nuevaCompra`, `foto`, `pcAnterior`, `posibleAsignacion`, `total`, `costoAlComprar`, `costoALaVenta`, `disponibilidad`, `propietario_Destino`, `foto2`, `fechaDeReasignacion`, `revisado`, `comment`) VALUES
(1, 'JUAN BAUTISTA HERNANDEZ', 40, 10, 'dev@kabzo.org', '12345', '12345', '12345', '12345', '12345', 'dev@kabzo.org', '12345', 'Tablet', '24', 'DELL', 'SSD', 'M1', '12', 'Mala', '1999', '2025-01-03', '2025', '2025-01-31', 'Activa', '2025-06-24', '123', '', 'no pc anterior', 'no posible asignacion', '129999', '14999', '6999', 'Reservada', 'no destino', '', '2025-06-24', '1', ''),
(1075, 'AARON GARCIA VALDEZ', 23, 7, 'mensajeria1@kabzo.org', 'yJ13B4pS', 'kdvbxaygwktqwmqt', 'N/A', 'N/A', 'N/A', 'N/A', '764ELOTES', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/04/2025', '', 'N/A', '24-G200LA', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Luis Alberto Escobar del Razo', 'N/A', '1/11/2024', '2', 'asignado_a, Id_departamento, Id_oficina, correo_asociado | Comentario adicional: 3 campos'),
(1076, 'ALAN ALFREDO SANCHEZ LOPEZ', 24, 8, 'juridico4@kabzo.org', 'VKGXy2DP', 'yoftksyhqcjvudgl', 'N/A', 'N/A', 'N/A', 'N/A', 'azul.0202', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/08/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP AIO 24-F0XX\"', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Julio César Rangel loza', 'N/A', '1/11/2024', '1', ''),
(1077, 'ALAN HERNANDEZ RUIZ', 21, 7, 'cobranza2@kabzo.org', 'Cobranza2022', 'rzyvudaecfujajsa', 'N/A', 'N/A', 'N/A', 'N/A', 'ANDROID13', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL 9020', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Edgardo Dariel Beltrán Martínez', 'N/A', '1/11/2024', '1', ''),
(1078, 'ALAN HERNANDEZ RUIZ', 21, 7, 'cobranza2@kabzo.org', 'Cobranza2022', 'rzyvudaecfujajsa', 'N/A', 'N/A', 'N/A', 'N/A', 'ANDROID13', 'Laptop', 'RTL8188EE', 'HP', 'SSD', 'I5 7ma', '8', 'BUENA', '$4,679.26', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL 9020', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Edgardo Dariel Beltrán Martínez', 'N/A', '1/11/2024', '1', ''),
(1079, 'JORGE', 21, 7, 'cobranza4@kabzo.org', '2024COB.ZA', 'nwiq bmao avoi dcsy\r', '', '', '', '', '', '', '', 'SIN MARCA', '', '', '', '', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', NULL),
(1080, 'ALAN JAHIR GONZALEZ MIRANDA', 24, 8, 'juridico3@kabzo.org', 'tiramisu1', 'bpljkqlynktyspja', 'N/A', 'N/A', 'N/A', 'N/A', '2023BANK', 'All in One', 'ASPIRE Z3-715', 'ACER', 'SSD', 'P', '6', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', 'N/A', 'N/A', '', '2', 'foto, costoAlComprar, foto2'),
(1081, 'ALAN SERNA', 32, 9, 'pagos34@kabzo.org', 'y^v0sW!p', 'ernarabyffiypueu', 'N/A', 'N/A', 'N/A', 'N/A', 'Eliud024', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/05/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', 'N/A', 'N/A', '', '2', 'asignado_a, Id_departamento, correo_asociado, contrasenaGmail2, correoAsociado3, modelo | Comentario adicional: no esta bien llenado los campos seleccionados'),
(1082, 'ALBA MARROQUIN', 26, 8, 'imss@kabzo.org', 'iMsS\"#$\"#$&#', 'oivi gcph inxy wllz', 'finiquitos@rhfinder.com\r', '', '', 'contabilidad3@kabzo.org', '1602060831', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/03/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL 9020', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Cinthia Gabriela Oviedo Espinoza ', 'N/A', '1/11/2024', '2', 'asignado_a, Id_departamento, Id_oficina, correo_asociado | Comentario adicional: 4 mismos campos mal'),
(1083, 'ALBA MARROQUIN', 26, 8, 'imss@kabzo.org', 'iMsS\"#$\"#$&#', 'oivi gcph inxy wllz', 'gmam vpyr uekb whuq', '', '', 'rddp uybr dmhg ilww\r', '', 'Laptop', 'IDEAPAD SLIM 3 15IAH8', 'LENOVO', 'SSD', 'I5 12va', '8', 'BUENA', '0', '', '2019', '01/01/2019', 'ACTIVA', '01/01/2026', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', '2', 'asignado_a, Id_departamento, Id_oficina, correo_asociado | Comentario adicional: 4 mismos campos mal'),
(1084, 'RECOJER EQUIPO', 26, 8, '', '', '', '', '', '', '', '', 'All in One', '24-F1XX', 'HP', 'SSD', 'R3', '8', 'REGULAR', '0', '', '2019', '01/01/2019', 'VENTA', '01/01/2026', '', '', '', '', '', '', 'N/A', '', '', '', '', '2', 'asignado_a, Id_departamento, Id_oficina, correo_asociado | Comentario adicional: 4 mismos campos mal'),
(1085, 'ALEIDYS (COMPUTADORA DE DISEÑO ARQUITECTURA)', 42, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Laptop', 'G7', 'DELL', 'SSD', 'I7 8VA', '16', 'BUENA', '0', '', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', 'OBSOLETO ', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', '1', ''),
(1086, 'ALEIDYS REYES ', 42, 10, 'arquitectura@kabzo.org', 'CJE<=h8yS6&75dG>', 'hjxexmwunkopocep', 'N/A', 'N/A', 'N/A', 'N/A', '30062022', 'CUSTOM', 'CUSTOM', 'CUSTOM', 'SSD', 'R7', '16', 'BUENA', '0', '', '2018', '01/01/2019', 'STOCK', '01/01/2026', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', '1', ''),
(1087, 'ABIGAIL GARZA LOPEZ', 22, 7, 'contabilidad9@kabzo.org', 'YWimiKU5', 'gyrkhwzansvgadpf', 'N/A', 'N/A', 'N/A', 'N/A', 'Solymar12', 'Micro CPU', 'Elitedesk 800 G4', 'HP', 'SSD', 'I5 8va', '16', 'BUENA', '$4,399.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', '2', 'disponibilidad, propietario_Destino, foto2, fechaDeReasignacion | Comentario adicional: comentatio extra'),
(1088, 'ALEXA BANDALA', 39, 21, 'audiovisual@produccionesdobleb.com', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'macbook pro 2023', 'MAC', 'SSD', 'm3 pro', '18', 'BUENA', '0', '', '2023', '01/01/2023', 'ACTIVA', '01/01/2026', 'N/A', 'N/A', 'COMPUTADORA PROPIA', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', '1', ''),
(1090, 'NADIA ISELA', 35, 15, 'tesoreria12@kabzo.org', '2024tes..A', 'unsj oaxq sbrz tzpb', 'N/A', 'N/A', 'N/A', 'N/A', 'android11\r', 'All in One', '24-F1XX', 'HP', 'HD', 'R3', '6', 'BUENA', '0', '', '2019', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1091, 'ALONDRA MARIBEL VARGAS TENEYUQUE', 28, 8, 'contratos@kabzo.org', 'aZxE00MMa', 'xgdmwbsntcychaai', 'juridico1@kabzo.org\r', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP AIO 24-F0XX', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Victor Alejandro Ruiz Gutierrez', 'N/A', '1/11/2024', NULL, NULL),
(1092, 'ALONDRA MARIBEL VARGAS TENEYUQUE', 28, 8, 'contratos@kabzo.org', 'aZxE00MMa', 'xgdmwbsntcychaai', 'juridico1@kabzo.org\r', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'IDEAPAD SLIM 3 15IAH8', 'LENOVO', 'SSD', 'I5 12va', '8', 'BUENA', '$4,679.26', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP AIO 24-F0XX', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1093, 'ALONDRA MATA FLORES', 32, 9, 'asistencia@kabzo.org', '5acOVGkka', 'rjqwhrebpzjeecmj', 'N/A', 'N/A', 'N/A', 'N/A', 'rkstar2022', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'LENOVO M SERIES', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Vladimir Rangel', 'N/A', '1/11/2024', NULL, NULL),
(1094, 'ANDREA NICOLE CANTU OROZCO', 23, 7, 'mensajeria4@kabzo.org', 'GW8Krs1v', 'pvvnkeecbvrfqmrr', 'N/A', 'N/A', 'N/A', 'N/A', '21sandias', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP AIO 24-F0XX', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Daniel Salgado Torres', 'N/A', '1/11/2024', NULL, NULL),
(1095, 'ANGELES SARAIN RIVAS ELIAS', 32, 9, 'pagos18@kabzo.org', 'UGOR%#Jm', 'gssfwuzmstwasrud', 'N/A', 'N/A', 'N/A', 'N/A', '14270189', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ELITE ONE 800G1', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Luis Solis', 'N/A', '1/11/2024', NULL, NULL),
(1096, 'JUAN BAUTISTA HERNANDEZ', 32, 9, 'asesorescarso1@gmail.com', 'Cancun2022', 'kvlr rrto uapa mnvb', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'OptiPlex 3090', 'DELL', 'SSD', 'I7 10ma', '8', 'BUENA', '0', '1/05/2023', '2019', '01/01/2021', 'ACTIVA', '01/01/2028', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1097, 'ARMANDO RODRIGUEZ', 19, 7, 'juridico@kabzo.org', 'VERSKAnS..', 'rdzc nymy nmxb sroy', 'N/A', 'N/A', 'N/A', 'N/A', '81802969', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ACER ASPIRE', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '1/11/2024', NULL, NULL),
(1098, 'ARMANDO RODRIGUEZ', 19, 7, 'juridico@kabzo.org', 'VERSKAnS..', 'rdzc nymy nmxb sroy', 'N/A', 'N/A', 'N/A', 'N/A', '81802969', 'Laptop', 'MACBROOK PRO ', 'MAC', 'SSD', 'M1 PRO', '16', 'BUENA', '0', '', '2021', '01/01/2021', 'ACTIVA', '01/01/2024', 'OBSOLETO ', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1099, 'ARNOLD VAZQUEZ', 35, 15, 'tesoreria8@kabzo.org', 'rtuFRhdJ', 'xmks sxyo umub kjeh', 'N/A', 'N/A', 'N/A', 'N/A', 'android10', 'All in One', 'ACER Z3-710', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1101, 'AZAEL RANGEL', 33, 12, 'verificador@consultoriavrf.com\nverificador@kabzo.org', 'Verif.2021', 'ryguuhgzigtbmwtt', 'verificador@kabzo.org', '2023VERIf', 'qhkn mrgz enhw nyfj', '', '1608', 'Laptop', 'ASPIRE 3', 'ACER', 'SSD', 'I5 12va', '8', 'BUENA', '0', '', '2022', '01/01/2023', 'ACTIVA', '01/01/2030', '', 'N/A', 'HP ELITEBOOK 850 G3', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1102, 'AZENETH SANCHEZ', 32, 9, 'operacioncarso@gmail.com', 'carso.2023a', 'cebuhgjdzdsmsabh', 'N/A', 'N/A', 'N/A', 'N/A', '2024ajo', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/05/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1103, 'BRANDON ALANIS ESCAMILLA', 23, 7, 'mensajeria2@kabzo.org', 'S6wK5yrj7', 'tdjvqfqxmevsbfne', 'N/A', 'N/A', 'N/A', 'N/A', 'aVELINo93', 'All in One', '23-F290LA', 'HP', 'SSD', 'I3 7', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1104, 'DELL 5580', 23, 7, 'mensajeria3@kabzo.org', 'd0CIAc82', 'alvyaxupccwllsrc', 'N/A', 'N/A', 'N/A', 'N/A', 'jellybean', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP AIO 24-F0XX', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Candelario Cruz', 'N/A', '1/11/2024', NULL, NULL),
(1105, 'BRENDA JULISSA TORRES GOMEZ', 32, 9, 'pagos2@kabzo.org', 'MvQsAX6X', 'wqkcazkuuchwcaho', 'N/A', 'N/A', 'N/A', 'N/A', 'guayabas2', 'Micro CPU', 'ThinkCentre M720 Tiny', 'LENOVO', 'SSD', 'I5 9na', '8', 'BUENA', '$4,600.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'ACER', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1106, 'BRENDA LIMAS', 33, 12, 'verificador10@consultoriavrf.com', '4582168545', '', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/03/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1107, 'BRENDA SALDIVAR SILVA', 24, 8, 'juridico7@kabzo.org', '', '', '', '', '', '', '', 'Laptop', '5580', 'DELL', '', 'I5', '8', 'BUENA', '0', '', '2017', '01/01/2017', 'ACTIVA', '01/01/2024', 'OBSOLETO ', '', '', '', '', '', 'N/A', 'N/A', '', '', '', NULL, NULL),
(1108, 'CARLOS ALMAGUER', 35, 12, 'tesoreria7@kabzo.org', 'F7h2ybK2', 'hyasokhafdxcbjcb', 'N/A', 'N/A', 'N/A', 'N/A', 'android6', 'All in One', '9010', 'DELL', 'SSD', 'I7', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1109, 'CARLOS CAVAZOS', 29, 14, 'logistica3@kabzo.org', 't%6FyJP!!!!', 'qcqaiggmcewpxznp', 'N/A', 'N/A', 'N/A', 'N/A', 'CARROS2022', 'Laptop', 'E5540', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'DOMICILIO FISCAL', '01/01/2022', 'OBSOLETO ', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1110, 'CARLOS CAVAZOS', 29, 14, 'logistica3@kabzo.org', '', '', '', '', '', '', '', 'Laptop', 'Acer Aspire 5 15 Slim', 'ACER', 'SSD', 'I5 13VA', '16', 'NUEVA', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(1111, 'CARLOS CAVAZOS', 29, 14, 'logistica3@kabzo.org', '', '', '', '', '', '', '', 'All in One', '9000', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1112, 'CARLOS REYES', 33, 12, 'verificador7@consultoriavrf.com', 'Consvrf7.2021', 'nhoggscwjgpkqybg', 'N/A', 'N/A', 'N/A', 'N/A', 'santiago08.', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/03/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL OPTIPLEX 9030 AIO', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1113, 'CARMEN GABRIELA', 35, 15, 'tesoreria1@kabzo.org', '%$&/#%&%$&/%$54', 'xlnqlssvmdvzmaoj', 'N/A', 'N/A', 'N/A', 'N/A', 'vostro2022', 'Laptop', 'Inspiron 5502', 'DELL', 'SSD', 'I7', '16', 'BUENA', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1114, 'CINTHIA OVIEDO', 25, 8, 'juridico15@kabzo.org', 'Juridico15.', '', 'N/A', 'N/A', 'N/A', 'N/A', 'monterrey$1', 'All in One', ' 24-F1XX', 'HP', 'SSD', 'R3', '6', 'BUENA', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1115, 'CINTHIA LOZANO', 27, 8, 'desarrollo.org@rhfinder.com', 'XRPhV9qT<', 'mroadypyjckxfkit', 'N/A', 'N/A', 'N/A', 'N/A', 'L2F8F889', 'CPU', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1116, 'CYNTHIA ALANIS', 35, 15, 'tesoreria5@kabzo.org', 'RWKnAPmP', 'wsnw khxp yiva syws', 'N/A', 'N/A', 'N/A', 'N/A', 'android6', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 24-F0XX', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1117, 'DAMARIS TORRES', 33, 12, 'verificador2@consultoriavrf.com\nverificador2@kabzo.org', 'verif2.2021', 'ljxbkxnnautduphp', 'N/A', 'N/A', 'N/A', 'N/A', 'cocacola2.', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/03/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1118, 'DAMARIS TORRES', 33, 12, 'verificador2@consultoriavrf.com', 'verif2.2021', 'ljxbkxnnautduphp', 'verificador2@kabzo.org', '', 'N/A', 'N/A', '', 'Laptop', 'E5540', 'DELL', 'SSD', 'I5 4ta', '8', 'BUENA', '0', '', '2022', '01/01/2023', 'ACTIVA', '01/01/2030', '', 'N/A', 'DELL OPTIPLEX 9020 AIO', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1119, 'DANIEL MARROQUIN MARROQUIN', 23, 7, 'mensajeria6@kabzo.org', 'eadi1RtJ', 'mhbltwxqfaqdoqjb', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', 'ASPIRE Z3 710', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1120, 'DANIEL PEÑA', 35, 13, 'tesoreria10@kabzo.org', 'jnwinfiuwnf93', 'djtugjivmagfzkqi', 'N/A', 'N/A', 'N/A', 'N/A', 'movviaya1', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 24-F0XX', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1121, 'DANIELA COVARRUBIAS', 39, 11, 'COMMUNITYMANAGER@produccionesdobleb.com', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'macbook air 2024', 'MAC', 'SSD', 'm3', '16', 'BUENA', '0', '', '2023', '01/01/2024', 'ACTIVA', '01/01/2027', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1122, 'DIEGO EDUARDO ELICERIO VARGAS', 23, 7, 'mensajeria16@kabzo.org', 'me6Vuh6J', 'zhtlgmjyqjjlidzb', 'N/A', 'N/A', 'N/A', 'N/A', '22Tunas', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL OPTIPLEX 9010 AIO', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Fernando Saldaña', 'N/A', '1/11/2024', NULL, NULL),
(1123, 'ALAN ANDRES MARTINEZ RIVERA', 23, 7, 'mensajeria8@kabzo.org', 'xJNFsZn9', 'gccihysedigyzprq', 'N/A', 'N/A', 'N/A', 'N/A', 'cesped10', 'All in One', 'ASPIRE Z3-615', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1124, 'EDGAR RODRIGUEZ SILVA', 23, 7, 'facturacion2@kabzo.org', 'NL1UfloC', 'yttzmpipdluyyavr', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', '24-F1XX', 'HP', 'SSD', 'R3', '8', 'BUENA', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1125, 'EDGARDO BELTRAN', 40, 11, 'auditorinterno@kabzo.org', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1126, 'EDWIN JASSIEL GARZA MARTINEZ', 32, 9, 'pagos21@kabzo.org', 'wYmwZKBG', 'byghaofblbrvblfk', 'N/A', 'N/A', 'N/A', 'N/A', 'betabel7', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ALL IN ONE 24 F0XX', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Fernando Saldaña', 'N/A', '1/11/2024', NULL, NULL),
(1127, 'ELIZABETH TAMEZ', 19, 9, 'administradora@kabzo.org', 'hShXQRKU.', 'zaacqwrkwytbuggz', 'N/A', 'N/A', 'N/A', 'N/A', 'admin2022', 'Laptop', 'MACBROOK PRO ', 'MAC', 'SSD', 'M1 PRO', '16', 'BUENA', '0', '', '2021', '01/01/2021', 'ACTIVA', '01/01/2024', 'OBSOLETO ', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1128, 'ELIZABETH TAMEZ', 19, 9, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', 'IMAC 24 INCH', 'MAC', 'SSD', 'APPLE MI', '8', 'BUENA', '0', '', '2019', '01/01/2019', 'ACTIVA', '01/01/2022', 'OBSOLETO ', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1129, 'EMELY  CARMONA VALDEZ', 34, 9, 'gastos@kabzo.org', '{zEHzQ79.!\r', 'nsqlugtwetqwcpcf\r', 'N/A', 'N/A', 'N/A', 'N/A', 'android2', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 23154la', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1130, 'EMELY  CARMONA VALDEZ', 34, 9, 'gastos@kabzo.org', '', '', '', '', '', '', '', 'Laptop', 'ASUSX515J', 'ASUS', 'SSD', 'I5 10', '8', 'BUENA', '0', '', '2021', '01/01/2021', 'ACTIVA', '01/01/2028', 'N/A', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1131, 'EMMA ESTEFANIA OZUNA CHAIRES', 32, 9, 'pagos28@kabzo.org', 'xaTnhCxx', 'gmyvkmgdnqocgajn', 'N/A', 'N/A', 'N/A', 'N/A', 'calabaza1', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9010', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Jesus Pedro Saldaña MArtinez', 'N/A', '1/11/2024', NULL, NULL),
(1132, 'COMPUTADORA ANTERIOR  DE MORE', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'CUSTOM', 'CUSTOM', 'CUSTOM', 'SSD', 'R7', '32', 'BUENA', '0', '', '2018', '01/01/2019', 'STOCK', '01/01/2026', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1133, 'ERNESTO ABRAHAM PEREZ MORALES', 23, 7, 'mensajeria15@kabzo.org', '8trAaHY9', '', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 23R036LA', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Felix Aaron Najera Loza ', 'N/A', '1/11/2024', NULL, NULL),
(1134, 'ERNESTO RODRIGUEZ', 23, 7, 'facturainterna@kabzo.org', 'ye59sRLi', 'nndakihbzcexahdn', 'N/A', 'N/A', 'N/A', 'N/A', 'nopales99', 'All in One', 'ASPIRE Z3-710', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1135, 'EVA MARIA SUAREZ BARRON', 18, 16, 'asistente2.admin@kabzo.org', 'l6VT5F5}Bfe{\r', 'dpwxwytijmgczhem', 'N/A', 'N/A', 'N/A', 'N/A', 'android2', 'Laptop', 'ASUSX515J', 'ASUS', 'SSD', 'I5 10', '8', 'BUENA', '0', '', '2021', '01/01/2022', 'ACTIVA', '01/01/2029', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1136, 'EVELYN TAMEZ GARZA', 24, 8, 'juridico10@kabzo.org', '2022JURIDICOx', 'grqm jujv xpic jses', 'N/A', 'N/A', 'N/A', 'N/A', 'Russell2', 'CPU', 'COMPAQ 8200', 'HP', 'SSD', 'I5', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1137, 'ARANZA VERA', 34, 9, 'administracion.empresarial5@rhfinder.com', 'XCDNd&4<*C987uE=1', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'android2', 'All in One', 'ASPIRE Z3-710', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1138, 'FELIX AARON NAJERA LOZA', 22, 7, 'contabilidad14@kabzo.org', '8AxUGZJE', 'vbbgwapqczhygbyg', 'N/A', 'N/A', 'N/A', 'N/A', 'ARON4580', 'All in One', 'ASPIRE Z3-615', 'ACER', 'HDD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1139, 'FERNANDO SALDAÑA DE LA ROSA', 32, 9, 'receptor@kabzo.org', 'Rec0000...', 'bbluodvhgppqmfoo', 'N/A', 'N/A', 'N/A', 'N/A', '2022and12', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/05/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL 3020', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Ricardo Rodriguez', 'N/A', '1/11/2024', NULL, NULL),
(1140, 'FERNANDO SALDAÑA DE LA ROSA', 32, 9, 'receptor@kabzo.org', '', '', '', '', '', '', '', 'Laptop', 'E5540', 'DELL', 'SSD', 'I5', '8', 'MAL', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', 'OBSOLETO ', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1141, 'FRANCISCO CAVAZOS', 32, 9, 'pagos6@kabzo.org', 'e5RwN$k9', 'awndiklzivs1olkf', 'N/A', 'N/A', 'N/A', 'N/A', '897934353', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1142, 'FRANCISCO EDUARDO GAYTAN CONTRERAS', 23, 7, 'mensajeria13@kabzo.org', 'BPPhIgna', 'lpozmgifnkiiprps', 'N/A', 'N/A', 'N/A', 'N/A', 'AND8OREO', 'All in One', '24-F1XX', 'HP', 'SSD', 'R3', '8', 'BUENA', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1143, 'FRIDA PIÑA', 34, 9, 'administracionempresarial3@rhfinder.com', 'D9@]G!;i', 'zbgvgxzjeenzwxsz', 'N/A', 'N/A', 'N/A', 'N/A', 'android5', 'CPU', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1144, 'HUMBERTO ALANIS', 23, 7, 'facturainterna1@kabzo.org', 'fv09XHh3', '', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', '24-G200LA', 'HP', 'SSD', 'A8', '8', 'REGULAR', '0', '', '2016', '01/01/2017', 'ACTIVA', '01/01/2024', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1145, 'RECOJER EQUIPO', 27, 8, 'rhfinder@kabzo.org', 'Finder2024', '', 'N/A', 'N/A', 'N/A', 'N/A', '20242025', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'i5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'STOCK', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1146, 'HECTOR GOMEZ', 30, 10, 'mantenimiento@kabzo.org', '2022.manT', 'ylaoznilnvneattd', 'N/A', 'N/A', 'N/A', 'N/A', 'android13', 'Laptop', 'PROBOOK 650 G1', 'HP', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', 'OBSOLETO ', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1147, 'JOSE GUADALUPE RODRIGUEZ TREVIÑO', 32, 9, 'pagos30@kabzo.org', '5HH4vx2p', 'eqonfomggywqiqgv', 'N/A', 'N/A', 'N/A', 'N/A', '2022PERAS', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP 24-G200LA', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1148, 'IRVIN ABIEL RUEDA CARRIZALES', 32, 9, 'pagos15@kabzo.org', '6gjY75Yk', 'yribpiywfsjrhutc', 'N/A', 'N/A', 'N/A', 'N/A', '6naranjas', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ALL IN ONE 24 F0XX', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Paola Alonso', 'N/A', '1/11/2024', NULL, NULL),
(1149, 'ISAIRA MORANTES MONTERO', 22, 7, 'contabilidad1@kabzo.org', 'cont.2021', 'gzijxwtfsstlqubg', 'N/A', 'N/A', 'N/A', 'N/A', 'e20t9g99', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 24-F0XX', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Vladimir Rangel', 'N/A', '1/11/2024', NULL, NULL),
(1150, 'ISAIRA MORANTES MONTERO', 22, 7, 'contabilidad1@kabzo.org', 'cont.2021', 'gzijxwtfsstlqubg', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'IDEAPAD SLIM 3 15IAH8', 'HP', 'SSD', 'I5 12va', '8', 'BUENA', '0', '', '2019', '01/01/2019', 'ACTIVA', '01/01/2026', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1151, 'ISAMAR GUTIERREZ BALDERAS', 26, 8, 'auxiliarimss@rhfinder.com', '%&$344FGFDGDF', 'aihb jdgr aiho xdfl', 'N/A', 'N/A', 'N/A', 'N/A', '30062022', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/03/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL 9020', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Javer de Jesus LLado Paz', 'N/A', '1/11/2024', NULL, NULL),
(1152, 'JACKELINE ZAPATA', 33, 12, 'verificador9@consultoriavrf.com', 'Vrf2022.!', 'xyyziyycvfskwbmw', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/03/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 24-F0XX', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1153, 'JAVIER DE JESUS LLADO PAZ', 22, 7, 'contabilidad15@kabzo.org', 'KAIBnmM7', 'poezzkrsgehvcgoz', 'N/A', 'N/A', 'N/A', 'N/A', 'Promised123', 'All in One', '24-F1XX', 'HP', 'SSD', 'R3', '6', 'BUENA', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1154, 'JAVIER IRAM TAMEZ', 23, 7, 'mensajeria9@kabzo.org', '16wkDm45', 'ngtbmuaqoaorexzz', 'N/A', 'N/A', 'N/A', 'N/A', 'METRO2023', 'CPU', 'DELL 3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1155, 'JESSICA LAU', 35, 17, 'entregas.guadalajara1@kabzo.org', 'P2FVdxS8', 'qhviipbcfffkytii', 'N/A', 'N/A', 'N/A', 'N/A', '2022TeqUIla', 'Micro CPU', 'Elitedesk 800 G4', 'HP', 'SSD', 'I5 8va', '16', 'BUENA', '$4,399.00', '', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1156, 'JESUS ALBERTO LEON DURAN', 24, 8, 'juridico12@kabzo.org', 'eX77d2nJ', 'palzbovedaizjovh', 'N/A', 'N/A', 'N/A', 'N/A', '20220523', 'All in One', '24 F0XX', 'HP', 'SSD', 'A9', '8', 'REGULAR', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1157, 'JESUS ALFONSO', 31, 10, 'sistemas5@kabzo.org', 'Uuj5h4dp', 'lafluztykuivmcjo', 'N/A', 'N/A', 'N/A', 'N/A', 'qwerty12345', 'CPU', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1158, 'JESUS GARCIA', 35, 18, 'entregas.playa@kabzo.org', 'h5zEAJ6n', 'ckybuznhnzsxsvcf', 'N/A', 'N/A', 'N/A', 'N/A', '2022WATEr', 'All in One', '24-B007LA', 'HP', 'HD', 'A9', '8', 'REGULAR', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1159, 'JOEL TAMEZ', 19, 8, 'reportes@kabzo.org', 'AlERem.999', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'MACBROOK PRO ', 'MAC', 'SSD', 'M1 PRO', '16', 'BUENA', '0', '', '2021', '01/01/2021', 'ACTIVA', '01/01/2024', 'OBSOLETO ', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1160, 'JORDAN ALMAGUER', 28, 8, 'admon.juridica@kabzo.org', 'HZg2Vvcs.0', 'muhvhxlglgutqqiw', 'N/A', 'N/A', 'N/A', 'N/A', '13901265', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP ALL IN ONE 24-F0XX', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Sergio Salas', 'N/A', '1/11/2024', NULL, NULL),
(1161, 'JORGE ALEJANDRO ALMAGUER GÁNDARA', 23, 7, 'mensajeria10@kabzo.org', 'CfH3ZD70', 'udgzrhxzewcyviqe', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 24-f0x', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Gumercindo', 'N/A', '1/11/2024', NULL, NULL),
(1162, 'JORGE ELIUD PEREZ LEIJA', 32, 9, 'pagos24@kabzo.org', 'y^v0sW!p', 'ernarabyffiypueu', 'N/A', 'N/A', 'N/A', 'N/A', 'Eliud024', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'ACER Z3 710', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Sergio Hernandez', 'N/A', '1/11/2024', NULL, NULL),
(1163, 'JORGE TAMEZ', 41, 11, 'sistemas3@kabzo.org', 'fVt8aESR..', 'jdvheyfwkovzblgr', 'N/A', 'N/A', 'N/A', 'N/A', 'pascualin', 'CPU', '3020', 'DELL', 'HD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1164, 'JOSE ANDRES NIETO JUAREZ', 32, 9, 'atencion@kabzo.org', '4N>UB>QZM8%Z3eW*1', 'dbselrchkkpxdjms', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9010', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Luis Solis', 'N/A', '1/11/2024', NULL, NULL),
(1165, 'VACANTE REVISION', 26, 8, 'contrataciones@rhfinder.com', '2024.202678.', 'qgyy afwn quzy wurd', 'N/A', 'N/A', 'N/A', 'N/A', 'Grupo.2024', 'Micro CPU', 'Elitedesk 800 G4', 'HP', 'SSD', 'I5 8va', '16', 'BUENA', '$4,399.00', '1/03/2024', '2017', '01/01/2019', 'STOCK', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1166, 'JOSE PEDRO SALDAÑA MARTINEZ', 22, 7, 'contabilidad6@kabzo.org', 'JlaCUepF', 'bnpavbkceyouxyco', 'N/A', 'N/A', 'N/A', 'N/A', 'luna0203', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 23 -Q153LA', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Alexis Adrian Casas Torres', 'N/A', '1/11/2024', NULL, NULL),
(1167, 'JUAN BAUTISTA', 41, 11, 'desarrollo@kabzo.org', 't2PjpyFR', 'bnanljbncdvzobps', 'N/A', 'N/A', 'N/A', 'N/A', 'bironga22', 'CPU', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1168, 'JULIAN ACOSTA', 23, 7, 'facturacion1@kabzo.org', 'rt9DpVj8', 'qijglslhhrdagdli', 'N/A', 'N/A', 'N/A', 'N/A', '999grapes', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', '24-G206LA', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Emilio Guadalupe Torres Salinas', 'N/A', '1/11/2024', NULL, NULL),
(1169, 'JULIAN LOZANO', 33, 12, 'verificador1@consultoriavrf.com', 'VRFCons.21', 'ecgfmzvoafwgtmrs', 'N/A', 'N/A', 'N/A', 'N/A', 'pollo123_', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/03/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP 24', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1170, 'JULIO ALBERTOMATA VAZQUEZ', 23, 7, 'mensajeria7@kabzo.org', '65uxrXYg', 'chzsgzsjlvclfmqx', 'N/A', 'N/A', 'N/A', 'N/A', 'Terry315', 'All in One', 'ASPIRE Z3 710', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1171, 'JULIO CESAR RANGEL LOZA', 22, 7, 'contabilidad12@kabzo.org', 'Gc3K0w0P', 'qgrdpjglumcjxpel', 'N/A', 'N/A', 'N/A', 'N/A', 'chupete26', 'Micro CPU', 'Elitedesk 800 G4', 'HP', 'SSD', 'I5 8va', '16', 'BUENA', '$4,399.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1172, 'KEVIN MORENO', 21, 7, 'cobranza@kabzo.org', 'rfNpQ7QZ!!.', 'vdnpqgjrzoehzrzx', 'N/A', 'N/A', 'N/A', 'N/A', 'moreno28', 'Laptop', 'OMEN', 'HP', 'SSD', 'I5', '8', 'BUENA', '0', '', '2021', '01/01/2021', 'ACTIVA', '01/01/2028', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1173, 'KEVIN MORENO', 21, 7, 'cobranza@kabzo.org', 'rfNpQ7QZ!!.', 'vdnpqgjrzoehzrzx', 'N/A', 'N/A', 'N/A', 'N/A', 'moreno28', 'CPU', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1174, 'LESLY JANETH SALAZAR SILVA', 32, 9, 'pagos33@kabzo.org', '2024.P33', 'wbov fqms tyfo sgun', 'N/A', 'N/A', 'N/A', 'N/A', 'ajo2024', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/05/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1175, 'LESLY MICHELLE SALINAS IBARRA', 28, 8, 'juridico6@kabzo.org', 'X1vc8tPw8', 'xgdmwbsntcychaai', 'N/A', 'N/A', 'N/A', 'N/A', 'asper2', 'All in One', '9010', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1176, 'LIZBETH ULAY MARTINEZ SILVA', 21, 7, 'cobranza3@kabzo.org', 'cobranza3@kabzo.org', 'kabzo.1995', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', '9000', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1177, 'LIZETH AYALA', 35, 13, 'tesoreriag@kabzo.org', 'Grupo2024', 'zgrs tans brkx qifp', 'N/A', 'N/A', 'N/A', 'N/A', 'TES2021', 'All in One', 'ACER Z3-710', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1178, 'LUIS JAVIER ALANIS ESCAMILLA', 22, 7, 'contabilidad10@kabzo.org', 'tFDGI9lI', 'zdfxxeizjmoazyjq', 'N/A', 'N/A', 'N/A', 'N/A', 'luis1993', 'All in One', '24 F101LA', 'HP', 'SSD', 'R3', '6', 'BUENA', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1179, 'LUIS SOLIS', 29, 12, 'logistica2@kabzo.org', 'nP7uG&nK...\"', 'gtcmxxrgstgiisve', 'N/A', 'N/A', 'N/A', 'N/A', '16GUITARRAS', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP R036LA', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Emilio Guadalupe Torres Salinas', 'N/A', '1/11/2024', NULL, NULL),
(1180, 'MANUEL ANTONIO MORENO RANGEL', 32, 9, 'pagos17@kabzo.org', '6@HKP5vT\r', 'ymnwodcqrgdezdvm\r', 'N/A', 'N/A', 'N/A', 'N/A', 'Magcasda16', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'LENOVO FOB10044AR', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Candelario Cruz', 'N/A', '1/11/2024', NULL, NULL),
(1181, 'MARIA DE LOS ANGELES TREVIÑO', 38, 9, 'administracion@kabzo.org', '5Et5S{,e', 'gxpppcrhjxbguqxi\r', 'N/A', 'N/A', 'N/A', 'N/A', 'android1', 'Laptop', '15 dx3xxx', 'HP', 'SSD', 'I7', '8', 'BUENA', '0', '', '2020', '01/01/2021', 'ACTIVA', '01/01/2028', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1182, 'DIEGO HERRERA', 18, 16, 'logistica8@kabzo.org', '', '', '', '', '', '', '', 'All in One', '9000', 'DELL', 'SSD', 'I5', '8', '', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1183, 'ASISTENTE EDUCATIVO', 18, 16, 'asistente.educatia@kabzo.org', '', '', '', '', '', '', '', 'SIN EQUIPO', 'SIN EQUIPO', 'SIN MARCA', '', '', '', '', '0', '', 'N/A', '01/01/N/A', 'SIN EQUIPO', '', '', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1184, 'CHEF', 18, 16, 'chef@kabzo.org', '', '', '', '', '', '', '', 'SIN EQUIPO', 'SIN EQUIPO', 'SIN MARCA', '', '', '', '', '0', '', 'N/A', '01/01/N/A', 'SIN EQUIPO', '', '', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1185, 'MARIA DOLORES', 35, 13, 'tesoreria3@kabzo.org', 'kSnXYtEZ', 'wgrjweqvhgdxkqlf', 'N/A', 'N/A', 'N/A', 'N/A', 'android8', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 24-F0XX', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1186, 'MARIANA ESCOBEDO GARCIA', 39, 11, 'copy@produccionesdobleb.com', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'Prodesk 400 g4 dm', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$3,799.00', '1/01/2024', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1187, 'MARIANA MAYELA GARCÍA GONZALEZ', 32, 9, 'pagos20@kabzo.org', 'gKpt#Zlr', 'wiymrgvidueiulax', 'N/A', 'N/A', 'N/A', 'N/A', 'rambo123', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9030', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Sara Gonzalez Briagas', 'N/A', '1/11/2024', NULL, NULL),
(1188, 'MARTIN OVIDIO TAMEZ TAMEZ', 32, 9, 'asesores@kabzo.org', 'nz7KMDjn', 'rynehtrlprwinvlm', 'N/A', 'N/A', 'N/A', 'N/A', '22manzanas', 'Micro CPU', '800 G3 Elitedesk', 'HP', 'SSD', 'I5 6ta', '16', 'BUENA', '$2,702', '1/05/2023', '2015', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1189, 'MELISA ALEJANDRA RANGEL BUSTOS', 32, 9, 'pagos1@kabzo.org', 'y7qg6nSx', 'sinckbkeoxearwai', 'N/A', 'N/A', 'N/A', 'N/A', 'MandarinA', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9010', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Alvaro Homero', 'N/A', '1/11/2024', NULL, NULL),
(1190, 'MIGUEL ANGEL LOZANO QUINTANILLA', 32, 9, 'contacto@kabzo.org', 'StVpF4@2', 'jpdowyjjhgqwhqdp', 'N/A', 'N/A', 'N/A', 'N/A', 'pISTAcHes', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'hp 24b104la', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1191, 'MIGUEL FERNANDO REYNA ROCHA', 32, 9, 'pagos4@kabzo.org', 'wHaFBXAw', 'ospuaktyeqps', 'N/A', 'N/A', 'N/A', 'N/A', '22LIMONES', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ALL IN ONE 24-F1XX', 'N/A', 'N/A', '', '$2000', 'VENDIDA', 'Hector Franco Gomez', 'N/A', '1/11/2024', NULL, NULL),
(1192, 'MIKEL GARZA', 25, 8, 'juridico9@kabzo.org', '2024.jur9', 'mhyx siqw rbpp lejk\r', 'N/A', 'N/A', 'N/A', 'N/A', 'gabriela1', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/03/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1193, 'MONICA ANAHI AGUILAR GONZALEZ', 41, 11, 'optimizacion@kabzo.org', 'f9Z{jT7+o6U,', 'ldrlhnowbwujagwq', 'N/A', 'N/A', 'N/A', 'N/A', '', 'CPU', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1194, 'MONITOREO', 16, 22, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'All in One', '9000', 'DELL', 'SSD', 'i5', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1195, 'NANCY ALEJANDRA JASSO MARTINEZ', 24, 8, 'juridico5@kabzo.org', '', '', '', '', '', '', '', 'Laptop', 'E5540', 'DELL', 'HD', 'I5 ', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', 'OBSOLETO ', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1196, 'NANCY ALEJANDRA JASSO MARTINEZ', 24, 8, 'juridico5@kabzo.org', '', '', '', '', '', '', '', 'Laptop', 'Acer Aspire 5 15 Slim', 'ACER', 'SSD', 'I5 13VA', '16', 'NUEVA', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(1197, 'COMPUTADORA DE RUBEN LIMON', 34, 9, 'asistente.sistemas@kabzo.org', 'pV7i4VYk', 'bbvncfucuiibezmd', 'N/A', 'N/A', 'N/A', 'N/A', '', 'CPU', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1198, 'NAYLA SANTOS', 32, 9, 'pagos29@kabzo.org', '&rHaXct5', 'bvxxezxbizsmoaps', 'N/A', 'N/A', 'N/A', 'N/A', 'KITKATA4.4', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9010', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '1/11/2024', NULL, NULL),
(1199, 'NAZIRA JAIME', 33, 12, 'verificador4@consultoriavrf.com', 'vFK4.2021', 'wjnvooiluyrorwpb', 'N/A', 'N/A', 'N/A', 'N/A', 'broca.08', 'Micro CPU', 'Prodesk 400 g4 dm', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$3,799.00', '1/03/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP 24-G200LA', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1200, 'OMAR SOTERO FERNANDEZ SIERRA', 32, 9, 'pagos12@kabzo.org', '$qiL$#YE', 'zhjrizqdryxmbqfs', 'N/A', 'N/A', 'N/A', 'N/A', 'zanahoriA', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ALL IN ON NE F 40', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1201, 'OMAR VILLALOBOS', 31, 10, 'sistemas2@kabzo.org', 'metro2033', 'pxztvwzsbbpttpae', 'N/A', 'N/A', 'N/A', 'N/A', '', 'CPU', '9020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1202, 'OMAR VILLALOBOS', 31, 10, 'sistemas2@kabzo.org', 'metro2033', 'pxztvwzsbbpttpae', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'IDEAPAD SLIM 3 15IAH8', 'LENOVO', 'SSD', 'I5 12va', '8', 'BUENA', '0', '', '2022', '01/01/2022', 'ACTIVA', '01/01/2029', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1203, 'OZIEL DELGADO', 30, 10, 'mantenimiento@kabzo.org', '2022.manT', 'ylaoznilnvneattd', 'N/A', 'N/A', 'N/A', 'N/A', 'wild2022', 'All in One', '9010', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2014', '01/01/2016', 'ACTIVA', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1204, 'PEDRO SUAREZ BARRON', 33, 12, 'verificador11@kabzo.org', 'VERIF11.24', 'hoky dgsp ftrn ligk', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/03/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1205, 'PERLA JANETH ESPINOZA GONZALEZ', 22, 7, 'contabilidad4@kabzo.org', 'HUCeQWRN..', 'eafmmucpeofwehev', 'dir.contable@kabzo.org', '', '', 'N/A', '2062015', 'All in One', 'ASPIRE Z3-710', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1206, 'REYNALDO SANTOS MORENO', 32, 9, 'consultores@kabzo.org', 'S$g&GJhe', 'rrdmhynfssghscaz', 'N/A', 'N/A', 'N/A', 'N/A', '1tOLolOche', 'Micro CPU', '800 G3 Elitedesk', 'HP', 'SSD', 'I5 6ta', '16', 'BUENA', '$2,702', '1/05/2023', '2015', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1207, 'RICARDO RAMIREZ SEGOVIA', 32, 9, 'pagos31@kabzo.org', 'ZMbgvR4n', 'tghgnbzeywhtmslj', 'N/A', 'N/A', 'N/A', 'N/A', 'rica020821', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ALL IN ONE 24-F1XX', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1208, 'RICARDO RODRIGUEZ FERNANDEZ', 22, 7, 'contabilidad7@kabzo.org', 'GjaZ4ThB6', 'hfwptleodsqktmhz', 'N/A', 'N/A', 'N/A', 'N/A', 'angeange11', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1209, 'ROCIO ELIZABETH VAZQUEZ LOPEZ', 32, 9, 'pagos22@kabzo.org', 'nQR&vVwV', 'fwkzmhqxbawaqton', 'N/A', 'N/A', 'N/A', 'N/A', 'AND7NOGAUT', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'hp 24-f0xx', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1210, 'RUBEN ENRIQUE RODRIGUEZ GONZALEZ', 32, 9, 'pagos27@kabzo.org', 'cRPsHY@&', 'yqxbcrorspauwysc', 'N/A', 'N/A', 'N/A', 'N/A', '2255', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ALL IN ONE 24-F1XX', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1211, 'VACANTE ( COMPUTADORA ANTERIOR DE RUBEN LIMON)', 38, 11, 'administracion.empresarial2@rhfinder.com', 'rhf.2021', 'gxoacllpiezqjerk', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'MACBROOK PRO 2018', 'MAC', 'SSD', 'I7', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'VENTA', '01/01/2024', 'OBSOLETO ', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1212, 'GONZALO GONZALEZ ALONSO', 22, 7, 'contabilidad5@kabzo.org', 'Jy94jIRH', 'gwuppwwriebnfxbf', 'N/A', 'N/A', 'N/A', 'N/A', 'Pegzz705', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1213, 'SAMANTHA REYNA FLORES', 23, 7, 'facturacion@kabzo.org', 'ZC5S9tDm', 'axulrickjgpttqoj', 'avisosfacturacion@kabzo.org\r', '', '', 'respaldo.facturas@kabzo.org', 'valmaguer1', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ACER Z3-615', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1214, 'SAMARA', 23, 7, 'mensajeria5@kabzo.org', 'c7uMwtQR', 'nworojcsvkgscsdm', 'N/A', 'N/A', 'N/A', 'N/A', 'honeycumb', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL INSPIRON ONE 2330', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL);
INSERT INTO `computadora` (`Id_computadora`, `asignado_a`, `Id_departamento`, `Id_oficina`, `correo_asociado`, `contrasenaGmail1`, `contrasenaOutlook1`, `correoAsociado2`, `contrasenaGmail2`, `contrasenaOutlook2`, `correoAsociado3`, `contrasenaWindow`, `tipo`, `modelo`, `marca`, `tipoDeDisco`, `procesador`, `ram`, `condicion`, `costoEquipoActual`, `fechaDeAsignacion`, `anoDeProcesador`, `fechaDeLanzamiento`, `status`, `posibleFechaParaVenta`, `nuevaCompra`, `foto`, `pcAnterior`, `posibleAsignacion`, `total`, `costoAlComprar`, `costoALaVenta`, `disponibilidad`, `propietario_Destino`, `foto2`, `fechaDeReasignacion`, `revisado`, `comment`) VALUES
(1215, 'SANDRA VARGAS GARCIA', 32, 9, 'pagos5@kabzo.org', 'zjX#70aU', 'zxgh mypu pzsx etbw', 'N/A', 'N/A', 'N/A', 'N/A', 'arboles9', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ALL IN ONE 24-F1XX', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1216, 'SARA MARIA GONZALEZ BRIAGAS', 32, 9, 'pagos7@kabzo.org', 'Bw#!Jqu*', 'owhduvqbqywpzaf', 'N/A', 'N/A', 'N/A', 'N/A', 'fresas25', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ELITE ONE 800G1', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1217, 'SARAI BELLO ALBARRÁN', 40, 11, 'projectmanager@kabzo.org', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'IDEAPADSLIM', 'LENOVO', 'SSD', 'I5', '16', 'BUENA', '0', '', '2023', '01/01/2023', 'ACTIVA', '01/01/2030', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1218, 'SERGIO HERNANDEZ', 16, 14, 'seguridad@kabzo.org', '8pL753&#', '', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', '21', 'HP', 'SSD', 'CELERON', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1219, 'SERGIO SALAS', 35, 15, 'tesorerias3@kabzo.org', 'kSnXYtEZ&%$', 'aecikbukinenohsa', 'N/A', 'N/A', 'N/A', 'N/A', 'android9', 'All in One', '24-F0XX', 'HP', 'SSD', 'I5', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1220, 'SERVER MORE', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'SERVER', 'ENTERPRISES', 'HP', 'HDHDD', '', '16', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1221, 'SERVER DE MONITOREO', 16, 19, '', '', '', '', '', '', '', '', 'SERVER', 'hp proliant ml30 G10', 'HP', 'SSD', 'xeon e-2314', '16', 'BUENA', '40260.12', '', '2021', '01/01/2022', 'ACTIVA', '01/01/2029', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(1222, 'SERVER DE FACTURACION', 23, 20, '', '', '', '', '', '', '', '', 'SERVER', 'HP PROLIANT DL325', 'HP', 'SSD', 'AMD EPYC 7313P', '32', 'BUENA', '117444.2', '11/11/2023', '2021', '01/01/2022', 'ACTIVA', '01/01/2029', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(1223, 'SERVER DE BIOTIME', 27, 20, '', '', '', '', '', '', '', '', 'SERVER', 'PROLIANT ML30 G9', 'HP', 'HDD', 'XEON E3-1220 V5', '32', 'BUENA', '0', '', '2015', '01/01/2016', 'ACTIVA', '01/01/2023', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(1224, 'MARIA PAULA', 39, 11, 'styling@produccionesdobleb.com', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', 'ACER Z3-615', 'ACER', 'SSD', 'p', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1225, 'CINTHIA GONZALEZ', 43, 11, 'disenografico@produccionesdobleb.com', '', '', '', '', '', '', '', 'All in One', 'imac 2021', 'MAC', 'ssd', 'm1', '8', 'BUENA', '0', '', '2021', '01/01/2021', 'ACTIVA', '01/01/2024', 'OBSOLETO ', '', 'N/A', 'N/A', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1226, 'MARIANA MENDOZA', 43, 11, 'asistentediseno@produccionesdobleb.com', '', '', '', '', '', '', '', 'All in One', 'imac 2020', 'MAC', '', 'i5', '8', 'BUENA', '0', '', '2020', '01/01/2020', 'ACTIVA', '01/01/2023', 'OBSOLETO ', '', 'N/A', 'N/A', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1227, 'TERESA MONTIEL', 36, 14, 'paqueteria2@kabzo.org', 'T8SBN5FN', 'beaonqqzprozfspn', 'N/A', 'N/A', 'N/A', 'N/A', 'PENTIUM85', 'All in One', 'ASPIRE Z3-615', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1228, 'THALIA MEDINA', 33, 12, 'verificador3@consultoriavrf.com', 'VERI3.21', 'hbbcpwrcntmghrhx', 'N/A', 'N/A', 'N/A', 'N/A', 'pepsi.03', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/03/2023', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'DELL OPTIPLEX 9020 AIO', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1229, 'ULISES FELIX FLORES CASTILLO', 32, 9, 'pagos10@kabzo.org', 'DUBPQtGb', 'rdslyoyzwgivtgpi', 'N/A', 'N/A', 'N/A', 'N/A', '3062022', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9010', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1230, 'VACANTE (COMPUTADORA DE DISEÑO ANTERIOR MENTE DE ARQUITECTURA)', 31, 10, 'arquitectura@kabzo.org', 'CJE<=h8yS6&75dG>', 'hjxexmwunkopocep', 'N/A', 'N/A', 'N/A', 'N/A', '', 'CUSTOM', 'CUSTOM', 'CUSTOM', 'SSD', 'R7', '16', 'BUENA', '0', '', '2016', '01/01/2016', 'STOCK', '01/01/2023', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1231, 'PAOLA JAZMIN ALONSO SERVIN', 22, 7, 'contabilidad11@kabzo.org', 'tFDGI9lI', 'zdfxxeizjmoazyjq', 'N/A', 'N/A', 'N/A', 'N/A', 'luis1993', 'All in One', '24-F0XX', 'HP', 'SSD', 'A9', '8', 'REGULAR', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1232, 'RECOJER EQUIPO', 25, 8, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'CPU', '3020', 'DELL', 'SSD', 'R3', '6', 'BUENA', '0', '', '2019', '01/01/2020', 'VENTA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1233, 'REVISAR', 23, 7, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', 'ASPIRE Z3-615', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'VENTA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1234, 'REVISAR', 23, 7, 'mensajeria17@kabzo.org', 'ABCQWERTY2.', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'WAWAWATES9', 'SERVER', '1577J6S', 'LENOVO', 'SSD', 'I5', '8', 'BUENA', '0', '', '2011', '01/01/2012', 'VENTA', '01/01/2019', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1235, 'VACANTE COMPUTADORA DE ASISTENTE ', 30, 10, 'asistente@kabzo.org', '2022asistentE', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '313', 'All in One', '9010', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '1/07/2024', '2016', '01/01/2017', 'VENTA', '01/01/2024', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1236, 'JOSE DIONICIO', 30, 10, 'asistente@kabzo.org', '2022asistentE', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', '5580', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2017', '01/01/2017', 'ACTIVA', '01/01/2024', 'OBSOLETO ', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1237, 'YULIANA LIZETH RUEDA CARRIZALEZ', 32, 9, 'pagos14@kabzo.org', 'KzUznv4Z', 'xenzlnlvqowgmqdb', 'N/A', 'N/A', 'N/A', 'N/A', '20220603', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9010', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1238, 'NATALY REYNA', 34, 9, 'administracion.empresarial@rhfinder.com', 'cu+JdPe.', 'veysavouzilbivgd', 'N/A', 'N/A', 'N/A', 'N/A', '1234', 'CPU', 'MAC MINI', 'MAC', 'SSD', 'M1', '8', 'BUENA', '0', '', '2020', '01/01/2022', 'ACTIVA', '01/01/2025', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1239, 'SERGIO SALAS', 35, 15, 'tesorerias3@kabzo.org', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'Vostro 3500', 'DELL', 'SSD', 'I5', '16', 'BUENA', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1240, 'ANALISTA DE DATOS', 40, 11, 'analistaprocesos2@kabzo.org', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Micro CPU', 'Prodesk 400 g4 dm', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$3,799.00', '1/01/2024', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1241, 'MARIANA GONZALEZ', 40, 11, 'auxcalidad@kabzo.org', '', '', '', '', '', '', '', 'Laptop', 'IDEAPADSLIM', 'LENOVO', 'SSD', 'I5', '8', 'BUENA', '0', '', '2022', '01/01/2023', 'ACTIVA', '01/01/2030', 'N/A', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1242, 'ESTEFANI LOZANO', 27, 8, 'reclutamiento2@rhfinder.com', 'NNBu=6RM', 'evyuhnkihuucueld', 'N/A', 'N/A', 'N/A', 'N/A', '20220629', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'ASIGNACION POR CRECIMIENTO', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1243, 'VALERIA FLORES', 39, 21, 'operacion@produccionesdobleb.com', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', 'MACBOOK PRO 2023', 'MAC', 'SSD', 'M3 PRO', '18', 'BUENA', '0', '', '2023', '01/01/2024', 'ACTIVA', '01/01/2027', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1244, 'COMPUTADORAS DE DISEÑO ) ANTERIOR DE MORE', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'CUSTOM', 'CUSTOM', 'CUSTOM', 'SSD', 'R7', '32', 'BUENA', '0', '', '2018', '01/01/2019', 'STOCK', '01/01/2026', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1245, 'COMPUTADORAS DE DISEÑO ) ANTERIOR DE MORE', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'CUSTOM', 'CUSTOM', 'CUSTOM', 'SSD', 'R7', '32', 'BUENA', '0', '', '2018', '01/01/2019', 'STOCK', '01/01/2026', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1246, 'VALERIA RODRIGUEZ', 39, 11, 'edicionvideo@produccionesdobleb.com', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', 'imac 2024', 'MAC', 'SSD', 'm3', '16', 'BUENA', '0', '', '2023', '01/01/2024', 'ACTIVA', '01/01/2027', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1247, 'VALERIA VARGAS', 34, 9, 'administracion.empresarial1@rhfinder.com', '1ADEMPuNO', 'atjocnxpztosoehx', 'N/A', 'N/A', 'N/A', 'N/A', 'admemp1', 'All in One', '9020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9020', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1248, 'FATIMA MARTINEZ', 34, 9, 'administracionempresarial3@rhfinder.com', '', '', '', '', '', '', '', 'CPU', '3020', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2016', '01/01/2017', 'ACTIVA', '01/01/2024', '', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1249, 'NAYELI LUNA', 34, 9, 'administracion.empresarial5@rhfinder.com', '', '', '', '', '', '', '', 'All in One', 'ASPIRE Z3-615', 'ACER', 'SSD', 'I5', '8', 'BUENA', '0', '', '2014', '01/01/2015', 'ACTIVA', '01/01/2022', '', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1250, 'PUNTO DE VENTA', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'CPU', '500', 'ACTECK', 'HD', '', '', 'BUENA', '0', '', '2014', '01/01/2014', 'STOCK', '01/01/2021', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1251, 'VICTOR ALEJANDRO RUIZ GUTIERREZ', 22, 7, 'contabilidad18@kabzo.org', 'zxuUBxXg', 'cnzqjakrjgfzamap', 'N/A', 'N/A', 'N/A', 'N/A', 'valdo1340', 'CPU', 'COMPAQ 8200', 'HP', 'HD', 'I5', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1252, 'VITAL BETANCOURT', 32, 9, 'pagos11@kabzo.org', 'kBMpnDbM', 'kgmskhhtelxznrrt', 'N/A', 'N/A', 'N/A', 'N/A', '2022j06n', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9010', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1253, 'VLADIMIR RANGEL RODRIGUEZ', 32, 9, 'servicios@kabzo.org', 'frhGzY#3', 'deuatkydellfkkvh', 'N/A', 'N/A', 'N/A', 'N/A', 'caHUAteS22', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'DELL 9010', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1254, 'WENDY YARESSY VALDEZ VALDEZ', 23, 7, 'mensajeria12@kabzo.org', '2023ME12', 'ejdjlxpbpioehmxb', 'N/A', 'N/A', 'N/A', 'N/A', '2024Tunas', 'Micro CPU', 'EliteDesk 800 G5', 'HP', 'SSD', 'I5 9na', '16', 'BUENA', '$4,425.00', '1/01/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 24-G200LA', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1255, 'XAVIER ALANIS', 30, 10, 'sistemas@kabzo.org', '2022.SISTEMAx', 'ggaenhtyslheblgp', 'N/A', 'N/A', 'N/A', 'N/A', 'Pazcualazo', 'All in One', '24', 'HP', 'SSD', 'R5', '8', 'BUENA', '0', '', '2019', '01/01/2020', 'ACTIVA', '01/01/2027', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1256, 'XAVIER ALANIS', 30, 10, 'sistemas@kabzo.org', '2022.SISTEMAx', 'ggaenhtyslheblgp', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', '5580', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2017', '01/01/2017', 'ACTIVA', '01/01/2024', 'OBSOLETO ', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1257, 'HECTOR GOMEZ', 30, 10, 'mantenimiento@kabzo.org', '', '', '', '', '', '', '', 'CPU', 'WORKSTATION', 'DELL', 'HD', 'XEON', '16', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', '', '', '', '', '', 'N/A', 'N/A', '', '', '', NULL, NULL),
(1258, 'XIMENA REYNA FLORES', 23, 7, 'mensajeria11@kabzo.org', 'CfH3ZD70', 'udgzrhxzewcyviqe', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', 'ASPIRE Z3-710', 'ACER', 'SSD', 'P', '8', 'BUENA', '0', '', '2015', '01/01/2015', 'ACTIVA', '01/01/2022', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1259, 'YADIRA CARDENAS CASTRO', 24, 8, 'juridico8@kabzo.org', 'sOLDIERbOY', 'huju mmse vwdm qxlj', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', '9010', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1260, 'YESSICA CAVAZOS', 32, 9, 'pagos@kabzo,org', 'eJZRjgmG.', 'bwtimhuczqelamdv', 'N/A', 'N/A', 'N/A', 'N/A', 'admin.2022', 'Micro CPU', 'ProDesk 600 G4', 'HP', 'SSD', 'I5 8va', '8', 'BUENA', '$9,000.00', '1/05/2023', '2017', '01/01/2018', 'ACTIVA', '01/01/2025', '', 'N/A', 'HP ALL IN ONE 24-F1XX', 'N/A', 'N/A', '', 'N/A', 'REVISION', '', 'N/A', '', NULL, NULL),
(1261, 'YESSICA CAVAZOS', 32, 9, 'pagos@kabzo,org', '', '', '', '', '', '', '', 'Laptop', '5580', 'DELL', 'SSD', 'I5', '8', 'REGULAR', '0', '', '2018', '01/01/2018', 'ACTIVA', '01/01/2025', 'OBSOLETO ', '', '', '', '', '', 'N/A', '', '', '', '', NULL, NULL),
(1262, 'YOWALY VAZQUEZ', 35, 15, 'tesoreriar@kabzo.org', 'FvE3mJwL..!', 'kgqj zlrz nmzk ifch', 'N/A', 'N/A', 'N/A', 'N/A', 'android12', 'Micro CPU', 'OptiPlex 5070', 'DELL', 'SSD', 'i5 9na', '16', 'BUENA', '$4,679.00', '1/04/2024', '2017', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', 'HP 24-F0XX', 'N/A', 'N/A', '', 'N/A', 'DOMICILIOS', '', 'N/A', '', NULL, NULL),
(1263, 'GUARDADAS QUE NO SIRVEN ( EQUIPO ANTERIOR DE SISTEMAS)', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Laptop', 'Satellite l55-c5211r', 'TOSHIBA', 'SSD', 'i5', '8', 'MALO', '0', '', '2015', '01/01/2015', 'DOMICILIO FISCAL', '01/01/2022', '', 'N/A', '', 'domicilios', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1264, 'GUARDADAS QUE NO SIRVEN ( EQUIPO ANTERIOR DE EVA ASISTENTE)', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Laptop', '5580', 'DELL', 'SSD', 'i5', '8', 'MALO', '0', '', '2018', '01/01/2019', 'DOMICILIO FISCAL', '01/01/2026', '', 'N/A', '', 'domicilios', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1265, 'GUARDADAS QUE NO SIRVEN ( EQUIPO  ANTERIOR DE AZAEL VERIFICADOR)', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Laptop', 'ELITEBOOK 850 G3', 'HP', 'SSD', 'i5', '8', 'MALO', '0', '', '2016', '01/01/2016', 'DOMICILIO FISCAL', '01/01/2023', '', 'N/A', '', 'domicilios', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1266, 'GUARDADAS QUE NO SIRVEN ( EQUIPO ANTERIOR DE ISAIRA CONTABILIDAD)', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Laptop', 'E5540', 'DELL', 'SSD', 'i5', '8', 'MALO', '0', '', '2014', '01/01/2015', 'DOMICILIO FISCAL', '01/01/2022', 'OBSOLETO ', 'N/A', '', 'venta', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1267, 'GUARDADAS QUE NO SIRVEN (EQUIPO ANTERIOR DE AGENDA)', 31, 10, 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', 'Laptop', 'E6530 ', 'DELL', 'SSD', 'i5', '8', 'MALO', '0', '', '2012', '01/01/2013', 'DOMICILIO FISCAL', '01/01/2020', '', 'N/A', '', 'domicilios', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1268, 'GABRIELA LEAL', 27, 8, 'direccion@rhfinder.com', 'nXlLZPqO\r', 'tuheecrmsquodzqj\r', 'N/A', 'N/A', 'N/A', 'N/A', '', 'All in One', '9030', 'DELL', 'SSD', 'I5', '8', 'BUENA', '0', '', '2019', '01/01/2019', 'ACTIVA', '01/01/2026', '', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1269, 'GABRIELA LEAL', 27, 8, 'direccion@rhfinder.com', 'nXlLZPqO\r', 'tuheecrmsquodzqj\r', 'N/A', 'N/A', 'N/A', 'N/A', '', 'Laptop', '5590', 'DELL', 'SSD', 'i5', '16', 'BUENA', '0', '', '2019', '01/01/2019', 'ACTIVA', '01/01/2026', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', 'N/A', '', 'N/A', '', NULL, NULL),
(1270, 'VALERIA RODRIGUEZ', 45, 10, '', '', '', '', '', '', '', '', 'All in One', 'IMAC 2019  RAM 8  M1', 'MAC', 'SSD', 'M1', '8', 'REGULAR', '0', '', '', '', 'VENTA', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(1271, 'ANGEL PULIDO', 45, 10, '', '', '', '', '', '', '', '', 'Laptop', 'MAC 13\"', 'MAC', 'SSD', 'M2', '8', 'REGULAR', '0', '', '', '', 'VENTA', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(1272, 'GRECIA MODESTO', 45, 10, '', '', '', '', '', '', '', '', 'Laptop', 'MACBOOK PRO 2020', 'MAC', 'SSD', 'I5 8va', '8', 'REGULAR', '0', '', '', '', 'VENTA', '', '', '', '', '', '', '', '', '', '', '', '', NULL, NULL),
(1273, '1', 17, 8, '1', '1', '1', '1', '1', '1', '1', '1', '8', '8', 'SIN MARCA', '12', '12', '12', '8', '8', '2025-01-22', '8', '2025-02-01', '9', '2025-01-10', '12', '', '12', '12', '12', '12', '12', '12', '12', '', '2025-02-01', NULL, NULL),
(1277, 'NANCY ALEJANDRA JASSO MTZ', 20, 8, 'dev@kabzo.org', '12345', '12345', '12345', '12345', '12345', 'dev@kabzo.org', '12345', 'Desktop', 'MODELO', 'CUSTOM', 'HDD', '2', '2', 'Mala', '2', '2025-01-10', '2', '2025-02-07', 'Vendida', '2025-01-10', '123', '', 'no pc anterior', 'no posible asignacion', '129999', '14999', '6999', 'En Uso', 'no destino', '', '2025-01-30', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `Id_departamento` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`Id_departamento`, `nombre`) VALUES
(16, 'SEGURIDAD'),
(17, 'TESORERIA GUADALAJARA'),
(18, 'PERSONAL DOMESTICO'),
(19, 'CEO'),
(20, 'DIRECTOR'),
(21, 'COBRANZA'),
(22, 'CONTABILIDAD'),
(23, 'FACTURACION'),
(24, 'BANCOS'),
(25, 'DOMICILIOS'),
(26, 'IMSS'),
(27, 'RECURSOS HUMANOS'),
(28, 'JURIDICO'),
(29, 'LOGISTICA'),
(30, 'MANTENIMIENTO'),
(31, 'SISTEMAS'),
(32, 'OPERACIONES'),
(33, 'VERIFICACIÓN'),
(34, 'PRESUPUESTOS'),
(35, 'TESORERIA'),
(36, 'ENTREGAS'),
(37, 'TESORERIA PLAYA'),
(38, 'AGENDA CORPORATIVA'),
(39, 'PRODUCCIONES'),
(40, 'PROYECTOS'),
(41, 'OPTIMIZACION'),
(42, 'ARQUITECTURA'),
(43, 'DISE;O '),
(44, 'DOMICILIO FISCAL'),
(45, 'SIN DEPARTAMENTO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id_direcc` int(10) NOT NULL,
  `nombre_dir` varchar(250) DEFAULT NULL,
  `id_departamentos` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `director`
--

CREATE TABLE `director` (
  `id` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `director`
--

INSERT INTO `director` (`id`, `nombre`) VALUES
(1, 'Maria de los Angeles'),
(2, 'Nancy Alejandra'),
(3, 'Perla Janeth'),
(4, 'Angel Jordan'),
(5, 'Alan Hernandez'),
(6, 'Azael Rangel'),
(7, 'Yessica Cacazos'),
(8, 'Carmen Gabriela'),
(9, 'Carloz Cavazos'),
(10, 'Grecia Violeta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `domicilios`
--

CREATE TABLE `domicilios` (
  `id_domicilio` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `total` varchar(5) NOT NULL,
  `Id_departamento` int(10) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `municipio` varchar(80) NOT NULL,
  `ubicacion` varchar(90) NOT NULL,
  `empresa1` varchar(150) NOT NULL,
  `foto1` varchar(100) NOT NULL,
  `empresa2` varchar(100) NOT NULL,
  `foto2` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Empleados`
--

CREATE TABLE `Empleados` (
  `id` int(5) NOT NULL,
  `nombre` text NOT NULL,
  `departamento` text NOT NULL,
  `puesto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Empleados`
--

INSERT INTO `Empleados` (`id`, `nombre`, `departamento`, `puesto`) VALUES
(1, 'LUIS FELIPE TORRES GARZA', 'SEGURIDAD', '6'),
(2, 'LUZ ROSAS SALCEDO', 'TESORERIA GUADALAJARA', '6'),
(3, 'DIANA ESTHER SANCHEZ REYES', 'PERSONAL DOMESTICO', '6'),
(4, 'LUIS ARMANDO RODRIGUEZ SILVA', 'CEO', '2'),
(5, 'ALAN HERNANDEZ RUIZ', 'DIRECTOR', '3'),
(6, 'KEVIN MORENO DE LA ROSA', 'COBRANZA', '4'),
(7, 'LIZBETH ULAY MARTINEZ SILVA', 'COBRANZA', '6'),
(8, 'JORGE RONALDO MARROQUIN MARROQUIN', 'COBRANZA', '6'),
(9, 'PERLA JANETH ESPINOZA GONZALEZ', 'DIRECTOR', '3'),
(10, 'ISAIRA MORANTES MONTERO', 'CONTABILIDAD', '4'),
(11, 'FELIX AARON NAJERA LOZA', 'CONTABILIDAD', '5'),
(12, 'VICTOR ALEJANDRO RUIZ GUTIERREZ', 'CONTABILIDAD', '5'),
(13, 'LUIS JAVIER ALANIS ESCAMILLA', 'CONTABILIDAD', '6'),
(14, 'JULIO CESAR RANGEL LOZA', 'CONTABILIDAD', '6'),
(15, 'RICARDO RODRIGUEZ FERNANDEZ', 'CONTABILIDAD', '6'),
(16, 'JOSÉ PEDRO SALDAÑA MARTINEZ', 'CONTABILIDAD', '6'),
(17, 'JAVIER DE JESUS LLADO PAZ', 'CONTABILIDAD', '6'),
(18, 'HUMBERTO ALEJANDRO MARTÍNEZ MORALES', 'CONTABILIDAD', '6'),
(19, 'GONZALO GONZALEZ ALONSO', 'CONTABILIDAD', '6'),
(20, 'FATIMA ABIGAIL GARZA LOPEZ', 'CONTABILIDAD', '6'),
(21, 'CAROL NICOLE GARCIA RUIZ Y CAVAZOS', 'CONTABILIDAD', '6'),
(22, 'PAOLA JAZMIN ALONSO SERVIN', 'CONTABILIDAD', '6'),
(23, 'SAMANTHA REYNA FLORES', 'FACTURACION', '4'),
(24, 'JOSE JULIAN ACOSTA RODRIGUEZ', 'FACTURACION', '5'),
(25, 'EDGAR RODRIGUEZ SILVA', 'FACTURACION', '5'),
(26, 'WENDY YARESY VALDEZ VALDEZ', 'FACTURACION', '6'),
(27, 'ANDREA NICOLE CANTU OROZCO', 'FACTURACION', '6'),
(28, 'FRANCISCO EDUARDO GAYTAN CONTRERAS', 'FACTURACION', '6'),
(29, 'BRANDON ALANIS ESCAMILLA', 'FACTURACION', '6'),
(30, 'JULIO ALBERTO MATA', 'FACTURACION', '6'),
(31, 'BRENDA FABIOLA GANDARA', 'FACTURACION', '6'),
(32, 'JORGE ALEJANDRO ALMAGUER GANDARA', 'FACTURACION', '6'),
(33, 'DANIEL MARROQUIN MARROQUIN', 'FACTURACION', '6'),
(34, 'ERNESTO ABRAHAM PEREZ MORALES', 'FACTURACION', '6'),
(35, 'JAVIER IRAM MARTINEZ TAMEZ', 'FACTURACION', '6'),
(36, 'ALAN ANDRES MARTINEZ RIVERA', 'FACTURACION', '6'),
(37, 'ERNESTO RODRIGUEZ ALVAREZ', 'FACTURACION', '6'),
(38, 'AARON GARCÍA VALDEZ', 'FACTURACION', '6'),
(39, 'DIEGO EDUARDO ELICERIO VARGAS', 'FACTURACION', '6'),
(40, 'SAMARA IVETTE SOTO PEÑA', 'FACTURACION', '6'),
(41, 'HUMBERTO ALANIS SIERRA', 'FACTURACION', '6'),
(42, 'XIMENA REYNA FLORES', 'FACTURACION', '6'),
(43, 'ROBERTO LEAL TAPIA', 'FACTURACION', '6'),
(44, 'NANCY ALEJANDRA JASSO MTZ', 'DIRECTOR', '3'),
(45, 'BRENDA SALDIVAR SILVA', 'BANCOS', '4'),
(46, 'EVELYN TAMEZ GARZA', 'BANCOS', '5'),
(47, 'ALAN ALFREDO SANCHEZ LOPEZ', 'BANCOS', '6'),
(48, 'JESUS ALBERTO LEON DURAN', 'BANCOS', '6'),
(49, 'LIZETH YADIRA CARDENAS CASTRO', 'BANCOS', '6'),
(50, 'ALAN JAHIR GONZALEZ MIRANDA', 'BANCOS', '6'),
(51, 'CINTHIA GABRIELA OVIEDO ESPINOZA', 'DOMICILIOS', '4'),
(52, 'MIKEL GARZA CARRILLO', 'DOMICILIOS', '5'),
(53, 'LUIS ALBERTO ESCOBAR DEL RAZO', 'DOMICILIOS', '6'),
(54, 'ANGEL JORDAN ALMAGUER', 'DIRECTOR', '3'),
(55, 'ALBA YARESSI MARROQUIN MARROQUIN', 'IMSS', '4'),
(56, 'ISAMAR GUTIERREZ BALDERAS', 'IMSS', '5'),
(57, 'GABRIELA LEAL GONZALEZ', 'RECURSOS HUMANOS', '4'),
(58, 'CYNTHIA ANAHI LOZANO HERNANDEZ', 'RECURSOS HUMANOS', '5'),
(59, 'STEPHANIE LIZBETH LOZANO HERNANDEZ', 'RECURSOS HUMANOS', '6'),
(60, 'MARIA MIRTHALA SALAZAR SALAZAR', 'RECURSOS HUMANOS', '6'),
(61, 'ANGEL GABRIEL ARANDA PUENTE', 'RECURSOS HUMANOS', '6'),
(62, 'ALONDRA MARIBEL VARGAS', 'JURIDICO', '4'),
(63, 'LESLY MICHELLE SALINAS IBARRA', 'JURIDICO', '6'),
(64, 'JOEL AYARZAGOITA TAMEZ', 'CEO', '2'),
(65, 'CARLOS CAVAZOS', 'DIRECTOR', '3'),
(66, 'EMILIO GUADALUPE TORRES SALINAS', 'LOGISTICA', '4'),
(67, 'JUAN LUIS SOLIS CAVAZOS', 'LOGISTICA', '5'),
(68, 'DANIEL ALBERTO SALGADO TORRES', 'LOGISTICA', '6'),
(69, 'GUMERCINDO GUERRERO VALLE', 'LOGISTICA', '6'),
(70, 'SERGIO ANTONIO GONZALEZ BRISEÑO', 'LOGISTICA', '6'),
(71, 'CARLOS ALBERTO CORTES RIOS', 'LOGISTICA', '6'),
(72, 'ARMANDO HERRERA GONZALEZ', 'LOGISTICA', '6'),
(73, 'RAMON MENDOZA MORALES', 'LOGISTICA', '6'),
(74, 'SERGIO HERNANDEZ MARTINEZ', 'SEGURIDAD', '4'),
(75, 'GAUDENCIO DE LA CRUZ MORALES', 'SEGURIDAD', '5'),
(76, 'CANDELARIO EDGAR RUIZ DE LA CRUZ', 'SEGURIDAD', '6'),
(77, 'MANUEL HERNANDEZ REYES', 'SEGURIDAD', '6'),
(78, 'ANTONIO CEDEÑO MARTINEZ', 'SEGURIDAD', '6'),
(79, 'AQUILES JORDAN GOMEZ', 'SEGURIDAD', '6'),
(80, 'OLEGARIO SANTIAGO BAUTISTA', 'SEGURIDAD', '6'),
(81, 'MIGUEL ANGEL MONROY RUBIO', 'SEGURIDAD', '6'),
(82, 'XAVIER ALEXANDER ALANIS SILVA', 'DIRECTOR', '3'),
(83, 'JONATHAN OZIEL DELGADO ZUÑIGA', 'MANTENIMIENTO', '5'),
(84, 'BRANDON ARNOLDO VALDES MOYA', 'MANTENIMIENTO', '6'),
(85, 'ALVARO HOMERO MARTÍNEZ CANTÚ', 'MANTENIMIENTO', '6'),
(86, 'JOSE OMAR VILLALOBOS SALINAS', 'SISTEMAS', '5'),
(87, 'JESUS ALFONSO AYALA REYES', 'SISTEMAS', '6'),
(88, '(ELY) YOLANDA ELIZABETH TAMEZ PEÑA', 'CEO', '2'),
(89, 'YESSICA CAVAZOS', 'DIRECTOR', '3'),
(90, 'FERNANDO SALDAÑA DE LA ROSA', 'OPERACIONES', '4'),
(91, 'ALONDRA MATA FLORES', 'OPERACIONES', '5'),
(92, 'REYNALDO SANTOS MORENO', 'OPERACIONES', '5'),
(93, 'MARTIN OVIDIO TAMEZ TAMEZ', 'OPERACIONES', '5'),
(94, 'JOSE ANDRES NIETO JUAREZ', 'OPERACIONES', '5'),
(95, 'VLADIMIR RANGEL RODRIGUEZ', 'OPERACIONES', '5'),
(96, 'MIGUEL ANGEL LOZANO QUINTANILLA', 'OPERACIONES', '5'),
(97, 'ULISES FELIX FLORES CASTILLO', 'OPERACIONES', '6'),
(98, 'MIRIAM REBECA VILLARREAL LUNA', 'OPERACIONES', '6'),
(99, 'JORGE ELIUD PEREZ LEIJA', 'OPERACIONES', '6'),
(100, 'OMAR SOTERO FERNANDEZ SIERRA', 'OPERACIONES', '6'),
(101, 'ANGELES SARAIN RIVAS ELIAS', 'OPERACIONES', '6'),
(102, 'MIGUEL FERNANDO REYNA ROCHA', 'OPERACIONES', '6'),
(103, 'MELISA ALEJANDRA RANGEL BUSTOS', 'OPERACIONES', '6'),
(104, 'RICARDO RAMIREZ SEGOVIA', 'OPERACIONES', '6'),
(105, 'IRVIN ABIEL RUEDA CARRIZALES', 'OPERACIONES', '6'),
(106, 'BLANCA ESTELA FLORES GALLARDO', 'OPERACIONES', '6'),
(107, 'SARA MARIA GONZALEZ BRIAGAS', 'OPERACIONES', '6'),
(108, 'EDWIN JASSIEL GARZA MARTINEZ', 'OPERACIONES', '6'),
(109, 'MANUEL ANTONIO MORENO RANGEL', 'OPERACIONES', '6'),
(110, 'EMMA ESTEFANIA OZUNA CHAIRES', 'OPERACIONES', '6'),
(111, 'ELIUD FELIX FLORES CASTILLO', 'OPERACIONES', '6'),
(112, 'JOSÉ GUADALUPE RODRIGUEZ TREVIÑO', 'OPERACIONES', '6'),
(113, 'RUBEN VITAL BETANCOURT', 'OPERACIONES', '6'),
(114, 'ROCIO ELIZABETH VAZQUEZ LOPEZ', 'OPERACIONES', '6'),
(115, 'SANDRA ELIZABETH VARGAS GARCÍA', 'OPERACIONES', '6'),
(116, 'AZENETH SANCHEZ VALDEZ', 'OPERACIONES', '6'),
(117, 'BRENDA JULISSA TORRES GOMEZ', 'OPERACIONES', '6'),
(118, 'LESLIE JEANNETH SALAZAR SILVA', 'OPERACIONES', '6'),
(119, 'ALAN AZAEL SERNA GARZA', 'OPERACIONES', '6'),
(120, 'FRANCISCO CAVAZOS LEAL', 'OPERACIONES', '6'),
(121, 'YULIANA LIZETH RUEDA CARRIZALES', 'OPERACIONES', '6'),
(122, 'NAYLA VERANIA SANTOS MORENO', 'OPERACIONES', '6'),
(123, 'EYMI AYLIN RAYAS SANTOS', 'OPERACIONES', '6'),
(124, 'AZAEL RANGEL RODRIGUEZ', 'DIRECTOR', '3'),
(125, 'DAMARIS TORRES OLIVARES', 'VERIFICACION', '4'),
(126, 'JULIAN LOZANO GARZA', 'VERIFICACION', '5'),
(127, 'FLOR THALIA MEDINA GALLEGOS', 'VERIFICACION', '5'),
(128, 'LUIS GERARDO PEÑA BELTRAN', 'VERIFICACION', '6'),
(129, 'ANDREA FLORES LOZANO', 'VERIFICACION', '6'),
(130, 'DANIELA NOHEMI GONZALEZ TORRES', 'VERIFICACION', '6'),
(131, 'BRENDA ALICIA LIMAS GARCIA', 'VERIFICACIÓN', '6'),
(132, 'BRISEIDA JACKELINE ZAPATA SANCHEZ', 'VERIFICACIÓN', '6'),
(133, 'NAZIRA YARESSI JAIME MARTINEZ', 'VERIFICACIÓN', '6'),
(134, 'PEDRO SUAREZ BARRON', 'VERIFICACIÓN', '6'),
(135, 'EMELY RUBI CARMONA VALDEZ', 'PRESUPUESTOS', '4'),
(136, 'VALERIA ALEJANDRA VARGAS GARCIA', 'PRESUPUESTOS', '5'),
(137, 'CLARA NATALIE REYNA ROCHA', 'PRESUPUESTOS', '5'),
(138, 'KAREN ARANTXA ROSALES VERA', 'PRESUPUESTOS', '6'),
(139, 'FRIDA DENISSE PIÑA GOMEZ', 'PRESUPUESTOS', '6'),
(140, 'NANCY NAYELI LUNA AREVALO', 'PRESUPUESTOS', '6'),
(141, 'FATIMA LUCIA MARTINEZ RODRIGUEZ', 'PRESUPUESTOS', '6'),
(142, 'CARMEN GABRIELA GONZALEZ GONZALEZ', 'DIRECTOR', '3'),
(143, 'SERGIO SALAS SALAZAR', 'TESORERIA', '4'),
(144, 'YOWALY VAZQUEZ VALDES', 'TESORERIA', '5'),
(145, 'LIZETH VERONICA AYALA AGUILAR', 'TESORERIA', '5'),
(146, 'CYNTHIA JANETH ALANIS SUAREZ', 'TESORERIA', '6'),
(147, 'ARNOLD ANTONIO VAZQUEZ VALDES', 'TESORERIA', '6'),
(148, 'CARLOS ALMAGUER SALAZAR', 'TESORERIA', '6'),
(149, 'MARIA DOLORES VILLAGOMEZ HERNANEZ', 'TESORERIA', '6'),
(150, 'DANIEL PEÑA MARTINEZ', 'TESORERIA', '6'),
(151, 'NADIA ISELA ELIZONDO TREVIÑO', 'TESORERIA', '6'),
(152, 'MARIA AURORA ANDRADE MENDOZA', 'ENTREGAS', '5'),
(153, 'TERESA MONTIEL CASTILLO', 'ENTREGAS', '6'),
(154, 'ALEXIS ADRIAN CASAS TORRES', 'ENTREGAS', '6'),
(155, 'JUAN JESUS GARCIA ALANIS', 'TESORERIA PLAYA', '6'),
(156, 'JESSICA LILIANA LAU CONTRERAS', 'TESORERIA GUADALAJARA', '6'),
(157, 'ABBY', 'CEO', '2'),
(158, 'ANGELES TREVIÑO', 'DIRECTOR', '3'),
(159, 'EVAMARIA SUAREZ BARRON', 'PERSONAL DOMESTICO', '4'),
(160, 'MARIA MAGDALENA HERNANDEZ', 'PERSONAL DOMESTICO', '5'),
(161, 'ALEJANDRA SILVA GARCIA', 'PERSONAL DOMESTICO', '6'),
(162, 'RAMON ORTEGA HERNANDEZ', 'PERSONAL DOMESTICO', '6'),
(163, 'ZOILA ROLDAN MARTINEZ', 'PERSONAL DOMESTICO', '6'),
(164, 'DIEGO ARMANDO HERRERA SALAS', 'PERSONAL DOMESTICO', '6'),
(165, 'JOSE DIONICIO MACIAS SALAZAR', 'PERSONAL DOMESTICO', '6'),
(166, 'ANGEL ALEJANDRO PULIDO BARRON', 'AGENDA CORPORATIVA', '6'),
(167, 'LOURDES CAROLINA VARGAS ORTIZ', 'AGENDA CORPORATIVA', '6'),
(168, 'GRECIA VIOLETA MODESTO PRIETO', 'DIRECTOR', '3'),
(169, 'ALEXA BANDALA SANCHEZ', 'PRODUCCIONES', '4'),
(170, 'VALERIA FLORES MORALES', 'PRODUCCIONES', '5'),
(171, 'DANIELA COVARRUBIAS ORTIZ', 'PRODUCCIONES', '6'),
(172, 'VALERIA RODRIGUEZ MARTINEZ', 'PRODUCCIONES', '6'),
(173, 'CYNTHIA ISABEL GONZALEZ RAMIREZ', 'PRODUCCIONES', '6'),
(174, 'MARIANA MONSERRAT MENDOZA NAVARRO', 'PRODUCCIONES', '6'),
(175, 'MARIA PAULA ROCHIN CERECER', 'PRODUCCIONES', '6'),
(176, 'CLAUDIA CAROLINA REYES FLORES', 'PRODUCCIONES', '6'),
(177, 'SARAI BELLO ALBARRAN', 'PROYECTOS', '4'),
(178, 'DAVID JORGE TAMEZ SALDIVAR', 'OPTIMIZACION', '5'),
(179, 'JUAN BAUTISTA HERNANDEZ', 'PROYECTOS', '5'),
(180, 'MONICA ANAHI AGUILAR GONZALEZ', 'OPTIMIZACION', '6'),
(181, 'EDGARDO DARIEL BELTRAN MARTINEZ', 'PROYECTOS', '6'),
(182, 'MARIANA GONZALEZ VILLARREAL', 'PROYECTOS', '6'),
(183, 'JONATHAN BERNARDO SOTO GONZALEZ', 'PROYECTOS', '6');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lider`
--

CREATE TABLE `Lider` (
  `id` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Lider`
--

INSERT INTO `Lider` (`id`, `nombre`) VALUES
(3, 'FELIX AARON NAJERA LOZA'),
(4, 'VICTOR ALEJANDRO RUIZ GUTIERREZ'),
(5, 'JOSE JULIAN ACOSTA RODRIGUEZ'),
(6, 'EDGAR RODRIGUEZ SILVA'),
(7, 'EVELYN TAMEZ GARZA'),
(8, 'MIKEL GARZA CARRILLO'),
(9, 'ISAMAR GUTIERREZ BALDERAS'),
(10, 'CYNTHIA ANAHI LOZANO HERNANDEZ'),
(11, 'JUAN LUIS SOLIS CAVAZOS'),
(12, 'GAUDENCIO DE LA CRUZ MORALES'),
(13, 'JONATHAN OZIEL DELGADO ZUÑIGA'),
(14, 'JOSE OMAR VILLALOBOS SALINAS'),
(15, 'ALONDRA MATA FLORES'),
(16, 'REYNALDO SANTOS MORENO'),
(17, 'MARTIN OVIDIO TAMEZ TAMEZ'),
(18, 'JOSE ANDRES NIETO JUAREZ'),
(19, 'VLADIMIR RANGEL RODRIGUEZ'),
(20, 'MIGUEL ANGEL LOZANO QUINTANILLA'),
(21, 'JULIAN LOZANO GARZA'),
(22, 'FLOR THALIA MEDINA GALLEGOS'),
(23, 'VALERIA ALEJANDRA VARGAS GARCIA'),
(24, 'CLARA NATALIE REYNA ROCHA'),
(25, 'YOWALY VAZQUEZ VALDES'),
(26, 'LIZETH VERONICA AYALA AGUILAR'),
(27, 'MARIA AURORA ANDRADE MENDOZA'),
(28, 'MARIA MAGDALENA HERNANDEZ'),
(29, 'VALERIA FLORES MORALES'),
(30, 'DAVID JORGE TAMEZ SALDIVAR'),
(31, 'JUAN BAUTISTA HERNANDEZ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `id` int(10) NOT NULL,
  `fullName` varchar(250) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `tokenUser` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`id`, `fullName`, `email`, `password`, `tokenUser`) VALUES
(1, 'Urian Viera ', 'iamdeveloper86@gmail.com', '123', NULL),
(18, 'Juan Bautista', 'desarrollo@kabzo.org', '15a0', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mobiliario`
--

CREATE TABLE `mobiliario` (
  `Id_mobiliario` int(11) NOT NULL,
  `foto` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `modelo` varchar(30) NOT NULL,
  `marca` varchar(30) NOT NULL,
  `condicion` varchar(20) NOT NULL,
  `total` varchar(10) NOT NULL,
  `Id_oficina` int(11) NOT NULL,
  `Id_departamento` int(11) NOT NULL,
  `costo` int(20) NOT NULL,
  `fecha_compra` varchar(30) DEFAULT NULL,
  `vida_util` varchar(30) DEFAULT NULL,
  `disponibilidad` varchar(20) NOT NULL,
  `asignado_a` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mobiliario`
--

INSERT INTO `mobiliario` (`Id_mobiliario`, `foto`, `descripcion`, `modelo`, `marca`, `condicion`, `total`, `Id_oficina`, `Id_departamento`, `costo`, `fecha_compra`, `vida_util`, `disponibilidad`, `asignado_a`) VALUES
(407, 'nohay.jpg', 'DESCRIPCION ', 'modelo', 'MARCA ', 'condicion', '1999', 11, 40, 2999, '2024-02-20', '2 años', 'no', 183);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelos`
--

CREATE TABLE `modelos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modelos`
--

INSERT INTO `modelos` (`id`, `nombre`, `descripcion`) VALUES
(1, 'usuarios', NULL),
(2, 'mobiliarios', NULL),
(3, 'celulares', NULL),
(4, 'computadoras', NULL),
(5, 'catalogos', NULL),
(6, 'domicilios', NULL),
(7, 'seguridad', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oficina`
--

CREATE TABLE `oficina` (
  `Id_Oficina` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(70) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `oficina`
--

INSERT INTO `oficina` (`Id_Oficina`, `nombre`, `direccion`) VALUES
(6, 'OFICINA', NULL),
(7, 'CUBIK DER', NULL),
(8, 'CUBIK IZQ', NULL),
(9, 'TEC', NULL),
(10, '102', NULL),
(11, 'KABZO', NULL),
(12, 'CAPITEL 1004', NULL),
(13, 'CAPITEL 905', NULL),
(14, '806', NULL),
(15, '807', NULL),
(16, 'HERRADURA', NULL),
(17, 'GUADALAJARA', NULL),
(18, 'PLAYA', NULL),
(19, 'MONITOREO', NULL),
(20, 'CUBIK DERECHO', NULL),
(21, 'HOME OFFICE', NULL),
(22, 'N/A', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`) VALUES
(1, 'desarrollo@kabzo.org', 'aab660aa0c8796327866a1d2f68a6286f5a4dd01d85b28e8bdab1d16c293ce94060d28bd2b3a1155ed35645ead81b6454c1c', '2024-07-16 18:17:54'),
(2, 'desarrollo@kabzo.org', '366cb28e5377db7702165c6c5a0ba13e2c73bbd7a44a2e17a2539f132fb85d0caefa920eabd77b3e4f2c5d464cc32dfce4e8', '2024-07-16 18:25:25'),
(3, 'desarrollo@kabzo.org', 'a43c8adc5aa1a07e60c2e8d815413efa5c2836e9eb6247d8aebe8f1e802575c62133e3933957d64ff7e55b66befd41783ab1', '2024-07-16 18:32:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `descripcion`) VALUES
(1, 'ver', NULL),
(2, 'crear', NULL),
(3, 'editar', NULL),
(4, 'eliminar', NULL),
(8, 'ver_contrasenas', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_modelos`
--

CREATE TABLE `permisos_modelos` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `modelo_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos_modelos`
--

INSERT INTO `permisos_modelos` (`id`, `rol_id`, `modelo_id`, `permiso_id`) VALUES
(775, 1, 1, 1),
(776, 1, 1, 2),
(777, 1, 1, 3),
(778, 1, 1, 4),
(779, 1, 1, 8),
(780, 1, 3, 1),
(781, 1, 3, 2),
(782, 1, 3, 3),
(783, 1, 3, 8),
(784, 1, 4, 1),
(785, 1, 4, 2),
(786, 1, 4, 3),
(787, 1, 4, 8),
(788, 1, 5, 1),
(789, 1, 5, 2),
(790, 1, 5, 3),
(791, 1, 7, 1),
(792, 2, 1, 1),
(793, 2, 2, 1),
(794, 2, 2, 2),
(795, 2, 2, 3),
(796, 2, 6, 1),
(797, 2, 6, 2),
(798, 2, 6, 3),
(799, 3, 1, 1),
(800, 3, 2, 1),
(801, 3, 3, 1),
(802, 3, 3, 8),
(803, 3, 4, 1),
(804, 3, 4, 8),
(805, 4, 1, 1),
(806, 4, 2, 1),
(807, 4, 3, 1),
(808, 4, 3, 8),
(809, 4, 4, 1),
(810, 4, 4, 8),
(811, 5, 1, 1),
(812, 5, 2, 1),
(813, 5, 3, 1),
(814, 5, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puestos`
--

CREATE TABLE `puestos` (
  `Id_puesto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puestos`
--

INSERT INTO `puestos` (`Id_puesto`, `nombre`) VALUES
(1, 'Empresa'),
(2, 'CEO'),
(3, 'Director'),
(4, 'Supervisor'),
(5, 'Lider'),
(6, 'Staff');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roless`
--

CREATE TABLE `roless` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roless`
--

INSERT INTO `roless` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Admin sistemas', 'Control total sobre el sistema'),
(2, 'Admin mantenimiento', 'Control total de Mobiliario y domicilios, ver, agregar y editar'),
(3, 'Director', 'Acceso a ver computadora, celular y mobiliario, no ve contrasenas'),
(4, 'CEO', 'Acceso a ver computadora, celular y mobiliario, no ve contrasenas'),
(5, 'Supervisor', 'Acceso a computadoras, celulares y mobiliarios - solo lectura');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_usuarios`
--

CREATE TABLE `roles_usuarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudbaja`
--

CREATE TABLE `solicitudbaja` (
  `Id_solicitudBaja` int(11) NOT NULL,
  `Id_inventario` int(11) NOT NULL,
  `motivoBaja` varchar(50) NOT NULL,
  `solicitudPresupuesto` varchar(10) NOT NULL,
  `Id_usuario` int(11) NOT NULL,
  `fechaSolicitud` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fechaAutorizacion` date NOT NULL,
  `id_rol_soli` int(10) NOT NULL,
  `tabla` varchar(50) DEFAULT NULL,
  `estatu` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudmaterialsinasignado`
--

CREATE TABLE `solicitudmaterialsinasignado` (
  `Id_solicitudMaterial` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `solicitudMantenimiento` varchar(50) NOT NULL,
  `Id_Usuario` int(11) NOT NULL,
  `fechaSolicitud` date NOT NULL,
  `fechaAutorizacion` date NOT NULL,
  `Id_inventario` int(11) NOT NULL,
  `motivoSolicitud` varchar(50) NOT NULL,
  `tipoProducto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudmovimiento`
--

CREATE TABLE `solicitudmovimiento` (
  `Id_movimiento` int(11) NOT NULL,
  `Id_inventario` int(11) NOT NULL,
  `motivoMovimiento` varchar(100) NOT NULL,
  `solicitudPresupuesto` varchar(10) NOT NULL,
  `Id_usuario` int(11) NOT NULL,
  `fechaSolicitud` date NOT NULL,
  `fechaAutorizacion` date NOT NULL,
  `solicitudMantenimiento` varchar(10) NOT NULL,
  `ultimoMovimiento` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Supervisor`
--

CREATE TABLE `Supervisor` (
  `id` int(10) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Supervisor`
--

INSERT INTO `Supervisor` (`id`, `nombre`) VALUES
(3, 'KEVIN MORENO DE LA ROSA'),
(4, 'ISAIRA MORANTES MONTERO'),
(5, 'SAMANTHA REYNA FLORES'),
(6, 'BRENDA SALDIVAR SILVA'),
(7, 'CINTHIA GABRIELA OVIEDO ESPINOZA'),
(8, 'ALBA YARESSI MARROQUIN MARROQUIN'),
(9, 'GABRIELA LEAL GONZALEZ'),
(10, 'ALONDRA MARIBEL VARGAS'),
(11, 'EMILIO GUADALUPE TORRES SALINAS'),
(12, 'SERGIO HERNANDEZ MARTINEZ'),
(13, 'FERNANDO SALDAÑA DE LA ROSA'),
(14, 'DAMARIS TORRES OLIVARES'),
(15, 'EMELY RUBI CARMONA VALDEZ'),
(16, 'SERGIO SALAS SALAZAR'),
(17, 'EVAMARIA SUAREZ BARRON'),
(18, 'ALEXA BANDALA SANCHEZ'),
(19, 'SARAI BELLO ALBARRAN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `forgot_pass_identity` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `Id_Usuario` int(11) NOT NULL,
  `email` varchar(60) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `contrasena` varchar(260) NOT NULL,
  `verificacionContrasena` varchar(10) DEFAULT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fechaUltimoIngreso` date DEFAULT NULL,
  `rolActual` int(20) DEFAULT NULL,
  `Id_puesto` int(11) NOT NULL,
  `Id_departamento` int(11) DEFAULT NULL,
  `Id_oficina` int(11) DEFAULT NULL,
  `id_depa` varchar(10) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `estatu` int(5) DEFAULT NULL,
  `administra` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`Id_Usuario`, `email`, `nombre`, `apellido`, `contrasena`, `verificacionContrasena`, `fechaRegistro`, `fechaUltimoIngreso`, `rolActual`, `Id_puesto`, `Id_departamento`, `Id_oficina`, `id_depa`, `token`, `estatu`, `administra`) VALUES
(83, 'desarrollo5@kabzo.org', 'Juan', 'Bautista Hernandez', '$2y$10$5wQ/cF/DEcQDZ6YKC8EF/OOykdbr9g498pPXkiC/.iLZ7oxAz49CK', '12345', '2025-01-28 20:28:05', '2025-01-28', 1, 1, 19, 21, NULL, NULL, 1, '5,6,7,8'),
(89, 'desarrollo2@kabzo.org', 'Juan', 'Bautista Hernandez', '$2y$10$oFmXSR2lRBjd66sy/swr8uOVXKDeQj0jpe5BvqozDcWG331XCx/K.', '12345', '2025-01-27 23:32:01', '2025-01-28', 2, 2, 16, 13, NULL, NULL, 1, '2,4'),
(90, 'desarrollo3@kabzo.org', 'Juan', 'Bautista Hernandez', '$2y$10$EN5.kAou0tyQmvm0ddBw7.du.dfHgvfGbwuQHYdCtMPNkG6meAknq', '12345', '2025-01-27 23:44:46', '2025-01-28', 3, 3, 16, 13, NULL, NULL, 1, '4,5'),
(91, 'desarrollo1@kabzo.org', 'Juan', 'Bautista Hernandez', '$2y$10$NT7ZeECGAARwQcIvQFsA7.TsjHZtFDfZo7dBV1Fq4LyCetDMsCUCW', '12345', '2025-01-28 00:54:00', '2025-01-28', 4, 2, 16, 13, NULL, NULL, 1, '3,5'),
(92, 'desarrollo4@kabzo.org', 'Juan', 'Bautista Hernandez', '$2y$10$O9VRgmMA4ke8XM.fvYTfdei46Nak2A6QqT.C1EtWj0ft.lbSeNrqm', '12345', '2025-01-27 23:46:00', '2025-01-28', 5, 4, 24, 10, NULL, NULL, 1, '3,6');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesorio`
--
ALTER TABLE `accesorio`
  ADD PRIMARY KEY (`Id_accesorio`),
  ADD KEY `Id_oficina` (`Id_oficina`),
  ADD KEY `Id_departamento` (`Id_departamento`);

--
-- Indices de la tabla `celular`
--
ALTER TABLE `celular`
  ADD PRIMARY KEY (`Id_celular`),
  ADD KEY `Id_oficina` (`Id_oficina`),
  ADD KEY `Id_departamento` (`Id_departamento`);

--
-- Indices de la tabla `CEO`
--
ALTER TABLE `CEO`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departamento` (`departamento`);

--
-- Indices de la tabla `computadora`
--
ALTER TABLE `computadora`
  ADD PRIMARY KEY (`Id_computadora`),
  ADD KEY `computadora_ibfk_1` (`Id_oficina`),
  ADD KEY `computadora_ibfk_4` (`Id_departamento`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`Id_departamento`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD KEY `id_direcc` (`id_direcc`),
  ADD KEY `id_departamentos` (`id_departamentos`);

--
-- Indices de la tabla `director`
--
ALTER TABLE `director`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `domicilios`
--
ALTER TABLE `domicilios`
  ADD PRIMARY KEY (`id_domicilio`),
  ADD KEY `domicilios_ibfk_1` (`Id_departamento`);

--
-- Indices de la tabla `Empleados`
--
ALTER TABLE `Empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Lider`
--
ALTER TABLE `Lider`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mobiliario`
--
ALTER TABLE `mobiliario`
  ADD PRIMARY KEY (`Id_mobiliario`),
  ADD KEY `Id_oficina` (`Id_oficina`),
  ADD KEY `Id_departamento` (`Id_departamento`),
  ADD KEY `asignado_a` (`asignado_a`);

--
-- Indices de la tabla `modelos`
--
ALTER TABLE `modelos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `oficina`
--
ALTER TABLE `oficina`
  ADD PRIMARY KEY (`Id_Oficina`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `permisos_modelos`
--
ALTER TABLE `permisos_modelos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `modelo_id` (`modelo_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `puestos`
--
ALTER TABLE `puestos`
  ADD PRIMARY KEY (`Id_puesto`);

--
-- Indices de la tabla `roless`
--
ALTER TABLE `roless`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `roles_usuarios`
--
ALTER TABLE `roles_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `solicitudbaja`
--
ALTER TABLE `solicitudbaja`
  ADD PRIMARY KEY (`Id_solicitudBaja`),
  ADD KEY `Id_usuario` (`Id_usuario`),
  ADD KEY `id_depa_encargado` (`id_rol_soli`);

--
-- Indices de la tabla `solicitudmaterialsinasignado`
--
ALTER TABLE `solicitudmaterialsinasignado`
  ADD PRIMARY KEY (`Id_solicitudMaterial`),
  ADD KEY `Id_Usuario` (`Id_Usuario`),
  ADD KEY `Id_producto` (`Id_inventario`);

--
-- Indices de la tabla `solicitudmovimiento`
--
ALTER TABLE `solicitudmovimiento`
  ADD PRIMARY KEY (`Id_movimiento`),
  ADD KEY `Id_usuario` (`Id_usuario`),
  ADD KEY `solicitudmovimiento_ibfk_2` (`Id_inventario`);

--
-- Indices de la tabla `Supervisor`
--
ALTER TABLE `Supervisor`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`Id_Usuario`),
  ADD KEY `Id_puesto` (`Id_puesto`),
  ADD KEY `Id_departamento` (`Id_departamento`),
  ADD KEY `Id_Oficina` (`Id_oficina`),
  ADD KEY `rolActual` (`rolActual`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesorio`
--
ALTER TABLE `accesorio`
  MODIFY `Id_accesorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `celular`
--
ALTER TABLE `celular`
  MODIFY `Id_celular` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1134;

--
-- AUTO_INCREMENT de la tabla `CEO`
--
ALTER TABLE `CEO`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `computadora`
--
ALTER TABLE `computadora`
  MODIFY `Id_computadora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1278;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `Id_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direcc` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `director`
--
ALTER TABLE `director`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `domicilios`
--
ALTER TABLE `domicilios`
  MODIFY `id_domicilio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=667;

--
-- AUTO_INCREMENT de la tabla `Empleados`
--
ALTER TABLE `Empleados`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT de la tabla `Lider`
--
ALTER TABLE `Lider`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `mobiliario`
--
ALTER TABLE `mobiliario`
  MODIFY `Id_mobiliario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=408;

--
-- AUTO_INCREMENT de la tabla `modelos`
--
ALTER TABLE `modelos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `oficina`
--
ALTER TABLE `oficina`
  MODIFY `Id_Oficina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `permisos_modelos`
--
ALTER TABLE `permisos_modelos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=815;

--
-- AUTO_INCREMENT de la tabla `puestos`
--
ALTER TABLE `puestos`
  MODIFY `Id_puesto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `roless`
--
ALTER TABLE `roless`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `roles_usuarios`
--
ALTER TABLE `roles_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `solicitudbaja`
--
ALTER TABLE `solicitudbaja`
  MODIFY `Id_solicitudBaja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `solicitudmaterialsinasignado`
--
ALTER TABLE `solicitudmaterialsinasignado`
  MODIFY `Id_solicitudMaterial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudmovimiento`
--
ALTER TABLE `solicitudmovimiento`
  MODIFY `Id_movimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Supervisor`
--
ALTER TABLE `Supervisor`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `Id_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesorio`
--
ALTER TABLE `accesorio`
  ADD CONSTRAINT `accesorio_ibfk_2` FOREIGN KEY (`Id_oficina`) REFERENCES `oficina` (`Id_Oficina`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `accesorio_ibfk_3` FOREIGN KEY (`Id_departamento`) REFERENCES `departamentos` (`Id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `celular`
--
ALTER TABLE `celular`
  ADD CONSTRAINT `celular_ibfk_2` FOREIGN KEY (`Id_oficina`) REFERENCES `oficina` (`Id_Oficina`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `celular_ibfk_3` FOREIGN KEY (`Id_departamento`) REFERENCES `departamentos` (`Id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `computadora`
--
ALTER TABLE `computadora`
  ADD CONSTRAINT `computadora_ibfk_1` FOREIGN KEY (`Id_oficina`) REFERENCES `oficina` (`Id_Oficina`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `computadora_ibfk_4` FOREIGN KEY (`Id_departamento`) REFERENCES `departamentos` (`Id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`id_departamentos`) REFERENCES `departamentos` (`Id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `domicilios`
--
ALTER TABLE `domicilios`
  ADD CONSTRAINT `domicilios_ibfk_1` FOREIGN KEY (`Id_departamento`) REFERENCES `departamentos` (`Id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mobiliario`
--
ALTER TABLE `mobiliario`
  ADD CONSTRAINT `mobiliario_ibfk_2` FOREIGN KEY (`Id_oficina`) REFERENCES `oficina` (`Id_Oficina`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mobiliario_ibfk_3` FOREIGN KEY (`Id_departamento`) REFERENCES `departamentos` (`Id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mobiliario_ibfk_4` FOREIGN KEY (`asignado_a`) REFERENCES `Empleados` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `permisos_modelos`
--
ALTER TABLE `permisos_modelos`
  ADD CONSTRAINT `permisos_modelos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roless` (`id`),
  ADD CONSTRAINT `permisos_modelos_ibfk_2` FOREIGN KEY (`modelo_id`) REFERENCES `modelos` (`id`),
  ADD CONSTRAINT `permisos_modelos_ibfk_3` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`);

--
-- Filtros para la tabla `roles_usuarios`
--
ALTER TABLE `roles_usuarios`
  ADD CONSTRAINT `roles_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`Id_Usuario`),
  ADD CONSTRAINT `roles_usuarios_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roless` (`id`);

--
-- Filtros para la tabla `solicitudbaja`
--
ALTER TABLE `solicitudbaja`
  ADD CONSTRAINT `solicitudbaja_ibfk_2` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `solicitudbaja_ibfk_3` FOREIGN KEY (`id_rol_soli`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudmaterialsinasignado`
--
ALTER TABLE `solicitudmaterialsinasignado`
  ADD CONSTRAINT `solicitudmaterialsinasignado_ibfk_1` FOREIGN KEY (`Id_Usuario`) REFERENCES `usuarios` (`Id_Usuario`),
  ADD CONSTRAINT `solicitudmaterialsinasignado_ibfk_2` FOREIGN KEY (`Id_inventario`) REFERENCES `inventarios` (`Id_inventario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudmovimiento`
--
ALTER TABLE `solicitudmovimiento`
  ADD CONSTRAINT `solicitudmovimiento_ibfk_1` FOREIGN KEY (`Id_usuario`) REFERENCES `usuarios` (`Id_Usuario`),
  ADD CONSTRAINT `solicitudmovimiento_ibfk_2` FOREIGN KEY (`Id_inventario`) REFERENCES `inventarios` (`Id_inventario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`Id_puesto`) REFERENCES `puestos` (`Id_puesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`Id_departamento`) REFERENCES `departamentos` (`Id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`Id_oficina`) REFERENCES `oficina` (`Id_Oficina`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_4` FOREIGN KEY (`rolActual`) REFERENCES `roless` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
