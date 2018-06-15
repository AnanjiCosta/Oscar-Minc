<?php
/**
 * Register our oscar_minc_options_page to the admin_menu action hook
 */
add_action( 'admin_menu', 'oscar_minc_options_page' );
function oscar_minc_options_page() {
    // add top level menu page
    add_submenu_page(
        'edit.php?post_type=inscricao',
        'Configurações',
        'Configurações',
        'manage_options',
        'inscricao-options-page',
        'oscar_minc_options_page_html'
    );
}
/**
 * top level menu:
 * callback functions
 */
function oscar_minc_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    // add error/update messages
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'oscar_minc_options', 'oscar_minc_options_message', __( 'Configurações salvas', 'oscar' ), 'updated' );
    }
    // show error/update messages
    settings_errors( 'oscar_minc_options' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "wporg"
            settings_fields( 'oscar' );
            // output setting sections and their fields
            // (sections are registered for "wporg", each field is registered to a specific section)
            do_settings_sections( 'oscar' );
            // output save settings button
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}
/**
 * register our wporg_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'oscar_minc_settings_init' );
function oscar_minc_settings_init() {
    register_setting( 'oscar', 'oscar_minc_options' );
    add_settings_section(
        'oscar_minc_video_upload_section',
        'Formulário de envio de vídeo',
        '',
        'oscar'
    );
    add_settings_section(
        'oscar_minc_mail_confirmation_section',
        'Email de confirmação',
        '',
        'oscar'
    );
    add_settings_section(
        'oscar_minc_deadline_section',
        'Prazo para inscrições',
        '',
        'oscar'
    );
    add_settings_section(
        'oscar_minc_debug_section',
        'Para o desenvolvedor',
        '',
        'oscar'
    );
    add_settings_field(
        'oscar_minc_movie_extensions',
        'Extensões permitidas',
        'oscar_minc_movie_extensions',
        'oscar',
        'oscar_minc_video_upload_section',
        [
            'label_for' => 'oscar_minc_movie_extensions',
            'class' => 'form-field',
        ]
    );
    add_settings_field(
        'oscar_minc_movie_max_size',
        'Tamanho máximo para o vídeo',
        'oscar_minc_movie_max_size',
        'oscar',
        'oscar_minc_video_upload_section',
        [
            'label_for' => 'oscar_minc_movie_max_size',
        ]
    );
    add_settings_field(
        'oscar_minc_movie_uploaded_message',
        'Mensagem de sucesso ao enviar o vídeo',
        'oscar_minc_movie_uploaded_message',
        'oscar',
        'oscar_minc_video_upload_section',
        [
            'label_for' => 'oscar_minc_movie_uploaded_message',
            'class' => 'form-field',
        ]
    );
    add_settings_field(
        'oscar_minc_email_from',
        'Email para o remetente',
        'oscar_minc_email_from',
        'oscar',
        'oscar_minc_mail_confirmation_section',
        [
            'label_for' => 'oscar_minc_email_from',
            'class' => 'form-field',
        ]
    );
    add_settings_field(
        'oscar_minc_email_body',
        'Texto para o email de envio do formulário',
        'oscar_minc_email_body',
        'oscar',
        'oscar_minc_mail_confirmation_section',
        [
            'label_for' => 'oscar_minc_email_body',
            'class' => 'form-field',
        ]
    );
    add_settings_field(
        'oscar_minc_email_body_video_received',
        'Texto para o email de recebimento do vídeo',
        'oscar_minc_email_body_video_received',
        'oscar',
        'oscar_minc_mail_confirmation_section',
        [
            'label_for' => 'oscar_minc_email_body_video_received',
            'class' => 'form-field',
        ]
    );
    add_settings_field(
        'oscar_minc_videos_folder',
        'Diretório de upload',
        'oscar_minc_videos_folder',
        'oscar',
        'oscar_minc_mail_confirmation_section',
        [
            'label_for' => 'oscar_minc_videos_folder',
            'class' => 'form-field',
        ]
    );
    add_settings_field(
        'oscar_minc_monitoring_emails',
        'Emails para monitoramento',
        'oscar_minc_monitoring_emails',
        'oscar',
        'oscar_minc_mail_confirmation_section',
        [
            'label_for' => 'oscar_minc_monitoring_emails',
            'class' => 'form-field',
        ]
    );
    add_settings_field(
        'oscar_minc_deadline_time',
        'Data para encerramento das inscrições',
        'oscar_minc_deadline_time',
        'oscar',
        'oscar_minc_deadline_section',
        [
            'label_for' => 'oscar_minc_deadline_time',
            'class' => 'form-field',
        ]
    );
    add_settings_field(
        'oscar_minc_deadline_text',
        'Mensagem para o usuário',
        'oscar_minc_deadline_text',
        'oscar',
        'oscar_minc_deadline_section',
        [
            'label_for' => 'oscar_minc_deadline_text',
            'class' => 'form-field',
        ]
    );
    if( !empty($_GET['debug']) ){
        add_settings_field(
            'oscar_minc_debug_view',
            'Debug',
            'oscar_minc_debug_view',
            'oscar',
            'oscar_minc_debug_section',
            [
                'label_for' => 'oscar_minc_debug_view',
                'class' => 'form-field',
            ]
        );
        add_settings_field(
            'oscar_minc_delete_user_video_sent_meta',
            'Deletar envio de vídeo de usuário',
            'oscar_minc_delete_user_video_sent_meta',
            'oscar',
            'oscar_minc_debug_section',
            [
                'label_for' => 'oscar_minc_delete_user_video_sent_meta',
                'class' => 'form-field',
            ]
        );
        add_settings_field(
            'oscar_minc_users_that_can_surpass_deadline',
            'Usuários além do prazo',
            'oscar_minc_users_that_can_surpass_deadline',
            'oscar',
            'oscar_minc_debug_section',
            [
                'label_for' => 'oscar_minc_users_that_can_surpass_deadline',
                'class' => 'form-field',
            ]
        );
    }
}
function oscar_minc_movie_extensions( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>

    <input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="text" value="<?php echo $options['oscar_minc_movie_extensions']; ?>">
    <p class="description">
        Defina as extensões permitidas para os vídeos, separando as com vírgulas. Exemplo: mp4, avi, mkv, wmv.
    </p>
    <?php
}
function oscar_minc_movie_max_size( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>

    <input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="number" value="<?php echo $options['oscar_minc_movie_max_size']; ?>">
    <p class="description">
        Tamanho em Gigabytes
    </p>
    <?php
}
function oscar_minc_movie_uploaded_message( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>
    <textarea id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" rows="5"><?php echo $options['oscar_minc_movie_uploaded_message']; ?></textarea>
    <p class="description">
        Essa é a mensagem que o usuário verá ao enviar um vídeo com sucesso. Além disso, um email de confirmação será enviado para o mesmo.
    </p>
    <?php
}
function oscar_minc_email_from( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>

    <input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="text" value="<?php echo $options['oscar_minc_email_from']; ?>">
    <?php
}
function oscar_minc_email_body( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>
    <textarea id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" rows="10"><?php echo $options['oscar_minc_email_body']; ?></textarea>
    <p class="description">
        Mensagem recebida pelo usuário ao realizar o cadastro do formulário.
    </p>
    <?php
}
function oscar_minc_email_body_video_received( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>
    <textarea id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" rows="10"><?php echo $options['oscar_minc_email_body_video_received']; ?></textarea>
    <p class="description">
        Mensagem recebida pelo usuário após o correto envio do vídeo.
    </p>
 <?php
}
function oscar_minc_videos_folder( $args ) {
    $uploads = wp_upload_dir();
    $path = $uploads['basedir'] . '/oscar-videos'; ?>

    <input id="<?php echo esc_attr( $args['label_for'] ); ?>" type="text" value="<?php echo $path; ?>" disabled="true">
    <p class="description">
        Diretório para onde os vídeos são enviados. Campo apenas para visualização!
    </p>
    <?php
}
function oscar_minc_debug_view( $args ) {
    $options = get_option( 'oscar_minc_options' );
    echo '<h3>Opções salvas</h3>';
    echo '<pre>';
    var_dump($options);
    echo '</pre>';
    echo '<h3>Sessões salvas</h3>';
    if( session_start() ){
        echo '<pre>';
        var_dump($_SESSION);
        echo '</pre>';
    }else {
        echo 'Não foi possível iniciar a sessão!';
    }
}
function oscar_minc_delete_user_video_sent_meta( $args ) { ?>
    <input id="delete_user_video_sent_meta" name="delete_user_video_sent_meta" type="number" value="">
    <p class="description">
        Insira o ID do usuário para deletar sua limitação ao enviar vídeos.
    </p>
    <?php
}
function oscar_minc_monitoring_emails( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>

    <input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="text" value="<?php echo $options['oscar_minc_monitoring_emails']; ?>">
    <p class="description">
        Estes emails receberão uma notificação sempre que for realizado uma inscrição ou edição do formulário de inscrição ao Oscar 2018. Separe múltiplos emails com vírgulas.
    </p>
    <?php
}
function oscar_minc_deadline_time( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>

    <input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="text" value="<?php echo $options['oscar_minc_deadline_time']; ?>">
    <p class="description">
        Seguindo o seguinte padrão: <b>AAAA-MM-DD 24:59:59</b>
    </p>
    <?php
}
function oscar_minc_deadline_text( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>

    <textarea id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" rows="5"><?php echo $options['oscar_minc_deadline_text']; ?></textarea>
    <?php
}
if( !empty( $_POST['delete_user_video_sent_meta'] ) ){
    if( !delete_user_meta($_POST['delete_user_video_sent_meta'], '_oscar_minc_video_sent') ){
        error_log("Não foi possível remover a limitação para envio de usuários do ID " . $_POST['delete_user_video_sent_meta']);
    }
}
function oscar_minc_users_that_can_surpass_deadline( $args ) {
    $options = get_option( 'oscar_minc_options' ); ?>

    <input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="oscar_minc_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="text" value="<?php echo $options['oscar_minc_users_that_can_surpass_deadline']; ?>">
    <p class="description">
        ID dos usuários que poderão fazer login (em páginas alternativas, não com slug <b>login</b>) e enviar vídeos normalmente. Separe os usuários por vírgula.
    </p>
    <?php
}