-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 03-Dez-2020 às 19:00
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
-- Estrutura da tabela `eventos`
--

DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_evento` tinyint(4) NOT NULL,
  `dt_inicio` date NOT NULL,
  `dt_fim` date NOT NULL,
  `titulo` varchar(80) NOT NULL,
  `descricao` text,
  `uf` char(2) DEFAULT NULL,
  `dia_letivo` tinyint(1) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_evento` (`tipo_evento`),
  KEY `dt_criacao` (`dt_criacao`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `eventos`
--

INSERT INTO `eventos` (`id`, `tipo_evento`, `dt_inicio`, `dt_fim`, `titulo`, `descricao`, `uf`, `dia_letivo`, `dt_criacao`, `dt_alteracao`) VALUES
(1, 1, '2020-12-25', '2020-12-25', 'Natal', NULL, NULL, 0, '2020-11-28 22:34:14', '2020-11-29 09:27:43');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo_eventos`
--

DROP TABLE IF EXISTS `tipo_eventos`;
CREATE TABLE IF NOT EXISTS `tipo_eventos` (
  `id` tinyint(10) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tipo_eventos`
--

INSERT INTO `tipo_eventos` (`id`, `descricao`) VALUES
(1, 'FERIADO'),
(2, 'SIMULADO');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo_usuarios`
--

DROP TABLE IF EXISTS `tipo_usuarios`;
CREATE TABLE IF NOT EXISTS `tipo_usuarios` (
  `id` tinyint(10) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tipo_usuarios`
--

INSERT INTO `tipo_usuarios` (`id`, `descricao`) VALUES
(1, 'ADMINISTRADOR');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tipo_usuario` int(11) NOT NULL,
  `nome` varchar(60) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `tipo_usuario`, `nome`, `chave`, `senha`, `dt_criacao`, `dt_alteracao`) VALUES
(1, 1, 'admin', '$2y$10$QAUFIvz8Hvp2CX8b9kkwq.Ovm0vFbdInNl/bye13nwSW5jybRkDJG', '$2y$10$QAUFIvz8Hvp2CX8b9kkwq.Ovm0vFbdInNl/bye13nwSW5jybRkDJG', '2020-12-03 16:26:05', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
