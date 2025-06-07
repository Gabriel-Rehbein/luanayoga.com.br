// index.js - adaptado para MySQL

const express = require("express");
const mysql = require("mysql2/promise");
const cors = require("cors");
require("dotenv").config();

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

// Conexão com MySQL
const pool = mysql.createPool({
  host: "mysql.hostinger.com",
  user: "u426680106_LuanaMoreira",       // o mesmo usado no PHP
  password: "LmBD123@",           // mesma senha
  database: "u426680106_luana_moreira",
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});


// Rota de teste
app.get("/", (req, res) => {
  res.send("Servidor de agendamento rodando!");
});

// Buscar todos os agendamentos
app.get("/agendamentos", async (req, res) => {
  try {
    const [rows] = await pool.query("SELECT * FROM agendamentos");
    res.json(rows);
  } catch (error) {
    res.status(500).json({ erro: "Erro ao buscar agendamentos." });
  }
});

// Adicionar um novo agendamento
app.post("/agendamentos", async (req, res) => {
  const { nome, email, data, hora } = req.body;
  try {
    await pool.query(
      "INSERT INTO agendamentos (nome, email, data, hora) VALUES (?, ?, ?, ?)",
      [nome, email, data, hora]
    );
    res.status(201).json({ mensagem: "Agendamento realizado com sucesso!" });
  } catch (error) {
    res.status(500).json({ erro: "Erro ao realizar agendamento." });
  }
});

// Buscar agendamentos por data
app.get("/agendamentos/data", async (req, res) => {
  const { data } = req.query;
  try {
    const [rows] = await pool.query("SELECT * FROM agendamentos WHERE data = ?", [data]);
    res.json(rows);
  } catch (error) {
    res.status(500).json({ erro: "Erro ao buscar agendamentos por data." });
  }
});

app.listen(PORT, () => {
  console.log(`Servidor rodando na porta ${PORT}`);
});
