const jwt = require('jsonwebtoken');

function autenticarToken(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1]; // Espera formato: "Bearer <token>"

  if (!token) return res.status(401).json({ mensagem: 'Token não fornecido' });

  jwt.verify(token, 'seu_segredo_aqui', (err, usuario) => {
    if (err) return res.status(403).json({ mensagem: 'Token inválido' });

    req.usuario = usuario; // O usuário decodificado estará disponível nas rotas
    next(); // Continua para a rota protegida
  });
}

module.exports = {autenticarToken};
