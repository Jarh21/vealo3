/*
SQLyog Ultimate v13.1.1 (32 bit)
MySQL - 5.7.24 : Database - vealo3_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `apartado_facturas_dolarizadas` */

DROP TABLE IF EXISTS `apartado_facturas_dolarizadas`;

CREATE TABLE `apartado_facturas_dolarizadas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero_factura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rif_proveedor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_credito` int(11) DEFAULT NULL,
  `descuento` double(8,2) DEFAULT NULL,
  `modo_pago` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rif_empresa` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `asistente_compras_detalles` */

DROP TABLE IF EXISTS `asistente_compras_detalles`;

CREATE TABLE `asistente_compras_detalles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visualiador_precio_drogueria_id` bigint(20) DEFAULT NULL,
  `empresa_rif` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `producto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `costo` decimal(28,2) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `archivo_pedido_id` bigint(20) DEFAULT NULL,
  `coordenadas_archivo` decimal(20,0) DEFAULT NULL,
  `drogueria` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `balance_general_pago_facturas` */

DROP TABLE IF EXISTS `balance_general_pago_facturas`;

CREATE TABLE `balance_general_pago_facturas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_relacion` int(10) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `empresa_rif` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `proveedor_rif` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `concepto_id` int(11) DEFAULT NULL,
  `codorigen` int(4) DEFAULT NULL,
  `observacion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `debe` decimal(28,2) DEFAULT NULL,
  `haber` decimal(28,2) DEFAULT NULL,
  `saldo` decimal(28,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `bancos` */

DROP TABLE IF EXISTS `bancos`;

CREATE TABLE `bancos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_corto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primeros_cuatro_digitos` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_bank_list` tinyint(1) DEFAULT '0' COMMENT 'pertenece a la lista de bancos',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `concepto_pago_facturas` */

DROP TABLE IF EXISTS `concepto_pago_facturas`;

CREATE TABLE `concepto_pago_facturas` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'nombre del concepto ej saldo apertura,cancelacion',
  `clasificacion` bigint(2) DEFAULT NULL COMMENT '1 va por el debe 2 por el haber',
  `siglas` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `contribuyentes` */

DROP TABLE IF EXISTS `contribuyentes`;

CREATE TABLE `contribuyentes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `porcentaje_retencion` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `cuadre_prestamos` */

DROP TABLE IF EXISTS `cuadre_prestamos`;

CREATE TABLE `cuadre_prestamos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto` decimal(28,2) DEFAULT NULL,
  `creado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `cuadre_tarjetas` */

DROP TABLE IF EXISTS `cuadre_tarjetas`;

CREATE TABLE `cuadre_tarjetas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `banco_id` int(11) DEFAULT NULL,
  `banco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto` decimal(28,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `cuadre_transferencias` */

DROP TABLE IF EXISTS `cuadre_transferencias`;

CREATE TABLE `cuadre_transferencias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `banco_emisor_id` int(11) DEFAULT NULL,
  `banco_receptor_id` int(11) DEFAULT NULL,
  `numero_transferencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_transferencia` date DEFAULT NULL,
  `monto` decimal(28,2) DEFAULT NULL,
  `creado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actualizado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eliminado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `cuadres` */

DROP TABLE IF EXISTS `cuadres`;

CREATE TABLE `cuadres` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_empleado` int(10) DEFAULT NULL,
  `nombre_empleado` varchar(255) DEFAULT NULL,
  `tipo_observacion` varchar(100) DEFAULT NULL,
  `accion` varchar(10) DEFAULT NULL,
  `monto` decimal(28,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `empresa_rif` varchar(20) DEFAULT NULL,
  `codigo_cuadre` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `cuadres_observacions` */

DROP TABLE IF EXISTS `cuadres_observacions`;

CREATE TABLE `cuadres_observacions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  `usuario` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sumarOrestar` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto` decimal(28,2) DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aprobacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actualizado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eliminado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_eliminado` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `cuentas_por_pagars` */

DROP TABLE IF EXISTS `cuentas_por_pagars`;

CREATE TABLE `cuentas_por_pagars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `factura_id` bigint(20) DEFAULT NULL,
  `banco_id` int(11) DEFAULT '0',
  `referencia_pago` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `n_control` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cierre` date DEFAULT NULL,
  `proveedor_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cod_concepto` bigint(2) DEFAULT '0',
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concepto_descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'descripcion del concepto',
  `documento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tasa` decimal(28,2) DEFAULT '0.00',
  `monto_bolivares` decimal(28,2) DEFAULT '0.00',
  `monto_divisa` decimal(28,2) DEFAULT '0.00',
  `debitos` decimal(28,2) DEFAULT '0.00',
  `creditos` decimal(28,2) DEFAULT '0.00',
  `poriva` decimal(5,2) DEFAULT '0.00',
  `porcentaje_retencion_iva` decimal(5,2) DEFAULT '0.00',
  `iva_factura` decimal(28,0) DEFAULT '0',
  `montoiva` decimal(28,2) DEFAULT '0.00',
  `gravado` decimal(28,2) DEFAULT '0.00',
  `excento` decimal(28,2) DEFAULT '0.00',
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_relacion_pago` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pago_efectuado` int(2) DEFAULT '0',
  `cod_tipo_moneda` bigint(2) DEFAULT NULL,
  `tipo_moneda` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `codigo_relacion_pago` (`codigo_relacion_pago`),
  KEY `documento` (`documento`),
  KEY `n_control` (`n_control`),
  KEY `proveedor_rif` (`proveedor_rif`),
  KEY `empresa_rif` (`empresa_rif`)
) ENGINE=InnoDB AUTO_INCREMENT=60437 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `emails` */

