-- banco.sql
-- Execute este script para criar o banco e as tabelas

-- Crie o banco antes de executar:
-- createdb cadastro_funcionarios

-- Tabela de usuários do sistema (login)
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(150) NOT NULL,
    criado_em TIMESTAMP DEFAULT NOW()
);

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO usuarios (username, senha, nome)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin')
ON CONFLICT (username) DO NOTHING;

-- Tabela de funcionários
CREATE TABLE IF NOT EXISTS funcionarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cargo VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(30),
    situacao VARCHAR(10) NOT NULL DEFAULT 'Ativo' CHECK (situacao IN ('Ativo', 'Inativo')),
    criado_em TIMESTAMP DEFAULT NOW(),
    atualizado_em TIMESTAMP DEFAULT NOW()
);

-- Dados de exemplo
INSERT INTO funcionarios (nome, cargo, email, telefone, situacao) VALUES
('João Silva',    'Administrador', 'joao@ensx.com',   '(11) 91234-5678', 'Ativo'),
('Ana Mendes',    'Gerente',       'ana@ensx.com',    '(11) 92345-6789', 'Ativo'),
('Pedro Souza',   'Assistente',    'pedro@ensx.com',  '(11) 93456-7890', 'Ativo'),
('Carla Oliveira','Administrador', 'carla@ensx.com',  '(11) 94567-8901', 'Ativo'),
('Lucas Martins', 'Assistente',    'lucas@ensx.com',  '(11) 95678-9012', 'Inativo')
ON CONFLICT DO NOTHING;
