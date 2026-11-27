-- BeautySchedule: cadastro dos cinco produtos da referência
-- Execute este SQL uma única vez no banco BeautySchedule.

INSERT INTO produtos (nome, materiais, preco, quantidade, imagem, ativo)
VALUES
('Gel de limpeza facial', 'Cuidados com a pele', 48.99, 0, 'imagens/produtos/gel-limpeza-facial.png', 1),
('Hidratante facial', 'Hidratação facial', 52.24, 0, 'imagens/produtos/hidratante-facial.png', 1),
('Protetor solar FPS 50', 'Proteção solar', 40.76, 0, 'imagens/produtos/protetor-solar-fps50.png', 1),
('Sérum de vitamina C', 'Cuidados faciais', 69.29, 0, 'imagens/produtos/serum-vitamina-c.png', 1),
('Hidratante corporal', 'Hidratação corporal', 26.02, 0, 'imagens/produtos/hidratante-corporal.png', 1);