DROP TABLE IF EXISTS `emails`;

CREATE TABLE `emails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cargo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `modulo_envia_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `empleado_declarantes` */

DROP TABLE IF EXISTS `empleado_declarantes`;

CREATE TABLE `empleado_declarantes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sueldo_base` decimal(18,2) DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contribuyente_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `e_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `empresas` */

DROP TABLE IF EXISTS `empresas`;

CREATE TABLE `empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_corto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `servidor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `puerto` int(11) DEFAULT NULL,
  `nomusua` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basedata` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `servidor2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `puerto2` int(11) DEFAULT NULL,
  `nomusua2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basedata2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clave2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firma` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_agente_retencion` bigint(1) DEFAULT '0',
  `is_sincronizacion_remota` bigint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `encabezado_xmls` */

DROP TABLE IF EXISTS `encabezado_xmls`;

CREATE TABLE `encabezado_xmls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rif_empresa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_empresa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre_usuario` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periodo_fiscal` date DEFAULT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechas_periodo_fiscal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=347 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `factura_espera_productos` */

DROP TABLE IF EXISTS `factura_espera_productos`;

CREATE TABLE `factura_espera_productos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `factura_espera_id` int(11) DEFAULT NULL,
  `codalmacen` int(11) DEFAULT NULL,
  `almacen` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codprod` int(11) DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cantidad` decimal(28,3) DEFAULT '0.000',
  `precio` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `descuento` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recargo` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `descuento_adicional` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `precio_descuento` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_renglon` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_exento` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alterno` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `costo` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codmarca` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codtipoproducto` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_producto_encartado` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utilc` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `regulado` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unidad_de_medida` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_despacho_directo` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codlinea` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `linea` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codsublinea` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codigosublinea` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sublinea` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_regulado` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_aplicable_descuento_adicional` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_proximo_precio` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_venta_autorizada` tinyint(1) DEFAULT '0',
  `porcentaje_iva` decimal(28,2) DEFAULT '0.00',
  `codcombo` int(11) DEFAULT '0',
  `codusua` int(11) DEFAULT NULL,
  `usuario` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `equipo` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `registrado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `factura_esperas` */

DROP TABLE IF EXISTS `factura_esperas`;

CREATE TABLE `factura_esperas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numero` int(10) unsigned zerofill DEFAULT NULL,
  `codcliente` int(11) DEFAULT NULL,
  `cliente_rif` varchar(20) DEFAULT NULL,
  `cliente_nombre` varchar(250) DEFAULT NULL,
  `cliente_direccion` varchar(255) DEFAULT NULL,
  `cliente_tlf` varchar(20) DEFAULT NULL,
  `monto_facturado` decimal(18,2) DEFAULT NULL,
  `codusua` int(11) DEFAULT NULL,
  `usuario` varchar(250) DEFAULT NULL,
  `equipo` varchar(20) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `registrado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `facturas_por_pagars` */

