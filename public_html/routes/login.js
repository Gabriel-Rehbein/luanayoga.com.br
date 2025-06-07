const express = require('express');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const router = express.Router();

const SECRET = 'segredo_jwt'; //mudar para algo mais profissional

router.post('/login', async (req, res) => {
  const { email, senha } = req.body;
  const db = req.app.locals.db;

  try {
    const result = await db.query('SELECT * FROM usuarios WHERE email = $1', [email]);
    const user = result.rows[0];

    if (!user) {
      return res.status(401).json({ error: 'Usuário não encontrado' });
    }

    const senhaOk = await bcrypt.compare(senha, user.senha);
    if (!senhaOk) {
      return res.status(401).json({ error: 'Senha incorreta' });
    }

    // Token
    const token = jwt.sign(
      { id: user.id, email: user.email, role: user.tipo },
      SECRET,
      { expiresIn: '2h' }
    );

     res.json({
      message: 'Login bem-sucedido',
      token: token,
      usuario: {
        id: user.id,
        nome: user.nome,
        tipo: user.tipo // útil para o frontend
      }
    });

  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
