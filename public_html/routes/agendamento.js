const express = require('express');
const router = express.Router();
const autenticarToken = require('../middleware/autenticarToken');

router.post('/agendar', autenticarToken, async (req, res) => {
  const db = req.app.locals.db;
  const { nome, email, telefone, horarioId } = req.body;

  try {
    const pacienteResult = await db.query(
      `INSERT INTO pacientes (nome, email, telefone) VALUES ($1, $2, $3) RETURNING id`,
      [nome, email, telefone]
    );
    const pacienteId = pacienteResult.rows[0].id;

    await db.query(
      `INSERT INTO agendamentos (paciente_id, horario_id) VALUES ($1, $2)`,
      [pacienteId, horarioId]
    );

    await db.query(`UPDATE horarios_disponiveis SET ocupado = TRUE WHERE id = $1`, [horarioId]);

    res.json({ success: true, message: 'Agendamento feito com sucesso.' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