DROP TABLE IF EXISTS `facturas_por_pagars`;

CREATE TABLE `facturas_por_pagars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_nombre_corto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda_secundaria` decimal(28,2) DEFAULT '0.00',
  `fecha_factura` date DEFAULT NULL,
  `dias_credito` int(3) DEFAULT '0',
  `fecha_real_pago` date DEFAULT NULL,
  `concepto` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `porcentaje_descuento` decimal(4,2) DEFAULT '0.00',
  `cod_modo_pago` int(4) DEFAULT NULL,
  `modo_pago` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_control` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cierre` date DEFAULT NULL,
  `proveedor_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto` decimal(28,2) DEFAULT '0.00',
  `monto_divisa` decimal(28,2) DEFAULT '0.00',
  `debitos` decimal(28,2) DEFAULT '0.00',
  `creditos` decimal(28,2) DEFAULT '0.00',
  `poriva` decimal(5,2) DEFAULT '0.00',
  `porcentaje_retencion_iva` decimal(5,2) DEFAULT '0.00',
  `iva_factura` decimal(28,2) DEFAULT '0.00',
  `montoiva` decimal(28,2) DEFAULT '0.00',
  `gravado` decimal(28,2) DEFAULT '0.00',
  `excento` decimal(28,2) DEFAULT '0.00',
  `is_retencion_iva` tinyint(1) DEFAULT '0',
  `retencion_iva` decimal(28,2) DEFAULT '0.00',
  `is_retencion_islr` tinyint(1) DEFAULT '0',
  `retencion_islr` decimal(28,2) DEFAULT '0.00',
  `codigo_relacion_pago` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_apartada_pago` tinyint(1) DEFAULT '0',
  `desapartada_pago` tinyint(1) DEFAULT '0',
  `is_igtf` tinyint(1) DEFAULT '0',
  `igtf` decimal(28,2) DEFAULT '0.00',
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pago_efectuado` int(2) DEFAULT '0',
  `origen` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_factura_revisada` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `codigo_relacion_pago` (`codigo_relacion_pago`),
  KEY `documento` (`documento`),
  KEY `n_control` (`n_control`),
  KEY `proveedor_rif` (`proveedor_rif`),
  KEY `empresa_rif` (`empresa_rif`)
) ENGINE=InnoDB AUTO_INCREMENT=36260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `facturas_solicitud_de_divisas` */

DROP TABLE IF EXISTS `facturas_solicitud_de_divisas`;

CREATE TABLE `facturas_solicitud_de_divisas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `solicitud_divisas_id` bigint(20) DEFAULT NULL,
  `numero_factura` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `monto_factura` decimal(28,2) DEFAULT NULL,
  `retencion_factura` decimal(28,2) DEFAULT NULL,
  `factura_escaneada` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `aprobar_factura` bigint(2) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `habladores` */

DROP TABLE IF EXISTS `habladores`;

CREATE TABLE `habladores` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(50) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `codprod` varchar(50) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `listado` varchar(255) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `tipo_iva` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=471 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

/*Table structure for table `historial_dolar` */

DROP TABLE IF EXISTS `historial_dolar`;

CREATE TABLE `historial_dolar` (
  `keycodigo` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `tasa_primera_actualizacion` decimal(12,2) DEFAULT '0.00',
  `tasa_segunda_actualizacion` decimal(12,2) DEFAULT '0.00',
  `tasa_promedio` decimal(12,2) DEFAULT '0.00',
  `registrado` datetime DEFAULT NULL,
  PRIMARY KEY (`keycodigo`)
) ENGINE=InnoDB AUTO_INCREMENT=1432 DEFAULT CHARSET=utf8;

/*Table structure for table `islr_detalles` */

DROP TABLE IF EXISTS `islr_detalles`;

