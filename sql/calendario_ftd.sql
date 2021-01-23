-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 23-Jan-2021 às 21:17
-- Versão do servidor: 5.7.31
-- versão do PHP: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `calendario_ftd`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `calendario`
--

DROP TABLE IF EXISTS `calendario`;
CREATE TABLE IF NOT EXISTS `calendario` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ano_referencia` smallint(6) NOT NULL,
  `dt_inicio_ano_letivo` date NOT NULL,
  `dt_fim_ano_letivo` date NOT NULL,
  `dt_inicio_recesso` date NOT NULL,
  `dt_fim_recesso` date NOT NULL,
  `qtde_volumes_1o_ano` tinyint(4) NOT NULL,
  `qtde_volumes_2o_ano` tinyint(4) NOT NULL,
  `qtde_volumes_3o_ano` tinyint(4) NOT NULL,
  `revisao_volume_3o_ano` tinyint(4) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `usuario_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `calendario_usuario` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `calendario`
--

INSERT INTO `calendario` (`id`, `ano_referencia`, `dt_inicio_ano_letivo`, `dt_fim_ano_letivo`, `dt_inicio_recesso`, `dt_fim_recesso`, `qtde_volumes_1o_ano`, `qtde_volumes_2o_ano`, `qtde_volumes_3o_ano`, `revisao_volume_3o_ano`, `dt_criacao`, `dt_alteracao`, `usuario_id`) VALUES
(1, 2020, '2020-01-13', '2020-12-11', '2020-07-06', '2020-07-31', 0, 0, 0, 0, '2020-12-06 18:28:11', NULL, 2),
(6, 2021, '2021-01-11', '2021-12-10', '2021-07-05', '2021-07-30', 2, 3, 4, 1, '2020-12-07 02:21:04', '2020-12-11 22:22:51', 2),
(7, 2021, '2021-01-11', '2021-12-10', '2021-07-05', '2021-07-30', 12, 12, 12, 0, '2020-12-11 21:59:53', '2021-01-21 00:03:31', 2),
(8, 2021, '2021-01-11', '2021-12-10', '2021-07-05', '2021-07-30', 5, 6, 7, 2, '2020-12-11 22:23:23', NULL, 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `calendario_evento`
--

DROP TABLE IF EXISTS `calendario_evento`;
CREATE TABLE IF NOT EXISTS `calendario_evento` (
  `calendario_id` int(10) NOT NULL,
  `evento_id` int(10) NOT NULL,
  UNIQUE KEY `calendario_evento` (`calendario_id`,`evento_id`) USING BTREE,
  KEY `calendario_id` (`calendario_id`) USING BTREE,
  KEY `evento_id` (`evento_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `calendario_evento`
--

INSERT INTO `calendario_evento` (`calendario_id`, `evento_id`) VALUES
(1, 3),
(6, 3),
(6, 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `evento`
--

DROP TABLE IF EXISTS `evento`;
CREATE TABLE IF NOT EXISTS `evento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dt_inicio` date NOT NULL,
  `dt_fim` date NOT NULL,
  `titulo` varchar(80) NOT NULL,
  `descricao` text,
  `uf` char(2) DEFAULT NULL,
  `dia_letivo` tinyint(1) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `evento_tipo_id` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `evento_tipo_id` (`evento_tipo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `evento`
--

INSERT INTO `evento` (`id`, `dt_inicio`, `dt_fim`, `titulo`, `descricao`, `uf`, `dia_letivo`, `dt_criacao`, `dt_alteracao`, `evento_tipo_id`) VALUES
(1, '2020-12-25', '2020-12-25', 'Natal', NULL, NULL, 0, '2020-11-28 22:34:14', '2020-12-06 17:52:02', 1),
(2, '2020-11-20', '2020-11-20', 'Dia da Consciência Negra', NULL, 'SP', 0, '2020-11-28 22:34:14', '2020-12-06 17:52:02', 1),
(3, '2020-04-24', '2020-04-24', 'Prova de Matematica', NULL, 'SP', 1, '2020-11-28 22:34:14', '2020-12-06 17:54:47', 4),
(5, '2020-01-01', '2020-01-01', 'Confraternização Universal', '', NULL, 0, '2020-11-29 04:02:05', '2020-12-06 17:52:02', 1),
(6, '2020-11-02', '2020-11-02', 'Dia de Finados', '', NULL, 0, '2020-11-29 06:31:12', '2020-12-12 19:41:18', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `evento_tipo`
--

DROP TABLE IF EXISTS `evento_tipo`;
CREATE TABLE IF NOT EXISTS `evento_tipo` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `evento_tipo`
--

INSERT INTO `evento_tipo` (`id`, `descricao`) VALUES
(1, 'FERIADO'),
(2, 'EVENTO FTD'),
(3, 'EVENTO INSTITUIÇÃO'),
(4, 'EVENTO PROFESSOR');

-- --------------------------------------------------------

--
-- Estrutura da tabela `instituicao`
--

DROP TABLE IF EXISTS `instituicao`;
CREATE TABLE IF NOT EXISTS `instituicao` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(80) DEFAULT NULL,
  `logo` mediumblob,
  `logo_content_type` varchar(30) DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `instituicao`
--

INSERT INTO `instituicao` (`id`, `nome`, `logo`, `logo_content_type`, `uf`, `dt_criacao`, `dt_alteracao`) VALUES
(1, 'Colégio Davina Gasparini', NULL, NULL, 'SP', '2020-12-06 18:25:00', '2021-01-23 20:50:59'),
(2, 'Colégio Santo Inácio', NULL, NULL, 'RJ', '2020-12-07 02:09:42', '2021-01-23 21:16:58'),
(3, 'Colégio São Judas Tadeo', NULL, NULL, 'SP', '2021-01-22 22:26:34', '2021-01-23 21:01:54');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `login_ftd` varchar(255) DEFAULT NULL,
  `senha_ftd` varchar(255) DEFAULT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `usuario_tipo_id` tinyint(4) NOT NULL,
  `instituicao_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`,`usuario_tipo_id`) USING BTREE,
  UNIQUE KEY `login_ftd` (`login_ftd`,`usuario_tipo_id`) USING BTREE,
  KEY `usuario_tipo_id` (`usuario_tipo_id`),
  KEY `usuario_instituicao_id` (`instituicao_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `login`, `senha`, `login_ftd`, `senha_ftd`, `dt_criacao`, `dt_alteracao`, `usuario_tipo_id`, `instituicao_id`) VALUES
(1, 'admin_ftd', NULL, 'admin', '$2y$10$WU1eKY9rC3MY4xwMlRBMSeHcLbFLzu9o6foKKGHVPkb214jfamz..', '', NULL, '2020-12-06 16:28:08', NULL, 1, NULL),
(2, 'Wellington', 'wellington.mathias@gmal.com', 'wellington', '$2y$10$iZ9oDfC29CoFFMQp1jmLCecTddBbIxiw3.rVEwVcY4Kq/42BTl1Si', 'wellington', '$2y$10$vwx/bzA0m/mM4Pm74b2rfeJVKpq6k5jmOShTdoWUPFs.AW9130XtC', '2020-12-06 16:28:54', '2021-01-21 05:27:10', 2, 1),
(3, 'PROFESSOR', NULL, 'login', '$2y$10$vwx/bzA0m/mM4Pm74b2rfeJVKpq6k5jmOShTdoWUPFs.AW9130XtC', 'login_prof_ftd', '$2y$10$YXJ/ygM7NWpIChy.RvxdXuum9Q0Mr/9KmKr.G7mV2xy5Ek0kRHDUu', '2020-12-12 01:00:59', NULL, 2, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario_tipo`
--

DROP TABLE IF EXISTS `usuario_tipo`;
CREATE TABLE IF NOT EXISTS `usuario_tipo` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario_tipo`
--

INSERT INTO `usuario_tipo` (`id`, `descricao`) VALUES
(1, 'ADMINISTRADOR'),
(2, 'PROFESSOR');

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `calendario_evento`
--
ALTER TABLE `calendario_evento`
  ADD CONSTRAINT `calendario_id` FOREIGN KEY (`calendario_id`) REFERENCES `calendario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evento_id` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `evento`
--
ALTER TABLE `evento`
  ADD CONSTRAINT `evento_tipo_id` FOREIGN KEY (`evento_tipo_id`) REFERENCES `evento_tipo` (`id`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_instituicao_id` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicao` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_tipo_id` FOREIGN KEY (`usuario_tipo_id`) REFERENCES `usuario_tipo` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
