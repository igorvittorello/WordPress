<?php
/** 
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'wordpress');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

/** nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'qkIURGrV@} 6V,|eBM}O%=lHV|rWq_c)K~+T#!Ru86=:AIa=3|-`S8y-0im/.2Nl');
define('SECURE_AUTH_KEY',  '@$nEc#,WC?Mi7GtQ-;;=B3->GGS--^9AnnaN,Nw8|^K?6xI,F/:ddmiDW!x++7O4');
define('LOGGED_IN_KEY',    '.RK(lT*|HL#ECBT3g1(%y2Qx-,Dg,U>V|q#06r?+ShZdQ9uS@4cD]kg%=EKhW>O|');
define('NONCE_KEY',        '[<h8MwtS+(0xwUp&R+sDj3D^^hleBhCrGtKW~_>n#f,pJf7}vs9{?&+6}+j#uF3c');
define('AUTH_SALT',        '#-GD?E|-j*g!tJ)qE=H*WFRDizG!SJC|@?(TxV_Xn}$LSXy;VgRaHe($*SvX=tH2');
define('SECURE_AUTH_SALT', '3e(}YGPR|,z=*;`J^xwGx} }K{U?pz=)e^XPYUAidlRw7f*26b<mS%Byhe(E1*zZ');
define('LOGGED_IN_SALT',   ';_aAUJ^Vqsn-85|-)C2$wd]U7-_b}Ws;MCoEe5oQZ-!2(mI&-II,VkD;p[Uf`^<3');
define('NONCE_SALT',       'I uiCm+r>W>ad.Vygaq`FhUJt5Km+.k r%$rY(m`MCWG7;6-o|3UgI~NQ|HV9ze@');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente ao
 * idioma escolhido deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define('WPLANG', 'pt_BR');

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');