CREATE TABLE `islr_detalles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `islr_id` int(11) DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `nControl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nFactura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto` decimal(18,2) DEFAULT NULL,
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_porcentaje_retencion` int(11) DEFAULT NULL,
  `porcentaje_retencion` double DEFAULT NULL,
  `monto_retenido` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sustraendo` decimal(18,2) DEFAULT NULL,
  `total_retener` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1170 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `islrs` */

DROP TABLE IF EXISTS `islrs`;

CREATE TABLE `islrs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nControl` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `empresa_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `proveedor_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_egreso_cheque` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monto` decimal(18,2) DEFAULT NULL,
  `proveedor_codfiscal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_retener` decimal(18,2) DEFAULT NULL,
  `total_letras` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `usuario_id` int(11) DEFAULT NULL,
  `borrado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sustraendo` decimal(18,2) DEFAULT NULL,
  `nControlFactura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nFactura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=991 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `metodo_pagos` */

DROP TABLE IF EXISTS `metodo_pagos`;

CREATE TABLE `metodo_pagos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siglas` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `model_has_permissions` */

DROP TABLE IF EXISTS `model_has_permissions`;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `model_has_roles` */

DROP TABLE IF EXISTS `model_has_roles`;

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `mov_pago_cxc_comision_ventas2` */

DROP TABLE IF EXISTS `mov_pago_cxc_comision_ventas2`;

