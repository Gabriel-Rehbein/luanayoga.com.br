const { Pool } = require("pg");
require("dotenv").config();

let pool;

async function connect() {
  if (!pool) {
    pool = new Pool({
      connectionString: process.env.CONNECTION_STRING,
    });
    console.log("✅ Conexão com o banco PostgreSQL estabelecida.");
  }

  return pool;
}

async function selecionarPacientes() {
  const db = await connect();
  const res = await db.query("SELECT * FROM pacientes");
  return res.rows;
}

async function selecionarPaciente(nome) {
  const db = await connect();
  const res = await db.query("SELECT * FROM pacientes WHERE nome = $1", [nome]);
  return res.rows;
}

async function inserirPaciente(paciente) {
  const db = await connect();
  const sql = "INSERT INTO pacientes (nome, email, telefone, senha) VALUES ($1, $2, $3, $4)";
  const values = [paciente.nome, paciente.email, paciente.telefone, paciente.senha];
  await db.query(sql, values);
}

async function atualizarPaciente(id, paciente) {
  const db = await connect();
  const sql = "UPDATE pacientes SET nome = $1, email = $2, telefone = $3, senha = $4 WHERE id = $5";
  const values = [paciente.nome, paciente.email, paciente.telefone, paciente.senha, id];
  await db.query(sql, values);
}

module.exports = {
  selecionarPacientes,
  selecionarPaciente,
  inserirPaciente,
  atualizarPaciente,
};
