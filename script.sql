-- --------------------------------------------------------
-- Host:                         localhost
-- Versión del servidor:         10.4.28-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para booknexus
CREATE DATABASE IF NOT EXISTS `booknexus` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `booknexus`;

-- Volcando estructura para tabla booknexus.ejemplares
CREATE TABLE IF NOT EXISTS `ejemplares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_libro` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ejemplares_libros_id` (`id_libro`),
  CONSTRAINT `fk_ejemplares_libros_id` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla booknexus.ejemplares: ~9 rows (aproximadamente)
INSERT INTO `ejemplares` (`id`, `id_libro`, `fecha_creacion`) VALUES
	(11, 21, '2023-11-12 12:47:05'),
	(12, 21, '2023-11-12 12:47:08'),
	(13, 21, '2023-11-12 12:47:10'),
	(14, 2, '2023-11-12 13:13:30'),
	(15, 2, '2023-11-12 13:13:31'),
	(16, 21, '2023-11-12 13:13:33'),
	(17, 16, '2023-11-12 17:38:10'),
	(18, 4, '2023-11-12 17:38:14'),
	(19, 11, '2023-11-12 17:38:20');

-- Volcando estructura para tabla booknexus.lectores
CREATE TABLE IF NOT EXISTS `lectores` (
  `id_usuario` int(11) NOT NULL,
  `nif` char(9) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `uq_lectores_nif` (`nif`),
  UNIQUE KEY `uq_lectores_nombre_apellidos` (`nombre`,`apellidos`),
  KEY `fk_lectores_usuarios_id_usuario` (`id_usuario`),
  CONSTRAINT `fk_lectores_usuarios_id_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `ck_lectores_fecha_nacimiento` CHECK (`fecha_nacimiento` <= `fecha_creacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla booknexus.lectores: ~0 rows (aproximadamente)

-- Volcando estructura para tabla booknexus.libros
CREATE TABLE IF NOT EXISTS `libros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(13) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `fecha_publicacion` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_libros_titulo` (`titulo`),
  UNIQUE KEY `uq_libros_isbn` (`isbn`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla booknexus.libros: ~12 rows (aproximadamente)
INSERT INTO `libros` (`id`, `isbn`, `titulo`, `fecha_publicacion`) VALUES
	(2, '9780451416107', 'To Kill a Mockingbird', '1960-07-11 00:00:00'),
	(4, '9780140274826', 'Pride and Prejudice', '1813-01-28 00:00:00'),
	(5, '9781451673319', 'The Great Gatsby', '1925-04-10 00:00:00'),
	(6, '9780141439600', 'Moby-Dick', '1851-10-18 00:00:00'),
	(7, '9781613821313', 'War and Peace', '1869-01-01 00:00:00'),
	(10, '9780679783279', 'Brave New World', '1932-10-14 00:00:00'),
	(11, '9781402794651', 'The Odyssey', '0000-00-00 00:00:00'),
	(12, '9781985131530', 'Lord of the Flies', '1954-09-17 00:00:00'),
	(14, '9781505255633', 'The Count of Monte Cristo', '1844-08-28 00:00:00'),
	(15, '9780140448035', 'Frankenstein', '1818-01-01 00:00:00'),
	(16, '9780743273565', 'The Catcher in the Rye', '1951-07-16 00:00:00'),
	(21, '9780061120087', 'El libro de nuestra vida', '2023-06-10 00:00:00');

-- Volcando estructura para tabla booknexus.prestamos
CREATE TABLE IF NOT EXISTS `prestamos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ejemplar` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `fecha_devolucion` datetime NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fk_prestamos_lectores_id_usuario` (`id_usuario`) USING BTREE,
  KEY `fk_prestamos_ejemplares_id` (`id_ejemplar`),
  CONSTRAINT `fk_prestamos_ejemplares_id` FOREIGN KEY (`id_ejemplar`) REFERENCES `ejemplares` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_prestamos_lectores_id_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `lectores` (`id_usuario`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `ck_prestamos_fecha_entrega` CHECK (`fecha_entrega` >= `fecha_creacion`),
  CONSTRAINT `ck_prestamos_fecha_devolucion` CHECK (`fecha_devolucion` >= `fecha_creacion`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla booknexus.prestamos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla booknexus.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla booknexus.roles: ~3 rows (aproximadamente)
INSERT INTO `roles` (`id`, `nombre`) VALUES
	(1, 'ADMINISTRADOR'),
	(5, 'PERSONAL'),
	(4, 'USUARIO');

-- Volcando estructura para tabla booknexus.sesiones
CREATE TABLE IF NOT EXISTS `sesiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `token` char(36) NOT NULL DEFAULT uuid(),
  `user_agent` text DEFAULT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sesiones_usuarios_id` (`id_usuario`),
  CONSTRAINT `fk_sesiones_usuarios_id` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `ck_sesiones_fecha_expiracion` CHECK (`fecha_expiracion` >= `fecha_creacion`)
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla booknexus.sesiones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla booknexus.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(15) NOT NULL,
  `correo_electronico` varchar(320) NOT NULL,
  `contrasena` varchar(60) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `uq_usuarios_correo_electronico` (`correo_electronico`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `fk_usuarios_roles_id` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla booknexus.usuarios: ~1 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `id_rol`, `nombre_usuario`, `correo_electronico`, `contrasena`, `fecha_creacion`) VALUES
	(30, 1, 'booknexus', 'booknexus@radis.dev', '$2y$10$AoV0nayACeGptH2ywbBNX.GPKor9BaLpgVyG6uBmSNMZOKTvElAXe', '2023-11-13 21:40:00');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
