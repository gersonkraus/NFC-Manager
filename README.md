# NFC Manager (PHP mini-app)

Sistema simples para cadastrar e gerenciar TAGs NFC e registrar leituras com geolocalização (quando o usuário permite).

## Requisitos
- PHP 8+
- MySQL/MariaDB
- Apache com mod_rewrite (para usar o `.htaccess`)

## Instalação
1. Crie o banco e tabelas:
   - Importe `db/schema.sql` no seu MySQL.
2. Copie `config/config.sample.php` para `config/config.php` e edite as credenciais de banco e `base_url`.
   - `base_url` deve apontar para a pasta `public`. Ex: `http://localhost/nfc_manager/public` ou seu domínio real.
3. Aponte o DocumentRoot do Apache para a pasta `public` ou acesse `public/` via URL.
4. Acesse `/admin` para entrar (padrão: `admin` / `admin123`). Altere a senha no banco em seguida.

## Uso
- Em **TAGs → Novo**, crie a tag. Se quiser, deixe o `Slug` em branco para gerar automaticamente.
- Grave na sua TAG NFC a URL: `${base_url}/t/SEU-SLUG` (use NFC Tools ou NXP TagWriter).
- A leitura registra um `scan` e redireciona ao destino. Se o usuário permitir, a posição é enviada e exibida no mapa de **Leituras**.

## Observações de Privacidade
- A localização depende de consentimento do navegador do usuário. Sem permissão, apenas IP/UA são salvos.
- Ajuste sua política de privacidade e termos conforme sua necessidade (LGPD).

## Estrutura
- `public/index.php` → roteador simples e rotas públicas (t, content, api, admin).
- `views/*` → telas do admin, mapa (Leaflet via CDN).
- `lib/db.php` / `lib/helpers.php` → conexão, sessão, utilitários.
- `db/schema.sql` → tabelas + admin padrão.