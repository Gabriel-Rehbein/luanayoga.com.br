-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 07/06/2025 às 00:52
-- Versão do servidor: 10.11.10-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u426680106_luana_moreira`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_disponibilidade` int(11) NOT NULL,
  `data_agendamento` datetime NOT NULL,
  `plano` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Confirmado',
  `pago` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `id_paciente`, `id_disponibilidade`, `data_agendamento`, `plano`, `status`, `pago`) VALUES
(1, 5, 5, '2025-06-02 10:30:00', '1aula', 'Confirmado', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `disponibilidade`
--

CREATE TABLE `disponibilidade` (
  `id` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'disponivel'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `disponibilidade`
--

INSERT INTO `disponibilidade` (`id`, `data_hora`, `status`) VALUES
(1, '2025-06-09 09:00:00', 'disponivel'),
(2, '2025-06-09 10:00:00', 'disponivel'),
(3, '2025-06-10 14:00:00', 'disponivel'),
(4, '2025-06-10 15:00:00', 'disponivel'),
(5, '2025-06-02 10:30:00', 'indisponivel');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fichas`
--

CREATE TABLE `fichas` (
  `id` int(11) NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `idade` int(11) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nascimento` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `praticou_yoga` varchar(10) DEFAULT NULL,
  `coluna` text DEFAULT NULL,
  `cirurgias` text DEFAULT NULL,
  `atividade_fisica` varchar(10) DEFAULT NULL,
  `qual_atividade` text DEFAULT NULL,
  `plano` varchar(100) DEFAULT NULL,
  `data_preenchimento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fichas`
--

INSERT INTO `fichas` (`id`, `id_paciente`, `nome`, `idade`, `estado_civil`, `email`, `nascimento`, `telefone`, `praticou_yoga`, `coluna`, `cirurgias`, `atividade_fisica`, `qual_atividade`, `plano`, `data_preenchimento`) VALUES
(1, 5, 'Lucca', 27, 'solteiro', 'lucam@email.com', '1997-09-04', '0000000000', 'Sim', '', '', 'Sim', '', '1aula', '2025-06-07 00:44:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pacientes`
--

INSERT INTO `pacientes` (`id`, `nome`, `email`, `senha`, `telefone`, `admin`) VALUES
(1, 'Luana Moreira', 'luana@fisioweb.com', '$2y$10$yI.i9.UqX.qV9O1aVd9Vp.8X4B.uC2s4.lT.lI.i9.UqX.qV9O1aV', '51999998888', 1),
(2, 'João da Silva', 'joao@email.com', '$2y$10$sP.o3.nL.eR.t5s6u7v8w9xY.z1a2b3c4d5e6f7g8h9i', '51988887777', 0),
(4, 'Gabriel Menezes Rehbein', 'gabrielmr0812@gmail.com', '$2y$10$b42XXtLt39WK85NbRyn6KOHrqaCfThJLLbdSiaacZxKdJJloXGzpS', '00000000', 1),
(5, 'Lucca Motta', 'lucam@email.com', '$2y$10$XOyh/Z6bU06DuBdi4cEY0.Mmmd74zUpL7C8N7K2SzV2urHw3Q2FfW', '00000000', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_disponibilidade` (`id_disponibilidade`);

--
-- Índices de tabela `disponibilidade`
--
ALTER TABLE `disponibilidade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_hora_unica` (`data_hora`);

--
-- Índices de tabela `fichas`
--
ALTER TABLE `fichas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Índices de tabela `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_idx` (`email`),
  ADD KEY `token_idx` (`token`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `disponibilidade`
--
ALTER TABLE `disponibilidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `fichas`
--
ALTER TABLE `fichas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `fichas`
--
ALTER TABLE `fichas`
  ADD CONSTRAINT `fichas_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
