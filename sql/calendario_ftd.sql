-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 30-Nov-2020 às 01:24
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `eventos`
--

INSERT INTO `eventos` (`id`, `tipo_evento`, `dt_inicio`, `dt_fim`, `titulo`, `descricao`, `uf`, `dia_letivo`, `dt_criacao`, `dt_alteracao`) VALUES
(1, 1, '2020-12-25', '2020-12-25', 'Natal', NULL, NULL, 0, '2020-11-28 22:34:14', '2020-11-29 09:27:43'),
(2, 1, '2020-11-20', '2020-11-20', 'Dia da Consciência Negra', NULL, 'SP', 0, '2020-11-28 22:34:14', NULL),
(3, 1, '2020-04-24', '2020-04-24', 'Dia do Samurai', NULL, 'SP', 1, '2020-11-28 22:34:14', NULL),
(4, 2, '2020-11-23', '2020-11-27', 'Semana da Língua Portuguesa', 'Semana de simulados sobre língua portuguesa', 'RJ', 1, '2020-11-28 22:38:33', '2020-11-28 22:39:40'),
(5, 1, '2020-01-01', '2020-01-01', 'Confraternização Universal', '', NULL, 0, '2020-11-29 04:02:05', NULL),
(6, 1, '2020-11-02', '2020-11-02', 'Dia de Finados', '', NULL, 0, '2020-11-29 06:31:12', '2020-11-29 09:33:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo_eventos`
--

DROP TABLE IF EXISTS `tipo_eventos`;
CREATE TABLE IF NOT EXISTS `tipo_eventos` (
  `id` tinyint(10) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tipo_eventos`
--

INSERT INTO `tipo_eventos` (`id`, `descricao`) VALUES
(1, 'FERIADO'),
(2, 'SIMULADO'),
(3, 'PROVA');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
