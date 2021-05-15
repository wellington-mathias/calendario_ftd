-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: calendario_ftd.mysql.dbaas.com.br
-- Generation Time: 07-Maio-2021 às 16:10
-- Versão do servidor: 5.7.17-13-log
-- PHP Version: 5.6.40-0+deb8u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `calendario_ftd`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `calendario`
--

CREATE TABLE `calendario` (
  `id` int(10) NOT NULL,
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
  `usuario_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Estrutura da tabela `calendario_evento`
--

CREATE TABLE `calendario_evento` (
  `calendario_id` int(10) NOT NULL,
  `evento_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Estrutura da tabela `evento`
--

CREATE TABLE `evento` (
  `id` int(11) NOT NULL,
  `dt_inicio` date NOT NULL,
  `dt_fim` date NOT NULL,
  `titulo` varchar(80) NOT NULL,
  `descricao` text,
  `uf` char(2) DEFAULT NULL,
  `dia_letivo` tinyint(1) NOT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `evento_tipo_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Estrutura da tabela `evento_tipo`
--

CREATE TABLE `evento_tipo` (
  `id` tinyint(4) NOT NULL,
  `descricao` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `evento_tipo`
--

INSERT INTO `evento_tipo` (`id`, `descricao`) VALUES
(1, 'Inicio e fim de aula'),
(2, 'Eventos Professor'),
(3, 'Feriados'),
(4, 'Eventos FTD'),
(5, 'Simulado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `instituicao`
--

CREATE TABLE `instituicao` (
  `id` int(10) NOT NULL,
  `nome` varchar(80) DEFAULT NULL,
  `logo` mediumblob,
  `logo_content_type` varchar(30) DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(10) NOT NULL,
  `nome` varchar(60) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `login_ftd` varchar(255) DEFAULT NULL,
  `senha_ftd` varchar(255) DEFAULT NULL,
  `dt_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `usuario_tipo_id` tinyint(4) NOT NULL,
  `instituicao_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `login`, `senha`, `login_ftd`, `senha_ftd`, `dt_criacao`, `dt_alteracao`, `usuario_tipo_id`, `instituicao_id`) VALUES
(1, 'admin_ftd', NULL, 'admin', '$2y$10$WU1eKY9rC3MY4xwMlRBMSeHcLbFLzu9o6foKKGHVPkb214jfamz..', '', NULL, '2020-12-06 16:28:08', '2021-03-23 17:57:06', 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario_tipo`
--

CREATE TABLE `usuario_tipo` (
  `id` tinyint(4) NOT NULL,
  `descricao` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuario_tipo`
--

INSERT INTO `usuario_tipo` (`id`, `descricao`) VALUES
(1, 'ADMINISTRADOR'),
(2, 'PROFESSOR');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calendario`
--
ALTER TABLE `calendario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calendario_usuario_id` (`usuario_id`);

--
-- Indexes for table `calendario_evento`
--
ALTER TABLE `calendario_evento`
  ADD UNIQUE KEY `calendario_evento` (`calendario_id`,`evento_id`) USING BTREE,
  ADD KEY `calendario_id` (`calendario_id`) USING BTREE,
  ADD KEY `evento_id` (`evento_id`);

--
-- Indexes for table `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_tipo_id` (`evento_tipo_id`);

--
-- Indexes for table `evento_tipo`
--
ALTER TABLE `evento_tipo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instituicao`
--
ALTER TABLE `instituicao`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`,`usuario_tipo_id`) USING BTREE,
  ADD UNIQUE KEY `login_ftd` (`login_ftd`,`usuario_tipo_id`) USING BTREE,
  ADD KEY `usuario_tipo_id` (`usuario_tipo_id`),
  ADD KEY `usuario_instituicao_id` (`instituicao_id`);

--
-- Indexes for table `usuario_tipo`
--
ALTER TABLE `usuario_tipo`
  ADD PRIMARY KEY (`id`);




--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `calendario`
--
ALTER TABLE `calendario`
  ADD CONSTRAINT `calendario_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
