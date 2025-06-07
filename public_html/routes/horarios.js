const express = require('express');
const router = express.Router();
const { autenticarToken, autorizarTipo } = require('../middleware');

// Listar horários disponíveis (qualquer usuário autenticado)
router.get('/', autenticarToken, async (req, res) => {
  const db = req.app.locals.db;
  try {
    const result = await db.query(`
      SELECT id, 
        TO_CHAR(data, 'DD/MM/YYYY') AS data, 
        TO_CHAR(hora, 'HH24:MI') AS hora 
      FROM horarios_disponiveis 
      WHERE ocupado = FALSE 
      ORDER BY data, hora
    `);
    res.json(result.rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// Criar horários disponíveis (só fisioterapeuta)
router.post('/', autenticarToken, autorizarTipo(['fisioterapeuta']), async (req, res) => {
  const db = req.app.locals.db;
  const { data, horas } = req.body; // data em formato YYYY-MM-DD, horas é array de strings "HH:MM"

  try {
    for (const hora of horas) {
      await db.query(
        `INSERT INTO horarios_disponiveis (data, hora, ocupado) VALUES ($1, $2, false)`,
        [data, hora]
      );
    }
    res.json({ success: true, message: 'Horários criados com sucesso.' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
