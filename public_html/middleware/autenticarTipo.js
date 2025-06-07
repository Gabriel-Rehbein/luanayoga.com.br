function autorizarTipo(tiposPermitidos = []) {
  return (req, res, next) => {
    const usuario = req.usuario; // vem do JWT já validado

    if (!usuario || !tiposPermitidos.includes(usuario.role)) {
      return res.status(403).json({ mensagem: 'Acesso negado: permissão insuficiente' });
    }

    next(); // usuário tem permissão, pode seguir
  };
}

module.exports = autorizarTipo;