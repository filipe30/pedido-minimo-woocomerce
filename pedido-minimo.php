<?php
/*
Plugin Name: Pedido Mínimo
Plugin Uri: https://www.ciawebsites.com.br/
Description: Esse Plugin  adiciona pedido mínimo no checkout
Version: 1.0
Author: Filipe César
Author Uri: https://filipecesar.com.br/
Text Domain: pedido-minimo
Licence: GPLv2
 */
function wc_minimum_order_amount() {
// Defina esta variável para especificar um valor mínimo do pedido
    $minimum = getField(); //definir valor minimo para pedido.
    if ( WC()->cart->total < $minimum ) {
        if( is_cart() ) {
            wc_print_notice(
                sprintf( 'O total do seu pedido atual é %s - você deve ter um pedido com no mínimo % s para fazer seu pedido ' ,
                    wc_price( WC()->cart->total ),
                    wc_price( $minimum )
                ), 'error'
            );
        } else {
            wc_add_notice(
                sprintf( 'O total do seu pedido atual é %s - você deve ter um pedido com no mínimo % s para fazer seu pedido' ,
                    wc_price( WC()->cart->total ),
                    wc_price( $minimum )
                ), 'error'
            );
        }
    }
}
add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart' , 'wc_minimum_order_amount' );
function getField(){
    $options = get_option('valor_pedido');
    return $options;
}
function minhas_configuracoes() {
    register_setting(
        'grupo_minhas_configuracoes',
        'valor_pedido',
        [
           $filter = 'sanitize_callback' => 'intval'

        ]
    );
    add_settings_section(
        'secao_pedido_minimo',
        'Pedido Mínimo',
        function( $args ) {
            echo '<p>Coloque aqui o valor mínimo para pedidos no site.</p>';
        },
        'grupo_minhas_configuracoes'
    );

    add_settings_field(
        'valor_pedido',
        'Valor mínimo para pedidos no site',
        function ($args){
           $options = getField()
        ?>

        <input id="<?= esc_attr($args['label_for']) ?>" type="text" name="valor_pedido" value="<?= esc_attr($options) ?>">

        <?php

        },
        'grupo_minhas_configuracoes',
        'secao_pedido_minimo',
        [
            'label_for' => 'id_pedido_minimo',
            'class' => 'classe-pedido-minimo',
        ]

    );
}
add_action( 'admin_init', 'minhas_configuracoes' );
function minhas_configuracoes_menu() {
    add_options_page(
        'Configurações de Pedido Mínimo',
        'Pedido Mínimo',
        'manage_options',
        'configuracoes-pedido-minimo',
        'configuracoes_pedido_minimo_html'
    );
}
add_action('admin_menu', 'minhas_configuracoes_menu');

function configuracoes_pedido_minimo_html() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'grupo_minhas_configuracoes' );
            do_settings_sections( 'grupo_minhas_configuracoes' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
function minhas_configuracoes_link_lista_plugins( $links ) {
    $settings_link = '<a href="options-general.php?page=configuracoes-pedido-minimo">Configurações</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'minhas_configuracoes_link_lista_plugins' );