CREATE TABLE `mov_pago_cxc_comision_ventas2` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(15) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `codigoVendedor` bigint(10) DEFAULT NULL,
  `nombreVendedor` varchar(255) DEFAULT NULL,
  `cCliente` bigint(10) DEFAULT NULL,
  `cGrupo` bigint(10) DEFAULT NULL,
  `cliente` varchar(200) DEFAULT NULL,
  `rif` varchar(100) DEFAULT NULL,
  `tipoPago` int(11) DEFAULT NULL,
  `tipoMoneda` int(11) DEFAULT NULL,
  `montoMoneda` decimal(28,2) DEFAULT NULL,
  `montoBase` decimal(28,2) DEFAULT NULL,
  `tasaConversion` decimal(28,2) DEFAULT NULL,
  `documento` varchar(11) DEFAULT NULL,
  `cod_vendedor_antiguo` bigint(10) DEFAULT NULL,
  `nombre_vendedor_antiguo` varchar(255) DEFAULT NULL,
  `keycodigo_siace` bigint(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empresa_rif` (`empresa_rif`)
) ENGINE=InnoDB AUTO_INCREMENT=571 DEFAULT CHARSET=latin1;

/*Table structure for table `parametros` */

DROP TABLE IF EXISTS `parametros`;

CREATE TABLE `parametros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `variable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `parametros_calculo_comision` */

DROP TABLE IF EXISTS `parametros_calculo_comision`;

CREATE TABLE `parametros_calculo_comision` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codgrupo` int(11) DEFAULT NULL,
  `porcentaje_calculo_comision` int(11) DEFAULT NULL,
  `porcentaje_descuento_comision` int(11) DEFAULT NULL,
  `vendedores_especiales_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_foraneo` double DEFAULT '0',
  `empresa_rif` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `peliculas` */

DROP TABLE IF EXISTS `peliculas`;

CREATE TABLE `peliculas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `genero` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `elenco` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `calidad` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voto` int(11) DEFAULT NULL,
  `imagen` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `permisos` */

DROP TABLE IF EXISTS `permisos`;

CREATE TABLE `permisos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `ruta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1539 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `porcentaje_retencion_iva` */

DROP TABLE IF EXISTS `porcentaje_retencion_iva`;

CREATE TABLE `porcentaje_retencion_iva` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `porcentaje` int(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

/*Table structure for table `proveedors` */

DROP TABLE IF EXISTS `proveedors`;

CREATE TABLE `proveedors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigoFiscal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `porcentaje_retener` int(6) DEFAULT NULL,
  `ultimo_porcentaje_retener_islr` int(2) DEFAULT NULL,
  `fecha_porcentaje_retener` date DEFAULT NULL,
  `tipo_proveedor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descontar_nota_credito` tinyint(1) DEFAULT '1',
  `agregar_nota_debito` tinyint(1) DEFAULT '1',
  `agregar_igtf` tinyint(1) DEFAULT '0',
  `agregar_islr` tinyint(1) DEFAULT '0',
  `dias_credito` int(3) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipo_contribuyente` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rif` (`rif`)
) ENGINE=InnoDB AUTO_INCREMENT=1182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `punto_de_venta_empresas` */

DROP TABLE IF EXISTS `punto_de_venta_empresas`;

CREATE TABLE `punto_de_venta_empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banco_id` int(11) DEFAULT NULL,
  `numero_de_afiliacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_de_terminal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_prestado` tinyint(1) NOT NULL DEFAULT '0',
  `is_activo` tinyint(1) NOT NULL DEFAULT '1',
  `prestamista_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prestamista_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `reporte_xmls` */

DROP TABLE IF EXISTS `reporte_xmls`;

CREATE TABLE `reporte_xmls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rif_empresa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `encabezado_id` int(11) DEFAULT NULL,
  `nombre_empresa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rif_retenido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_retenido` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_factura` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_control` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `codigo_servicio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto_operacion` decimal(18,2) NOT NULL,
  `porcentaje_retencion` double(8,2) NOT NULL,
  `total_retener` decimal(18,2) NOT NULL DEFAULT '0.00',
  `islr_o_rrhh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `islr_y_rrhh_id` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22852 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `retenciones` */

DROP TABLE IF EXISTS `retenciones`;

CREATE TABLE `retenciones` (
  `keycodigo` bigint(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `periodo` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `comprobante` bigint(14) NOT NULL,
  `fecha` date NOT NULL,
  `rif_agente` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `nom_agente` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `rif_retenido` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `nom_retenido` varchar(110) COLLATE utf8_spanish_ci NOT NULL,
  `cheque` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `total` double(18,2) NOT NULL DEFAULT '0.00',
  `estatus` varchar(1) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'C',
  `fecha_usua` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cod_usua` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(31) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`keycodigo`),
  KEY `comprobante` (`comprobante`),
  KEY `rif_agente` (`rif_agente`)
) ENGINE=InnoDB AUTO_INCREMENT=82958 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Table structure for table `retenciones_dat` */

DROP TABLE IF EXISTS `retenciones_dat`;

CREATE TABLE `retenciones_dat` (
  `keycodigo` bigint(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `comprobante` bigint(14) NOT NULL DEFAULT '0',
  `rif_retenido` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `nom_retenido` varchar(110) COLLATE utf8_spanish_ci NOT NULL,
  `fecha_docu` date NOT NULL,
  `tipo_docu` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `serie` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `documento` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `estatus` varchar(1) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'N',
  `control_fact` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  `tipo_trans` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fact_afectada` varchar(12) COLLATE utf8_spanish_ci DEFAULT '0',
  `comprasmasiva` double(18,2) NOT NULL DEFAULT '0.00',
  `sincredito` double(18,2) NOT NULL DEFAULT '0.00',
  `base_impon` double(18,2) NOT NULL DEFAULT '0.00',
  `porc_alic` double(6,2) NOT NULL DEFAULT '0.00',
  `iva` double(18,2) NOT NULL DEFAULT '0.00',
  `iva_retenido` double(18,2) NOT NULL DEFAULT '0.00',
  `porc_reten` double(6,2) NOT NULL DEFAULT '0.00',
  `rif_agente` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `nom_agente` varchar(40) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cod_usua` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(31) COLLATE utf8_spanish_ci NOT NULL,
  `correo_enviado` int(2) DEFAULT '0' COMMENT 'cunado la retencion se evia por correo cambia la bandera',
  PRIMARY KEY (`keycodigo`),
  UNIQUE KEY `rif_retenido` (`rif_retenido`,`documento`),
  KEY `comprobante` (`comprobante`),
  KEY `tipo_docu` (`tipo_docu`),
  KEY `rif_agente` (`rif_agente`)
) ENGINE=InnoDB AUTO_INCREMENT=92615 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `retencions` */

DROP TABLE IF EXISTS `retencions`;

CREATE TABLE `retencions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `procent_retencion` int(11) NOT NULL,
  `valorUT` int(11) NOT NULL,
  `factor` double NOT NULL,
  `sustraendo` double NOT NULL,
  `monto_min_retencion` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `role_has_permissions` */

DROP TABLE IF EXISTS `role_has_permissions`;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `role_user` */

DROP TABLE IF EXISTS `role_user`;

CREATE TABLE `role_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `rrhhs` */

DROP TABLE IF EXISTS `rrhhs`;

CREATE TABLE `rrhhs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `sueldo_base` decimal(18,2) NOT NULL,
  `rif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa_rif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `empresa_id` int(11) DEFAULT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `servicios` */

DROP TABLE IF EXISTS `servicios`;

CREATE TABLE `servicios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `solicitud_de_divisas` */

DROP TABLE IF EXISTS `solicitud_de_divisas`;

CREATE TABLE `solicitud_de_divisas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha_solicitud` date DEFAULT NULL,
  `tipo_beneficiario` tinytext COLLATE utf8_unicode_ci,
  `rif_beneficiario` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nombre_beneficiario` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `monto_solicitud` decimal(28,2) DEFAULT NULL,
  `codigo_tipo_moneda` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `empresa_solicitante_id` int(11) DEFAULT NULL,
  `rif_empresa_solicitante` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observacion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `autorizar_pago` tinyint(1) DEFAULT '0',
  `codusua` bigint(20) DEFAULT NULL,
  `enviar_solicitud` bigint(2) DEFAULT '0',
  `aprobar_solicitud` bigint(2) DEFAULT '0',
  `rif_empresa_que_cancela` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tipo_moneda` */

DROP TABLE IF EXISTS `tipo_moneda`;

CREATE TABLE `tipo_moneda` (
  `keycodigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_singular` varchar(50) DEFAULT NULL,
  `nombre_plural` varchar(50) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `codigo` varchar(3) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `is_nacional` tinyint(1) DEFAULT '0',
  `precio_anterior_monto` decimal(28,10) DEFAULT '0.0000000000',
  `precio_anterior_fecha` datetime DEFAULT NULL,
  `precio_compra_moneda_nacional` decimal(28,10) DEFAULT '0.0000000000',
  `precio_venta_moneda_nacional` decimal(28,10) DEFAULT '0.0000000000',
  `precio_actual_fecha` datetime DEFAULT NULL,
  `is_actualizar_precio_en_moneda_nacional` tinyint(1) DEFAULT '0',
  `is_activo` tinyint(1) DEFAULT '1',
  `is_nueva` tinyint(1) DEFAULT '0',
  `is_moneda_base` tinyint(1) DEFAULT '0',
  `is_imprimir_facturas_en_esta_moneda` tinyint(1) DEFAULT '0',
  `is_moneda_secundaria` tinyint(1) DEFAULT '0',
  `cambio_en_moneda_nacional` decimal(28,0) DEFAULT '0',
  `is_otra_tasa_visual_para_la_compra` tinyint(1) DEFAULT '0',
  `is_avance_de_efectivo` tinyint(1) DEFAULT '0',
  `precio_para_la_compra_monto_visual` decimal(28,10) DEFAULT '0.0000000000',
  `cant_decimales` int(11) DEFAULT '2',
  `is_imprimir_referencia_al_cambio_facturacion` tinyint(1) DEFAULT '1',
  `is_actualizar_precios_en_base_utilidad` tinyint(1) DEFAULT '0',
  `codusua` int(11) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `registrado` datetime DEFAULT NULL,
  `equipo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`keycodigo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Table structure for table `tipo_moneda_historial_tasa` */

DROP TABLE IF EXISTS `tipo_moneda_historial_tasa`;

CREATE TABLE `tipo_moneda_historial_tasa` (
  `keycodigo` int(11) NOT NULL AUTO_INCREMENT,
  `codtipomoneda` int(11) DEFAULT '0',
  `anterior_tasa_de_cambio_en_moneda_nacional` decimal(28,2) DEFAULT '0.00',
  `nueva_tasa_de_cambio_en_moneda_nacional` decimal(28,2) DEFAULT '0.00',
  `codusua` int(11) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `registrado` datetime DEFAULT NULL,
  `equipo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`keycodigo`)
) ENGINE=InnoDB AUTO_INCREMENT=1672 DEFAULT CHARSET=utf8;

/*Table structure for table `unidad_tributarias` */

DROP TABLE IF EXISTS `unidad_tributarias`;

CREATE TABLE `unidad_tributarias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `anio` date DEFAULT NULL,
  `monto` decimal(18,2) NOT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `usuarios_acceso_empresas` */

DROP TABLE IF EXISTS `usuarios_acceso_empresas`;

CREATE TABLE `usuarios_acceso_empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_rif` varchar(20) COLLATE utf8mb4_spanish2_ci DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
