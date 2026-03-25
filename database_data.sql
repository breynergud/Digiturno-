-- MySQL Data Export (Final Verified Version)
SET FOREIGN_KEY_CHECKS=0;

-- Data for table `persona`
INSERT INTO `persona` (`pers_doc`, `pers_tipodoc`, `pers_nombres`, `pers_apellidos`, `pers_telefono`, `pers_fecha_nac`) VALUES ('100000001', 'CC', 'Asesor', 'Víctimas', '3000000001', NULL);
INSERT INTO `persona` (`pers_doc`, `pers_tipodoc`, `pers_nombres`, `pers_apellidos`, `pers_telefono`, `pers_fecha_nac`) VALUES ('100000002', 'CC', 'Asesor', 'General', '3000000002', NULL);
INSERT INTO `persona` (`pers_doc`, `pers_tipodoc`, `pers_nombres`, `pers_apellidos`, `pers_telefono`, `pers_fecha_nac`) VALUES ('100000003', 'CC', 'Asesor', 'Prioritario', '3000000003', NULL);
INSERT INTO `persona` (`pers_doc`, `pers_tipodoc`, `pers_nombres`, `pers_apellidos`, `pers_telefono`, `pers_fecha_nac`) VALUES ('123456789', 'CC', '', '', '3000000000', NULL);

-- Data for table `usuario`
INSERT INTO `usuario` (`user_id`, `user_tipo`, `PERSONA_pers_doc`) VALUES ('1', 'cliente', '123456789');

-- Data for table `asesor`
INSERT INTO `asesor` (`ase_id`, `ase_nrocontrato`, `ase_tipo_asesor`, `PERSONA_pers_doc`, `ase_vigencia`, `ase_password`, `ase_correo`, `ase_estado`, `ase_turno_actual_id`, `ase_turno_actual_tipo`) VALUES ('2', '002', 'G', '100000002', '2026', 'asesor123', 'general@sena.gov.co', 'disponible', NULL, NULL);
INSERT INTO `asesor` (`ase_id`, `ase_nrocontrato`, `ase_tipo_asesor`, `PERSONA_pers_doc`, `ase_vigencia`, `ase_password`, `ase_correo`, `ase_estado`, `ase_turno_actual_id`, `ase_turno_actual_tipo`) VALUES ('3', '003', 'P', '100000003', '2026', 'asesor123', 'prioritario@sena.gov.co', 'disponible', NULL, NULL);
INSERT INTO `asesor` (`ase_id`, `ase_nrocontrato`, `ase_tipo_asesor`, `PERSONA_pers_doc`, `ase_vigencia`, `ase_password`, `ase_correo`, `ase_estado`, `ase_turno_actual_id`, `ase_turno_actual_tipo`) VALUES ('1', '001', 'V', '100000001', '2026', 'asesor123', 'victimas@sena.gov.co', 'disponible', NULL, NULL);

-- Data for table `turno`
INSERT INTO `turno` (`tur_id`, `tur_hora_fecha`, `tur_numero`, `tur_tipo`, `USUARIO_user_id`) VALUES ('1', '2026-03-24 15:44:06', 'V-001', 'Victimas', '1');

-- Data for table `atencion`
INSERT INTO `atencion` (`atnc_id`, `atnc_hora_inicio`, `atnc_hora_fin`, `atnc_tipo`, `ASESOR_ase_id`, `TURNO_tur_id`) VALUES ('1', '2026-03-24 15:44:07', '2026-03-24 15:44:07', 'Victimas', '1', '1');

-- Data for table `turnos`
INSERT INTO `turnos` (`id`, `tipo_atencion`, `tipo_documento`, `numero_documento`, `telefono`, `codigo_turno`, `created_at`, `updated_at`, `mesa`) VALUES ('1', 'victimas', 'CC', '123456789', '3000000000', 'V-001', '2026-03-24 15:44:06', '2026-03-24 15:44:06', '1');

SET FOREIGN_KEY_CHECKS=1;
