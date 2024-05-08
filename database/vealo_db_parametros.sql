/*
SQLyog Ultimate v13.1.1 (32 bit)
MySQL - 10.5.15-MariaDB-0+deb11u1 : Database - vealo_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `parametros` */

insert  into `parametros`(`id`,`variable`,`valor`,`observacion`,`created_at`,`updated_at`) values 
(1,'contador_retencion_J-30631519-5','815','Contador del registro de retencion impuesto FH FORMATO: contador_retencion_rifempresa',NULL,NULL),
(2,'contador_retencion_J-30227462-1','401','Contador del registro de retencion impuesto FFA FORMATO: contador_retencion_rifempresa',NULL,NULL),
(3,'contador_retencion_J-31473007-0','168','Contador del registro de retencion impuesto FFADOS FORMATO: contador_retencion_rifempresa',NULL,NULL),
(4,'contador_retencion_J-40093505-9','246','Contador del registro de retencion impuesto FE FORMATO: contador_retencion_rifempresa',NULL,NULL),
(5,'contador_retencion_J-31404973-9','4','Contador del registro de retencion impuesto CIDV FORMATO: contador_retencion_rifempresa',NULL,NULL),
(6,'poriva','16.00','porcentaje del IVA(impuesto del valor agregado)',NULL,NULL),
(7,'contador_retencion_J-31385109-4','196','Contador del registro de retencion impuesto FS FORMATO: contador_retencion_rifempresa',NULL,NULL),
(8,'contador_retencion_J-50225548-6','116','Contador del registro de retencion impuesto mas ahorro FORMATO: contador_retencion_rifempresa',NULL,NULL),
(9,'igtf','3.00','valor del porcentaje de impuestos por pagos en divisas igtf',NULL,NULL),
(10,'pago_facturas_desde_facturas_por_pagar','0',NULL,NULL,NULL),
(11,'nombre_general_empresa','GRUPO FARMA DESCUENTO',NULL,NULL,NULL),
(12,'logo_empresa','imagen/1665415989-AdminLTELogo.png',NULL,NULL,NULL),
(13,'verificar_facturas_en_siace','1',NULL,NULL,NULL),
(14,'verificar_tasa_dolar_tipo_moneda_o_historial_dolar','tipo_moneda_historial_tasa_vealo',NULL,NULL,NULL),
(15,'contador_retencion_J-50341171-6','62','retencion mas ahorro2',NULL,NULL),
(16,'id_banco_decontado','15','codigo id del registro en bancos correspondiente a de contado',NULL,NULL),
(17,'base_datos_tipo_moneda','tipo_moneda_vealo',NULL,NULL,NULL),
(18,'conversion_moneda_nacional_a_extranjera',NULL,NULL,NULL,NULL),
(19,'importar_server2_a_server1_cxp','0',NULL,NULL,NULL),
(20,'contador_retencion_J-50439862-4','37','CONTADOR DE IMPUESTO SOBRE LA RENTA',NULL,NULL),
(21,'numero_registros_importar_cxp',NULL,NULL,NULL,NULL),
(22,'numero_registros_importar_notacredito',NULL,NULL,NULL,NULL),
(23,'select_banco_desde_modo_pago_divisa','0',NULL,NULL,NULL),
(25,'cxp_valor_tasa_is_manual_en_cancelar_factura','0','cancelar facturas se agrega manual el valor de la tasa',NULL,NULL),
(26,'contador_reten_iva_J-30631519-5','44327','FARMA HOSPITAL C.A.',NULL,NULL),
(27,'contador_reten_iva_J-30227462-1','19845','FARMACIAS FUERZAS ARMADAS, C.A.',NULL,NULL),
(28,'contador_reten_iva_J-31473007-0','16049','FARMACIA FUERZAS ARMADAS DOS, C.A.',NULL,NULL),
(29,'contador_reten_iva_J-40093505-9','9895','FARMA EXPRES C.A.',NULL,NULL),
(30,'contador_reten_iva_J-31385109-4','2335','FARMASUR, C.A',NULL,NULL),
(31,'contador_reten_iva_J-50225548-6','934','+ AHORRO C.A.',NULL,NULL),
(32,'contador_reten_iva_+ AHORRO 2, CA','1','+ AHORRO 2, CA',NULL,NULL),
(33,'reten_iva_modo_operacion','C','C es compra y V es venta',NULL,NULL),
(34,'contador_reten_iva_J-31404973-9','3','CONSTRUCTORA INMOBILIARIA DON VILLA C.A',NULL,NULL),
(35,'contador_reten_iva_J-50439862-4','1','FARMA MOTOS 1, C.A.',NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
